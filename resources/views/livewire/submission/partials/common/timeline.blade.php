{{-- Gunakan directive @role dari Spatie --}}
@role('mahasiswa')
<div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
        <i class="fas fa-stream mr-3 text-blue-500"></i>
        Timeline Status Pengajuan
    </h3>

    @php
    $currentStatus = $submission->status;

    // 1. Tentukan langkah-langkah timeline secara dinamis
    $timelineSteps = [
    'pending' => ['icon' => 'fa-hourglass-half', 'color' => 'amber', 'label' => 'Menunggu Persetujuan'],
    ];

    // 2. Langkah kondisional: Hanya tampilkan 'rejected' ATAU 'approved'
    if ($currentStatus == 'rejected') {
    // Jika status 'rejected', tampilkan langkah 'rejected'
    $timelineSteps['rejected'] = ['icon' => 'fa-times-circle', 'color' => 'red', 'label' => 'Ditolak'];
    } else {
    // Jika status 'pending', 'approved', atau 'verified', tampilkan 'approved' sebagai jalur normal
    $timelineSteps['approved'] = ['icon' => 'fa-thumbs-up', 'color' => 'blue', 'label' => 'Disetujui Admin'];
    }

    // 3. Langkah final: Hanya tampilkan 'verified' jika jalurnya *bukan* 'rejected'
    if ($currentStatus != 'rejected') {
    $timelineSteps['verified'] = ['icon' => 'fa-circle-check', 'color' => 'emerald', 'label' => 'Terverifikasi &
    Ditandatangani'];
    }

    $stepKeys = array_keys($timelineSteps);
    $currentStepIndex = array_search($currentStatus, $stepKeys);
    $stepCount = count($timelineSteps);
    @endphp

    <div class="relative lg:grid {{ " lg:grid-cols-{$stepCount}" }} lg:gap-8">
        @foreach($timelineSteps as $status => $step)
        @php
        $stepIndex = array_search($status, $stepKeys);
        $isCompleted = $stepIndex <= $currentStepIndex; $isCurrent=$status===$currentStatus; $color=$step['color'];
            @endphp <div class="relative flex items-start mb-8 last:mb-0 lg:flex-col lg:items-center lg:mb-0">
            {{-- Connector Line (Desktop) --}}
            @if(!$loop->first)
            <div class="hidden lg:block absolute top-6 left-0 w-full h-0.5 z-0 {{ $isCompleted ? " bg-{$color}-400"
                : 'bg-gray-200' }}" style="transform: translateX(-50%);">
            </div>
            @endif

            {{-- Icon Container --}}
            <div class="flex-shrink-0 relative z-10">
                <div class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300
                                    {{ $isCompleted ? " bg-gradient-to-br from-{$color}-400 to-{$color}-500 text-white
                    shadow-md" : 'bg-gray-200 text-gray-400' }} {{ $isCurrent ? "ring-4 ring-{$color}-100 scale-110"
                    : '' }}">
                    <i class="fas {{ $step['icon'] }} text-lg"></i>
                </div>

                {{-- Connector Line (Mobile) --}}
                @if(!$loop->last)
                <div class="absolute top-12 left-1/2 transform -translate-x-1/2 w-0.5 h-full lg:hidden
                                        {{ $stepIndex < $currentStepIndex ? " bg-gradient-to-b from-{$color}-500
                    to-{$color}-300" : 'bg-gray-200' }}">
                </div>
                @endif
            </div>

            {{-- Text Container --}}
            <div class="ml-4 pt-2 lg:ml-0 lg:pt-4 lg:text-center">
                <p class="text-sm font-semibold {{ $isCompleted ? " text-{$color}-700" : 'text-gray-500' }}">
                    {{ $step['label'] }}
                </p>

                @if($isCurrent)
                <p class="text-xs text-gray-500 mt-1 flex items-center lg:justify-center">
                    <i class="far fa-clock mr-1"></i>
                    @if($submission->updated_at && $status != 'pending')
                    {{ $submission->updated_at->format('d M Y H:i') }}
                    @else
                    Menunggu proses
                    @endif
                </p>
                @endif
            </div>
    </div>
    @endforeach
</div>
</div>
@endrole