<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
        Informasi Tambahan
    </h3>
    <div class="space-y-3 text-sm">
        {{-- Tanggal Pengajuan --}}
        <div class="flex justify-between items-center py-3 border-b border-gray-100">
            <span class="text-gray-600 flex items-center">
                <i class="far fa-calendar-plus text-gray-400 mr-2"></i>
                Tanggal Pengajuan
            </span>
            <span class="text-gray-800 font-medium">{{ $submission->created_at->format('d M Y H:i') }}</span>
        </div>

        {{-- Disetujui Oleh --}}
        @if($submission->admin)
        <div class="flex justify-between items-center py-3 border-b border-gray-100">
            <span class="text-gray-600 flex items-center">
                <i class="fas fa-user-shield text-gray-400 mr-2"></i>
                Disetujui Oleh
            </span>
            <span class="text-gray-800 font-medium">{{ $submission->admin->name }}</span>
        </div>
        @endif

        {{-- Ditandatangani Oleh --}}
        @if($submission->leader)
        <div class="flex justify-between items-center py-3 border-b border-gray-100">
            <span class="text-gray-600 flex items-center">
                <i class="fas fa-user-tie text-gray-400 mr-2"></i>
                Ditandatangani Oleh
            </span>
            <span class="text-gray-800 font-medium">{{ $submission->leader->user->name }}</span>
        </div>
        @endif

        {{-- Terakhir Diupdate --}}
        <div class="flex justify-between items-center py-3">
            <span class="text-gray-600 flex items-center">
                <i class="far fa-clock text-gray-400 mr-2"></i>
                Terakhir Diupdate
            </span>
            <span class="text-gray-800 font-medium">{{ $submission->updated_at->format('d M Y H:i') }}</span>
        </div>
    </div>


</div>