<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Pimpinan</h3>

    @if($submission->status === 'approved' && $submission->sent_to_leader)
    <div class="space-y-4">
        <div class="text-sm text-gray-600">
            Admin telah mengirimkan surat tugas untuk Anda verifikasi. Silakan review dokumen di sebelah kiri, lalu klik
            tombol di bawah untuk menandatangani secara digital.
        </div>

        {{-- Tombol Verifikasi Digital --}}
        <button wire:click="openVerifyModal"
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold rounded-xl shadow-lg transform transition hover:-translate-y-0.5">
            <i class="fas fa-file-signature mr-2"></i>
            Verifikasi & Tanda Tangan Digital
        </button>
    </div>
    @elseif($submission->status === 'verified')
    <div class="p-4 bg-green-50 border border-green-100 rounded-lg flex flex-col items-center text-center">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
            <i class="fas fa-check text-green-600 text-xl"></i>
        </div>
        <h4 class="font-semibold text-green-800">Sudah Ditandatangani</h4>
        <p class="text-sm text-green-600 mt-1">Dokumen telah diverifikasi pada {{ $submission->updated_at->format('d M Y
            H:i') }}</p>
    </div>
    @else
    <div class="text-gray-400 italic text-sm text-center py-4">
        Belum ada aksi yang diperlukan. Menunggu Admin mengirim dokumen.
    </div>
    @endif
</div>

{{-- Modal Konfirmasi Pimpinan --}}
@if($showConfirmModal)
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            wire:click="$set('showConfirmModal', false)"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-signature text-purple-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Konfirmasi Tanda Tangan
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Dengan melanjutkan, sistem akan menyematkan <strong>QR Code Verifikasi</strong> dan nama
                                Anda pada dokumen ini sebagai tanda persetujuan yang sah. Dokumen tidak dapat diubah
                                setelah ini.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="verifyAndSign" type="button"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Ya, Tanda Tangani
                </button>
                <button wire:click="$set('showConfirmModal', false)" type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endif