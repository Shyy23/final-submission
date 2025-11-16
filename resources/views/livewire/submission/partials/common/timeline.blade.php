<div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
        <i class="fas fa-stream mr-3 text-blue-500"></i>
        Timeline Status Pengajuan
    </h3>

    <div class="relative">
        @php
        $timelineSteps = [
        'pending' => ['icon' => 'fa-hourglass-half', 'color' => 'amber', 'label' => 'Menunggu Persetujuan'],
        'approved' => ['icon' => 'fa-thumbs-up', 'color' => 'emerald', 'label' => 'Disetujui Admin'],
        'rejected' => ['icon' => 'fa-times-circle', 'color' => 'red', 'label' => 'Ditolak'],
        'verified' => ['icon' => 'fa-circle-check', 'color' => 'emerald', 'label' => 'Terverifikasi & Ditandatangani'],
        ];

        $currentStatus = $submission->status;
        $stepKeys = array_keys($timelineSteps);
        $currentStepIndex = array_search($currentStatus, $stepKeys);
        @endphp

        @foreach($timelineSteps as $status => $step)
        @php
        $stepIndex = array_search($status, $stepKeys);
        $isCompleted = $stepIndex <= $currentStepIndex;
            $isCurrent=$status===$currentStatus;
            $color=$step['color'];
            @endphp

            <div class="flex items-start mb-8 last:mb-0">
            <div class="flex-shrink-0 relative">
                <div class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300
                            {{ $isCompleted ? "bg-gradient-to-br from-{$color}-400 to-{$color}-500 text-white shadow-md" : 'bg-gray-200 text-gray-400' }}
                            {{ $isCurrent ? "ring-4 ring-{$color}-100 scale-110" : '' }}">
                    <i class="fas {{ $step['icon'] }} text-lg"></i>
                </div>

                {{-- Connector line --}}
                @if(!$loop->last)
                <div class="absolute top-12 left-1/2 transform -translate-x-1/2 w-0.5 h-8 
                            {{ $stepIndex < $currentStepIndex ? "bg-gradient-to-b from-{$color}-500 to-{$color}-300" : 'bg-gray-200' }}"></div>
                @endif
            </div>

            <div class="ml-4 pt-2">
                <p class="text-sm font-semibold {{ $isCompleted ? "text-{$color}-700" : 'text-gray-500' }}">
                    {{ $step['label'] }}
                </p>
                @if($isCurrent)
                <p class="text-xs text-gray-500 mt-1 flex items-center">
                    <i class="far fa-clock mr-1"></i>
                    @if($status === 'pending')
                    Menunggu persetujuan admin
                    @elseif($status === 'approved')
                    Disetujui pada {{ $submission->updated_at->format('d M Y H:i') }}
                    @elseif($status === 'rejected')
                    Ditolak pada {{ $submission->updated_at->format('d M Y H:i') }}
                    @elseif($status === 'verified')
                    Terverifikasi pada {{ $submission->updated_at->format('d M Y H:i') }}
                    @endif
                </p>
                @endif
            </div>
    </div>
    @endforeach
</div>
</div>