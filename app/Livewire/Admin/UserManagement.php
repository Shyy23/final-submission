<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Student;
use App\Models\Leader;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\On;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $perPage = 10;

    // Form properties
    public $showForm = false;
    public $formType = 'create'; // 'create' or 'edit'
    public $userId = null;
    public $name = '';
    public $email = '';
    public $role = '';
    public $password = '';
    public $password_confirmation = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
    ];

    public function mount()
    {
        // Inisialisasi jika diperlukan
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->roleFilter = '';
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset([
            'showForm',
            'formType',
            'userId',
            'name',
            'email',
            'role',
            'password',
            'password_confirmation'
        ]);
    }

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
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->userId,
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
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                ]);

                // Assign role
                $user->assignRole($this->role);

                session()->flash('message', 'User berhasil ditambahkan. User dapat melengkapi NIM/NID saat login pertama kali.');
            } else {
                $user = User::findOrFail($this->userId);
                $user->update([
                    'name' => $this->name,
                    'email' => $this->email,
                ]);

                // Update password jika diisi
                if ($this->password) {
                    $user->update([
                        'password' => Hash::make($this->password),
                    ]);
                }

                // Sync role
                $user->syncRoles([$this->role]);

                session()->flash('message', 'User berhasil diperbarui.');
            }

            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function confirmDelete($userId)
    {
        $this->dispatch('show-confirm-dialog', 
            message: 'Apakah Anda yakin ingin menghapus user ini?', 
            method: 'do-delete-user', 
            params: ['userId' => $userId] // <-- *** UBAH MENJADI SEPERTI INI ***
        );
    }

    #[On('do-delete-user')] // <-- 3. TAMBAHKAN LISTENER INI
    public function deleteUser($userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Prevent deleting own account
            if ($user->id === Auth::id()) {
                session()->flash('error', 'Tidak dapat menghapus akun sendiri.');
                return;
            }

            $user->delete();
            session()->flash('message', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function getUsersQuery()
    {
        $query = User::with(['student', 'leader', 'roles'])
            ->whereHas('roles', function ($q) {
                if ($this->roleFilter) {
                    $q->where('name', $this->roleFilter);
                }
            });

        // Apply search filter
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

        return $query->orderBy('name', 'asc');
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
        ])->layout('layouts.app');
    }
}
