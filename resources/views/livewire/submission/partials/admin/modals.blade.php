{{-- Action Modal untuk Admin --}}
@if($showActionModal)
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-{{ $actionType === 'approve' ? 'emerald' : 'red' }}-100 to-{{ $actionType === 'approve' ? 'teal' : 'pink' }}-100 rounded-lg flex items-center justify-center mr-3">
                    <i
                        class="fas {{ $actionType === 'approve' ? 'fa-circle-check' : 'fa-times' }} text-{{ $actionType === 'approve' ? 'emerald' : 'red' }}-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">
                    {{ $actionType === 'approve' ? 'Setujui' : 'Tolak' }} Pengajuan
                </h3>
            </div>
            <button wire:click="$set('showActionModal', false)"
                class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form wire:submit="processAdminAction">
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-comment-alt text-blue-500 mr-2"></i>
                    Feedback
                </label>
                <textarea wire:model="feedback" rows="4"
                    class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                    placeholder="{{ $actionType === 'approve' ? 'Pengajuan diterima. Menunggu untuk ditandatangani...' : 'Berikan alasan penolakan...' }}"></textarea>
                @error('feedback')
                <span class="text-red-500 text-xs mt-2 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </span>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" wire:click="$set('showActionModal', false)"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-{{ $actionType === 'approve' ? 'emerald' : 'red' }}-500 to-{{ $actionType === 'approve' ? 'teal' : 'pink' }}-500 hover:from-{{ $actionType === 'approve' ? 'emerald' : 'red' }}-600 hover:to-{{ $actionType === 'approve' ? 'teal' : 'pink' }}-600 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm">
                    <i class="fas {{ $actionType === 'approve' ? 'fa-circle-check' : 'fa-times' }} mr-2"></i>
                    {{ $actionType === 'approve' ? 'Setujui' : 'Tolak' }} Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endif