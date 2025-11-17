{{-- Welcome Card --}}
<div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl shadow-sm p-6 mb-6 border border-emerald-100">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Halo, {{ Auth::user()->name }}! 👋</h3>
            <p class="text-gray-600">Kelola pengajuan surat tugas akhir Anda dengan mudah</p>
        </div>
        <div class="hidden md:block">
            <i class="fas fa-file-contract text-emerald-400 text-6xl"></i>
        </div>
    </div>
</div>

{{-- Statistics Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
    {{-- Total Submissions --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-file-lines text-blue-500 text-2xl"></i>
            </div>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">Total Pengajuan</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $totalSubmissions ?? 0 }}</p>
    </div>

    {{-- Pending --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-hourglass-half text-amber-500 text-2xl"></i>
            </div>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">Menunggu</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $pendingCount ?? 0 }}</p>
    </div>

    {{-- Approved --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-thumbs-up text-emerald-500 text-2xl"></i>
            </div>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">Disetujui</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $approvedCount ?? 0 }}</p>
    </div>

    {{-- Rejected --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-thumbs-down text-red-500 text-2xl"></i>
            </div>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">Ditolak</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $rejectedCount ?? 0 }}</p>
    </div>

    {{-- Verified --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-teal-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-circle-check text-teal-500 text-2xl"></i>
            </div>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">Terverifikasi</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $verifiedCount ?? 0 }}</p>
    </div>
</div>

{{-- Quick Links --}}
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-bolt mr-2 text-emerald-500"></i>
        Aksi Cepat
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('submissions.create') }}" class="flex items-center p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg hover:from-emerald-100 hover:to-teal-100 transition-all duration-300 border border-emerald-100">
            <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-plus-circle text-white text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Buat Pengajuan</p>
                <p class="text-sm text-gray-600">Ajukan surat baru</p>
            </div>
        </a>

        <a href="{{ route('submissions.history') }}" class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg hover:from-blue-100 hover:to-indigo-100 transition-all duration-300 border border-blue-100">
            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-history text-white text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Lihat Semua</p>
                <p class="text-sm text-gray-600">Riwayat pengajuan</p>
            </div>
        </a>

        <a href="{{ route('dashboard') }}" class="flex items-center p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg hover:from-purple-100 hover:to-pink-100 transition-all duration-300 border border-purple-100">
            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-gauge-high text-white text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Dashboard</p>
                <p class="text-sm text-gray-600">Halaman utama</p>
            </div>
        </a>
    </div>
</div>

{{-- Empty State (if no submissions) --}}
@if($totalSubmissions === 0)
<div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
    <div class="flex justify-center mb-6">
        <i class="fas fa-file-lines text-gray-300 text-8xl"></i>
    </div>
    <h3 class="text-2xl font-bold text-gray-800 mb-3">Belum Ada Pengajuan</h3>
    <p class="text-gray-600 mb-6 max-w-md mx-auto">
        Anda belum memiliki pengajuan surat tugas akhir. Mulai buat pengajuan pertama Anda sekarang!
    </p>
    <a href="{{ route('submissions.create') }}" class="inline-flex items-center px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg shadow-sm transition-colors duration-300">
        <i class="fas fa-plus mr-2"></i>
        Buat Pengajuan Pertama
    </a>
</div>
@endif

{{-- Recent Submissions --}}
@if($totalSubmissions > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
            <i class="fas fa-clock mr-2 text-emerald-500"></i>
            Pengajuan Terbaru
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perusahaan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($recentSubmissions as $submission)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-800">{{ $submission->company_name }}</div>
                        <div class="text-sm text-gray-500">{{ Str::limit($submission->address_company, 40) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-800">{{ $submission->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $submission->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                        $statusConfig = [
                        'pending' => ['bg-amber-100', 'text-amber-700', 'Menunggu', 'fa-hourglass-half'],
                        'approved' => ['bg-emerald-100', 'text-emerald-700', 'Disetujui', 'fa-thumbs-up'],
                        'rejected' => ['bg-red-100', 'text-red-700', 'Ditolak', 'fa-thumbs-down'],
                        'verified' => ['bg-teal-100', 'text-teal-700', 'Terverifikasi', 'fa-circle-check'],
                        ];
                        [$bg, $text, $label, $icon] = $statusConfig[$submission->status];
                        @endphp
                        <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full {{ $bg }} {{ $text }}">
                            <i class="fas {{ $icon }} mr-1"></i>
                            {{ $label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('submissions.show', $submission->submission_id) }}" class="text-emerald-600 hover:text-emerald-700 font-medium flex items-center">
                            <i class="fas fa-eye mr-1"></i>
                            Lihat Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 bg-gray-50 border-t border-gray-100 text-center">
        <a href="{{ route('submissions.history') }}" class="text-emerald-600 hover:text-emerald-700 font-medium text-sm flex items-center justify-center">
            Lihat Semua Pengajuan <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
</div>
@endif