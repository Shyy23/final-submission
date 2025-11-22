@role('mahasiswa')

<div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
        <i class="fas fa-stream mr-3 text-blue-500"></i>
        Timeline Status Pengajuan
    </h3>

    @php
    $currentStatus = $submission->status;

    // PERBAIKAN: Definisikan CLASS WARNA secara eksplisit (bukan 'amber' tapi 'bg-amber-400')
    // agar Tailwind JIT mendeteksinya.
    $timelineSteps = [
    'pending' => [
    'icon' => 'fa-hourglass-half',
    'bg_color' => 'bg-amber-400', // Warna Garis/Bg Icon Aktif
    'gradient_from' => 'from-amber-400', // Gradient Icon
    'gradient_to' => 'to-amber-500',
    'text_color' => 'text-amber-700', // Warna Teks
    'ring_color' => 'ring-amber-100', // Ring saat aktif
    'label' => 'Menunggu Persetujuan'
    ],
    ];

    if ($currentStatus == 'rejected') {
    $timelineSteps['rejected'] = [
    'icon' => 'fa-times-circle',
    'bg_color' => 'bg-red-400',
    'gradient_from' => 'from-red-400',
    'gradient_to' => 'to-red-500',
    'text_color' => 'text-red-700',
    'ring_color' => 'ring-red-100',
    'label' => 'Ditolak'
    ];
    } else {
    $timelineSteps['approved'] = [
    'icon' => 'fa-thumbs-up',
    'bg_color' => 'bg-blue-400',
    'gradient_from' => 'from-blue-400',
    'gradient_to' => 'to-blue-500',
    'text_color' => 'text-blue-700',
    'ring_color' => 'ring-blue-100',
    'label' => 'Disetujui Admin'
    ];
    }

    if ($currentStatus != 'rejected') {
    $timelineSteps['verified'] = [
    'icon' => 'fa-circle-check',
    'bg_color' => 'bg-emerald-400',
    'gradient_from' => 'from-emerald-400',
    'gradient_to' => 'to-emerald-500',
    'text_color' => 'text-emerald-700',
    'ring_color' => 'ring-emerald-100',
    'label' => 'Terverifikasi & Ditandatangani'
    ];
    }

    $stepKeys = array_keys($timelineSteps);
    $currentStepIndex = array_search($currentStatus, $stepKeys);
    $stepCount = count($timelineSteps);
    @endphp

    <div class="relative lg:grid {{ " lg:grid-cols-{$stepCount}" }}">

        @foreach($timelineSteps as $status => $step)
        @php
        $stepIndex = array_search($status, $stepKeys);
        $isCompleted = $stepIndex <= $currentStepIndex; $isCurrent=$status===$currentStatus; @endphp <div
            class="relative flex items-start mb-8 last:mb-0 lg:flex-col lg:items-center lg:mb-0">

            {{-- Connector Line (Desktop) --}}
            @if(!$loop->first)
            <div class="hidden lg:block absolute top-6 left-0 w-full h-0.5 z-0 {{ $isCompleted ? $step['bg_color'] : 'bg-gray-200' }}"
                style="transform: translateX(-50%);">
            </div>
            @endif

            {{-- Icon Container --}}
            <div class="flex-shrink-0 relative z-10 bg-white px-2 lg:px-0">
                <div class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300
                    {{ $isCompleted ? " bg-gradient-to-br {$step['gradient_from']} {$step['gradient_to']} text-white
                    shadow-md" : 'bg-gray-200 text-gray-400' }} {{ $isCurrent ? "ring-4 {$step['ring_color']} scale-110"
                    : '' }}">
                    <i class="fas {{ $step['icon'] }} text-lg"></i>
                </div>

                {{-- Connector Line (Mobile) --}}
                @if(!$loop->last)
                <div class="absolute top-12 left-1/2 transform -translate-x-1/2 w-0.5 h-full lg:hidden
                    {{ $stepIndex < $currentStepIndex ? " bg-gradient-to-b {$step['gradient_from']} to-gray-300"
                    : 'bg-gray-200' }}">
                </div>
                @endif
            </div>

            {{-- Text Container --}}
            <div class="ml-4 pt-2 lg:ml-0 lg:pt-4 lg:text-center lg:px-2">
                <p class="text-sm font-semibold {{ $isCompleted ? $step['text_color'] : 'text-gray-500' }}">
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