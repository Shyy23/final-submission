@if($submission->qr_url)

<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-qrcode mr-2 text-purple-600"></i>
        QR Code Verifikasi
    </h3>
    <div class="text-center">
        <div class="bg-white p-4 rounded-lg border-2 border-dashed border-gray-300 inline-block mb-3">
            {{-- Menggunakan asset() untuk QR Code karena disimpan di public/qr-code --}}
            <img src="{{ asset('qr-code/' . $submission->qr_url) }}" alt="QR Code Verifikasi" class="w-48 h-48 mx-auto">
        </div>
        <p class="text-sm text-gray-600 mb-3 flex items-center justify-center">
            <i class="fas fa-mobile-alt text-gray-400 mr-2"></i>
            Scan untuk verifikasi keaslian dokumen
        </p>
        <button wire:click="downloadQRCode"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm">
            <i class="fas fa-download mr-2"></i>
            Download QR Code
        </button>
    </div>
</div>
@endif