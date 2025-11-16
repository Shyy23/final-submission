<div>
    {{-- Flash Message --}}
    @if (session()->has('message'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('message') }}
    </div>
    @endif

    @if (session()->has('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header & Actions --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Kelola Pengguna</h2>
            <p class="text-gray-600 mt-1">Kelola semua pengguna sistem</p>
        </div>
        <button wire:click="showCreateForm"
            class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
            <i class="fas fa-plus mr-2"></i>
            Tambah Pengguna
        </button>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" wire:model.live="search" placeholder="Cari berdasarkan nama, email, NIM/NID..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                </div>
            </div>
            <div class="flex gap-2">
                <select wire:model.live="roleFilter"
                    class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                    <option value="">Semua Role</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="admin">Admin</option>
                    <option value="pimpinan">Pimpinan</option>
                </select>
                <button wire:click="resetFilters"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium">
                    <i class="fas fa-filter mr-2"></i>
                    Reset
                </button>
            </div>
        </div>
    </div>

    {{-- User Form Modal --}}
    @if($showForm)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    {{ $formType === 'create' ? 'Tambah Pengguna' : 'Edit Pengguna' }}
                </h3>
            </div>

            <form wire:submit.prevent="saveUser" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" wire:model="name"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="Masukkan nama lengkap">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" wire:model="email"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="Masukkan alamat email">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select wire:model="role"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="">Pilih Role</option>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="admin">Admin</option>
                        <option value="pimpinan">Pimpinan</option>
                    </select>
                    @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                    {{-- Info tambahan untuk role --}}
                    @if($role)
                    <div class="mt-2 text-xs text-gray-500 bg-gray-50 p-2 rounded">
                        @if($role === 'mahasiswa')
                        <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                        User akan diminta untuk melengkapi NIM saat login pertama kali.
                        @elseif($role === 'pimpinan')
                        <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                        User akan diminta untuk melengkapi NID saat login pertama kali.
                        @else
                        <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                        Admin tidak memerlukan identifier tambahan.
                        @endif
                    </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Password {{ $formType === 'edit' ? '(Biarkan kosong jika tidak ingin mengubah)' : '' }}
                    </label>
                    <input type="password" wire:model="password"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="{{ $formType === 'create' ? 'Masukkan password' : 'Kosongkan jika tidak diubah' }}">
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" wire:model="password_confirmation"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="Konfirmasi password">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit"
                        class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white py-2 px-4 rounded-lg transition-colors font-medium">
                        {{ $formType === 'create' ? 'Tambah' : 'Simpan' }}
                    </button>
                    <button type="button" wire:click="resetForm"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg transition-colors font-medium">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Empty State --}}
    @if($totalUsers === 0)
    <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
        <div class="flex justify-center mb-6">
            <div
                class="w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-5xl text-blue-500"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-3">Tidak Ada Pengguna</h3>
        <p class="text-gray-600 mb-6 max-w-md mx-auto">
            @if($search || $roleFilter)
            Tidak ada pengguna yang sesuai dengan filter yang dipilih.
            @else
            Belum ada pengguna yang terdaftar dalam sistem.
            @endif
        </p>
        @if($search || $roleFilter)
        <button wire:click="resetFilters"
            class="inline-flex items-center px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-refresh mr-2"></i>
            Reset Filter
        </button>
        @else
        <button wire:click="showCreateForm"
            class="inline-flex items-center px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Pengguna Pertama
        </button>
        @endif
    </div>
    @endif

    {{-- Users Table --}}
    @if($totalUsers > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-users mr-2 text-blue-500"></i>
                Daftar Pengguna
                @if($roleFilter)
                - {{ ucfirst($roleFilter) }}
                @endif
            </h3>
            <p class="text-sm text-gray-600 mt-1">
                Kelola semua pengguna sistem submission surat tugas akhir
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Identifier</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status Data</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tanggal Daftar</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($users as $index => $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-800">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">
                                        {{ $user->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $user->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $role = $user->getRoleNames()->first();
                            $roleConfig = [
                            'mahasiswa' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' =>
                            'fa-user-graduate'],
                            'admin' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'fa-user-cog'],
                            'pimpinan' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' =>
                            'fa-user-tie'],
                            ];
                            $config = $roleConfig[$role] ?? $roleConfig['mahasiswa'];
                            @endphp
                            <span
                                class="px-3 py-1.5 inline-flex items-center text-xs leading-5 font-semibold rounded-full {{ $config['bg'] }} {{ $config['text'] }}">
                                <i class="fas {{ $config['icon'] }} mr-1.5"></i>
                                {{ ucfirst($role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-800">
                                @if($user->student)
                                NIM: {{ $user->student->nim }}
                                @elseif($user->leader)
                                NID: {{ $user->leader->nid }}
                                @else
                                <span class="text-gray-400 italic">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(($user->hasRole('mahasiswa') && !$user->student) ||
                            (($user->hasRole('admin') || $user->hasRole('pimpinan')) && !$user->leader))
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-700">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Belum Lengkap
                            </span>
                            @else
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                <i class="fas fa-check-circle mr-1"></i>
                                Lengkap
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-800">
                                <i class="far fa-calendar text-gray-400 mr-1"></i>
                                {{ $user->created_at->format('d M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="showEditForm({{ $user->id }})"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-colors duration-200 shadow-sm">
                                    <i class="fas fa-edit mr-1.5"></i>
                                    Edit
                                </button>
                                @if($user->id !== auth()->id())
                                <button wire:click="confirmDelete({{ $user->id }})"
                                    class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-colors duration-200 shadow-sm">
                                    <i class="fas fa-trash mr-1.5"></i>
                                    Hapus
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination Info --}}
        @if($users->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Menampilkan <span class="font-semibold text-gray-800">{{ $users->firstItem() }}</span>
                    sampai <span class="font-semibold text-gray-800">{{ $users->lastItem() }}</span>
                    dari <span class="font-semibold text-gray-800">{{ $users->total() }}</span> pengguna
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Pagination Links --}}
    @if($users->hasPages())
    <div class="mt-6">
        {{ $users->links() }}
    </div>
    @endif
    @endif
</div>