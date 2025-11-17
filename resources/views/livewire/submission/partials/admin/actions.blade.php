<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Admin</h3>

    {{-- KASUS 1: STATUS PENDING --}}
    @if($submission->status === 'pending')
    <div class="space-y-3">
        <button wire:click="openActionModal('approve')"
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-lg shadow-sm">
            <i class="fas fa-check-circle mr-2"></i> Setujui & Buat Dokumen
        </button>

        <button wire:click="openRejectModal"
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-white border border-red-200 text-red-600 hover:bg-red-50 font-semibold rounded-lg shadow-sm">
            <i class="fas fa-reply mr-2"></i> Kembalikan untuk Revisi
        </button>
    </div>
    @endif

    {{-- KASUS 2: STATUS APPROVED (Draft Dokumen Ada) --}}
    @if($submission->status === 'approved')
    <div class="space-y-4">
        <div class="p-3 bg-blue-50 text-blue-700 text-sm rounded-lg">
            <i class="fas fa-info-circle mr-1"></i> Dokumen Draft telah dibuat.
        </div>

        {{-- Tombol Kirim ke Pimpinan --}}
        @if(!$submission->sent_to_leader)
        <button wire:click="sendToLeader"
            class="w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md">
            <i class="fas fa-paper-plane mr-2"></i> Kirim ke Pimpinan
        </button>
        @else
        <div class="w-full px-4 py-2 bg-gray-100 text-gray-500 text-center rounded border">
            <i class="fas fa-clock mr-1"></i> Menunggu TTD Pimpinan
        </div>
        @endif

        <div class="border-t pt-4 mt-2">
            <h4 class="text-xs font-uppercase text-gray-400 font-bold mb-2">KOREKSI DOKUMEN</h4>

            <div class="grid grid-cols-2 gap-2">
                {{-- Tombol Edit --}}
                <button wire:click="openEditModal"
                    class="flex items-center justify-center px-3 py-2 bg-amber-50 text-amber-700 border border-amber-200 rounded hover:bg-amber-100 text-sm">
                    <i class="fas fa-edit mr-2"></i> Edit Data
                </button>

                {{-- Tombol Hapus / Reset --}}
                <button wire:click="deleteDocument"
                    onclick="confirm('Apakah Anda yakin? Dokumen akan dihapus dan status kembali ke Pending.') || event.stopImmediatePropagation()"
                    class="flex items-center justify-center px-3 py-2 bg-red-50 text-red-700 border border-red-200 rounded hover:bg-red-100 text-sm">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus & Reset
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- MODAL EDIT DATA --}}
@if($showEditModal)
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="$set('showEditModal', false)"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Data Dokumen</h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tujuan Surat (Nama
                            Perusahaan/Instansi)</label>
                        <input type="text" wire:model="editData.company_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" wire:model="editData.start_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Durasi (Hari)</label>
                            <input type="number" wire:model="editData.duration_days"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="updateAndRegenerate"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                    Simpan & Regenerate PDF
                </button>
                <button wire:click="$set('showEditModal', false)"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endif