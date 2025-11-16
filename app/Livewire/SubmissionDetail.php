<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Submission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\NotificationService;
use Livewire\Attributes\On;

class SubmissionDetail extends Component
{
    use WithFileUploads;

    public $submission;
    public $submissionId;
    public $newFile;
    public $signedFile;
    public $showUploadModal = false;
    public $showSignatureModal = false;
    public $showActionModal = false;
    public $feedback = '';
    public $actionType = ''; // 'approve' or 'reject'
    public $qrCodeGenerated = false;

    protected $rules = [
        'newFile' => 'required|file|mimes:pdf|max:10240',
        'signedFile' => 'required|file|mimes:pdf|max:10240',
        'feedback' => 'required_if:actionType,reject|max:1000',
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
        $user = Auth::user();

        if ($user->hasRole('mahasiswa')) {
            $student = $user->student;
            $this->submission = Submission::with([
                'representative.user',
                'memberStudents.user',
                'admin',
                'leader.user'
            ])->where(function ($query) use ($student) {
                $query->where('representative_nim', $student->nim)
                    ->orWhereHas('members', function ($q) use ($student) {
                        $q->where('student_nim', $student->nim);
                    });
            })->findOrFail($this->submissionId);
        } else {
            $this->submission = Submission::with([
                'representative.user',
                'memberStudents.user',
                'admin',
                'leader.user'
            ])->findOrFail($this->submissionId);
        }
    }

    // Method untuk Mahasiswa

    public function confirmCancelSubmission() // <-- 1. BUAT FUNGSI BARU INI
    {
        // Karena tidak ada parameter yang diperlukan, kita hanya mengirim method
        $this->dispatch('show-confirm-dialog',
            message: 'Apakah Anda yakin ingin membatalkan pengajuan ini? Tindakan ini tidak dapat dibatalkan.',
            method: 'do-cancel-submission'
        );
    }

    #[On('do-cancel-submission')] // <-- 2. TAMBAHKAN LISTENER INI
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

        // Hapus file-file terkait (jika perlu, seperti yang Anda lakukan di SubmissionList)
        // Jika tidak, lanjutkan saja.

