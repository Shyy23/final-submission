@if($submission->document_path && (!Auth::user()->hasRole('mahasiswa') || $submission->status === 'verified'))

{{-- FIX: Padding responsif (p-4 di mobile, p-6 di desktop) --}}
<div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <div
            class="w-10 h-10 bg-gradient-to-br from-purple-100 to-pink-100 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-file-contract text-purple-600"></i>
        </div>
        <span>Dokumen Surat Tugas</span>
    </h3>

    {{-- FIX: Layout Flex Column di Mobile, Row di Desktop (sm:flex-row) --}}
    <div class="bg-gradient-to-r from-gray-50 to-purple-50 rounded-lg border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">

            {{-- Bagian Kiri: Ikon & Teks --}}
            <div class="flex items-center">
                {{-- Ikon PDF (Shrink-0 agar tidak gepeng) --}}
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 bg-red-100 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                    <i class="fas fa-file-pdf text-red-500 text-xl sm:text-2xl"></i>
                </div>

                {{-- Teks Detail --}}
                <div class="min-w-0">
                    <div class="font-semibold text-gray-800 text-sm sm:text-base truncate">
                        @if($submission->status === 'verified')
                        Surat Tugas Akhir (Terverifikasi)
                        @else
                        Draft Surat Tugas (Menunggu TTD)
                        @endif
                    </div>
                    <div class="text-xs sm:text-sm text-gray-500 flex items-center mt-1">
                        <i class="far fa-calendar text-gray-400 mr-1"></i>
                        Update: {{ $submission->updated_at->format('d M Y H:i') }}
                    </div>
                </div>
            </div>

            <button wire:click="downloadDocument"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm">
                <i class="fas fa-eye mr-2"></i>
                Preview / Download
            </button>
        </div>
    </div>
</div>
@endif