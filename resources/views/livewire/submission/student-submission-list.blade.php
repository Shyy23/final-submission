<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $status = '';
    
    // Reset pagination saat filter berubah
    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }

    public function with(): array
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return ['submissions' => collect([]), 'totalSubmissions' => 0];
        }

        $query = Submission::query()
            ->where(function ($q) use ($student) {
                $q->where('representative_nim', $student->nim)
                  ->orWhereHas('members', function ($subQ) use ($student) {
                      $subQ->where('student_nim', $student->nim);
                  });
            });

        // Filter Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%')
                  ->orWhere('address_company', 'like', '%' . $this->search . '%');
            });
        }

        // Filter Status
        if ($this->status) {
            $query->where('status', $this->status);
        }

        $submissions = $query->orderBy('created_at', 'desc')->paginate(10);

        return [
            'submissions' => $submissions,
            'totalSubmissions' => $submissions->total()
        ];
    }
}; ?>

<div>
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Riwayat Pengajuan</h2>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Kelola dan pantau status pengajuan surat tugas akhir Anda
            </p>
        </div>
        <div>
            <a href="{{ route('submissions.create') }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all shadow-sm w-full sm:w-auto"
                wire:navigate>
                <i class="fas fa-plus mr-2"></i>
                Buat Pengajuan
            </a>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <div class="flex-1 w-full">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama perusahaan atau alamat..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:flex sm:flex-row gap-2 w-full md:w-auto">
                <select wire:model.live="status"
                    class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm w-full sm:w-auto">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                    <option value="verified">Terverifikasi</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Empty State --}}
    @if($totalSubmissions === 0 && empty($search) && empty($status))
    <div class="bg-white rounded-xl shadow-sm p-8 sm:p-12 text-center border border-gray-100">
        <div class="flex justify-center mb-6">
            <div
                class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center">
                <i class="fas fa-file-contract text-4xl sm:text-5xl text-emerald-500"></i>
            </div>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Belum Ada Pengajuan</h3>
        <p class="text-sm sm:text-base text-gray-600 mb-6 max-w-md mx-auto">
            Anda belum memiliki riwayat pengajuan. Mulai langkah awal tugas akhir Anda sekarang.
        </p>
        <a href="{{ route('submissions.create') }}"
            class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-md transition-all duration-300"
            wire:navigate>
            <i class="fas fa-plus mr-2"></i>
            Buat Pengajuan Baru
        </a>
    </div>
    @elseif($totalSubmissions === 0)
    <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-search text-gray-400 text-2xl"></i>
            </div>
        </div>
        <p class="text-gray-500 font-medium">Tidak ditemukan data yang sesuai filter.</p>
        <button wire:click="$set('search', '')" class="text-emerald-600 hover:underline text-sm mt-2">Hapus
            pencarian</button>
    </div>
    @else

    {{-- Content Wrapper --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Table Title (Desktop Only) --}}
        <div class="hidden md:block p-6 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list-ul mr-2 text-emerald-500"></i>
                Daftar Pengajuan Anda
            </h3>
        </div>

        {{-- DESKTOP VIEW: TABLE --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">No</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/3">
                            Instansi Tujuan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($submissions as $index => $submission)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                            {{ ($submissions->currentPage() - 1) * $submissions->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1 min-w-[200px]">
                                <div class="text-sm font-bold text-gray-800">{{ $submission->company_name }}</div>
                                <div class="text-xs text-gray-500 flex items-start"
                                    title="{{ $submission->address_company }}">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1.5 mt-0.5 flex-shrink-0"></i>
                                    <span class="line-clamp-1">{{ Str::limit($submission->address_company, 60) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-800">
                                <i class="far fa-calendar text-gray-400 mr-1.5"></i>
                                {{ $submission->created_at->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5 ml-5">
                                {{ $submission->created_at->format('H:i') }} WIB
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $statusConfig = [
                            'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Menunggu',
                            'icon' => 'fa-hourglass-half'],
                            'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' =>
                            'Disetujui', 'icon' => 'fa-thumbs-up'],
                            'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Ditolak', 'icon'
                            => 'fa-times-circle'],
                            'verified' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-700', 'label' => 'Terverifikasi',
                            'icon' => 'fa-check-circle'],
                            ];
                            $config = $statusConfig[$submission->status] ?? $statusConfig['pending'];
                            @endphp
                            <span
                                class="px-3 py-1.5 inline-flex items-center text-xs leading-5 font-semibold rounded-full {{ $config['bg'] }} {{ $config['text'] }}">
                                <i class="fas {{ $config['icon'] }} mr-1.5"></i> {{ $config['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('submissions.show', $submission->submission_id) }}" wire:navigate
                                class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-emerald-600 rounded-lg text-xs font-medium transition-all shadow-sm">
                                <i class="fas fa-eye mr-1.5"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- MOBILE VIEW: CARDS --}}
        <div class="md:hidden">
            @foreach($submissions as $submission)
            <div class="p-4 border-b border-gray-200 last:border-0 hover:bg-gray-50 transition-colors">

                {{-- Row 1: Company & Status Badge --}}
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-3 max-w-[70%]">
                        <div
                            class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900 line-clamp-1">
                                {{ $submission->company_name }}
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5 flex items-center">
                                <i class="far fa-calendar-alt mr-1"></i>
                                {{ $submission->created_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    {{-- Status Badge Compact --}}
                    @php
                    $statusColors = [
                    'pending' => 'bg-amber-100 text-amber-700',
                    'approved' => 'bg-emerald-100 text-emerald-700',
                    'rejected' => 'bg-red-100 text-red-700',
                    'verified' => 'bg-teal-100 text-teal-700',
                    ];
                    $statusIcons = [
                    'pending' => 'fa-hourglass-start',
                    'approved' => 'fa-check',
                    'rejected' => 'fa-times',
                    'verified' => 'fa-check-double',
                    ];
                    $color = $statusColors[$submission->status] ?? 'bg-gray-100 text-gray-700';
                    $icon = $statusIcons[$submission->status] ?? 'fa-question';
                    @endphp
                    <span class="flex-shrink-0 px-2 py-1 rounded-md text-xs font-bold {{ $color }}">
                        <i class="fas {{ $icon }}"></i>
                    </span>
                </div>

                {{-- Row 2: Address --}}
                <div class="mb-3 pl-[3.25rem]">
                    <div class="text-xs text-gray-500 flex items-start">
                        <i class="fas fa-map-marker-alt text-gray-400 mt-0.5 mr-1.5 flex-shrink-0"></i>
                        <span class="line-clamp-2 leading-relaxed">{{ $submission->address_company }}</span>
                    </div>
                </div>

                {{-- Row 3: Action --}}
                <div class="flex items-center justify-end pt-2 border-t border-dashed border-gray-100 pl-[3.25rem]">
                    <a href="{{ route('submissions.show', $submission->submission_id) }}" wire:navigate
                        class="inline-flex items-center text-xs font-medium text-emerald-600 hover:text-emerald-700 transition-colors">
                        Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($submissions->hasPages())
        <div class="px-4 py-3 sm:px-6 bg-gray-50 border-t border-gray-100">
            {{ $submissions->links() }}
        </div>
        @endif
    </div>
    @endif
</div>