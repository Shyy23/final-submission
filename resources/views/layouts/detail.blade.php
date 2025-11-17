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

<body class="font-sans antialiased bg-gradient-to-br from-emerald-50 via-white to-teal-50">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Back Button & Title -->
                <div class="flex items-center space-x-4">
                    {{-- Dynamic Back Button berdasarkan Role --}}
                    @auth
                    @role('mahasiswa')
                    <a href="{{ route('submissions.history') }}"
                        class="flex items-center px-3 py-2 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-lg mr-2"></i>
                        <span class="font-medium">Kembali</span>
                    </a>
                    @endrole

                    @role('admin')
                    <a href="{{ route('admin.submissions') }}"
                        class="flex items-center px-3 py-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-lg mr-2"></i>
                        <span class="font-medium">Kembali</span>
                    </a>
                    @endrole

                    @role('pimpinan')
                    <a href="{{ route('submissions.signature') }}"
                        class="flex items-center px-3 py-2 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left text-lg mr-2"></i>
                        <span class="font-medium">Kembali</span>
                    </a>
                    @endrole
                    @endauth

                    <div class="h-6 w-px bg-gray-300"></div>

                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-gray-900">
                                Detail Pengajuan
                            </h1>
                            <p class="text-xs text-gray-500">Surat Tugas Akhir</p>
                        </div>
                    </div>
                </div>

                <!-- User Info -->
                @auth
                <div class="flex items-center space-x-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center shadow-md">
                        <span class="text-sm font-bold text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </span>
                    </div>

                    {{-- Dynamic Role Badge berdasarkan Role --}}
                    @role('mahasiswa')
                    <span
                        class="px-3 py-1 bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700 text-xs font-semibold rounded-full border border-emerald-200">
                        Mahasiswa
                    </span>
                    @endrole

                    @role('admin')
                    <span
                        class="px-3 py-1 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 text-xs font-semibold rounded-full border border-blue-200">
                        Admin
                    </span>
                    @endrole

                    @role('pimpinan')
                    <span
                        class="px-3 py-1 bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 text-xs font-semibold rounded-full border border-purple-200">
                        Pimpinan
                    </span>
                    @endrole
                </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white/80 backdrop-blur-sm border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-sm text-gray-500">
                © {{ date('Y') }} Submission System. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Scripts -->
    <script>
        // BARU (Bisa mengirim 'method' + 'params')
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('show-confirm-dialog', ({ message, method, params = null }) => { // <-- 1. Tambah 'params'
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // 2. Kirim method DAN params-nya
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