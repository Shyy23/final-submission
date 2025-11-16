{{-- Upload Modal untuk Mahasiswa --}}
@if($showUploadModal)
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-cloud-upload-alt text-blue-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Upload File Baru</h3>
            </div>
            <button wire:click="$set('showUploadModal', false)"
                class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form wire:submit="uploadNewFile">
            <div class="mb-6">
                <label class=" text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                    File Baru (PDF, maksimal 10MB)
                </label>
                <input type="file" wire:model="newFile"
                    class="w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-blue-50 file:to-indigo-50 file:text-blue-700 hover:file:from-blue-100 hover:file:to-indigo-100 transition-all cursor-pointer"
                    accept=".pdf">
                @error('newFile')
                <span class="text-red-500 text-xs mt-2 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </span>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                <button type="button"
                    wire:click="$set('showUploadModal', false)"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm">
                    <i class="fas fa-upload mr-2"></i>
                    Upload File
                </button>
            </div>
        </form>
    </div>
</div>
@endif