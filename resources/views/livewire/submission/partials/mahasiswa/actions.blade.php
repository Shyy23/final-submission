<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Status & Aksi</h3>

    @php
    $statusConfig = [
    'pending' => ['bg' => 'bg-gradient-to-r from-amber-100 to-orange-100', 'text' => 'text-amber-700', 'label' =>
    'Menunggu Persetujuan', 'icon' => 'fa-hourglass-half'],
    'approved' => ['bg' => 'bg-gradient-to-r from-emerald-100 to-teal-100', 'text' => 'text-emerald-700', 'label' =>
    'Disetujui Admin', 'icon' => 'fa-thumbs-up'],
    'rejected' => ['bg' => 'bg-gradient-to-r from-red-100 to-pink-100', 'text' => 'text-red-700', 'label' => 'Ditolak',
    'icon' => 'fa-times-circle'],
    'verified' => ['bg' => 'bg-gradient-to-r from-teal-100 to-cyan-100', 'text' => 'text-teal-700', 'label' =>
    'Terverifikasi', 'icon' => 'fa-circle-check'],
    ];
    $config = $statusConfig[$submission->status];
    @endphp

    <div class="mb-6">
        <div
            class="px-4 py-3 inline-flex items-center text-sm font-semibold rounded-xl {{ $config['bg'] }} {{ $config['text'] }} border border-current border-opacity-20">
            <i class="fas {{ $config['icon'] }} mr-2 text-lg"></i>
            {{ $config['label'] }}
        </div>
    </div>

    {{-- Action Buttons untuk Mahasiswa --}}
    <div class="space-y-3">
        @if($submission->status === 'pending' && Auth::user()->student->nim === $submission->representative_nim)
        {{-- BARU --}}
        <button wire:click="confirmCancelSubmission"
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-semibold rounded-lg transition-all duration-200 shadow-sm">
            <i class="fas fa-times mr-2"></i>
            Batalkan Pengajuan
        </button>
        @endif

        @if($submission->status === 'verified')
        <button wire:click="downloadFile"
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-lg transition-all duration-200 shadow-sm">
            <i class="fas fa-download mr-2"></i>
            Download Surat Final
        </button>
        @endif
    </div>
</div>