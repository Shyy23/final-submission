{{-- FIX: Padding responsif (p-4 di mobile, p-6 di desktop) --}}
<div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4 flex items-center">
        {{-- FIX: Ukuran ikon responsif --}}
        <div
            class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-users text-blue-600 text-sm sm:text-base"></i>
        </div>
        <span>Daftar Anggota Kelompok</span>
    </h3>

    <div class="space-y-3">
        @foreach($submission->memberStudents as $member)
        {{-- FIX: Layout flex-col di mobile (tumpuk), flex-row di desktop (sebelahan) --}}
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between p-3 sm:p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg border border-gray-100 hover:shadow-sm transition-shadow duration-200 gap-3 sm:gap-0">

            {{-- Bagian Kiri: Avatar & Info --}}
            <div class="flex items-center min-w-0">
                {{-- Avatar Responsif --}}
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center mr-3 shadow-md flex-shrink-0">
                    <span class="text-white font-bold text-sm sm:text-base">{{ strtoupper(substr($member->user->name, 0,
                        1)) }}</span>
                </div>

                {{-- Info dengan Truncate --}}
                <div class="min-w-0 flex-1 pr-2">
                    <div class="font-semibold text-gray-800 text-sm sm:text-base truncate">{{ $member->user->name }}
                    </div>
                    <div class="text-xs sm:text-sm text-gray-500 flex items-center mt-0.5">
                        <i class="fas fa-id-card text-gray-400 mr-1"></i>
                        {{ $member->nim }}
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan/Bawah: Badge Perwakilan --}}
            @if($member->nim === $submission->representative_nim)
            <span
                class="self-start sm:self-center inline-flex items-center px-2.5 py-1 sm:px-3 sm:py-1.5 bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700 text-xs sm:text-sm font-semibold rounded-full border border-emerald-200 whitespace-nowrap">
                <i class="fas fa-crown mr-1.5 text-xs sm:text-sm"></i>
                Perwakilan
            </span>
            @endif
        </div>
        @endforeach
    </div>
</div>