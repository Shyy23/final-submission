<?php

use Livewire\Volt\Component;
use App\Models\Submission;
use App\Models\User;

new class extends Component {
    public function placeholder()
    {
        return view('components.skeleton.admin-dashboard')->render();
    }

    public function with(): array
    {
        // Query Stats
        $submissionStats = Submission::selectRaw(
            'COUNT(*) as total_submissions,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_submissions,
            SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_submissions,
            SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_submissions,
            SUM(CASE WHEN status = "verified" THEN 1 ELSE 0 END) as verified_submissions'
        )->first();

        // Recent Data
        $recentSubmissions = Submission::with(['representative.user'])
            ->latest()
            ->take(5)
            ->get();

        // Status Counts Array
        $statusCounts = [
            [
                'label' => 'Pending',
                'count' => $submissionStats->pending_submissions,
                'icon' => 'fa-clock',
                'color' => 'amber'
            ],
            [
                'label' => 'Approved',
                'count' => $submissionStats->approved_submissions,
                'icon' => 'fa-circle-check',
                'color' => 'emerald'
            ],
            [
                'label' => 'Rejected',
                'count' => $submissionStats->rejected_submissions,
                'icon' => 'fa-times-circle',
                'color' => 'red'
            ],
            [
                'label' => 'Verified',
                'count' => $submissionStats->verified_submissions,
                'icon' => 'fa-shield-alt',
                'color' => 'teal'
            ],
        ];

        return [
            'totalUsers' => User::count(),
            'totalSubmissions' => $submissionStats->total_submissions ?? 0,
            'pendingSubmissions' => $submissionStats->pending_submissions ?? 0,
            'approvedSubmissions' => $submissionStats->approved_submissions ?? 0,
            'recentSubmissions' => $recentSubmissions,
            'statusCounts' => $statusCounts
        ];
    }
}; ?>

