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

class SubmissionForm extends Component
{
    public $company_name;
    public $address_company;
    public $note;
    public $start_date; // Tambahkan field tanggal mulai
    public $duration_days; // Tambahkan field durasi dalam hari

    // Untuk filtering
    public $selectedStudy = '';
    public $searchTerm = '';

    // Untuk selected members
    public $selectedMembers = [];
    public $availableStudents = [];
    public $isLoading = false;
    public $endDate = null; // Untuk perhitungan tanggal berakhir

    protected $rules = [
        'company_name' => 'required|string|max:255',
        'address_company' => 'required|string',
        'note' => 'nullable|string|max:255',
        'start_date' => 'required|date|after_or_equal:today',
        'duration_days' => 'required|numeric|min:1|max:365',
        'selectedMembers' => 'array|max:5'
    ];

    public function mount()
    {
        $currentUserNim = Auth::user()->student->nim;
        $this->selectedMembers[$currentUserNim] = [
            'nim' => $currentUserNim,
            'name' => Auth::user()->name,
            'is_representative' => true
        ];

        $this->loadAvailableStudents();
    }

    public function updatedStartDate($value)
    {
        $this->calculateEndDate();
    }

    public function updatedDurationDays($value)
    {
        $this->calculateEndDate();
    }

 private function calculateEndDate()
    {
        if ($this->start_date && $this->duration_days !== null && $this->duration_days !== '') {
            try {
               
                $duration = is_numeric($this->duration_days) ? (int) $this->duration_days : 0;
                
                if ($duration > 0) {
                    $startDate = Carbon::parse($this->start_date);
                    $this->endDate = $startDate->copy()->addDays($duration)->format('d F Y');
                } else {
                    $this->endDate = null;
                }
            } catch (\Exception $e) {
                $this->endDate = null;
                Log::error('Error calculating end date: ' . $e->getMessage());
            }
        } else {
            $this->endDate = null;
        }
    }


    public function loadAvailableStudents()
    {
        $this->isLoading = true;

        $query = Student::with(['user', 'studyProgram'])
            ->where('nim', '!=', Auth::user()->student->nim);

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

        $this->availableStudents = $query->get()
            ->map(function ($student) {
                return [
                    'nim' => $student->nim,
                    'name' => $student->user->name,
                    'study_program' => $student->studyProgram->study_name,
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
        // Debounce untuk menghindari terlalu banyak request
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

            $this->selectedMembers[$nim] = [
                'nim' => $nim,
                'name' => $name,
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
                'start_date' => $this->start_date,
                'duration_days' => $this->duration_days,
                'status' => 'pending'
            ]);

            foreach ($this->selectedMembers as $member) {
                SubmissionMember::create([
                    'submission_id' => $submission->submission_id,
                    'student_nim' => $member['nim'],
                    'is_representative' => $member['is_representative']
                ]);
            }

            session()->flash('success', 'Submission berhasil dikirim! Silakan tunggu persetujuan Admin.');
            return redirect()->route('submissions.create');
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
        ])
            ->layout('layouts.app');
    }
}