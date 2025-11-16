<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-pink-100 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-file-upload text-purple-600"></i>
        </div>
        <span>File Pengajuan</span>
    </h3>

    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-purple-50 rounded-lg border border-gray-200">
        <div class="flex items-center">
            <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
            </div>
            <div>
                <div class="font-semibold text-gray-800">
                    @if($submission->status === 'verified')
                    Dokumen Terverifikasi
                    @else
                    File Surat Pengajuan
                    @endif
                </div>
                <div class="text-sm text-gray-500 flex items-center mt-1">
                    <i class="far fa-calendar text-gray-400 mr-1"></i>
                    @if($submission->status === 'verified')
                    Ditandatangani: {{ $submission->updated_at->format('d M Y H:i') }}
                    @else
                    Diupload: {{ $submission->created_at->format('d M Y H:i') }}
                    @endif
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <button wire:click="downloadFile"
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm">
                <i class="fas fa-download mr-2"></i>
                Download
            </button>

            {{-- Upload Ulang untuk Mahasiswa jika rejected --}}
            @if($submission->status === 'rejected' && Auth::user()->hasRole('mahasiswa') && Auth::user()->student->nim === $submission->representative_nim)
            <button wire:click="$set('showUploadModal', true)"
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm">
                <i class="fas fa-upload mr-2"></i>
                Upload Ulang
            </button>
            @endif
        </div>
    </div>

    @if($submission->status === 'rejected')
    <div class="mt-4 p-4 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl mt-0.5 mr-3"></i>
            <div>
                <div class="font-semibold text-red-800 mb-1">Pengajuan Ditolak</div>
                <div class="text-red-700 text-sm">
                    {{ $submission->feedback ?: 'Pengajuan Anda ditolak. Silakan upload ulang file dengan perbaikan yang diperlukan.' }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>