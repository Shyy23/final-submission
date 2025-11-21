<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public function confirmLogout(): void
    {
        $this->dispatch('show-confirm-dialog', 
            message: 'Anda yakin ingin keluar dari aplikasi?', 
            method: 'do-logout'
        );
    }

    #[On('do-logout')]
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    {{-- 
        1. MOBILE TOP BAR (FIXED)
        - Ubah dari 'sticky' ke 'fixed top-0 w-full' agar tetap di atas meski konten di-scroll.
        - Tambahkan 'transition-all' agar halus.
    --}}
    <div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between fixed top-0 left-0 w-full z-40 shadow-sm h-16">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('assets/img/unjani.png') }}" alt="logo unjani" class="w-8 h-8">
            <span class="font-bold text-gray-800 text-sm">Submission System</span>
        </div>
        
        {{-- Tombol Hamburger --}}
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-emerald-600 focus:outline-none p-2 rounded-md hover:bg-gray-100 transition-colors">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>

    {{-- 
        2. MOBILE OVERLAY (BACKDROP) 
        - Error Alpine sering terjadi di sini saat navigasi.
        - x-show mengontrol visibilitas.
    --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-gray-900/50 z-40 md:hidden"
         style="display: none;">
    </div>

    {{-- 
        3. SIDEBAR UTAMA 
    --}}
    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 h-screen flex flex-col justify-between transition-transform duration-300 ease-in-out 
               md:translate-x-0  md:inset-auto md:h-screen md:sticky md:top-0 shadow-xl md:shadow-none">

        <div class="flex-1 overflow-y-auto">
            <!-- Logo/Brand (Desktop Only) -->
            <a href="{{ route('dashboard') }}" class="hidden md:block px-6 py-6 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('assets/img/unjani.png') }}" alt="logo unjani" class="w-10 h-10">
                    <div>
                        <h1 class="text-gray-800 text-lg font-bold">Submission</h1>
                        <p class="text-xs text-gray-500">Tugas Akhir</p>
                    </div>
                </div>
            </a>
            
            <!-- Logo Header untuk Mobile (Di dalam Sidebar) -->
            <div class="md:hidden px-6 py-4 border-b border-gray-100 flex justify-between items-center h-16">
                 <div class="flex items-center space-x-2">
                    <img src="{{ asset('assets/img/unjani.png') }}" alt="logo" class="w-8 h-8">
                    <span class="font-bold text-gray-800">Menu Utama</span>
                 </div>
                 <button @click="sidebarOpen = false" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                 </button>
            </div>

            <!-- Navigation Links -->
            {{-- 
                PENTING: Tambahkan @click="sidebarOpen = false" pada <nav>
                agar saat link diklik di mobile, sidebar menutup otomatis.
                Ini membantu mencegah konflik transisi Alpine.
            --}}
            <nav class="px-4 py-6 space-y-2" @click.stop="if(window.innerWidth < 768) sidebarOpen = false">
                <!-- Dashboard Link -->
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200
                   {{ request()->routeIs('dashboard') 
                       ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 shadow-sm border border-emerald-100' 
                       : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-house w-5 {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-gray-400' }}"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                @role('admin')
                <!-- Admin Section -->
                <div class="pt-6">
                    <h3 class="px-4 mb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Admin Area
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route('admin.users') }}" wire:navigate
                            class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200
                           {{ request()->routeIs('admin.users') 
                               ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 shadow-sm border border-emerald-100' 
                               : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="fa-solid fa-users w-5 {{ request()->routeIs('admin.users') ? 'text-emerald-600' : 'text-gray-400' }}"></i>
                            <span class="font-medium">Kelola Pengguna</span>
                        </a>

                        <a href="{{ route('admin.submissions') }}" wire:navigate
                            class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200
                           {{ request()->routeIs('admin.submissions') 
                               ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 shadow-sm border border-emerald-100' 
                               : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="fa-solid fa-file-lines w-5 {{ request()->routeIs('admin.submissions') ? 'text-emerald-600' : 'text-gray-400' }}"></i>
                            <span class="font-medium">Kelola Pengajuan</span>
                        </a>
                    </div>
                </div>
                @endrole

                @role('pimpinan')
                <!-- Pimpinan Section -->
                <div class="pt-6">
                    <h3 class="px-4 mb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Pimpinan
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route('submissions.signature') }}" wire:navigate
                            class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200
                           {{ request()->routeIs('submissions.signature') 
                               ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 shadow-sm border border-emerald-100' 
                               : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="fa-solid fa-signature w-5 {{ request()->routeIs('submissions.signature') ? 'text-emerald-600' : 'text-gray-400' }}"></i>
                            <span class="font-medium">Tanda Tangan</span>
                        </a>
                    </div>
                </div>
                @endrole

                @role('mahasiswa')
                <!-- Mahasiswa Section -->
                <div class="pt-6">
                    <h3 class="px-4 mb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Mahasiswa
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route('submissions.create') }}" wire:navigate
                            class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200
                           {{ request()->routeIs('submissions.create') 
                               ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 shadow-sm border border-emerald-100' 
                               : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="fa-solid fa-plus-circle w-5 {{ request()->routeIs('submissions.create') ? 'text-emerald-600' : 'text-gray-400' }}"></i>
                            <span class="font-medium">Buat Pengajuan</span>
                        </a>

                        <a href="{{ route('submissions.history') }}" wire:navigate
                            class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200
                           {{ request()->routeIs('submissions.history') 
                               ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 shadow-sm border border-emerald-100' 
                               : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="fa-solid fa-clock-rotate-left w-5 {{ request()->routeIs('submissions.history') ? 'text-emerald-600' : 'text-gray-400' }}"></i>
                            <span class="font-medium">Riwayat Pengajuan</span>
                        </a>
                    </div>
                </div>
                @endrole
            </nav>
        </div>

        <!-- User Profile Section -->
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            <div class="mb-4">
                <div class="flex items-center space-x-3 px-3 py-3 rounded-xl bg-white border border-gray-200 shadow-sm">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-md">
                            <span class="text-sm font-bold text-white">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm text-gray-800 truncate">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="text-xs text-gray-500 truncate">
                            {{ auth()->user()->email }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile') }}" wire:navigate
                    class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm transition-all duration-200 border border-transparent hover:border-gray-200">
                    <i class="fa-solid fa-user w-5 text-gray-400"></i>
                    <span class="font-medium">Profil Saya</span>
                </a>

                <button wire:click="confirmLogout"
                    class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-red-50 hover:text-red-600 hover:shadow-sm transition-all duration-200 border border-transparent hover:border-red-100">
                    <i class="fa-solid fa-right-from-bracket w-5 text-gray-400"></i>
                    <span class="font-medium">Keluar</span>
                </button>
            </div>
        </div>
    </aside>
</div>