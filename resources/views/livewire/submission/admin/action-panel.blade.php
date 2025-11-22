<div> {{-- ROOT ELEMENT PEMBUNGKUS (WAJIB) --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Admin</h3>

        {{-- KASUS 1: STATUS PENDING --}}
        @if($submission->status === 'pending')
        <div class="space-y-3">
            <button wire:click="openActionModal('approve')" wire:loading.attr="disabled"
                class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-lg shadow-sm hover:from-emerald-600 hover:to-teal-600 transition-all">
                <i class="fas fa-check-circle mr-2"></i> Setujui & Buat Dokumen
            </button>

            <button wire:click="openRejectModal" wire:loading.attr="disabled"
                class="w-full inline-flex items-center justify-center px-4 py-3 bg-white border border-red-200 text-red-600 hover:bg-red-50 font-semibold rounded-lg shadow-sm transition-all">
                <i class="fas fa-reply mr-2"></i> Kembalikan untuk Revisi
            </button>
        </div>
        @endif

        {{-- KASUS 2: STATUS APPROVED (Draft Dokumen Ada) --}}
        @if($submission->status === 'approved')
        <div class="space-y-4">

            {{-- Info Box --}}
            <div class="p-3 bg-blue-50 text-blue-700 text-sm rounded-lg flex items-start">
                <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                <span>Dokumen Draft telah dibuat. Silakan preview sebelum dikirim.</span>
            </div>

            {{-- TOMBOL BARU: PREVIEW DRAFT --}}
            @if($submission->document_path)
            <button wire:click="downloadDocument" wire:loading.attr="disabled"
                class="w-full px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg shadow-sm hover:bg-gray-50 transition-all flex items-center justify-center">
                <span wire:loading.remove wire:target="downloadDocument">
                    <i class="fas fa-eye mr-2 text-gray-500"></i> Preview Draft PDF
                </span>
                <span wire:loading wire:target="downloadDocument">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Mendownload...
                </span>
            </button>
            @endif

            {{-- Tombol Kirim ke Pimpinan --}}
            @if(!$submission->sent_to_leader)
            <button wire:click="sendToLeader" wire:loading.attr="disabled"
                wire:loading.class="opacity-75 cursor-not-allowed"
                class="w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md transition-all flex items-center justify-center">

                {{-- State Normal --}}
                <span wire:loading.remove wire:target="sendToLeader">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim ke Pimpinan
                </span>

                {{-- State Loading --}}
                <span wire:loading wire:target="sendToLeader">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...
                </span>
            </button>
            @else
            <div
                class="w-full px-4 py-3 bg-gray-100 text-gray-500 text-center rounded-lg border border-gray-200 font-medium">
                <i class="fas fa-clock mr-2"></i> Menunggu TTD Pimpinan
            </div>
            @endif

            {{-- Bagian Edit / Reset --}}
            <div class="border-t pt-4 mt-2">
                <h4 class="text-xs font-uppercase text-gray-400 font-bold mb-2">KOREKSI DOKUMEN</h4>
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="openEditModal"
                        class="flex items-center justify-center px-3 py-2 bg-amber-50 text-amber-700 border border-amber-200 rounded hover:bg-amber-100 text-sm transition-colors">
                        <i class="fas fa-edit mr-2"></i> Edit Data
                    </button>

                    <button wire:click="deleteDocument"
                        onclick="confirm('Apakah Anda yakin? Dokumen akan dihapus dan status kembali ke Pending.') || event.stopImmediatePropagation()"
                        class="flex items-center justify-center px-3 py-2 bg-red-50 text-red-700 border border-red-200 rounded hover:bg-red-100 text-sm transition-colors">
                        <i class="fas fa-trash-alt mr-2"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- KASUS 3: STATUS VERIFIED --}}
        @if($submission->status === 'verified')
        <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-lg text-emerald-700 text-center">
            <i class="fas fa-check-double text-2xl mb-2 block"></i>
            <span class="font-semibold">Selesai</span>
            <p class="text-sm mt-1">Dokumen sudah ditandatangani dan terverifikasi.</p>
        </div>
        @endif
    </div>

    {{-- MODAL EDIT DATA (Sekarang di dalam Root Element) --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="$set('showEditModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Data Dokumen</h3>

                    <form wire:submit.prevent="updateAndRegenerate" class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tujuan Surat (Nama
                                Perusahaan/Instansi)</label>
                            <input type="text" wire:model="editData.company_name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('editData.company_name') <span class="text-red-500 text-xs mt-1">{{ $message
                                }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                <input type="date" wire:model="editData.start_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('editData.start_date') <span class="text-red-500 text-xs mt-1">{{ $message
                                    }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Durasi (Hari)</label>
                                <input type="number" wire:model="editData.duration_days"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('editData.duration_days') <span class="text-red-500 text-xs mt-1">{{ $message
                                    }}</span> @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="updateAndRegenerate" type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan & Regenerate PDF
                    </button>
                    <button wire:click="$set('showEditModal', false)" type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal action (Sekarang di dalam Root Element) --}}
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

            <form wire:submit.prevent="processAdminAction">
                <div class="mb-6">
                    <label class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
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

</div>