        $this->submission->delete();
        session()->flash('message', 'Pengajuan berhasil dibatalkan.');
        return redirect()->route('submissions.history');
    }

    public function uploadNewFile()
    {
        $this->validateOnly('newFile');

        if ($this->submission->status !== 'rejected') {
            session()->flash('error', 'Hanya pengajuan yang ditolak yang dapat mengupload ulang file.');
            return;
        }

        if (Auth::user()->student->nim !== $this->submission->representative_nim) {
            session()->flash('error', 'Hanya perwakilan kelompok yang dapat mengupload ulang file.');
            return;
        }

        // Delete old file
        if ($this->submission->file_submission && Storage::exists($this->submission->file_submission)) {
            Storage::delete($this->submission->file_submission);
        }

        $filePath = $this->newFile->store('submission', 'local');

        $this->submission->update([
            'file_submission' => $filePath,
            'status' => 'pending',
            'feedback' => null,
        ]);

        $this->showUploadModal = false;
        $this->newFile = null;
        $this->loadSubmission();

        session()->flash('message', 'File berhasil diupload ulang. Status kembali menjadi pending.');
    }

    // Method untuk Admin
    public function openActionModal($type)
    {
        $this->actionType = $type;
        $this->feedback = $type === 'approve' ? 'Pengajuan diterima. Menunggu untuk ditandatangani.' : '';
        $this->showActionModal = true;
    }

    public function processSubmission()
    {
        $this->validate([
            'feedback' => $this->actionType === 'reject' ? 'required|max:1000' : 'max:1000',
        ]);

        $updateData = [
            'status' => $this->actionType === 'approve' ? 'approved' : 'rejected',
            'feedback' => $this->feedback,
            'admin_id' => Auth::id(),
        ];

        // Jika approve, generate QR code
        if ($this->actionType === 'approve') {
            $this->generateQRCode();
        }

        $this->submission->update($updateData);

        // KIRIM EMAIL NOTIFIKASI
        $actionBy = Auth::user()->name . ' (Admin)';
        $emailSent = $this->notificationService->sendSubmissionStatusUpdate(
            $this->submission, 
            $updateData['status'], 
            $actionBy, 
            $this->feedback
        );

        $this->showActionModal = false;
        $this->feedback = '';
        $this->loadSubmission();

        $message = $this->actionType === 'approve'
            ? 'Pengajuan berhasil disetujui. QR code telah digenerate.' . ($emailSent ? ' Email notifikasi telah dikirim.' : '')
            : 'Pengajuan berhasil ditolak.' . ($emailSent ? ' Email notifikasi telah dikirim.' : '');

        session()->flash('message', $message);
    }

    // Method untuk Pimpinan
    // Method untuk Pimpinan - UPDATE method uploadSignedDocument
    public function uploadSignedDocument()
    {
        $this->validateOnly('signedFile');

        if ($this->submission->status !== 'approved') {
            session()->flash('error', 'Hanya pengajuan yang sudah disetujui yang dapat diupload dokumen tandatangan.');
            return;
        }

        // Store signed document
        $signedFilePath = $this->signedFile->store('signed-submissions', 'local');

        // Dapatkan leader_id dari user yang login
        $leader = Auth::user()->leader;

        $this->submission->update([
            'file_submission' => $signedFilePath,
            'status' => 'verified',
            'leader_id' => $leader->nid,
        ]);

        // KIRIM EMAIL NOTIFIKASI
        $actionBy = Auth::user()->name . ' (Pimpinan)';
        $emailSent = $this->notificationService->sendSubmissionStatusUpdate(
            $this->submission, 
            'verified', 
            $actionBy
        );

        $this->showSignatureModal = false;
        $this->signedFile = null;
        $this->loadSubmission();

        $message = 'Dokumen berhasil diupload dan status diubah menjadi terverifikasi.' . 
                  ($emailSent ? ' Email notifikasi telah dikirim.' : '');

        session()->flash('message', $message);
    }

    private function generateQRCode()
    {
        try {
            // Generate QR content - HANYA URL untuk verifikasi
            $verificationUrl = route('submissions.verification', $this->submission->submission_id);

            // Gunakan HANYA URL sebagai konten QR code
            $qrContent = $verificationUrl;

            // Generate nama file
            $qrFileName = 'qr_submission_' . $this->submission->submission_id . '_' . Str::random(8) . '.png';
            $qrDirectory = public_path('qr-code');
            $qrPath = $qrDirectory . '/' . $qrFileName;

            // Buat direktori jika belum ada
            if (!file_exists($qrDirectory)) {
                mkdir($qrDirectory, 0755, true);
            }

            // Generate QR Code dengan ImageMagick
            $qrCode = QrCode::format('png')
                ->size(400)
                ->margin(2)
                ->errorCorrection('H')
                ->color(30, 64, 175) // Warna biru
                ->backgroundColor(255, 255, 255)
                ->encoding('UTF-8');

            // Tambahkan logo jika ada
            $logoPath = public_path('assets/img/unjani.png');
            if (file_exists($logoPath)) {
                $qrCode = $qrCode->merge($logoPath, .2, true);
            }

            $qrCodePng = $qrCode->generate($qrContent);

            // Simpan ke public/qr-code/
            file_put_contents($qrPath, $qrCodePng);

            // Update submission dengan path QR code
            $this->submission->update([
                'qr_url' => $qrFileName
            ]);

            $this->qrCodeGenerated = true;

            Log::info('QR Code generated successfully with URL: ' . $verificationUrl);
        } catch (\Exception $e) {
            Log::error('QR Code Generation Error: ' . $e->getMessage());
            // Fallback ke QR code sederhana dengan URL saja
            $this->generateSimpleQRCode();
        }
    }

    private function generateSimpleQRCode()
    {
        try {
            $qrFileName = 'qr_simple_' . $this->submission->submission_id . '.png';
            $qrDirectory = public_path('qr-code');
            $qrPath = $qrDirectory . '/' . $qrFileName;

            // Buat direktori jika belum ada
            if (!file_exists($qrDirectory)) {
                mkdir($qrDirectory, 0755, true);
            }

            $verificationUrl = route('submissions.verification', $this->submission->submission_id);

            // Hanya URL sebagai konten
            $qrContent = $verificationUrl;

            $qrCodePng = QrCode::format('png')
                ->size(300)
                ->margin(2)
                ->errorCorrection('H')
                ->color(30, 64, 175)
                ->backgroundColor(255, 255, 255)
                ->generate($qrContent);

            file_put_contents($qrPath, $qrCodePng);

            $this->submission->update([
                'qr_url' => $qrFileName
            ]);

            Log::info('Simple QR Code generated with URL: ' . $verificationUrl);
        } catch (\Exception $e) {
            Log::error('Simple QR Code Generation Error: ' . $e->getMessage());
        }
    }
    // Common Methods
    public function downloadFile()
    {
        if (!Storage::exists($this->submission->file_submission)) {
            session()->flash('error', 'File tidak ditemukan.');
            return;
        }

        $fileName = $this->submission->status === 'verified'
            ? 'surat_tugas_akhir_verified_' . $this->submission->company_name . '.pdf'
            : 'surat_tugas_akhir_' . $this->submission->company_name . '.pdf';

        return Storage::download($this->submission->file_submission, $fileName);
    }

    public function downloadQRCode()
    {
        if (!$this->submission->qr_url) {
            session()->flash('error', 'QR Code tidak tersedia.');
            return;
        }

        $qrPath = public_path('qr-code/' . $this->submission->qr_url);

        if (!file_exists($qrPath)) {
            session()->flash('error', 'File QR Code tidak ditemukan.');
            return;
        }

        $fileName = 'qr_code_verifikasi_' . $this->submission->company_name . '.png';

        return response()->download($qrPath, $fileName);
    }

    public function render()
    {
        return view('livewire.submission.submission-detail')
            ->layout('layouts.detail');
    }
}
