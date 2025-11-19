<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Submission System - Tugas Akhir</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Alpine.js for mobile menu toggle -->
    <!-- Saya tambahkan ini untuk fungsionalitas menu mobile -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.1/cdn.min.js" defer></script>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased font-sans bg-gradient-to-br from-emerald-50 via-white to-teal-50">
    <div class="min-h-screen flex flex-col">

        <!-- Header/Navigation -->
        <!-- Menambahkan x-data untuk state menu mobile -->
        <header x-data="{ open: false }"
            class="bg-white/80 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Mengganti justify-center ke justify-between agar hamburger bisa diletakkan di kanan -->
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('assets/img/unjani.png') }}" alt="logo unjani" class="w-10 h-10">
                        <div>
                            <h1 class="text-lg font-bold text-gray-800">Submission System</h1>
                            <p class="text-xs text-gray-500">Tugas Akhir Mahasiswa</p>
                        </div>
                    </div>

                    <!-- Auth Links (Desktop) -->
                    @if (Route::has('login'))
                    <!-- Tetap 'hidden' di mobile dan 'flex' di sm+ -->
                    <div class="hidden sm:flex items-center space-x-3">
                        @auth
                        <a href="{{ url('/dashboard') }}"
                            class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-lg shadow-md transition-all duration-300">
                            <i class="fas fa-home mr-2"></i>
                            Dashboard
                        </a>
                        @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center px-5 py-2.5 text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login
                        </a>
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-lg shadow-md transition-all duration-300">
                            <i class="fas fa-user-plus mr-2"></i>
                            Register
                        </a>
                        @endif
                        @endauth
                    </div>
                    @endif

                    <div class="sm:hidden flex items-center">
                        <button @click="open = !open" type="button"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <i x-show="!open" class="fas fa-bars text-xl" aria-hidden="true"></i>
                            <i x-show="open" class="fas fa-times text-xl" aria-hidden="true" style="display: none;"></i>
                        </button>
                    </div>

                </div>
            </div>
            <div x-show="open" @click.away="open = false" class="sm:hidden" id="mobile-menu" style="display: none;">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    @if (Route::has('login'))
                    @auth
                    <a href="{{ url('/dashboard') }}"
                        class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                        <i class="fas fa-home mr-3 w-5 text-center"></i>
                        Dashboard
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                        class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                        <i class="fas fa-sign-in-alt mr-3 w-5 text-center"></i>
                        Login
                    </a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                        <i class="fas fa-user-plus mr-3 w-5 text-center"></i>
                        Register
                    </a>
                    @endif
                    @endauth
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-6xl w-full">

                <!-- Hero Section -->
                <!-- Mengurangi margin bottom di mobile (mb-12) dan menambahkannya di sm+ (sm:mb-16) -->
                <div class="text-center mb-12 sm:mb-16">
                    <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br
                        from-emerald-400 to-teal-500 rounded-2xl shadow-lg mb-5 sm:mb-6">
                        <!-- Mengecilkan font ikon di mobile (text-3xl) -->
                        <i class="fas fa-graduation-cap text-3xl sm:text-4xl text-white"></i>
                    </div>
                    <!-- Mengecilkan font judul di mobile (text-3xl) -->
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                        Sistem Pengajuan
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-500">Tugas
                            Akhir</span>
                    </h1>
                    <!-- Mengecilkan font paragraf di mobile (text-base) -->
                    <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                        Platform modern untuk mengelola pengajuan surat tugas akhir mahasiswa dengan mudah dan efisien
                    </p>
                </div>

                <!-- Features Grid -->
                <!-- Ini sudah responsif (grid-cols-1 by default) jadi tidak perlu diubah -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <!-- Feature 1 -->
                    <div
                        class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-file-alt text-2xl text-blue-500"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Pengajuan Mudah</h3>
                        <p class="text-sm text-gray-600">
                            Ajukan surat tugas akhir dengan proses yang simpel dan terintegrasi
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div
                        class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-circle-check text-2xl text-emerald-500"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Tracking Real-time</h3>
                        <p class="text-sm text-gray-600">
                            Pantau status pengajuan Anda secara real-time dari pending hingga verified
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div
                        class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-signature text-2xl text-purple-500"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Tanda Tangan Digital</h3>
                        <p class="text-sm text-gray-600">
                            Sistem verifikasi dan tanda tangan digital yang aman dan terpercaya
                        </p>
                    </div>
                </div>

                <!-- CTA Section -->
                @guest
                <!-- Mengurangi padding di mobile (p-6) dan membesarkannya di sm+ (sm:p-8) -->
                <div
                    class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl shadow-sm p-6 sm:p-8 border border-emerald-100 text-center">
                    <h2 class="text-2xl font-bold text-gray-800 mb-3">
                        Siap Memulai?
                    </h2>
                    <p class="text-gray-600 mb-6 max-w-xl mx-auto">
                        Daftar sekarang dan kelola pengajuan tugas akhir Anda dengan lebih efisien
                    </p>
                    <!-- Ini sudah responsif (flex-col sm:flex-row) jadi tidak perlu diubah -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-lg shadow-md transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-user-plus mr-2"></i>
                            Daftar Sekarang
                        </a>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center w-full sm:w-auto px-8 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-lg shadow-sm border border-gray-200 transition-all duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sudah Punya Akun? Login
                        </a>
                    </div>
                </div>
                @endguest

                @auth
                <!-- Mengurangi padding di mobile (p-6) -->
                <div
                    class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl shadow-sm p-6 sm:p-8 border border-emerald-100 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full shadow-md mb-4">
                        <i class="fas fa-circle-check text-3xl text-emerald-500"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-3">
                        Selamat Datang Kembali!
                    </h2>
                    <p class="text-gray-600 mb-6">
                        Anda sudah login. Klik tombol di bawah untuk melanjutkan ke dashboard
                    </p>
                    <a href="{{ url('/dashboard') }}"
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-lg shadow-md transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-home mr-2"></i>
                        Ke Dashboard
                    </a>
                </div>
                @endauth

            </div>
        </main>

        <!-- Footer -->
        <!-- Ini sudah responsif (flex-col md:flex-row) jadi tidak perlu diubah -->
        <footer class="bg-white/80 backdrop-blur-sm border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row items-center justify-center">
                    <p class="text-sm text-gray-500 mb-4 md:mb-0">
                        © {{ date('Y') }} Submission System. All rights reserved.
                    </p>

                </div>
            </div>
        </footer>

    </div>
</body>

</html>