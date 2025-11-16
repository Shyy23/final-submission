<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @role('mahasiswa')
                {{ __('Dashboard Mahasiswa') }}
                @elserole('admin')
                {{ __('Admin Dashboard') }}
                @elserole('pimpinan')
                {{ __('Dashboard Pimpinan') }}
                @else
                {{ __('Dashboard') }}
                @endrole
            </h2>
            <p class="text-sm text-gray-500">
                @role('mahasiswa')
                Selamat datang, {{ Auth::user()->name }}
                @elserole('admin')
                Kelola sistem submission
                @elserole('pimpinan')
                Verifikasi pengajuan surat
                @endrole
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @role('mahasiswa')
            @include('dashboard.partials.student-dashboard')
            @elserole('admin')
            @include('dashboard.partials.admin-dashboard')
            @elserole('pimpinan')
            @include('dashboard.partials.pimpinan-dashboard')
            @else
            <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-3">Akses Ditolak</h3>
                <p class="text-gray-600 mb-6">
                    Anda tidak memiliki role yang valid untuk mengakses dashboard.
                </p>
            </div>
            @endrole
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-sm text-gray-500">
                © {{ date('Y') }} Submission System. All rights reserved.
            </p>
        </div>
    </footer>
</x-app-layout>