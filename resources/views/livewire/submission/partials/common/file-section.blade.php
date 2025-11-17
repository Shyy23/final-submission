{{-- File Section: Hanya tampil jika dokumen sudah digenerate oleh Admin --}}
@if($submission->document_path)
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <div
            class="w-10 h-10 bg-gradient-to-br from-purple-100 to-pink-100 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-file-contract text-purple-600"></i>
        </div>
        <span>Dokumen Surat Tugas</span>
    </h3>

    <div
        class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-purple-50 rounded-lg border border-gray-200">
        <div class="flex items-center">
            <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
            </div>
            <div>
                <div class="font-semibold text-gray-800">
                    @if($submission->status === 'verified')
                    Surat Tugas Akhir (Terverifikasi)
                    @else
                    Draft Surat Tugas (Menunggu TTD)
                    @endif
                </div>
                <div class="text-sm text-gray-500 flex items-center mt-1">
                    <i class="far fa-calendar text-gray-400 mr-1"></i>
                    Update Terakhir: {{ $submission->updated_at->format('d M Y H:i') }}
                </div>
            </div>
        </div>

        {{-- Tombol Download Umum --}}
        <button wire:click="downloadDocument"
            class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm">
            <i class="fas fa-eye mr-2"></i>
            Preview / Download
        </button>
    </div>
</div>
@endif