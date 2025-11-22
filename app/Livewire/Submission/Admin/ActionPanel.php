<?php

namespace App\Livewire\Submission\Admin;

use App\Livewire\Submission\SubmissionDetail;
use App\Mail\SubmissionStatusNotification;
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

    public $editData = [
        'start_date' => '',
        'duration_days' => '',
        'company_name' => '',
    ];

    public function mount(Submission $submission)
    {
        $this->submission = $submission;
    }
    
    // Muat ulang data saat ada event dari parent
    #[On('submission-updated')] 
    public function refreshData()
    {
        $this->submission->refresh();
    }

    public function openActionModal($type)
    {
        $this->actionType = $type;
        $this->feedback = $type === 'approve' ? 'Pengajuan disetujui. Dokumen telah dibuat.' : '';
        $this->showActionModal = true;
    }

    // --- FITUR BARU: PREVIEW DOCUMENT ---
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
            // Simpan status type untuk email (approve/reject)
            $emailStatusType = $this->actionType === 'approve' ? 'approved' : 'rejected';
            
            if ($this->actionType === 'approve') {
                // 1. Update Status
                $this->submission->update([
                    'status' => 'approved',
                    'admin_id' => Auth::id(),
                    'feedback' => $this->feedback,
                    'sent_to_leader' => false, 
                ]);

                // 2. Generate PDF
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

            // --- LOGIKA KIRIM EMAIL NOTIFIKASI ---
            try {
                $representative = $this->submission->representative;
                if ($representative && $representative->user && $representative->user->email) {
                    // Kirim Email
                    Mail::to($representative->user->email)->send(
                        new SubmissionStatusNotification($this->submission, $emailStatusType, $this->feedback)
                    );
                    Log::info("Email notifikasi ($emailStatusType) dikirim ke: " . $representative->user->email);
                }
            } catch (\Exception $mailEx) {
                // Jangan hentikan proses hanya karena email gagal
                Log::error('Gagal mengirim email notifikasi admin: ' . $mailEx->getMessage());
            }
            // -------------------------------------

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
        // Load data saat ini ke form edit
        $this->editData = [
            'company_name' => $this->submission->company_name,
            'start_date' => $this->submission->start_date ? $this->submission->start_date->format('Y-m-d') : '',
            'duration_days' => $this->submission->duration_days,
        ];
        $this->showEditModal = true;
    }

    protected function editRules()
    {
        return [
            'editData.company_name' => 'required|string',
            'editData.start_date' => 'required|date',
            'editData.duration_days' => 'required|numeric',
        ];
    }

    public function updateAndRegenerate()
    {
        $this->validate($this->editRules());

        try {
            // Update Data di DB
            $this->submission->update([
                'company_name' => $this->editData['company_name'],
                'start_date' => $this->editData['start_date'],
                'duration_days' => $this->editData['duration_days'],
            ]);

            // Regenerate PDF dengan data baru (Tanpa QR)
            $this->dispatch('generateAndSavePdf', withQr: false)->to(SubmissionDetail::class); 

            $this->showEditModal = false;
            session()->flash('message', 'Data diperbarui dan dokumen berhasil dibuat ulang.');
            $this->dispatch('submission-updated');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal update dan regenerate dokumen: ' . $e->getMessage());
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