<div class="space-y-6">

    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl shadow-sm p-4 sm:p-6 border border-emerald-100">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Selamat Datang, Admin! 👋</h3>
                <p class="text-sm sm:text-base text-gray-600">Kelola pengguna dan pengajuan submission dengan mudah</p>
            </div>
            <div class="hidden md:block">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-shield text-3xl sm:text-4xl text-emerald-500"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

        {{-- Total Users --}}
        <div
            class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-users text-xl sm:text-2xl text-blue-500"></i>
                </div>
                <div class="text-right">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalUsers ?? 0 }}</p>
                </div>
            </div>
            <h3 class="text-gray-600 text-xs sm:text-sm font-medium mb-1">Total Pengguna</h3>
            <div class="flex items-center text-[10px] sm:text-xs text-gray-500">
                <i class="fas fa-circle text-blue-400 mr-2" style="font-size: 6px;"></i>
                <span>Terdaftar di sistem</span>
            </div>
        </div>

        {{-- Pending Submissions --}}
        <div
            class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-clock text-xl sm:text-2xl text-amber-500"></i>
                </div>
                <div class="text-right">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $pendingSubmissions ?? 0 }}</p>
                </div>
            </div>
            <h3 class="text-gray-600 text-xs sm:text-sm font-medium mb-1">Menunggu Review</h3>
            <div class="flex items-center text-[10px] sm:text-xs text-gray-500">
                <i class="fas fa-circle text-amber-400 mr-2" style="font-size: 6px;"></i>
                <span>Perlu ditinjau</span>
            </div>
        </div>

        {{-- Approved (Waiting Verified) --}}
        <div
            class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-hourglass-half text-xl sm:text-2xl text-emerald-500"></i>
                </div>
                <div class="text-right">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $approvedSubmissions ?? 0 }}</p>
                </div>
            </div>
            <h3 class="text-gray-600 text-xs sm:text-sm font-medium mb-1">Menunggu Verifikasi</h3>
            <div class="flex items-center text-[10px] sm:text-xs text-gray-500">
                <i class="fas fa-circle text-emerald-400 mr-2" style="font-size: 6px;"></i>
                <span>Approved, belum verified</span>
            </div>
        </div>

        {{-- Total Submissions --}}
        <div
            class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-file-alt text-xl sm:text-2xl text-purple-500"></i>
                </div>
                <div class="text-right">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalSubmissions ?? 0 }}</p>
                </div>
            </div>
            <h3 class="text-gray-600 text-xs sm:text-sm font-medium mb-1">Total Submission</h3>
            <div class="flex items-center text-[10px] sm:text-xs text-gray-500">
                <i class="fas fa-circle text-purple-400 mr-2" style="font-size: 6px;"></i>
                <span>Semua pengajuan</span>
            </div>
        </div>
    </div>

    {{-- Quick Links (Aksi Cepat) --}}
    <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-bolt text-emerald-500 mr-2"></i>
            Aksi Cepat
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">

            <a href="{{ route('admin.users') }}"
                class="group flex items-center p-3 sm:p-5 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl hover:from-blue-100 hover:to-indigo-100 transition-all duration-300 border border-blue-100 hover:shadow-md">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500 rounded-xl flex items-center justify-center mr-3 sm:mr-4 group-hover:scale-110 transition-transform duration-300 shadow-md flex-shrink-0">
                    <i class="fas fa-users-cog text-base sm:text-xl text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm sm:text-base truncate">Kelola Pengguna</p>
                    <p class="text-xs sm:text-sm text-gray-600 truncate">Manajemen user</p>
                </div>
                <i class="fas fa-arrow-right ml-2 text-blue-400 group-hover:translate-x-1 transition-transform"></i>
            </a>

            <a href="{{ route('admin.submissions') }}"
                class="group flex items-center p-3 sm:p-5 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl hover:from-emerald-100 hover:to-teal-100 transition-all duration-300 border border-emerald-100 hover:shadow-md">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-500 rounded-xl flex items-center justify-center mr-3 sm:mr-4 group-hover:scale-110 transition-transform duration-300 shadow-md flex-shrink-0">
                    <i class="fas fa-tasks text-base sm:text-xl text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm sm:text-base truncate">Kelola Submission</p>
                    <p class="text-xs sm:text-sm text-gray-600 truncate">Review pengajuan</p>
                </div>
                <i class="fas fa-arrow-right ml-2 text-emerald-400 group-hover:translate-x-1 transition-transform"></i>
            </a>

            <a href="{{ route('profile') }}"
                class="group flex items-center p-3 sm:p-5 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl hover:from-purple-100 hover:to-pink-100 transition-all duration-300 border border-purple-100 hover:shadow-md">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-500 rounded-xl flex items-center justify-center mr-3 sm:mr-4 group-hover:scale-110 transition-transform duration-300 shadow-md flex-shrink-0">
                    <i class="fas fa-user-circle text-base sm:text-xl text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm sm:text-base truncate">Profil Saya</p>
                    <p class="text-xs sm:text-sm text-gray-600 truncate">Pengaturan akun</p>
                </div>
                <i class="fas fa-arrow-right ml-2 text-purple-400 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>

    {{-- Recent Activity Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">

        {{-- Recent Submissions --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-file-alt text-emerald-500 mr-2"></i>
                    Submission Terbaru
                </h3>
            </div>
            <div class="p-4 sm:p-6">
                @if(isset($recentSubmissions) && $recentSubmissions->count() > 0)
                <div class="space-y-3 sm:space-y-4">
                    @foreach($recentSubmissions as $submission)
                    <div
                        class="flex items-start sm:items-center p-3 sm:p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        {{-- Icon --}}
                        <div
                            class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg flex items-center justify-center shadow-sm mt-1 sm:mt-0">
                            <i class="fas fa-file text-white text-xs sm:text-base"></i>
                        </div>

                        {{-- Content Wrapper --}}
                        <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $submission->company_name }}</p>
                            <p class="text-[10px] sm:text-xs text-gray-500 truncate">{{
                                $submission->representative->user->name ?? 'N/A' }}</p>
                        </div>

                        {{-- Status Badge --}}
                        @php
                        $statusConfig = [
                        'pending' => ['bg-amber-100', 'text-amber-700'],
                        'approved' => ['bg-emerald-100', 'text-emerald-700'],
                        'rejected' => ['bg-red-100', 'text-red-700'],
                        'verified' => ['bg-teal-100', 'text-teal-700'],
                        ];
                        [$bg, $text] = $statusConfig[$submission->status] ?? ['bg-gray-100', 'text-gray-700'];
                        @endphp
                        <span
                            class="ml-2 px-2 py-0.5 sm:py-1 text-[10px] sm:text-xs font-medium rounded-full {{ $bg }} {{ $text }} flex-shrink-0">
                            {{ ucfirst($submission->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-6 sm:py-8">
                    <i class="fas fa-inbox text-3xl sm:text-4xl text-gray-300 mb-2 sm:mb-3"></i>
                    <p class="text-sm sm:text-base text-gray-500">Belum ada submission</p>
                </div>
                @endif
            </div>
            <div class="p-3 sm:p-4 bg-gray-50 border-t border-gray-100 text-center">
                <a href="{{ route('admin.submissions') }}"
                    class="text-emerald-600 hover:text-emerald-700 font-medium text-xs sm:text-sm inline-flex items-center">
                    Lihat Semua Submission
                    <i class="fas fa-arrow-right ml-2 text-[10px] sm:text-xs"></i>
                </a>
            </div>
        </div>

        {{-- System Overview --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-chart-pie text-blue-500 mr-2"></i>
                    Ringkasan Status
                </h3>
            </div>
            <div class="p-4 sm:p-6">
                @if(isset($statusCounts) && count($statusCounts) > 0)
                <div class="space-y-3 sm:space-y-4">
                    @foreach($statusCounts as $status)
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 sm:w-10 sm:h-10 bg-{{ $status['color'] }}-100 rounded-lg flex items-center justify-center mr-3 sm:mr-4">
                                <i
                                    class="fas {{ $status['icon'] }} text-{{ $status['color'] }}-500 text-sm sm:text-base"></i>
                            </div>
                            <span class="font-medium text-gray-700 text-xs sm:text-sm">{{ $status['label'] }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-lg sm:text-2xl font-bold text-gray-800 mr-2">{{ $status['count'] ?? 0
                                }}</span>
                            <span class="text-[10px] sm:text-sm text-gray-500">subm.</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-6 sm:py-8">
                    <i class="fas fa-chart-bar text-3xl sm:text-4xl text-gray-300 mb-2 sm:mb-3"></i>
                    <p class="text-sm sm:text-base text-gray-500">Tidak ada data status</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>