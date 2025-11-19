<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Pengajuan') }}
        </h2>
    </x-slot>

    <div class="pt-12">
        <div class=" mx-auto sm:px-6 lg:px-8">

            {{-- Flash Message Global (Opsional, jika child component emit event ke atas) --}}
            @if (session()->has('message'))
            <div
                class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center shadow-sm">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('message') }}
            </div>
            @endif

            @if (session()->has('error'))
            <div
                class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center shadow-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
            @endif

            {{-- LOAD COMPONENT BERDASARKAN ROLE --}}
            {{-- Menggunakan lazy loading agar UI tampil lebih cepat --}}

            @role('mahasiswa')
            <livewire:submission.student-submission-list lazy />
            @elserole('admin')
            <livewire:submission.admin-submission-list lazy />
            @elserole('pimpinan')
            <livewire:submission.pimpinan-submission-list lazy />
            @else
            <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-3">Akses Ditolak</h3>
                <p class="text-gray-600">Role Anda tidak memiliki akses ke halaman ini.</p>
            </div>
            @endrole

        </div>
    </div>

    <x-footer class="mt-12" />
</div>