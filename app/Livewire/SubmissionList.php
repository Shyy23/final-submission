<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Submission;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class SubmissionList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $perPage = 10;
    public $role;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function mount()
    {
        $this->role = Auth::user()->getRoleNames()->first();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->resetPage();
    }

    public function getSubmissionsQuery()
    {
        $query = Submission::with([
            'representative.user',
            'memberStudents.user',
            'admin'
        ]);

        // Filter berdasarkan role
        switch ($this->role) {
            case 'mahasiswa':
                $student = Auth::user()->student;
                if ($student) {
                    $query->where(function ($q) use ($student) {
                        $q->where('representative_nim', $student->nim)
                            ->orWhereHas('members', function ($q) use ($student) {
                                $q->where('student_nim', $student->nim);
                            });
                    });
                }
                break;

            case 'admin':
                // Admin melihat semua submission tanpa default filter
                // Biarkan filter status bekerja normal
                break;

            case 'pimpinan':
                // Pimpinan melihat yang approved dan verified
                if (!$this->status) {
                    $query->whereIn('status', ['approved', 'verified']);
                }
                break;
        }

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%')
                    ->orWhere('address_company', 'like', '%' . $this->search . '%')
                    ->orWhereHas('representative.user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Apply status filter
        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function getSubmissionsProperty()
    {
        return $this->getSubmissionsQuery()->paginate($this->perPage);
    }

    public function getTotalSubmissionsProperty()
    {
        return $this->getSubmissionsQuery()->count();
    }

    public function confirmDeleteSubmission($submissionId) // <-- 2. BUAT FUNGSI BARU INI
    {
        $this->dispatch('show-confirm-dialog', 
            message: 'Apakah Anda yakin ingin menghapus pengajuan ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua file yang terkait.', 
            method: 'do-delete-submission', // Nama event baru
            params: ['submissionId' => $submissionId] // Kirim ID sebagai array
        );
    }

    #[On('do-delete-submission')] // <-- 3. TAMBAHKAN LISTENER INI
    public function deleteSubmission($submissionId)
    {
        try {
            $submission = Submission::findOrFail($submissionId);

            // Hapus file-file yang terkait
            $this->deleteSubmissionFiles($submission);

            // Hapus record dari database
            $submission->delete();

            session()->flash('message', 'Pengajuan berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus pengajuan: ' . $e->getMessage());
        }
    }

    private function deleteSubmissionFiles(Submission $submission)
    {
        try {
            // Hapus file submission utama
            if ($submission->file_submission && Storage::disk('local')->exists($submission->file_submission)) {
                Storage::disk('local')->delete($submission->file_submission);
            }

            // Hapus signed file jika ada
            if ($submission->signed_file && Storage::disk('local')->exists($submission->signed_file)) {
                Storage::disk('local')->delete($submission->signed_file);
            }

            // Hapus QR code dari public path
            if ($submission->qr_url) {
                $qrPath = public_path($submission->qr_url);
                if (file_exists($qrPath)) {
                    unlink($qrPath);
                }

                // Juga hapus dari storage jika ada
                $qrStoragePath = str_replace('qr-code/', 'qr-code/', $submission->qr_url);
                if (Storage::disk('public')->exists($qrStoragePath)) {
                    Storage::disk('public')->delete($qrStoragePath);
                }
            }
        } catch (\Exception $e) {
            // Log error tetapi jangan hentikan proses penghapusan
            Log::error('Error deleting submission files: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.submission.submission-list', [
            'submissions' => $this->submissions,
            'totalSubmissions' => $this->totalSubmissions,
        ])->layout('layouts.app');
    }
}
