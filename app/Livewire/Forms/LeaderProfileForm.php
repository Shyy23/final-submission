<?php

namespace App\Http\Livewire\Forms;

use App\Models\Leader;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LeaderProfileForm extends Component
{
    public $nid;
    public $position_id;
    public $positions;

    protected $rules = [
        'nid' => 'required|string|max:20|unique:leaders,nid',
        'position_id' => 'required|exists:positions,position_id',
    ];

    protected $messages = [
        'nid.required' => 'NID wajib diisi',
        'nid.unique' => 'NID sudah terdaftar',
        'position_id.required' => 'Jabatan wajib dipilih',
    ];

    public function mount()
    {
        $this->positions = Position::all();
        
        // Jika sudah ada data, isi form
        $leader = Auth::user()->leader;
        if ($leader) {
            $this->nid = $leader->nid;
            $this->position_id = $leader->position_id;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            Leader::create([
                'nid' => $this->nid,
                'position_id' => $this->position_id,
                'user_id' => Auth::id(),
            ]);

            session()->flash('success', 'Data pimpinan berhasil disimpan!');
            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.forms.leader-profile-form');
    }
}