<?php

use Livewire\Volt\Component;
use App\Models\Submission;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.guest')] class extends Component {
    public Submission $submission;

    public function mount($id)
    {
        $this->submission = Submission::with([
            'leader.user',
            'leader.position'
        ])->findOrFail($id);
    }

    public function downloadDocument()
    {
        // Pastikan path dokumen ada
        if (!$this->submission->document_path) {
            session()->flash('error', 'Dokumen belum tersedia.');
            return;
        }

        $filePath = $this->submission->document_path;
        
        // Cek apakah file ada di Storage (Private/Public Storage Laravel)
        if (Storage::exists($filePath)) {
             return Storage::download($filePath);
        }
        // Cek apakah file ada di folder public (Legacy/Direct Upload)
        elseif (file_exists(public_path($filePath))) {
             return response()->download(public_path($filePath));
        }
        // Cek folder public/storage/ (Symlink)
        elseif (file_exists(public_path('storage/' . $filePath))) {
             return response()->download(public_path('storage/' . $filePath));
        }

        session()->flash('error', 'File fisik tidak ditemukan di server.');
    }

    public function downloadQRCode()
    {
        if (!$this->submission->qr_url) {
            session()->flash('error', 'QR Code belum tersedia.');
            return;
        }

        $path = public_path('qr-code/' . $this->submission->qr_url);

        if (file_exists($path)) {
            return response()->download($path, 'QR-Verifikasi-' . $this->submission->id . '.png');
        }

        session()->flash('error', 'File QR Code tidak ditemukan.');
    }
}; ?>

<div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-4 sm:p-6">

    {{-- Notifikasi Error --}}
    @if (session()->has('error'))
    <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-lg z-50 animate-bounce"
        role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Main Card Container --}}
    <div class="w-full max-w-3xl bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

        {{-- Header: Status Banner --}}
        <div
            class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-8 text-center text-white relative overflow-hidden">
            {{-- Background Decoration --}}
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <i class="fas fa-check-circle text-9xl absolute -top-4 -left-4"></i>
                <i class="fas fa-file-contract text-9xl absolute -bottom-4 -right-4"></i>
            </div>

            <div class="relative z-10">
                <div
                    class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-white/30 shadow-inner">
                    <i class="fas fa-check text-3xl text-white"></i>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2 tracking-tight">Dokumen Terverifikasi</h1>
                <p class="text-emerald-50 text-sm sm:text-base max-w-lg mx-auto leading-relaxed">
                    Surat Tugas ini sah, telah diverifikasi, dan ditandatangani secara digital oleh sistem Akademik FSI
                    UNJANI.
                </p>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-8">

            {{-- 1. Informasi Utama Dokumen --}}
            <div class="text-center space-y-3 border-b border-gray-100 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 leading-snug">{{ $submission->company_name }}
                </h2>

                {{-- ID Dihapus, hanya Tanggal --}}
                <div class="flex items-center justify-center">
                    <span
                        class="flex items-center bg-gray-100 px-4 py-1.5 rounded-full text-sm text-gray-600 font-medium">
                        <i class="far fa-calendar-alt mr-2 text-gray-400"></i>
                        Terbit: {{ $submission->updated_at->translatedFormat('d F Y') }}
                    </span>
                </div>
            </div>

            {{-- 2. Detail Validasi & Aksi (Grid Layout Responsif) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- Kiri: QR Code & Validator --}}
                <div class="space-y-6 flex flex-col h-full">

                    {{-- QR Card --}}
                    <div
                        class="bg-gray-50 rounded-xl p-6 text-center border border-gray-100 flex-1 flex flex-col justify-center items-center">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Kode Validasi Digital
                        </div>

                        @if($submission->qr_url)
                        <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200 mb-4 group relative">
                            <img src="{{ asset('qr-code/' . $submission->qr_url) }}" alt="QR Code"
                                class="w-40 h-40 sm:w-48 sm:h-48 object-contain">

                            {{-- Hover Effect untuk QR (Opsional visual cue) --}}
                            <div
                                class="absolute inset-0 bg-black/5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                            </div>
                        </div>

                        {{-- Tombol Download QR --}}
                        <button wire:click="downloadQRCode"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium inline-flex items-center transition-colors">
                            <i class="fas fa-download mr-1.5"></i> Download QR
                        </button>
                        @else
                        <div class="w-40 h-40 bg-gray-200 rounded flex items-center justify-center text-gray-400 mb-4">
                            <i class="fas fa-qrcode text-3xl opacity-50"></i>
                        </div>
                        <p class="text-sm text-red-400">QR Code tidak tersedia</p>
                        @endif

                        <p class="text-xs text-gray-500 mt-3 leading-relaxed max-w-xs mx-auto">
                            Scan QR code ini untuk membuktikan keaslian dokumen fisik secara langsung.
                        </p>
                    </div>

                    {{-- Validator Info --}}
                    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex items-center">
                        <div
                            class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-purple-600 mr-4 flex-shrink-0 border border-purple-100">
                            <i class="fas fa-signature text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wide mb-0.5">Penanda Tangan
                            </p>
                            <p class="font-bold text-gray-800 truncate">{{ $submission->leader->user->name ?? 'Pimpinan
                                FSI' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $submission->leader->position->position_name ??
                                'Pejabat Berwenang' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Preview File & Aksi --}}
                <div class="space-y-6 flex flex-col h-full justify-center">

                    {{-- File Card --}}
                    <div
                        class="bg-blue-50 border border-blue-100 rounded-xl p-6 relative overflow-hidden group hover:shadow-md transition-shadow duration-300">
                        {{-- Background Icon --}}
                        <div
                            class="absolute -top-6 -right-6 p-4 opacity-10 group-hover:opacity-15 transition-opacity transform group-hover:scale-110 duration-500">
                            <i class="fas fa-file-pdf text-9xl text-blue-600 rotate-12"></i>
                        </div>

                        <div class="relative z-10">
                            <div
                                class="w-12 h-12 bg-white rounded-lg flex items-center justify-center text-blue-600 shadow-sm mb-4">
                                <i class="fas fa-file-alt text-2xl"></i>
                            </div>

                            <h3 class="text-lg font-bold text-blue-900 mb-2">Dokumen Surat Tugas</h3>
                            <p class="text-sm text-blue-800 mb-6 opacity-80 leading-relaxed">
                                Dokumen digital asli yang tersimpan aman di server universitas. Gunakan tombol di bawah
                                untuk mengunduh file asli.
                            </p>

                            <button wire:click="downloadDocument"
                                class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition-all transform hover:translate-y-[-2px] active:translate-y-0 focus:ring-4 focus:ring-blue-200 group-hover:bg-blue-700">
                                <i class="fas fa-download mr-2 animate-bounce"></i>
                                Download Dokumen Asli
                            </button>
                        </div>
                    </div>

                    {{-- Security Badge --}}
                    <div class="flex items-start p-4 bg-amber-50 border border-amber-100 rounded-xl">
                        <i class="fas fa-shield-alt text-amber-500 text-xl mr-3 mt-0.5"></i>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm mb-1">Jaminan Keaslian</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">
                                Halaman verifikasi ini adalah rujukan utama. Jika data pada dokumen fisik berbeda dengan
                                halaman ini, maka dokumen tersebut dinyatakan <strong>TIDAK VALID</strong>.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>