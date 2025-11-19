<div>
    {{-- Flash Message --}}
    @if (session()->has('message'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('message') }}
    </div>
    @endif

    @if (session()->has('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header & Actions --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Kelola Pengguna</h2>
            <p class="text-gray-600 mt-1">Verifikasi dan kelola akun pengguna sistem</p>
        </div>
        <button wire:click="showCreateForm"
            class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
            <i class="fas fa-plus mr-2"></i>
            Tambah Pengguna
        </button>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            <div class="flex-1 w-full">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama, email, NIM/NID..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                </div>
            </div>

            <div class="flex gap-2 w-full lg:w-auto">
                {{-- Filter Role --}}
                <select wire:model.live="roleFilter"
                    class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm w-full md:w-auto">
                    <option value="">Semua Role</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="pimpinan">Pimpinan</option>
                    <option value="admin">Admin</option>
                </select>

                {{-- Filter Status Verifikasi (BARU) --}}
                <select wire:model.live="verificationFilter"
                    class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm w-full md:w-auto">
                    <option value="">Semua Status</option>
                    <option value="1">Terverifikasi</option>
                    <option value="0">Belum Verifikasi</option>
                </select>

                <button wire:click="resetFilters"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium"
                    title="Reset Filter">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    @if($totalUsers > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
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
                            <div class="flex flex-col">
                                <span class="font-medium">NIM: {{ $user->student->nim ?? '-' }}</span>
                                <span class="text-xs text-gray-400">{{ $user->student->major ?? '' }}</span>
                            </div>
                            @elseif($user->hasRole('pimpinan'))
                            <span class="font-medium">NID: {{ $user->leader->nid ?? '-' }}</span>
                            @else
                            -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($user->is_verified)
                            <span
                                class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                                <i class="fas fa-check-circle mr-1 my-auto"></i> Verified
                            </span>
                            @else
                            <span
                                class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-700 border border-red-200 animate-pulse">
                                <i class="fas fa-times-circle mr-1 my-auto"></i> Unverified
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Tombol Detail (BARU) --}}
                                <button wire:click="showUserDetail({{ $user->id }})"
                                    class="text-gray-500 hover:text-blue-600 transition-colors p-1"
                                    title="Detail & Verifikasi">
                                    <i class="fas fa-eye text-lg"></i>
                                </button>

                                <button wire:click="showEditForm({{ $user->id }})"
                                    class="text-gray-500 hover:text-amber-500 transition-colors p-1" title="Edit User">
                                    <i class="fas fa-edit text-lg"></i>
                                </button>

                                @if($user->id !== auth()->id())
                                <button wire:click="confirmDelete({{ $user->id }})"
                                    class="text-gray-500 hover:text-red-600 transition-colors p-1" title="Hapus User">
                                    <i class="fas fa-trash text-lg"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-search text-3xl text-gray-400"></i>
            </div>
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Data Tidak Ditemukan</h3>
        <p class="text-gray-500">Coba ubah filter atau kata kunci pencarian Anda.</p>
    </div>
    @endif

    <x-footer class="mt-12" />

    {{-- ========================== --}}
    {{-- MODALS SECTION --}}
    {{-- ========================== --}}

    {{-- 1. User Form Modal (Create/Edit) - Tetap Sama, hanya dirapikan --}}
    @if($showForm)
    <div
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50 transition-opacity">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
            <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">
                    {{ $formType === 'create' ? 'Tambah Pengguna Baru' : 'Edit Data Pengguna' }}
                </h3>
                <button wire:click="resetForm" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveUser" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" wire:model="name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent shadow-sm">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" wire:model="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent shadow-sm">
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select wire:model="role"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent shadow-sm">
                        <option value="">Pilih Role</option>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="admin">Admin</option>
                        <option value="pimpinan">Pimpinan</option>
                    </select>
                    @error('role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    <div class="mt-2 text-xs text-gray-500 bg-blue-50 p-2 rounded border border-blue-100">
                        <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                        User yang dibuat Admin akan otomatis <b>Terverifikasi</b>.
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Password {{ $formType === 'edit' ? '(Opsional)' : '' }}
                    </label>
                    <input type="password" wire:model="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent shadow-sm"
                        placeholder="********">
                    @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" wire:model="password_confirmation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent shadow-sm"
                        placeholder="********">
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" wire:click="resetForm"
                        class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium transition-colors shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- 2. DETAIL & VERIFICATION MODAL (BARU) --}}
    @if($showDetail && $selectedUser)
    <div
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">

            {{-- Modal Header --}}
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Detail Pengguna</h3>
                    <p class="text-sm text-gray-500">Informasi lengkap dan verifikasi data</p>
                </div>
                <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Modal Body (Scrollable) --}}
            <div class="p-6 overflow-y-auto">

                {{-- Status Banner --}}
                <div
                    class="mb-6 p-4 rounded-lg border {{ $selectedUser->is_verified ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }} flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full flex items-center justify-center {{ $selectedUser->is_verified ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                            <i class="fas {{ $selectedUser->is_verified ? 'fa-check' : 'fa-times' }} text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold {{ $selectedUser->is_verified ? 'text-emerald-800' : 'text-red-800' }}">
                                {{ $selectedUser->is_verified ? 'Akun Terverifikasi' : 'Belum Diverifikasi' }}
                            </p>
                            <p class="text-xs {{ $selectedUser->is_verified ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $selectedUser->is_verified ? 'Pengguna dapat mengakses sistem sepenuhnya.' :
                                'Pengguna perlu diverifikasi untuk akses penuh.' }}
                            </p>
                        </div>
                    </div>
                    @if(!$selectedUser->is_verified)
                    <button wire:click="verifyUser({{ $selectedUser->id }})"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all">
                        <i class="fas fa-check-circle mr-1"></i> Setujui & Verifikasi
                    </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Data Akun --}}
                    <div>
                        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3 border-b pb-1">
                            Informasi Akun</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs text-gray-400">Nama Lengkap</label>
                                <p class="font-medium text-gray-800">{{ $selectedUser->name }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-400">Email</label>
                                <p class="font-medium text-gray-800">{{ $selectedUser->email }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-400">Role</label>
                                <span
                                    class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded capitalize">
                                    {{ $selectedUser->getRoleNames()->first() }}
                                </span>
                            </div>
                            <div>
                                <label class="text-xs text-gray-400">Bergabung Sejak</label>
                                <p class="text-sm text-gray-600">{{ $selectedUser->created_at->format('d F Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Data Spesifik Role --}}
                    <div>
                        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3 border-b pb-1">Detail
                            Profil</h4>

                        @if($selectedUser->hasRole('mahasiswa') && $selectedUser->student)
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-gray-400">NIM (Nomor Induk Mahasiswa)</label>
                                <p
                                    class="text-lg font-mono font-bold text-blue-600 bg-blue-50 p-2 rounded border border-blue-100 inline-block">
                                    {{ $selectedUser->student->nim }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-400">Program Studi</label>
                                <p class="font-medium text-gray-800">{{ $selectedUser->student->major ?? '-' }}</p>
                            </div>

                            {{-- FOTO KTM (FIXED: Menggunakan ktm_path dan asset storage) --}}
                            <div>
                                <label class="text-xs text-gray-400 block mb-2">Foto Kartu Tanda Mahasiswa (KTM)</label>
                                @if($selectedUser->student->ktm_path)
                                {{-- Menggunakan path dari storage --}}
                                <div class="relative group cursor-pointer"
                                    onclick="window.open('{{ asset('storage/' . $selectedUser->student->ktm_path) }}', '_blank')">
                                    <img src="{{ asset('storage/' . $selectedUser->student->ktm_path) }}" alt="Foto KTM"
                                        class="w-full h-40 object-cover rounded-lg border-2 border-gray-200 hover:border-blue-400 transition-all">
                                    <div
                                        class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                        <span class="text-white text-sm font-medium"><i
                                                class="fas fa-search-plus mr-1"></i> Lihat Penuh</span>
                                    </div>
                                </div>
                                @else
                                <div
                                    class="w-full h-32 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-image text-2xl mb-2"></i>
                                    <span class="text-xs">KTM belum diunggah</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @elseif($selectedUser->hasRole('pimpinan') && $selectedUser->leader)
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs text-gray-400">NID</label>
                                <p class="font-medium text-gray-800">{{ $selectedUser->leader->nid }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-400">Jabatan</label>
                                <p class="font-medium text-gray-800">{{ $selectedUser->leader->position ?? '-' }}</p>
                            </div>
                        </div>
                        @elseif($selectedUser->hasRole('admin'))
                        {{-- KHUSUS ADMIN: Tidak ada tabel profil tambahan --}}
                        <div
                            class="h-full flex flex-col items-center justify-center p-6 bg-purple-50 rounded-lg border border-purple-100 text-center">
                            <div
                                class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-sm mb-3">
                                <i class="fas fa-user-shield text-2xl text-purple-500"></i>
                            </div>
                            <h5 class="font-bold text-purple-800 text-sm">Administrator</h5>
                            <p class="text-xs text-purple-600 mt-1 leading-relaxed">
                                Akun ini memiliki akses penuh sistem. Tidak ada data profil tambahan (NIM/NID/KTM) untuk
                                role Admin.
                            </p>
                        </div>
                        @else
                        <div
                            class="p-4 bg-amber-50 rounded-lg text-center text-amber-700 text-sm border border-amber-100">
                            <i class="fas fa-exclamation-triangle mb-2 text-2xl"></i><br>
                            <span class="font-bold">Data Belum Lengkap</span><br>
                            <span class="text-xs opacity-80">User ini (Mahasiswa/Pimpinan) belum melengkapi data
                                profilnya.</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
                @if($selectedUser->id !== auth()->id())
                <button wire:click="confirmDelete({{ $selectedUser->id }})"
                    class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus Akun Permanen
                </button>
                @else
                <span></span>
                @endif

                <button wire:click="closeDetail"
                    class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif
</div>