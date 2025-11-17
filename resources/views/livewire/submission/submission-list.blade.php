<div>
    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text"
                        wire:model.live="search"
                        placeholder="Cari berdasarkan perusahaan, alamat, atau nama mahasiswa..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                </div>
            </div>
            <div class="flex gap-2">
                <select wire:model.live="status" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                    <option value="verified">Terverifikasi</option>
                </select>
                <button wire:click="resetFilters" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium">
                    <i class="fas fa-filter mr-2"></i>
                    Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
        {{ session('message') }}
    </div>
    @endif

    {{-- Switch case untuk partials berdasarkan role --}}
    @switch($role)
    @case('mahasiswa')
    @include('livewire.partials.mahasiswa-submission-list')
    @break

    @case('admin')
    @include('livewire.partials.admin-submission-list')
    @break

    @case('pimpinan')
    @include('livewire.partials.pimpinan-submission-list')
    @break

    @default
    <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
        <div class="text-red-500 text-lg font-semibold">
            Role tidak dikenali. Silakan hubungi administrator.
        </div>
    </div>
    @endswitch
</div>