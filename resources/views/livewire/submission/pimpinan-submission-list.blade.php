<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Submission;
use Illuminate\Support\Str;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $status = ''; // 'approved' atau 'verified'

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
        // Pimpinan hanya melihat yang approved (siap ttd) atau verified (sudah ttd)
        $query = Submission::with(['representative.user'])
            ->whereIn('status', ['approved', 'verified']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('representative.user', function ($u) {
                      $u->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

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
    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari Mahasiswa atau Perusahaan..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                </div>
            </div>
            <div class="flex gap-2">
                <select wire:model.live="status"
                    class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                    <option value="">Semua Data</option>
                    <option value="approved">Menunggu TTD</option>
                    <option value="verified">Selesai (Verified)</option>
                </select>
                <button wire:click="resetFilters"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium">
                    <i class="fas fa-filter mr-2"></i>
                    Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Empty State --}}
    @if($totalSubmissions === 0)
    <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
        <div class="flex justify-center mb-6">
            <div
                class="w-24 h-24 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center">
                <i class="fas fa-signature text-5xl text-purple-500"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-3">
            @if($status || $search)
            Tidak Ditemukan
            @else
            Tidak Ada Pengajuan untuk Ditandatangani
            @endif
        </h3>
        <p class="text-gray-600 mb-6 max-w-md mx-auto">
            @if($status || $search)
            Tidak ada data yang cocok dengan filter pencarian Anda.
            @else
            Saat ini tidak ada pengajuan yang memerlukan tanda tangan atau verifikasi.
            @endif
        </p>
    </div>
    @else
    {{-- Submissions Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-indigo-50">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-stamp mr-2 text-purple-500"></i>
                @if($status == 'approved')
                Daftar Pengajuan - Menunggu TTD
                @elseif($status == 'verified')
                Daftar Pengajuan - Selesai (Verified)
                @else
                Daftar Pengajuan untuk Ditandatangani
                @endif
            </h3>
            <p class="text-sm text-gray-600 mt-1">
                Verifikasi dan tanda tangani pengajuan surat tugas akhir
            </p>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase font-semibold border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4">Mahasiswa</th>
                    <th class="px-6 py-4">Instansi Tujuan</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($submissions as $submission)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $submission->representative->user->name ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500">{{ $submission->representative_nim }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-700">
                        <div class="font-semibold">{{ $submission->company_name }}</div>
                        <div class="text-xs text-gray-500">{{ Str::limit($submission->address_company, 40) }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($submission->status == 'approved')
                        <span
                            class="px-2 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold border border-amber-200">
                            <i class="fas fa-clock mr-1"></i> Menunggu TTD
                        </span>
                        @else
                        <span
                            class="px-2 py-1 bg-teal-100 text-teal-800 rounded-full text-xs font-bold border border-teal-200">
                            <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($submission->status == 'approved')
                        <a href="{{ route('submissions.signature') }}"
                            class="inline-flex items-center px-3 py-1.5 bg-amber-500 text-white rounded-lg hover:bg-amber-600 text-xs font-bold shadow-sm transition-colors"
                            wire:navigate>
                            <i class="fas fa-pen-nib mr-1.5"></i> TTD Sekarang
                        </a>
                        @else
                        <a href="{{ route('submissions.show', $submission->submission_id) }}"
                            class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-200 rounded-lg text-xs font-bold transition-colors"
                            title="Lihat Detail" wire:navigate>
                            <i class="fas fa-eye mr-1.5"></i> Detail
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($submissions->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            {{ $submissions->links() }}
        </div>
        @endif
    </div>
    @endif
</div>