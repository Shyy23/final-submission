{{-- Signature Modal untuk Pimpinan --}}
@if($showSignatureModal)
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-pink-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-signature text-purple-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Upload Dokumen Tandatangan</h3>
            </div>
            <button wire:click="$set('showSignatureModal', false)"
                class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form wire:submit="uploadSignedDocument">
            <div class="mb-6">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center text-purple-700 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="font-semibold">Instruksi:</span>
                    </div>
                    <p class="text-sm text-purple-600">
                        1. Download dokumen asli<br>
                        2. Tambahkan QR code dan tandatangan<br>
                        3. Upload kembali dokumen yang telah ditandatangani
                    </p>
                </div>

                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-file-signature text-purple-500 mr-2"></i>
                    Dokumen Tandatangan (PDF, maksimal 10MB)
                </label>
                <input type="file" wire:model="signedFile"
                    class="w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-purple-50 file:to-pink-50 file:text-purple-700 hover:file:from-purple-100 hover:file:to-pink-100 transition-all cursor-pointer"
                    accept=".pdf">
                @error('signedFile')
                <span class="text-red-500 text-xs mt-2 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </span>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                <button type="button"
                    wire:click="$set('showSignatureModal', false)"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm">
                    <i class="fas fa-upload mr-2"></i>
                    Upload & Verifikasi
                </button>
            </div>
        </form>
    </div>
</div>
@endif