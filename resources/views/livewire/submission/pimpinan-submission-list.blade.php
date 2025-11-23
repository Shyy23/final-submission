<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Submission;
use Illuminate\Support\Str;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $status = ''; // 'approved' (Menunggu TTD) atau 'verified' (Selesai)

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }

    public function resetFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->resetPage();
    }

    public function with(): array
    {
        // Pimpinan melihat status 'approved' (siap ttd) atau 'verified' (sudah ttd)
        $query = Submission::with(['representative.user', 'memberStudents'])
            ->whereIn('status', ['approved', 'verified']);

        // Filter Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%')
                  ->orWhere('address_company', 'like', '%' . $this->search . '%')
                  ->orWhereHas('representative.user', function ($u) {
                      $u->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filter Status
        if ($this->status) {
            $query->where('status', $this->status);
        }

        $submissions = $query->orderBy('updated_at', 'desc')->paginate(10);

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
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Daftar Pengajuan</h2>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Review dan tanda tangani surat tugas mahasiswa</p>
        </div>
        {{-- Placeholder untuk keseimbangan layout --}}
        <div class="hidden sm:block"></div>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <div class="flex-1 w-full">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari mahasiswa, instansi, atau alamat..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:flex sm:flex-row gap-2 w-full md:w-auto">
                <select wire:model.live="status"
                    class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm w-full sm:w-auto">
                    <option value="">Semua Status</option>
                    <option value="approved">Menunggu TTD</option>
                    <option value="verified">Selesai (Verified)</option>
                </select>

                <button wire:click="resetFilters"
                    class="col-span-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium flex items-center justify-center">
                    <i class="fas fa-filter sm:mr-2"></i>
                    <span class="hidden sm:inline">Reset</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Empty State --}}
    @if($totalSubmissions === 0)
    <div class="bg-white rounded-xl shadow-sm p-8 sm:p-12 text-center border border-gray-100">
        <div class="flex justify-center mb-4 sm:mb-6">
            <div
                class="w-16 h-16 sm:w-24 sm:h-24 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center">
                <i class="fas fa-signature text-3xl sm:text-5xl text-purple-500"></i>
            </div>
        </div>
        <h3 class="text-lg sm:text-2xl font-bold text-gray-800 mb-2">Tidak Ada Data</h3>
        <p class="text-sm sm:text-base text-gray-600 mb-6 max-w-md mx-auto">
            @if($status || $search)
            Tidak ada pengajuan yang cocok dengan pencarian Anda.
            @else
            Saat ini tidak ada pengajuan yang memerlukan tanda tangan atau verifikasi.
            @endif
        </p>
    </div>
    @else

    {{-- Content Wrapper --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Table Title (Desktop Only) --}}
        <div class="hidden md:block p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-indigo-50">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-file-contract mr-2 text-purple-500"></i>
                @if($status == 'approved')
                Menunggu Tanda Tangan
                @elseif($status == 'verified')
                Riwayat Terverifikasi
                @else
                Semua Pengajuan
                @endif
            </h3>
        </div>

        {{-- DESKTOP VIEW: TABLE --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">No</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/4">
                            Mahasiswa</th>
                        {{-- Kolom Instansi & Alamat Digabung --}}
                        <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/3">
                            Informasi Instansi</th>
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
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-user-graduate text-purple-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">{{
                                        $submission->representative->user->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">NIM: {{ $submission->representative_nim }}</div>
                                    @if($submission->memberStudents->count() > 0)
                                    <div class="text-xs text-gray-400 mt-1">+{{ $submission->memberStudents->count() }}
                                        anggota</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1 min-w-[200px]"> {{-- min-w agar tidak gepeng di tablet --}}
                                <div class="text-sm font-bold text-gray-800">{{ $submission->company_name }}</div>
                                <div class="text-xs text-gray-500 flex items-start"
                                    title="{{ $submission->address_company }}">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1.5 mt-0.5 flex-shrink-0"></i>
                                    <span class="line-clamp-2">{{ Str::limit($submission->address_company, 80) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($submission->status == 'approved')
                            <span
                                class="px-3 py-1.5 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-700">
                                <i class="fas fa-clock mr-1.5"></i> Menunggu TTD
                            </span>
                            @else
                            <span
                                class="px-3 py-1.5 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                <i class="fas fa-check-circle mr-1.5"></i> Terverifikasi
                            </span>
                            @endif
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

                {{-- Row 1: Mahasiswa & Status --}}
                <div class="flex justify-between items-start mb-3">
                    <div class="flex gap-3">
                        <div
                            class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center text-purple-600 font-bold text-xs">
                            {{ substr($submission->representative->user->name ?? '?', 0, 2) }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 line-clamp-1">
                                {{ $submission->representative->user->name ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500 font-mono mt-0.5">
                                {{ $submission->representative_nim }}
                                @if($submission->memberStudents->count() > 0)
                                <span class="text-purple-500 ml-1 font-sans">(+{{ $submission->memberStudents->count()
                                    }})</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Status Badge Compact --}}
                    @if($submission->status == 'approved')
                    <span class="flex-shrink-0 px-2 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-700">
                        <i class="fas fa-pen-nib"></i> TTD
                    </span>
                    @else
                    <span class="flex-shrink-0 px-2 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-700">
                        <i class="fas fa-check"></i> Selesai
                    </span>
                    @endif
                </div>

                {{-- Row 2: Perusahaan & Alamat (Digabung) --}}
                <div class="mb-3 pl-[3.25rem]">
                    <div class="text-sm font-bold text-gray-800 mb-0.5">
                        {{ $submission->company_name }}
                    </div>
                    <div class="text-xs text-gray-500 flex items-start">
                        <i class="fas fa-map-marker-alt text-gray-400 mt-0.5 mr-1.5 flex-shrink-0"></i>
                        <span class="line-clamp-2">{{ $submission->address_company }}</span>
                    </div>
                </div>

                {{-- Row 3: Tanggal & Aksi --}}
                <div class="flex items-center justify-between pt-2 border-t border-dashed border-gray-100 pl-[3.25rem]">
                    <div class="text-xs text-gray-400">
                        <i class="far fa-calendar mr-1"></i> {{ $submission->updated_at->format('d M Y') }}
                    </div>

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