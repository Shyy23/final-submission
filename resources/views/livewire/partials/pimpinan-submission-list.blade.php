{{-- Empty State --}}
@if($totalSubmissions === 0)
<div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
    <div class="flex justify-center mb-6">
        <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center">
            <i class="fas fa-signature text-5xl text-purple-500"></i>
        </div>
    </div>
    <h3 class="text-2xl font-bold text-gray-800 mb-3">
        @if($status)
        Tidak Ada Pengajuan
        @else
        Tidak Ada Pengajuan untuk Ditandatangani
        @endif
    </h3>
    <p class="text-gray-600 mb-6 max-w-md mx-auto">
        @if($status)
        Tidak ada pengajuan dengan status yang dipilih.
        @else
        Tidak ada pengajuan yang memerlukan tanda tangan saat ini.
        @endif
    </p>
</div>
@endif

{{-- Submissions Table --}}
@if($totalSubmissions > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-indigo-50">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
            <i class="fas fa-stamp mr-2 text-purple-500"></i>
            @if($status)
            Daftar Pengajuan - {{ ucfirst($status) }}
            @else
            Daftar Pengajuan untuk Ditandatangani
            @endif
        </h3>
        <p class="text-sm text-gray-600 mt-1">
            Verifikasi dan tanda tangani pengajuan surat tugas akhir
        </p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mahasiswa</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Perusahaan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alamat</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Pengajuan</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catatan</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($submissions as $index => $submission)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-800">
                            {{ ($submissions->currentPage() - 1) * $submissions->perPage() + $index + 1 }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-user-graduate text-blue-600"></i>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-800">
                                    {{ $submission->representative->user->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    NIM: {{ $submission->representative_nim }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-800">{{ $submission->company_name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-600 max-w-xs">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                            {{ Str::limit($submission->address_company, 50) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-800">
                            <i class="far fa-calendar text-gray-400 mr-1"></i>
                            {{ $submission->created_at->format('d M Y') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                        $statusConfig = [
                        'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Menunggu', 'icon' => 'fa-hourglass-half'],
                        'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' => 'Disetujui', 'icon' => 'fa-thumbs-up'],
                        'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Ditolak', 'icon' => 'fa-thumbs-down'],
                        'verified' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-700', 'label' => 'Terverifikasi', 'icon' => 'fa-circle-check'],
                        ];
                        $config = $statusConfig[$submission->status] ?? $statusConfig['pending'];
                        @endphp
                        <span class="px-3 py-1.5 inline-flex items-center text-xs leading-5 font-semibold rounded-full {{ $config['bg'] }} {{ $config['text'] }}">
                            <i class="fas {{ $config['icon'] }} mr-1.5"></i>
                            {{ $config['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($submission->note)
                        <div class="text-sm text-gray-600 max-w-xs">
                            {{ Str::limit($submission->note, 30) }}
                        </div>
                        @else
                        <span class="text-xs text-gray-400 italic">Tidak ada catatan</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('submissions.show', $submission->submission_id) }}"
                                class="inline-flex items-center px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium rounded-lg transition-colors duration-200 shadow-sm">
                                <i class="fas fa-eye mr-1.5"></i>
                                Detail
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
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Menampilkan <span class="font-semibold text-gray-800">{{ $submissions->firstItem() }}</span>
                sampai <span class="font-semibold text-gray-800">{{ $submissions->lastItem() }}</span>
                dari <span class="font-semibold text-gray-800">{{ $submissions->total() }}</span> pengajuan
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Pagination Links --}}
@if($submissions->hasPages())
<div class="mt-6">
    {{ $submissions->links() }}
</div>
@endif
@endif