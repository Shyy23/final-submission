<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 1. JUDUL DINAMIS: Menggunakan variabel $title jika ada, jika tidak pakai config --}}
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-100">
    {{--
    2. STRUKTUR RESPONSIF:
    - Mobile: flex-col (Atas ke Bawah)
    - Desktop (md): flex-row (Samping menyamping)
    --}}
    <div class="min-h-screen flex flex-col md:flex-row" x-data="{ sidebarOpen: false }">

        {{-- Navigasi (Menerima state sidebarOpen) --}}
        <livewire:layout.navigation />

        {{-- Konten Utama --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden pt-16 md:pt-0 transition-all duration-300">

            <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
                <x-alert />
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    <script>
        // Mendaftarkan event listener SweetAlert2 global untuk Livewire
        if (typeof Livewire !== 'undefined') {
            Livewire.on('show-confirm-dialog', ({ message, method, params = null }) => {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981', // Emerald-500
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Dispatch event kembali ke Livewire dengan metode yang diminta
                        if (params) {
                            Livewire.dispatch(method, params); 
                        } else {
                            Livewire.dispatch(method);
                        }
                    }
                });
            });
        }
    </script>
</body>

</html>