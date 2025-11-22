<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gradient-to-br from-emerald-50 via-white to-teal-50 text-gray-900">

    <!-- Header -->
    <header
        class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-gray-200 sticky top-0 z-40 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-auto sm:py-4">

                <!-- Left Side: Back Button & Title -->
                <div class="flex items-center gap-2 sm:gap-4">
                    {{-- Dynamic Back Button berdasarkan Role --}}
                    @auth
                    @php
                    $backRoute = '#';
                    $hoverClass = 'text-gray-600';

                    if(Auth::user()->hasRole('mahasiswa')) {
                    $backRoute = route('submissions.history');
                    $hoverClass = 'hover:text-emerald-600 hover:bg-emerald-50';
                    } elseif(Auth::user()->hasRole('admin')) {
                    $backRoute = route('admin.submissions');
                    $hoverClass = 'hover:text-blue-600 hover:bg-blue-50';
                    } elseif(Auth::user()->hasRole('pimpinan')) {
                    $backRoute = route('submissions.signature');
                    $hoverClass = 'hover:text-purple-600 hover:bg-purple-50';
                    }
                    @endphp

                    <a href="{{ $backRoute }}"
                        class="flex items-center justify-center p-2 sm:px-3 sm:py-2 text-gray-600 rounded-lg transition-all duration-200 {{ $hoverClass }}">
                        <i class="fas fa-arrow-left text-lg sm:mr-2"></i>
                        {{-- Sembunyikan teks 'Kembali' di mobile --}}
                        <span class="font-medium hidden sm:inline">Kembali</span>
                    </a>
                    @endauth

                    {{-- Divider: Sembunyikan di mobile --}}
                    <div class="h-6 w-px bg-gray-300 hidden sm:block"></div>

                    <div class="flex items-center">
                        {{-- Icon Box: Kecilkan di mobile (w-8 h-8) --}}
                        <div
                            class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg flex items-center justify-center mr-2 sm:mr-3 shadow-sm flex-shrink-0">
                            <i class="fas fa-file-alt text-white text-sm sm:text-base"></i>
                        </div>

                        {{-- Title Text --}}
                        <div class="leading-tight">
                            <h1 class="text-sm sm:text-lg font-bold text-gray-900">
                                Detail Pengajuan
                            </h1>
                            <p class="text-[10px] sm:text-xs text-gray-500 hidden xs:block">Surat Tugas Akhir</p>
                        </div>
                    </div>
                </div>

                <!-- Right Side: User Info -->
                @auth
                <div class="flex items-center gap-2 sm:gap-3">
                    {{-- Text Info: Hanya muncul di tablet ke atas (sm:block) --}}
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>

                    {{-- Avatar: Kecilkan sedikit di mobile --}}
                    <div
                        class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center shadow-md flex-shrink-0">
                        <span class="text-xs sm:text-sm font-bold text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </span>
                    </div>

                    {{-- Role Badge: Sembunyikan di mobile agar tidak penuh --}}
                    <div class="hidden sm:block">
                        @role('mahasiswa')
                        <span
                            class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full border border-emerald-200">
                            Mahasiswa
                        </span>
                        @endrole

                        @role('admin')
                        <span
                            class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full border border-blue-200">
                            Admin
                        </span>
                        @endrole

                        @role('pimpinan')
                        <span
                            class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full border border-purple-200">
                            Pimpinan
                        </span>
                        @endrole
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-footer />

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Scripts -->
    <script>
        document.addEventListener('livewire:initialized', () => {
        Livewire.on('show-confirm-dialog', ({ message, method, params = null }) => {
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981', // Emerald-500 matches theme
                cancelButtonColor: '#ef4444', // Red-500
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'rounded-lg',
                    cancelButton: 'rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (params) {
                        Livewire.dispatch(method, params); 
                    } else {
                        Livewire.dispatch(method);
                    }
                }
            });
        });
    });
    </script>
</body>

</html>