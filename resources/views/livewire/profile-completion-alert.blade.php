<div>
    @if ($showAlert)
    <div class="mb-6 bg-amber-50 border-l-4 border-amber-400 p-4 rounded-lg shadow-sm animate-pulse">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-amber-400 text-lg mt-0.5"></i>
            </div>
            <div class="ml-3 flex-1">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-sm font-semibold text-amber-800">
                            Perhatian: Data Profil Belum Lengkap
                        </h3>
                        <div class="mt-1 text-sm text-amber-700">
                            <p>{{ $alertMessage }}</p>
                            <p class="mt-1 text-xs">
                                <i class="fas fa-info-circle mr-1"></i>
                                Anda tidak dapat mengakses fitur sistem hingga melengkapi data profil.
                            </p>
                        </div>
                    </div>
                    <button wire:click="dismissAlert"
                        class="ml-4 flex-shrink-0 text-amber-600 hover:text-amber-800 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mt-3 flex space-x-3">
                    <button wire:click="goToProfile"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                        <i class="fas fa-user-edit mr-1.5"></i>
                        Lengkapi Profil Sekarang
                    </button>
                    <button wire:click="dismissAlert"
                        class="inline-flex items-center px-3 py-1.5 border border-amber-300 text-xs font-medium rounded-lg text-amber-700 bg-white hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                        <i class="fas fa-clock mr-1.5"></i>
                        Nanti Saja
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>