<?php

namespace App\Http\Livewire\Forms;

use App\Models\Student;
use App\Models\StudyProgram;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentProfileForm extends Component
{
    public $nim;
    public $study_id;
    public $studyPrograms;

    protected $rules = [
        'nim' => 'required|string|max:20|unique:students,nim',
        'study_id' => 'required|exists:study_programs,study_id',
    ];

    protected $messages = [
        'nim.required' => 'NIM wajib diisi',
        'nim.unique' => 'NIM sudah terdaftar',
        'study_id.required' => 'Program studi wajib dipilih',
    ];

    public function mount()
    {
        $this->studyPrograms = StudyProgram::all();
        
        // Jika sudah ada data, isi form
        $student = Auth::user()->student;
        if ($student) {
            $this->nim = $student->nim;
            $this->study_id = $student->study_id;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            Student::create([
                'nim' => $this->nim,
                'study_id' => $this->study_id,
                'user_id' => Auth::id(),
            ]);

            session()->flash('success', 'Data mahasiswa berhasil disimpan!');
            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.forms.student-profile-form');
    }
}