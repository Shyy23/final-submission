<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Submission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\NotificationService;
use Livewire\Attributes\On;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SubmissionDetail extends Component
{

   public $submission;
    public $submissionId;
    
    // Modal controls
    public $showActionModal = false; // Untuk Admin (Approve/Reject)
    public $showConfirmModal = false; // Untuk Pimpinan (Konfirmasi TTD)
    
    public $feedback = '';
    public $actionType = ''; // 'approve' or 'reject'
    public $showEditModal = false;
    public $editData = [
        'start_date' => '',
        'duration_days' => '',
        'company_name' => '',
    ];
    protected $notificationService;

public function boot()
    {
        $this->notificationService = new NotificationService();
    }

    public function mount($id)
    {
        $this->submissionId = $id;
        $this->loadSubmission();
    }

   public function loadSubmission()
    {
        $this->submission = Submission::with(['representative.user', 'memberStudents.user', 'admin', 'leader.user'])
            ->findOrFail($this->submissionId);
    }

    // Method untuk Mahasiswa
    public function confirmCancelSubmission()
    {
        $this->dispatch('show-confirm-dialog',
            message: 'Apakah Anda yakin ingin membatalkan pengajuan ini? Tindakan ini tidak dapat dibatalkan.',
            method: 'do-cancel-submission'
        );
    }

    #[On('do-cancel-submission')]
    public function cancelSubmission()
    {
        if ($this->submission->status !== 'pending') {
            session()->flash('error', 'Hanya pengajuan dengan status pending yang dapat dibatalkan.');
            return;
        }

        if (Auth::user()->student->nim !== $this->submission->representative_nim) {
            session()->flash('error', 'Hanya perwakilan kelompok yang dapat membatalkan pengajuan.');
            return;
        }

        $this->submission->delete();
        session()->flash('message', 'Pengajuan berhasil dibatalkan.');
        return redirect()->route('submissions.history');
    }

   // ==========================================
    // 1. ADMIN ACTIONS
    // ==========================================

    public function openActionModal($type)
    {
        $this->actionType = $type;
        $this->feedback = $type === 'approve' ? 'Pengajuan disetujui. Dokumen telah dibuat.' : '';
        $this->showActionModal = true;
    }
    public function deleteDocument()
    {
        if ($this->submission->status !== 'approved') {
            return;
        }

        // Hapus file fisik
        if ($this->submission->document_path && Storage::exists($this->submission->document_path)) {
            Storage::delete($this->submission->document_path);
        }

        // Reset status ke pending
        $this->submission->update([
            'status' => 'pending',
            'document_path' => null,
            'admin_id' => null,
            'sent_to_leader' => false
        ]);

        session()->flash('message', 'Dokumen dihapus. Status kembali menjadi Pending.');
        $this->loadSubmission();
    }
    // 2. ACTION: KEMBALIKAN REVISI (Reject with Feedback)
    public function openRejectModal()
    {
        $this->actionType = 'reject';
        $this->feedback = ''; 
        $this->showActionModal = true;
    }

   public function processAdminAction()
    {
        $this->validate([
            'feedback' => $this->actionType === 'reject' ? 'required|max:1000' : 'max:1000',
        ]);

        if ($this->actionType === 'approve') {
            // 1. Update Status
            $this->submission->update([
                'status' => 'approved',
                'admin_id' => Auth::id(),
                'feedback' => $this->feedback,
                'sent_to_leader' => false, // Reset, belum dikirim
            ]);

            // 2. Generate PDF Awal (Tanpa QR / QR Kosong)
            $this->generateAndSavePdf(withQr: false);
            
            session()->flash('message', 'Pengajuan disetujui. Dokumen berhasil dibuat. Silakan cek preview sebelum dikirim ke pimpinan.');

        } else {
        $this->submission->update([
            'status' => 'rejected',
            'feedback' => $this->feedback,
            'document_path' => null // Hapus draft jika ada
        ]);
            
            // Kirim Email Notifikasi Reject di sini
            session()->flash('message', 'Pengajuan ditolak.');
        }

        $this->showActionModal = false;
        $this->loadSubmission();
    }

    public function sendToLeader()
    {
        if ($this->submission->status !== 'approved' || !$this->submission->document_path) {
            session()->flash('error', 'Dokumen belum siap untuk dikirim.');
            return;
        }

        $this->submission->update(['sent_to_leader' => true]);
        
        // Kirim Email Notifikasi ke Pimpinan
        
        session()->flash('message', 'Dokumen berhasil dikirim ke Pimpinan untuk ditandatangani.');
    }

    // 3. ACTION: EDIT & REGENERATE
    public function openEditModal()
    {
        // Load data saat ini ke form edit
        $this->editData = [
            'company_name' => $this->submission->company_name,
            'start_date' => $this->submission->start_date ? $this->submission->start_date->format('Y-m-d') : '',
            'duration_days' => $this->submission->duration_days,
        ];
        $this->showEditModal = true;
    }

    public function updateAndRegenerate()
    {
        $this->validate([
            'editData.company_name' => 'required|string',
            'editData.start_date' => 'required|date',
            'editData.duration_days' => 'required|numeric',
        ]);

        // Update Data di DB
        $this->submission->update([
            'company_name' => $this->editData['company_name'],
            'start_date' => $this->editData['start_date'],
            'duration_days' => $this->editData['duration_days'],
        ]);

        // Regenerate PDF dengan data baru
        $this->generateAndSavePdf(withQr: false); // Masih draft (tanpa QR)

        $this->showEditModal = false;
        session()->flash('message', 'Data diperbarui dan dokumen berhasil dibuat ulang.');
        $this->loadSubmission();
    }

   // ==========================================
    // 2. PIMPINAN ACTIONS
    // ==========================================

    public function openVerifyModal()
    {
        $this->showConfirmModal = true;
    }
   public function verifyAndSign()
    {
        $leader = Auth::user()->leader; // Asumsi user login punya relasi ke tabel leaders

        // 1. Generate QR Code
        $qrFileName = $this->generateQRCode();

        // 2. Update Data Submission
        $this->submission->update([
            'status' => 'verified',
            'leader_id' => $leader->nid, // Simpan ID Pimpinan
            'qr_url' => $qrFileName,
            'updated_at' => now(),
        ]);

        // 3. Re-Generate PDF (Sekarang dengan QR Code & TTD Digital)
        $this->generateAndSavePdf(withQr: true);

        // 4. Notifikasi
        // $this->notificationService->sendSubmissionVerified(...);

        $this->showConfirmModal = false;
        session()->flash('message', 'Dokumen berhasil ditandatangani secara digital dan diverifikasi.');
        $this->loadSubmission();
    }

    // Generate dokumen
    private function generateQRCode()
    {
        $verificationUrl = route('submissions.verification', $this->submission->submission_id);
        $qrFileName = 'qr_' . $this->submission->submission_id . '_' . Str::random(6) . '.png';
        $path = public_path('qr-code/' . $qrFileName);
        $logoPath = public_path('assets/img/unjani.png'); // Path ke logo unjani

        if (!file_exists(public_path('qr-code'))) {
            mkdir(public_path('qr-code'), 0755, true);
        }

        // Inisiasi QR Code Generator
        $qrCodeInstance = QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->color(0, 0, 0)
            ->errorCorrection('H'); // Error correction HARUS 'H' (High) saat merge logo

        // Merge logo jika file logo ada
        if (file_exists($logoPath)) {
            $qrCodeInstance->merge($logoPath, .25, true) // .25 = 25% ukuran QR, true = path file
                           ->generate($verificationUrl, $path);
        } else {
            // Generate QR biasa jika logo tidak ditemukan
            $qrCodeInstance->generate($verificationUrl, $path);
            Log::warning('Logo UNJANI tidak ditemukan di ' . $logoPath . '. QR Code dibuat tanpa logo.');
        }

        return $qrFileName;
    }

  // app/Livewire/SubmissionDetail.php (Method generateAndSavePdf)

      private function generateAndSavePdf($withQr = false)
    {
        // 1. Setup Locale
        Carbon::setLocale('id');
        $dateIndo = Carbon::now()->translatedFormat('d F Y');

        // 2. Data Mahasiswa (Anti Duplikat)
        $representative = $this->submission->representative;
        $members = $this->submission->memberStudents;
        $fixedStudents = collect([$representative])
            ->merge($members)
            ->unique('nim')
            ->values();

        // 3. Persiapan Data View
        $data = [
            'submission' => $this->submission,
            'students'   => $fixedStudents,
            'withQr'     => $withQr,
            'qrPath'     => $withQr && $this->submission->qr_url ? public_path('qr-code/' . $this->submission->qr_url) : null,
            'date'       => $dateIndo,
            'nomor_surat'=> 'B/'.$this->submission->submission_id.'/FSI-Unjani/'. \Carbon\Carbon::now()->format('m/Y'),

            // Path Logo (Gunakan public_path agar terbaca oleh DomPDF)
            'logo_ykep'   => public_path('assets/img/ykep.png'),
            'logo_unjani' => public_path('assets/img/unjani.png'), // Logo ini juga dipakai di TTD
        ];

        // 4. Nama File Output
        $fileName = 'surat_tugas_' . $this->submission->submission_id . '.pdf';
        $storagePath = 'documents/' . $fileName;

        // 5. Load View & Save (Ubah nama view ke yang baru)
        $pdf = Pdf::loadView('pdf.surat_tugas', $data)
                ->setPaper('a4', 'portrait');

        Storage::put($storagePath, $pdf->output());

        // Update database
        $this->submission->update(['document_path' => $storagePath]);
    }
        // Common Methods
    public function downloadFile()
    {
        if (!$this->submission->file_submission || !Storage::exists($this->submission->file_submission)) {
            session()->flash('error', 'File tidak ditemukan.');
            return;
        }

        $fileName = $this->submission->status === 'verified'
            ? 'surat_tugas_akhir_verified_' . $this->submission->company_name . '.pdf'
            : 'surat_tugas_akhir_' . $this->submission->company_name . '.pdf';

        return Storage::download($this->submission->file_submission, $fileName);
    }

    public function downloadDocument()
    {
        if (!$this->submission->document_path || !Storage::exists($this->submission->document_path)) {
            session()->flash('error', 'File dokumen tidak ditemukan.');
            return;
        }
        return Storage::download($this->submission->document_path);
    }

    public function render()
    {
        return view('livewire.submission.submission-detail')
            ->layout('layouts.detail');
    }
}