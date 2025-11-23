<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Illuminate\Support\Str;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $status = '';

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
        $query = Submission::with([
            'representative.user',
            'memberStudents.user'
        ]);

        // Filter Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%')
                  ->orWhere('address_company', 'like', '%' . $this->search . '%')
                  ->orWhereHas('representative.user', function ($subQ) {
                      $subQ->where('name', 'like', '%' . $this->search . '%');
                  });
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

    // --- LOGIKA HAPUS ---
    public function confirmDeleteSubmission($submissionId)
    {
        $this->dispatch('show-confirm-dialog', 
            message: 'Apakah Anda yakin ingin menghapus pengajuan ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua file yang terkait.', 
            method: 'do-delete-submission-admin', 
            params: ['submissionId' => $submissionId]
        );
    }

    #[On('do-delete-submission-admin')] 
    public function deleteSubmission($submissionId)
    {
        try {
            $submission = Submission::findOrFail($submissionId);
            $this->deleteSubmissionFiles($submission);
            $submission->delete();

            session()->flash('message', 'Pengajuan berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus pengajuan: ' . $e->getMessage());
        }
    }

    private function deleteSubmissionFiles(Submission $submission)
    {
        try {
            if ($submission->file_submission && Storage::disk('local')->exists($submission->file_submission)) {
                Storage::disk('local')->delete($submission->file_submission);
            }
            if ($submission->signed_file && Storage::disk('local')->exists($submission->signed_file)) {
                Storage::disk('local')->delete($submission->signed_file);
            }
            if ($submission->qr_url) {
                $qrPath = public_path($submission->qr_url);
                if (file_exists($qrPath)) { unlink($qrPath); }

                $qrStoragePath = str_replace('qr-code/', 'qr-code/', $submission->qr_url);
                if (Storage::disk('public')->exists($qrStoragePath)) {
                    Storage::disk('public')->delete($qrStoragePath);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error deleting submission files: ' . $e->getMessage());
        }
    }
}; ?>

<div>
    {{-- Flash Messages --}}
    @if (session()->has('message'))
    <div
        class="mb-4 sm:mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center text-sm sm:text-base">
        <i class="fas fa-check-circle mr-2 flex-shrink-0"></i>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    @if (session()->has('error'))
    <div
        class="mb-4 sm:mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center text-sm sm:text-base">
        <i class="fas fa-exclamation-circle mr-2 flex-shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Kelola Submission</h2>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Review dan kelola pengajuan surat tugas akhir</p>
        </div>
        {{-- Placeholder tombol future use --}}
        <div class="hidden sm:block"></div>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <div class="flex-1 w-full">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari perusahaan, alamat, atau nama..."
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
                class="w-16 h-16 sm:w-24 sm:h-24 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-tasks text-3xl sm:text-5xl text-amber-500"></i>
            </div>
        </div>
        <h3 class="text-lg sm:text-2xl font-bold text-gray-800 mb-2">Tidak Ada Pengajuan</h3>
        <p class="text-sm sm:text-base text-gray-600 mb-6 max-w-md mx-auto">
            @if($status || $search)
            Tidak ada pengajuan dengan kriteria pencarian tersebut.
            @else
            Belum ada pengajuan surat tugas akhir yang masuk.
            @endif
        </p>
    </div>
    @else

    {{-- Content Wrapper --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Table Title (Desktop Only) --}}
        <div class="hidden md:block p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list-check mr-2 text-blue-500"></i>
                @if($status)
                Daftar Pengajuan - {{ ucfirst($status) }}
                @else
                Semua Pengajuan
                @endif
            </h3>
        </div>

        {{-- DESKTOP VIEW: TABLE --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">
                            No</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/4">
                            Mahasiswa</th>
                        {{-- Kolom Perusahaan & Alamat digabung --}}
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/3">
                            Informasi Instansi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        {{-- Kolom Catatan Dihapus --}}
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
                                    class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-user-graduate text-blue-600"></i>
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
                        {{-- Sel Gabungan Perusahaan & Alamat --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <div class="text-sm font-bold text-gray-800">{{ $submission->company_name }}</div>
                                <div class="text-xs text-gray-500 flex items-start"
                                    title="{{ $submission->address_company }}">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1.5 mt-0.5 flex-shrink-0"></i>
                                    <span class="line-clamp-2">{{ Str::limit($submission->address_company, 80) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-800">{{ $submission->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $submission->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                                class="px-3 py-1.5 inline-flex items-center text-xs leading-5 font-semibold rounded-full {{ $config['bg'] }} {{ $config['text'] }}">
                                <i class="fas {{ $config['icon'] }} mr-1.5"></i>
                                {{ $config['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('submissions.show', $submission->submission_id) }}" wire:navigate
                                    class="p-1.5 rounded-lg text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 transition-all"
                                    title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button wire:click="confirmDeleteSubmission('{{ $submission->submission_id }}')"
                                    class="p-1.5 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition-all"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- MOBILE VIEW: CARDS --}}
        <div class="md:hidden">
            @foreach($submissions as $submission)
            {{-- Menggunakan border-gray-200 untuk pembatas yang lebih terlihat tapi tetap tipis --}}
            <div class="p-4 border-b border-gray-200 last:border-0 hover:bg-gray-50 transition-colors">
                {{-- Row 1: Mahasiswa & Status --}}
                <div class="flex justify-between items-start mb-3">
                    <div class="flex gap-3">
                        <div
                            class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-xs">
                            {{ substr($submission->representative->user->name ?? '?', 0, 2) }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 line-clamp-1">
                                {{ $submission->representative->user->name ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500 font-mono mt-0.5">
                                {{ $submission->representative_nim }}
                                @if($submission->memberStudents->count() > 0)
                                <span class="text-blue-500 ml-1 font-sans">(+{{ $submission->memberStudents->count()
                                    }})</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Status Badge Compact --}}
                    @php
                    $statusConfig = [
                    'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'fa-clock'],
                    'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'fa-check'],
                    'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-times'],
                    'verified' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-700', 'icon' => 'fa-shield-alt'],
                    ];
                    $config = $statusConfig[$submission->status] ?? $statusConfig['pending'];
                    @endphp
                    <span
                        class="flex-shrink-0 px-2 py-1 rounded-md text-xs font-bold {{ $config['bg'] }} {{ $config['text'] }}">
                        <i class="fas {{ $config['icon'] }}"></i>
                        <span class="ml-1 capitalize">{{ $submission->status }}</span>
                    </span>
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

                {{-- Row 3: Tanggal & Aksi (Catatan dihilangkan) --}}
                <div class="flex items-center justify-between pt-2 border-t border-dashed border-gray-100 pl-[3.25rem]">
                    <div class="text-xs text-gray-400">
                        <i class="far fa-calendar mr-1"></i> {{ $submission->created_at->format('d M Y') }}
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('submissions.show', $submission->submission_id) }}" wire:navigate
                            class="text-gray-400 hover:text-emerald-600 transition-colors p-1">
                            <i class="fas fa-eye text-lg"></i>
                        </a>
                        <button wire:click="confirmDeleteSubmission('{{ $submission->submission_id }}')"
                            class="text-gray-400 hover:text-red-600 transition-colors p-1">
                            <i class="fas fa-trash text-lg"></i>
                        </button>
                    </div>
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