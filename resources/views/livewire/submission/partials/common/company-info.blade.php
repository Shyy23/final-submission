<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <div
            class="w-10 h-10 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-building text-emerald-600"></i>
        </div>
        <span>Informasi Perusahaan</span>
    </h3>
    <div class="space-y-4">
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
            <label class="text-sm font-semibold text-gray-600 mb-2 flex items-center">
                <i class="fas fa-building-columns text-gray-400 mr-2"></i>
                Nama Perusahaan
            </label>
            <p class="text-gray-800 font-medium">{{ $submission->company_name }}</p>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
            <label class="text-sm font-semibold text-gray-600 mb-2 flex items-center">
                <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                Alamat Perusahaan
            </label>
            <p class="text-gray-800">{{ $submission->address_company }}</p>
        </div>
        <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
            <label class="text-sm font-semibold text-blue-700 mb-2 flex items-center">
                <i class="fas fa-sticky-note text-blue-500 mr-2"></i>
                Catatan
            </label>
            <p class="text-gray-800">
                {{ $submission->note ?: 'Tidak ada catatan' }}
            </p>
        </div>
    </div>
</div>