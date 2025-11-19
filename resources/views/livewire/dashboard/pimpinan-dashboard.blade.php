<?php

use Livewire\Volt\Component;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;

new class extends Component {        
    public function placeholder()
    {
        return view('components.skeleton.pimpinan-dashboard')->render();
    }
    // Lazy Loading Placeholder (Skeleton Loading)
    public function with(): array
    {
        $user = Auth::user();
        
        // Coba ambil relasi leader jika ada, jika tidak ada abaikan (null)
        // Pastikan Anda sudah membuat relasi 'leader()' di model User jika ingin menggunakan ini
        $leader = $user->leader ?? null; 

        // Jika profil pimpinan belum lengkap, kembalikan data default TAPI sertakan $user
        // Kita gunakan pengecekan role atau profil di sini
        if (!$user->hasRole('pimpinan')) {
             return [
                'user' => $user, // <--- PENTING: Sertakan user agar tidak Undefined Variable
                'approvedSubmissions' => 0,
                'verifiedSubmissions' => 0,
                'totalSubmissions' => 0,
                'pendingSignature' => collect([]),
                'recentVerified' => collect([]),
                'progressPercentage' => 0
            ];
        }

        // 1. Ambil Statistik Utama
        $stats = Submission::selectRaw(
            "COUNT(*) as total_submissions,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
            SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) as verified_count"
        )->first();

        // Extract values dengan default 0
        $approvedCount = $stats->approved_count ?? 0;
        $verifiedCount = $stats->verified_count ?? 0;
        $totalSubmissions = $stats->total_submissions ?? 0;

        // 2. Ambil Submission Menunggu (Status: approved)
        $pendingSignature = Submission::with(['representative.user'])
            ->where('status', 'approved')
            ->latest()
            ->take(5)
            ->get();

        // 3. Ambil Submission Terverifikasi (Status: verified)
        $recentVerified = Submission::with(['representative.user'])
            ->where('status', 'verified')
            ->latest()
            ->take(5)
            ->get();

        // 4. Hitung Progress
        $totalApproved = $approvedCount + $verifiedCount;
        $progressPercentage = $totalApproved > 0
            ? round(($verifiedCount / $totalApproved) * 100)
            : 0;

        return [
            'user' => $user, // Kirim user ke view
            'approvedSubmissions' => $approvedCount,
            'verifiedSubmissions' => $verifiedCount,
            'totalSubmissions' => $totalSubmissions,
            'pendingSignature' => $pendingSignature,
            'recentVerified' => $recentVerified,
            'progressPercentage' => $progressPercentage
        ];
    }
}; ?>

