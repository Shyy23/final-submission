<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $verificationFilter = ''; // Filter baru: '' (Semua), '1' (Verified), '0' (Unverified)
    public $perPage = 10;

    // Form Properties (Create/Edit)
    public $showForm = false;
    public $formType = 'create';
    public $userId = null;
    public $name = '';
    public $email = '';
    public $role = '';
    public $password = '';
    public $password_confirmation = '';

    // Detail Modal Properties
    public $showDetail = false;
    public $selectedUser = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'verificationFilter' => ['except' => ''],
    ];

    // --- Lifecycle & Updates ---

    public function updatingSearch() { $this->resetPage(); }
    public function updatingRoleFilter() { $this->resetPage(); }
    public function updatingVerificationFilter() { $this->resetPage(); }

    public function resetFilters()
    {
        $this->search = '';
        $this->roleFilter = '';
        $this->verificationFilter = '';
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset(['showForm', 'formType', 'userId', 'name', 'email', 'role', 'password', 'password_confirmation']);
        $this->resetErrorBag();
    }

    // --- Form Handling (Create/Edit) ---

    public function showCreateForm()
    {
        $this->resetForm();
        $this->formType = 'create';
        $this->showForm = true;
    }

    public function showEditForm($userId)
    {
        $user = User::findOrFail($userId);
        $this->resetForm();
        $this->formType = 'edit';
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->getRoleNames()->first() ?? '';
        $this->showForm = true;
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email,' . $this->userId,
                'regex:/^[a-zA-Z0-9._%+-]+@(unjani\.ac\.id|gmail\.com)$/i' 
            ],
            'role' => 'required|in:mahasiswa,admin,pimpinan',
        ];

        if ($this->formType === 'create') {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        } else {
            $rules['password'] = ['nullable', 'confirmed', Rules\Password::defaults()];
        }

        return $rules;
    }

    public function saveUser()
    {
        $this->validate();

        try {
            if ($this->formType === 'create') {
                // FIX 1: User yang dibuat Admin otomatis is_verified = true
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'is_verified' => true, // <--- Auto verified
                ]);

                $user->assignRole($this->role);
                session()->flash('message', 'User berhasil ditambahkan & terverifikasi otomatis.');
            } else {
                $user = User::findOrFail($this->userId);
                $user->update([
                    'name' => $this->name,
                    'email' => $this->email,
                ]);

                if ($this->password) {
                    $user->update(['password' => Hash::make($this->password)]);
                }

                $user->syncRoles([$this->role]);
                session()->flash('message', 'User berhasil diperbarui.');
            }

            $this->showForm = false; // Tutup modal form
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // --- Detail & Verification Logic (NEW) ---

    public function showUserDetail($userId)
    {
        // Load user beserta relasi student dan leader untuk ditampilkan di modal detail
        $this->selectedUser = User::with(['student', 'leader', 'roles'])->findOrFail($userId);
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedUser = null;
    }

    public function verifyUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->update(['is_verified' => true]);
            
            // Refresh data selectedUser agar tampilan modal terupdate
            $this->selectedUser = $user->refresh(); 
            
            session()->flash('message', 'Akun Pengguna berhasil diverifikasi.');
            
            // Opsional: Tutup modal setelah verifikasi
            // $this->closeDetail(); 
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memverifikasi user.');
        }
    }

    // --- Delete Logic ---

    public function confirmDelete($userId)
    {
        $this->dispatch('show-confirm-dialog', 
            message: 'Apakah Anda yakin ingin menghapus user ini? Data terkait (mahasiswa/pimpinan) juga akan terhapus.', 
            method: 'do-delete-user', 
            params: ['userId' => $userId]
        );
    }

    #[On('do-delete-user')]
    public function deleteUser($userId)
    {
        try {
            $user = User::findOrFail($userId);

            if ($user->id === Auth::id()) {
                session()->flash('error', 'Tidak dapat menghapus akun sendiri.');
                return;
            }

            $user->delete();
            $this->showDetail = false; // Tutup detail modal jika sedang terbuka
            session()->flash('message', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    // --- Query Logic ---

    public function getUsersQuery()
    {
        $query = User::with(['student', 'leader', 'roles'])
            ->whereHas('roles', function ($q) {
                if ($this->roleFilter) {
                    $q->where('name', $this->roleFilter);
                }
            });

        // FIX 2: Filter Verify Status
        if ($this->verificationFilter !== '') {
            $status = $this->verificationFilter === '1';
            $query->where('is_verified', $status);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('student', function ($q) {
                        $q->where('nim', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('leader', function ($q) {
                        $q->where('nid', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Urutkan yang belum verified di atas agar Admin notice
        return $query->orderBy('is_verified', 'asc')->orderBy('created_at', 'desc');
    }

    public function getUsersProperty()
    {
        return $this->getUsersQuery()->paginate($this->perPage);
    }

    public function getTotalUsersProperty()
    {
        return $this->getUsersQuery()->count();
    }

    public function render()
    {
        return view('livewire.admin.user-management', [
            'users' => $this->users,
            'totalUsers' => $this->totalUsers,
        ]);
    }
}