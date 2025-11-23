<div>
    {{-- Flash Message --}}
    @if (session()->has('message'))
    <div
        class="mb-4 sm:mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center text-sm sm:text-base">
        <i class="fas fa-check-circle mr-2 flex-shrink-0"></i>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    @if (session()->has('error'))
    <div
        class="mb-4 sm:mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center text-sm sm:text-base">
        <i class="fas fa-exclamation-circle mr-2 flex-shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Header & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Kelola Pengguna</h2>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Verifikasi dan kelola akun pengguna sistem</p>
        </div>
        <button wire:click="showCreateForm"
            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm text-sm">
            <i class="fas fa-plus mr-2"></i>
            Tambah Pengguna
        </button>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
            <div class="flex-1 w-full">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, email, NIM..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:flex sm:flex-row gap-2 w-full lg:w-auto">
                {{-- Filter Role --}}
                <select wire:model.live="roleFilter"
                    class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm w-full sm:w-auto">
                    <option value="">Semua Role</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="pimpinan">Pimpinan</option>
                    <option value="admin">Admin</option>
                </select>

                {{-- Filter Status --}}
                <select wire:model.live="verificationFilter"
                    class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm w-full sm:w-auto">
                    <option value="">Status</option>
                    <option value="1">Verified</option>
                    <option value="0">Unverified</option>
                </select>

                <button wire:click="resetFilters"
                    class="col-span-2 sm:col-span-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium flex justify-center items-center"
                    title="Reset Filter">
                    <i class="fas fa-sync-alt mr-1 sm:mr-0"></i> <span class="sm:hidden">Reset</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Users List --}}
    @if($totalUsers > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- DESKTOP VIEW (Table) - Hidden on Mobile --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            User Info</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Identifier</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mr-3 text-blue-600 font-bold text-sm">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $role = $user->getRoleNames()->first();
                            $badges = [
                            'mahasiswa' => 'bg-blue-100 text-blue-700',
                            'admin' => 'bg-purple-100 text-purple-700',
                            'pimpinan' => 'bg-amber-100 text-amber-700',
                            ];
                            $badgeClass = $badges[$role] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span
                                class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                {{ ucfirst($role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            @if($user->hasRole('mahasiswa'))
                            <span class="font-medium font-mono text-xs bg-gray-50 px-2 py-1 rounded">{{
                                $user->student->nim ?? '-' }}</span>
                            @elseif($user->hasRole('pimpinan'))
                            <span class="font-medium font-mono text-xs bg-gray-50 px-2 py-1 rounded">{{
                                $user->leader->nid ?? '-' }}</span>
                            @else
                            -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($user->is_verified)
                            <span class="text-emerald-500 text-lg" title="Verified"><i
                                    class="fas fa-check-circle"></i></span>
                            @else
                            <span class="text-red-400 text-lg animate-pulse" title="Unverified"><i
                                    class="fas fa-times-circle"></i></span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="showUserDetail({{ $user->id }})"
                                    class="p-1.5 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all"><i
                                        class="fas fa-eye"></i></button>
                                <button wire:click="showEditForm({{ $user->id }})"
                                    class="p-1.5 rounded-lg text-gray-500 hover:text-amber-600 hover:bg-amber-50 transition-all"><i
                                        class="fas fa-edit"></i></button>
                                @if($user->id !== auth()->id())
                                <button wire:click="confirmDelete({{ $user->id }})"
                                    class="p-1.5 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition-all"><i
                                        class="fas fa-trash"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- MOBILE VIEW (Cards) - Visible below md breakpoint --}}
        <div class="md:hidden divide-y divide-gray-100">
            @foreach($users as $user)
            <div class="p-4 bg-white hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start mb-3">
                    {{-- User Info & Identifier Combined --}}
                    <div class="flex items-start gap-3">
                        <div
                            class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-semibold text-gray-900">{{ $user->name }}</h3>
                                {{-- Status Icon Inline --}}
                                @if($user->is_verified)
                                <i class="fas fa-check-circle text-emerald-500 text-xs" title="Verified"></i>
                                @else
                                <i class="fas fa-exclamation-circle text-red-500 text-xs animate-pulse"
                                    title="Belum Verifikasi"></i>
                                @endif
                            </div>

                            {{-- Identifier & Role Row --}}
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                @php
                                $role = $user->getRoleNames()->first();
                                $roleColor = match($role) {
                                'mahasiswa' => 'text-blue-600 bg-blue-50',
                                'admin' => 'text-purple-600 bg-purple-50',
                                'pimpinan' => 'text-amber-600 bg-amber-50',
                                default => 'text-gray-600 bg-gray-50'
                                };
                                @endphp
                                <span
                                    class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded {{ $roleColor }} border border-opacity-20">
                                    {{ $role }}
                                </span>

                                @if($user->hasRole('mahasiswa'))
                                <span class="text-xs text-gray-500 font-mono bg-gray-100 px-1.5 rounded">{{
                                    $user->student->nim ?? 'No NIM' }}</span>
                                @elseif($user->hasRole('pimpinan'))
                                <span class="text-xs text-gray-500 font-mono bg-gray-100 px-1.5 rounded">{{
                                    $user->leader->nid ?? 'No NID' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Row --}}
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                    <span class="text-xs text-gray-400 truncate max-w-[150px]">{{ $user->email }}</span>

                    <div class="flex gap-3">
                        <button wire:click="showUserDetail({{ $user->id }})"
                            class="text-gray-400 hover:text-blue-600 transition-colors">
                            <i class="fas fa-eye text-lg"></i>
                        </button>
                        <button wire:click="showEditForm({{ $user->id }})"
                            class="text-gray-400 hover:text-amber-500 transition-colors">
                            <i class="fas fa-edit text-lg"></i>
                        </button>
                        @if($user->id !== auth()->id())
                        <button wire:click="confirmDelete({{ $user->id }})"
                            class="text-gray-400 hover:text-red-500 transition-colors">
                            <i class="fas fa-trash text-lg"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($users->hasPages())
        <div class="px-4 py-3 sm:px-6 bg-gray-50 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
    @else
    {{-- Empty State Responsif --}}
    <div class="bg-white rounded-xl shadow-sm p-8 sm:p-12 text-center border border-gray-100">
        <div class="flex justify-center mb-4 sm:mb-6">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-search text-2xl sm:text-3xl text-gray-400"></i>
            </div>
        </div>
        <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-2">Data Tidak Ditemukan</h3>
        <p class="text-sm text-gray-500">Coba ubah filter atau kata kunci pencarian Anda.</p>
    </div>
    @endif

    <x-footer class="mt-8 sm:mt-12" />

    {{-- ========================== --}}
    {{-- MODALS SECTION (Responsif) --}}
    {{-- ========================== --}}

    {{-- 1. User Form Modal --}}
    @if($showForm)
    <div
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 z-50 transition-opacity">
        <div
            class="bg-white rounded-t-2xl sm:rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all max-h-[90vh] flex flex-col">
            <div class="p-4 sm:p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-800">
                    {{ $formType === 'create' ? 'Tambah Pengguna' : 'Edit Pengguna' }}
                </h3>
                <button wire:click="resetForm" class="text-gray-400 hover:text-gray-600 p-2">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="overflow-y-auto p-4 sm:p-6 space-y-4">
                <form wire:submit.prevent="saveUser">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" wire:model="name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" wire:model="email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select wire:model="role"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                                <option value="">Pilih Role</option>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="admin">Admin</option>
                                <option value="pimpinan">Pimpinan</option>
                            </select>
                            @error('role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password {{ $formType === 'edit'
                                ? '(Opsional)' : '' }}</label>
                            <input type="password" wire:model="password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm"
                                placeholder="********">
                            @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                            <input type="password" wire:model="password_confirmation"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm"
                                placeholder="********">
                        </div>
                    </div>

                    <div class="pt-6 flex gap-3">
                        <button type="button" wire:click="resetForm"
                            class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium text-sm">Batal</button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-emerald-500 text-white rounded-lg font-medium text-sm shadow-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- 2. Detail Modal --}}
    @if($showDetail && $selectedUser)
    <div
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 z-50 transition-opacity">
        <div
            class="bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[95vh] sm:max-h-[90vh]">

            {{-- Header --}}
            <div class="p-4 sm:p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 flex-shrink-0">
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800">Detail Pengguna</h3>
                </div>
                <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-4 sm:p-6 overflow-y-auto">
                {{-- Status Banner Compact --}}
                <div
                    class="mb-6 p-3 sm:p-4 rounded-lg border {{ $selectedUser->is_verified ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }}">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <i
                                class="fas {{ $selectedUser->is_verified ? 'fa-check-circle text-emerald-600' : 'fa-times-circle text-red-600' }} text-2xl"></i>
                            <div>
                                <p
                                    class="font-bold text-sm sm:text-base {{ $selectedUser->is_verified ? 'text-emerald-800' : 'text-red-800' }}">
                                    {{ $selectedUser->is_verified ? 'Terverifikasi' : 'Belum Verifikasi' }}
                                </p>
                                <p
                                    class="text-xs {{ $selectedUser->is_verified ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $selectedUser->is_verified ? 'Akses penuh diberikan.' : 'Akses terbatas.' }}
                                </p>
                            </div>
                        </div>
                        @if(!$selectedUser->is_verified)
                        <button wire:click="verifyUser({{ $selectedUser->id }})"
                            class="w-full sm:w-auto px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-lg shadow-sm">
                            Setujui
                        </button>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Info Akun --}}
                    <div>
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 border-b pb-1">Akun
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div><label class="text-xs text-gray-400 block">Nama</label><span
                                    class="font-medium text-gray-800">{{ $selectedUser->name }}</span></div>
                            <div><label class="text-xs text-gray-400 block">Email</label><span
                                    class="font-medium text-gray-800 break-all">{{ $selectedUser->email }}</span></div>
                            <div>
                                <label class="text-xs text-gray-400 block">Role</label>
                                <span
                                    class="inline-block px-2 py-0.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded capitalize mt-1">{{
                                    $selectedUser->getRoleNames()->first() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Info Profil --}}
                    <div>
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 border-b pb-1">Profil
                        </h4>
                        @if($selectedUser->hasRole('mahasiswa') && $selectedUser->student)
                        <div class="space-y-3 text-sm">
                            <div>
                                <label class="text-xs text-gray-400 block">NIM</label>
                                <span class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">{{
                                    $selectedUser->student->nim }}</span>
                            </div>
                            <div><label class="text-xs text-gray-400 block">Prodi</label><span class="font-medium">{{
                                    $selectedUser->student->major ?? '-' }}</span></div>

                            <div class="mt-2">
                                <label class="text-xs text-gray-400 block mb-1">KTM</label>
                                @if($selectedUser->student->ktm_path)
                                <a href="{{ asset('storage/' . $selectedUser->student->ktm_path) }}" target="_blank"
                                    class="block relative group rounded-lg overflow-hidden border border-gray-200">
                                    <img src="{{ asset('storage/' . $selectedUser->student->ktm_path) }}"
                                        class="w-full h-32 object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-white text-xs"><i class="fas fa-search-plus"></i> Lihat</span>
                                    </div>
                                </a>
                                @else
                                <div
                                    class="h-20 bg-gray-50 rounded border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-xs">
                                    No KTM</div>
                                @endif
                            </div>
                        </div>
                        @elseif($selectedUser->hasRole('pimpinan') && $selectedUser->leader)
                        <div class="space-y-3 text-sm">
                            <div><label class="text-xs text-gray-400 block">NID</label><span class="font-medium">{{
                                    $selectedUser->leader->nid }}</span></div>
                            <div><label class="text-xs text-gray-400 block">Jabatan</label><span class="font-medium">{{
                                    $selectedUser->leader->position->position_name ?? '-' }}</span></div>
                        </div>
                        @else
                        <p class="text-xs text-gray-400 italic">Tidak ada data profil tambahan.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="p-4 sm:p-6 border-t border-gray-100 bg-gray-50 flex justify-between items-center flex-shrink-0">
                @if($selectedUser->id !== auth()->id())
                <button wire:click="confirmDelete({{ $selectedUser->id }})"
                    class="text-red-600 hover:text-red-800 text-xs sm:text-sm font-medium flex items-center">
                    <i class="fas fa-trash-alt mr-1"></i> <span class="hidden sm:inline">Hapus Permanen</span><span
                        class="sm:hidden">Hapus</span>
                </button>
                @else
                <div></div>
                @endif
                <button wire:click="closeDetail"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif
</div>