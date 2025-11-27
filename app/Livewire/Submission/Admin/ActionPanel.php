<?php

namespace App\Livewire\Submission\Admin;

use App\Livewire\Submission\SubmissionDetail;
use App\Mail\SubmissionStatusNotification;
use App\Models\StudyProgram;
use Livewire\Component;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class ActionPanel extends Component
{
    public Submission $submission;
    public $showActionModal = false;
    public $showEditModal = false;
    public $feedback = '';
    public $actionType = ''; // 'approve' or 'reject'

    // DATA EDIT
    public $editData = [
        'company_name' => '',
        'department_name' => '', 
    ];

    public $studyPrograms = []; 

    public function mount(Submission $submission)
    {
        $this->submission = $submission;
        // Ambil data prodi untuk dropdown
        $this->studyPrograms = StudyProgram::orderBy('study_name')->get();
    }
    
    #[On('submission-updated')] 
    public function refreshData()
    {
        $this->submission->refresh();
    }

    // Helper: Ambil Kode Prodi dari Database
    private function getStudyCodeFromDB($prodiName)
    {
        $prodi = StudyProgram::where('study_name', $prodiName)->first();
        return $prodi ? $prodi->study_code : null; // Return code atau null
    }

    public function openActionModal($type)
    {
        $this->actionType = $type;
        $this->feedback = $type === 'approve' ? 'Pengajuan disetujui. Dokumen telah dibuat.' : '';
        $this->showActionModal = true;
    }

    // --- FITUR : PREVIEW DOCUMENT ---
    public function downloadDocument()
    {
        if (!$this->submission->document_path || !Storage::exists($this->submission->document_path)) {
            session()->flash('error', 'File dokumen tidak ditemukan.');
            return;
        }
        // Download file draft
        return Storage::download($this->submission->document_path, 'DRAFT_Surat_Tugas_' . $this->submission->id . '.pdf');
    }
    public function openRejectModal()
    {
        $this->actionType = 'reject';
        $this->feedback = ''; 
        $this->showActionModal = true;
    }
    
    protected function rules()
    {
        return [
            'feedback' => $this->actionType === 'reject' ? 'required|max:1000' : 'max:1000',
        ];
    }

    public function processAdminAction()
    {
        $this->validate();

        try {
            $emailStatusType = $this->actionType === 'approve' ? 'approved' : 'rejected';
            
            if ($this->actionType === 'approve') {
                // 1. Tentukan Nama Prodi (Prioritas: yang sudah ada di submission -> representative)
                $deptName = $this->submission->department_name ?? $this->submission->representative->studyProgram->study_name;
                
                // 2. AMBIL KODE DARI DATABASE (Otomatis)
                $deptCode = $this->getStudyCodeFromDB($deptName) ?? 'XX';

                // 3. Update Submission dengan Data Prodi & Kode yang Valid
                $this->submission->update([
                    'status' => 'approved',
                    'admin_id' => Auth::id(),
                    'feedback' => $this->feedback,
                    'sent_to_leader' => false,
                    'department_name' => $deptName,
                    'department_code' => $deptCode, // Simpan kode agar PDF konsisten
                ]);

                // Generate PDF
                $this->dispatch('generateAndSavePdf', withQr: false)->to(SubmissionDetail::class); 
                session()->flash('message', 'Pengajuan disetujui. Dokumen berhasil dibuat.');

            } else {
                $this->submission->update([
                    'status' => 'rejected',
                    'feedback' => $this->feedback,
                    'document_path' => null 
                ]);
                session()->flash('message', 'Pengajuan ditolak.');
            }

            // Kirim Email Notifikasi
            try {
                $representative = $this->submission->representative;
                if ($representative && $representative->user && $representative->user->email) {
                    Mail::to($representative->user->email)->send(
                        new SubmissionStatusNotification($this->submission, $emailStatusType, $this->feedback)
                    );
                }
            } catch (\Exception $mailEx) {
                Log::error('Gagal kirim email: ' . $mailEx->getMessage());
            }

            $this->showActionModal = false;
            $this->dispatch('submission-updated'); 
            
        } catch (\Exception $e) {
             session()->flash('error', 'Gagal memproses aksi: ' . $e->getMessage());
        }
    }

    public function sendToLeader()
    {
        // Validasi dasar
        if ($this->submission->status !== 'approved' || !$this->submission->document_path) {
            session()->flash('error', 'Dokumen belum siap/status salah.');
            return;
        }

        try {
            // Update database
            $this->submission->update(['sent_to_leader' => true]);
            
            // Kirim event notifikasi (Flash message)
            session()->flash('message', 'Dokumen berhasil dikirim ke Pimpinan.');
            
            // Refresh komponen ini dan parent
            $this->dispatch('submission-updated');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengirim: ' . $e->getMessage());
        }
    }

    // 3. ACTION: EDIT & REGENERATE
    public function openEditModal()
    {
        // Load data saat ini
        $defaultDept = $this->submission->department_name 
            ?? ($this->submission->representative->studyProgram->study_name ?? '');

        $this->editData = [
            'company_name' => $this->submission->company_name,
            'department_name' => $defaultDept, 
        ];
        $this->showEditModal = true;
    }

    protected function editRules()
    {
        return [
            'editData.company_name' => 'required|string',
            'editData.department_name' => 'required|string|exists:study_programs,study_name', // Validasi ke tabel
        ];
    }

    public function updateAndRegenerate()
    {
        $this->validate($this->editRules());

        try {
            // 1. Cari Kode Prodi baru dari DB berdasarkan pilihan Dropdown
            $newCode = $this->getStudyCodeFromDB($this->editData['department_name']) ?? 'XX';

            // 2. Update Data di DB
            $this->submission->update([
                'company_name' => $this->editData['company_name'],
                'department_name' => $this->editData['department_name'],
                'department_code' => $newCode, // Update kode otomatis
            ]);

            // 3. Regenerate PDF
            $this->dispatch('generateAndSavePdf', withQr: false)->to(SubmissionDetail::class); 

            $this->showEditModal = false;
            session()->flash('message', 'Data diperbarui. Prodi: ' . $this->editData['department_name'] . ' (Kode: ' . $newCode . ')');
            $this->dispatch('submission-updated');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function confirmDeleteDocument()
    {
        $this->dispatch('show-confirm-dialog', 
            message: 'Apakah Anda yakin ingin menghapus/reset dokumen ini? Status pengajuan akan kembali menjadi Pending.', 
            method: 'do-delete-document-admin'
        );
    }

    // 2. Tambahkan Listener Event
    #[On('do-delete-document-admin')]
    public function deleteDocument()
    {
        if ($this->submission->status !== 'approved') {
            session()->flash('error', 'Aksi ini hanya bisa dilakukan pada status Approved.');
            return;
        }

        try {
            // Hapus file fisik
            if ($this->submission->document_path && Storage::exists($this->submission->document_path)) {
                Storage::delete($this->submission->document_path);
            }

            // Reset status ke pending
            $this->submission->update([
                'status' => 'pending',
                'document_path' => null,
                'admin_id' => null,
                'sent_to_leader' => false,
                'feedback' => null,
            ]);

            session()->flash('message', 'Dokumen dihapus. Status kembali menjadi Pending.');
            $this->dispatch('submission-updated');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.submission.admin.action-panel');
    }
}