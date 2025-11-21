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
    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama perusahaan atau alamat..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                </div>
            </div>
            <div class="flex gap-2">
                <select wire:model.live="status"
                    class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
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
    <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
        <div class="flex justify-center mb-6">
            <div
                class="w-24 h-24 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center">
                <i class="fas fa-inbox text-5xl text-emerald-500"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-3">Belum Ada Riwayat Pengajuan</h3>
        <p class="text-gray-600 mb-6 max-w-md mx-auto">
            Anda belum memiliki pengajuan submission. Mulai buat pengajuan pertama Anda untuk tugas akhir sekarang!
        </p>
        <a href="{{ route('submissions.create') }}"
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-lg shadow-md transition-all duration-300"
            wire:navigate>
            <i class="fas fa-plus mr-2"></i>
            Buat Pengajuan Pertama
        </a>
    </div>
    @elseif($totalSubmissions === 0)
    <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
        <p class="text-gray-500">Tidak ditemukan data yang sesuai filter.</p>
    </div>
    @else
    {{-- Submissions Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list mr-2 text-emerald-500"></i>
                Semua Pengajuan Submission
            </h3>
            <p class="text-sm text-gray-600 mt-1">Daftar lengkap riwayat pengajuan surat tugas akhir Anda</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th
                            class="px-3 md:px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            No
                        </th>
                        <th
                            class="px-3 md:px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Perusahaan
                        </th>
                        {{-- HIDE DI MOBILE/TABLET --}}
                        <th
                            class="hidden md:table-cell px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Alamat
                        </th>
                        <th
                            class="hidden md:table-cell px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tanggal Pengajuan
                        </th>
                        {{-- END HIDE --}}
                        <th
                            class="px-3 md:px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        {{-- HIDE DI MOBILE/TABLET --}}
                        <th
                            class="hidden md:table-cell px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Catatan
                        </th>
                        {{-- END HIDE --}}
                        <th
                            class="px-3 md:px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($submissions as $index => $submission)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        {{-- No --}}
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-800">
                                {{ ($submissions->currentPage() - 1) * $submissions->perPage() + $index + 1 }}
                            </div>
                        </td>
                        {{-- Perusahaan (Kolom Utama) --}}
                        <td class="px-3 md:px-6 py-4">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-building text-emerald-600 text-sm md:text-base"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">{{ $submission->company_name }}
                                    </div>
                                    {{-- Tampilkan Tanggal di bawah nama Perusahaan (Khusus Mobile) --}}
                                    <div class="md:hidden text-xs text-gray-500 mt-1">
                                        <i class="far fa-calendar-alt text-gray-400 mr-1"></i>
                                        {{ $submission->created_at->format('d M Y') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        {{-- Alamat (Hidden Mobile) --}}
                        <td class="hidden md:table-cell px-6 py-4">
                            <div class="text-sm text-gray-600 max-w-xs">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                {{ Str::limit($submission->address_company, 50) }}
                            </div>
                        </td>
                        {{-- Tanggal Pengajuan (Hidden Mobile) --}}
                        <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-800">
                                <i class="far fa-calendar text-gray-400 mr-1"></i>
                                {{ $submission->created_at->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                <i class="far fa-clock text-gray-400 mr-1"></i>
                                {{ $submission->created_at->format('H:i') }} WIB
                            </div>
                        </td>
                        {{-- Status --}}
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap">
                            @php
                            $statusConfig = [
                            'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Menunggu',
                            'icon' => 'fa-hourglass-half'],
                            'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' =>
                            'Disetujui', 'icon' => 'fa-thumbs-up'],
                            'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Ditolak', 'icon'
                            => 'fa-thumbs-down'],
                            'verified' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-700', 'label' => 'Terverifikasi',
                            'icon' => 'fa-circle-check'],
                            ];
                            $config = $statusConfig[$submission->status] ?? $statusConfig['pending'];
                            @endphp
                            <span
                                class="px-2 py-1 inline-flex items-center text-[10px] sm:text-xs leading-5 font-semibold rounded-full {{ $config['bg'] }} {{ $config['text'] }}">
                                <i class="fas {{ $config['icon'] }} mr-1 sm:mr-1.5 text-xs font-medium"></i>
                                {{-- Hilangkan label di layar kecil, tampilkan di sm: ke atas --}}
                                <span class="hidden sm:inline">{{ $config['label'] }}</span>
                            </span>
                        </td>
                        {{-- Catatan (Hidden Mobile) --}}
                        <td class="hidden md:table-cell px-6 py-4">
                            @if($submission->note)
                            <div class="text-sm text-gray-600 max-w-xs">
                                <i class="fas fa-sticky-note text-gray-400 mr-1"></i>
                                {{ Str::limit($submission->note, 30) }}
                            </div>
                            @else
                            <span class="text-xs text-gray-400 italic">Tidak ada catatan</span>
                            @endif
                        </td>
                        {{-- Aksi --}}
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('submissions.show', $submission->submission_id) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium rounded-lg transition-colors duration-200 shadow-sm"
                                    wire:navigate>
                                    <i class="fas fa-eye md:mr-1.5"></i>
                                    {{-- Teks Aksi hanya muncul di md: ke atas --}}
                                    <span class="hidden md:inline">Detail</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination Info --}}
        @if($submissions->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            <div class="flex items-center justify-between text-xs md:text-sm">
                <div class="text-gray-600">
                    Menampilkan <span class="font-semibold text-gray-800">{{ $submissions->firstItem() }}</span>
                    sampai <span class="font-semibold text-gray-800">{{ $submissions->lastItem() }}</span>
                    dari <span class="font-semibold text-gray-800">{{ $submissions->total() }}</span> pengajuan
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Pagination Links --}}
    <div class="mt-6">
        {{ $submissions->links() }}
    </div>
    @endif
</div>