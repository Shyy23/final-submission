<?php

namespace App\Livewire\Submission\Mahasiswa;

use Livewire\Component;
use App\Models\Submission;
use App\Models\Student;
use App\Models\StudyProgram;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActionPanel extends Component
{
    public Submission $submission;
    
    // Form Properties
    public $company_name;
    public $address_company;
    public $note;
    public $start_date;
    public $duration_days;
    public $endDate;

    // Team Member Logic
    public $searchMember = '';
    public $selectedMembers = [];
    public $availableStudents = [];
    
    public function mount(Submission $submission)
    {
        $this->submission = $submission;

        // Jika status REJECTED, muat data lama ke form
        if ($this->submission->status === 'rejected') {
            $this->company_name = $this->submission->company_name;
            $this->address_company = $this->submission->address_company;
            $this->note = $this->submission->note;
            $this->start_date = $this->submission->start_date ? $this->submission->start_date->format('Y-m-d') : '';
            $this->duration_days = $this->submission->duration_days;
            
            $this->calculateEndDate();
            $this->loadExistingMembers();
        }
    }

    public function loadExistingMembers()
    {
        $members = $this->submission->memberStudents;
        foreach($members as $member) {
            // Jangan masukkan ketua (diri sendiri) ke list yang bisa dihapus
            if($member->nim !== $this->submission->representative_nim) {
                $this->selectedMembers[] = [
                    'nim' => $member->nim,
                    'name' => $member->user->name,
                    'study_program' => $member->studyProgram->study_name ?? '-',
                ];
            }
        }
    }

    public function updatedStartDate() { $this->calculateEndDate(); }
    public function updatedDurationDays() { $this->calculateEndDate(); }

    public function calculateEndDate()
    {
        if ($this->start_date && $this->duration_days) {
            try {
                $this->endDate = Carbon::parse($this->start_date)
                    ->addDays((int)$this->duration_days)
                    ->translatedFormat('d F Y');
            } catch (\Exception $e) {
                $this->endDate = null;
            }
        }
    }

    public function updatedSearchMember()
    {
        $this->loadAvailableStudents();
    }

    public function loadAvailableStudents()
    {
        if (strlen($this->searchMember) < 3) {
            $this->availableStudents = [];
            return;
        }

        $currentNim = Auth::user()->student->nim;
        $selectedNims = array_column($this->selectedMembers, 'nim');
        $selectedNims[] = $currentNim;

        $this->availableStudents = Student::with(['user', 'studyProgram'])
            ->whereNotIn('nim', $selectedNims)
            ->where(function ($q) {
                $q->where('nim', 'like', '%' . $this->searchMember . '%')
                  ->orWhereHas('user', function ($u) {
                      $u->where('name', 'like', '%' . $this->searchMember . '%');
                  });
            })
            ->limit(5)
            ->get()
            ->map(function ($student) {
                return [
                    'nim' => $student->nim,
                    'name' => $student->user->name,
                    'study_program' => $student->studyProgram->study_name ?? '-',
                ];
            })->toArray();
    }

    public function addMember($nim, $name)
    {
        if (count($this->selectedMembers) >= 5) {
            $this->addError('members', 'Maksimal anggota tambahan 5 orang.');
            return;
        }

        // Kita perlu ambil prodi lagi agar tampilan konsisten
        $student = Student::with('studyProgram')->where('nim', $nim)->first();

        $this->selectedMembers[] = [
            'nim' => $nim,
            'name' => $name,
            'study_program' => $student->studyProgram->study_name ?? '-' 
        ];
        
        $this->searchMember = '';
        $this->availableStudents = [];
    }

    public function removeMember($index)
    {
        unset($this->selectedMembers[$index]);
        $this->selectedMembers = array_values($this->selectedMembers);
    }

    public function resubmit()
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'address_company' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'duration_days' => 'required|numeric|min:1',
            'selectedMembers' => 'array|max:5'
        ]);

        DB::transaction(function () {
            // 1. Update Data Submission
            $this->submission->update([
                'company_name' => $this->company_name,
                'address_company' => $this->address_company,
                'start_date' => $this->start_date,
                'duration_days' => $this->duration_days,
                'note' => $this->note,
                'status' => 'pending', // Reset ke Pending
                'feedback' => null,
                'document_path' => null,
                'admin_id' => null,
            ]);

            // 2. Sync Members (FIXED BUG: Representative Hilang)
            
            // A. Siapkan Array untuk Sync
            $syncData = [];

            // B. Masukkan Ketua (Representative) secara manual
            // Kita set is_representative = true
            $representativeNim = $this->submission->representative_nim;
            $syncData[$representativeNim] = ['is_representative' => true];

            // C. Masukkan Anggota Tambahan dari selectedMembers
            foreach ($this->selectedMembers as $member) {
                // Pastikan tidak menimpa data ketua jika ada duplikasi NIM (defensive)
                if ($member['nim'] !== $representativeNim) {
                    $syncData[$member['nim']] = ['is_representative' => false];
                }
            }
            
            // D. Lakukan Sync
            // Sync akan menghapus data di tabel pivot yang TIDAK ada di array $syncData
            // dan menambahkan/update data yang ADA di array $syncData.
            $this->submission->memberStudents()->sync($syncData);
        });

        session()->flash('message', 'Pengajuan berhasil diperbaiki dan dikirim ulang.');
        
        // Redirect untuk refresh halaman penuh agar state bersih
        return redirect()->route('submissions.show', $this->submission->submission_id);
    }

    // Logika Pembatalan (Hanya Pending)
    public function confirmCancelSubmission()
    {
        $this->dispatch('show-confirm-dialog',
            message: 'Apakah Anda yakin ingin membatalkan pengajuan ini?',
            method: 'do-cancel-submission', 
            params: ['submissionId' => $this->submission->submission_id] // Gunakan submission_id sesuai Primary Key model
        );
    }

    public function render()
    {
        return view('livewire.submission.mahasiswa.action-panel');
    }
}