<div>
    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl shadow-sm p-6 mb-6 border border-emerald-100">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang, Pimpinan! 👋</h3>
                <p class="text-gray-600">Kelola verifikasi dan tanda tangan submission mahasiswa</p>
            </div>
            <div class="hidden md:block">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-tie text-4xl text-emerald-500"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        {{-- Menunggu Verifikasi (Approved) --}}
        <div
            class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-hourglass-half text-2xl text-amber-500"></i>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-gray-800">{{ $approvedSubmissions }}</p>
                </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Menunggu Verifikasi</h3>
            <div class="flex items-center text-xs text-gray-500">
                <i class="fas fa-circle text-amber-400 mr-2" style="font-size: 6px;"></i>
                <span>Perlu ditandatangani</span>
            </div>
            @if($approvedSubmissions > 0)
            <div class="mt-3 pt-3 border-t border-gray-100">
                <a href="{{ route('submissions.signature') }}"
                    class="text-xs text-amber-600 hover:text-amber-700 font-medium inline-flex items-center"
                    wire:navigate>
                    <i class="fas fa-signature mr-1"></i>
                    Tandatangani Sekarang
                </a>
            </div>
            @endif
        </div>

        {{-- Sudah Terverifikasi --}}
        <div
            class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-check-double text-2xl text-emerald-500"></i>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-gray-800">{{ $verifiedSubmissions }}</p>
                </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Sudah Terverifikasi</h3>
            <div class="flex items-center text-xs text-gray-500">
                <i class="fas fa-circle text-emerald-400 mr-2" style="font-size: 6px;"></i>
                <span>Selesai ditandatangani</span>
            </div>
            @if($verifiedSubmissions > 0)
            <div class="mt-3 pt-3 border-t border-gray-100">
                <span class="text-xs text-emerald-600 font-medium inline-flex items-center">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Verified Complete
                </span>
            </div>
            @endif
        </div>

        {{-- Total Submission Progress --}}
        <div
            class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-file-alt text-2xl text-blue-500"></i>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-gray-800">{{ $totalSubmissions }}</p>
                </div>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Total Submission</h3>
            <div class="flex items-center text-xs text-gray-500">
                <i class="fas fa-circle text-blue-400 mr-2" style="font-size: 6px;"></i>
                <span>Semua pengajuan masuk</span>
            </div>
            @if($totalSubmissions > 0)
            <div class="mt-3 pt-3 border-t border-gray-100">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Progress Verifikasi</span>
                    <span class="text-blue-600 font-semibold">{{ $progressPercentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-1.5 rounded-full"
                        style="width: {{ $progressPercentage }}%"></div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-bolt text-emerald-500 mr-2"></i>
            Aksi Cepat
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('submissions.signature') }}"
                class="group flex items-center p-5 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl hover:from-emerald-100 hover:to-teal-100 transition-all duration-300 border border-emerald-100 hover:shadow-md"
                wire:navigate>
                <div
                    class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300 shadow-md">
                    <i class="fas fa-signature text-xl text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">Tanda Tangan Submission</p>
                    <p class="text-sm text-gray-600">Verifikasi dan tandatangani</p>
                </div>
                @if($approvedSubmissions > 0)
                <div
                    class="flex items-center justify-center w-8 h-8 bg-amber-500 text-white rounded-full text-xs font-bold shadow-md mr-3 animate-pulse">
                    {{ $approvedSubmissions }}
                </div>
                @endif
                <i class="fas fa-arrow-right text-emerald-400 group-hover:translate-x-1 transition-transform"></i>
            </a>

            <a href="{{ route('profile') }}"
                class="group flex items-center p-5 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl hover:from-purple-100 hover:to-pink-100 transition-all duration-300 border border-purple-100 hover:shadow-md"
                wire:navigate>
                <div
                    class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300 shadow-md">
                    <i class="fas fa-user-circle text-xl text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">Profil Saya</p>
                    <p class="text-sm text-gray-600">Pengaturan akun</p>
                </div>
                <i class="fas fa-arrow-right text-purple-400 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>

    {{-- Empty State (if no approved submissions and verified > 0) --}}
    @if($approvedSubmissions === 0 && $verifiedSubmissions > 0)
    <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100 mb-8">
        <div class="flex justify-center mb-6">
            <div
                class="w-24 h-24 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clipboard-check text-5xl text-emerald-500"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-3">Tidak Ada Submission Pending</h3>
        <p class="text-gray-600 mb-6 max-w-md mx-auto">
            Luar biasa! Semua pengajuan surat tugas akhir yang masuk telah Anda verifikasi.
        </p>
        <div class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-600 font-semibold rounded-lg">
            <i class="fas fa-circle-check text-emerald-500 mr-2"></i>
            Semua Tugas Selesai
        </div>
    </div>
    @endif

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Submission Menunggu Verifikasi --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-hourglass-half text-amber-500 mr-2"></i>
                    Menunggu Tanda Tangan
                </h3>
            </div>
            <div class="p-6">
                @if($pendingSignature->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingSignature as $submission)
                    <div
                        class="flex items-center p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg hover:from-amber-100 hover:to-orange-100 transition-all duration-200 border border-amber-100">
                        <div
                            class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center shadow-sm">
                            <i class="fas fa-file-signature text-white"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-semibold text-gray-800">{{ $submission->company_name }}</p>
                            <p class="text-xs text-gray-500">{{ $submission->representative->user->name ?? 'Mahasiswa'
                                }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="far fa-calendar-alt mr-1"></i>
                                {{ $submission->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                        <a href="{{ route('submissions.signature') }}"
                            class="ml-2 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-medium rounded-lg transition-colors duration-200 shadow-sm"
                            wire:navigate>
                            <i class="fas fa-signature mr-1"></i>
                            Tanda Tangan
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-circle-check text-4xl text-emerald-300 mb-3"></i>
                    <p class="text-gray-500">Tidak ada submission menunggu</p>
                </div>
                @endif
            </div>
            @if($pendingSignature->count() > 0)
            <div class="p-4 bg-amber-50 border-t border-amber-100 text-center">
                <a href="{{ route('submissions.signature') }}"
                    class="text-amber-600 hover:text-amber-700 font-medium text-sm inline-flex items-center"
                    wire:navigate>
                    Lihat Semua Submission
                    <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
            @endif
        </div>

        {{-- Submission Terverifikasi --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-check-double text-emerald-500 mr-2"></i>
                    Sudah Terverifikasi
                </h3>
            </div>
            <div class="p-6">
                @if($recentVerified->count() > 0)
                <div class="space-y-4">
                    @foreach($recentVerified as $submission)
                    <div
                        class="flex items-center p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg border border-emerald-100">
                        <div
                            class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg flex items-center justify-center shadow-sm">
                            <i class="fas fa-shield-alt text-white"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-semibold text-gray-800">{{ $submission->company_name }}</p>
                            <p class="text-xs text-gray-500">{{ $submission->representative->user->name ?? 'Mahasiswa'
                                }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="far fa-calendar-check mr-1"></i>
                                {{ $submission->updated_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                        <div
                            class="ml-2 flex items-center px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-lg">
                            <i class="fas fa-circle-check mr-1"></i>
                            Verified
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada submission verified</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>