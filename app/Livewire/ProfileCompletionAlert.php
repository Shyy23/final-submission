<?php

namespace App\Livewire;

use App\Models\Leader;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileCompletionAlert extends Component
{
    public $showAlert = false;
    public $alertMessage = '';
    public $alertType = 'warning';

    public function mount()
    {
        $user = Auth::user();
        
        if (!$user) return;

        // Cek apakah perlu menampilkan alert
        $this->showAlert = session('show_profile_alert', false);
        $this->alertMessage = session('warning', '');

        // Jika tidak dari session, cek langsung dari database
        if (!$this->showAlert) {
            if ($user->hasRole('mahasiswa')) {
                $student = Student::where('user_id', $user->id)->first();
                if (!$student || empty($student->nim) || empty($student->study_id)) {
                    $this->showAlert = true;
                    $this->alertMessage = 'Harap lengkapi data mahasiswa (NIM dan Program Studi) sebelum mengakses fitur lainnya.';
                }
            }

            if ($user->hasRole('pimpinan')) {
                $leader = Leader::where('user_id', $user->id)->first();
                if (!$leader || empty($leader->nid) || empty($leader->position_id)) {
                    $this->showAlert = true;
                    $this->alertMessage = 'Harap lengkapi data pimpinan (NID dan Jabatan) sebelum mengakses fitur lainnya.';
                }
            }
        }
    }

    public function dismissAlert()
    {
        $this->showAlert = false;
    }

    public function goToProfile()
    {
        return redirect()->route('profile');
    }

    public function render()
    {
        return view('livewire.profile-completion-alert');
    }
}