<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\StudyProgram;
use App\Models\Submission;
use App\Models\SubmissionMember;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SubmissionForm extends Component
{
    public $company_name;
    public $address_company;
    public $note;

    // Untuk filtering
    public $selectedStudy = '';
    public $searchTerm = '';

    // Untuk selected members
    public $selectedMembers = [];
    public $availableStudents = [];
    public $isLoading = false; 

    protected $rules = [
        'company_name' => 'required|string|max:255',
        'address_company' => 'required|string',
        'note' => 'nullable|string|max:255',
        'selectedMembers' => 'array|max:5'
    ];

    public function mount()
    {
        $currentUserNim = Auth::user()->student->nim;
        
        // FIX: Ambil data study program user yang sedang login agar konsisten
        $myStudy = Auth::user()->student->studyProgram->study_name ?? '-';

        $this->selectedMembers[$currentUserNim] = [
            'nim' => $currentUserNim,
            'name' => Auth::user()->name,
            'study_program' => $myStudy, // Simpan prodi
            'is_representative' => true
        ];

        $this->loadAvailableStudents();
    }

    public function loadAvailableStudents()
    {
        $this->isLoading = true;

        $query = Student::with(['user', 'studyProgram'])
            ->where('nim', '!=', Auth::user()->student->nim)
            // FIX: Tambahkan filter whereHas user -> is_verified = true
            ->whereHas('user', function($q) {
                $q->where('is_verified', true);
            });

        if ($this->selectedStudy) {
            $query->where('study_id', $this->selectedStudy);
        }

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('nim', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        }

        $this->availableStudents = $query->limit(10)->get() // Limit agar tidak terlalu berat
            ->map(function ($student) {
                return [
                    'nim' => $student->nim,
                    'name' => $student->user->name,
                    'study_program' => $student->studyProgram->study_name ?? '-',
                    'is_selected' => isset($this->selectedMembers[$student->nim])
                ];
            })->toArray();

        $this->isLoading = false;
    }

    public function updatedSelectedStudy()
    {
        $this->loadAvailableStudents();
    }

    public function updatedSearchTerm()
    {
        $this->loadAvailableStudents();
    }

    public function toggleMember($nim, $name)
    {
        if (isset($this->selectedMembers[$nim])) {
            unset($this->selectedMembers[$nim]);
        } else {
            if (count($this->selectedMembers) >= 6) {
                session()->flash('error', 'Maksimal 6 anggota termasuk diri sendiri');
                return;
            }

            // FIX: Cari data mahasiswa untuk mendapatkan nama Prodinya
            $studentData = Student::with('studyProgram')->where('nim', $nim)->first();
            $studyName = $studentData->studyProgram->study_name ?? '-';

            $this->selectedMembers[$nim] = [
                'nim' => $nim,
                'name' => $name,
                'study_program' => $studyName, // Simpan prodi ke array selected
                'is_representative' => false
            ];
        }

        $this->loadAvailableStudents();
    }

    public function removeMember($nim)
    {
        if ($nim !== Auth::user()->student->nim) {
            unset($this->selectedMembers[$nim]);
            $this->loadAvailableStudents();
        }
    }

    public function submit()
    {
        $this->validate();

        try {
            $submission = Submission::create([
                'representative_nim' => Auth::user()->student->nim,
                'company_name' => $this->company_name,
                'address_company' => $this->address_company,
                'note' => $this->note,
            ]);

            foreach ($this->selectedMembers as $member) {
                SubmissionMember::create([
                    'submission_id' => $submission->submission_id,
                    'student_nim' => $member['nim'],
                    'is_representative' => $member['is_representative']
                ]);
            }

            session()->flash('success', 'Submission berhasil dikirim! Silakan tunggu persetujuan Admin.');
            return redirect()->route('submissions.history');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $studyPrograms = StudyProgram::all();

        return view('livewire.submission.submission-form', [
            'studyPrograms' => $studyPrograms,
            'currentUser' => Auth::user()
        ]);
    }
}