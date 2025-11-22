@if($submission->feedback && in_array($submission->status, ['approved', 'rejected', 'verified']))

<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <div
            class="w-10 h-10 bg-gradient-to-br from-teal-100 to-cyan-100 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-comment-alt text-teal-600"></i>
        </div>
        <span>Feedback</span>
    </h3>
    <div class="p-4 bg-gradient-to-r from-teal-50 to-cyan-50 border border-teal-200 rounded-lg">
        <p class="text-teal-900">{{ $submission->feedback }}</p>
        @if($submission->status === 'verified' && $submission->leader)
        <div class="flex items-center font-medium">
            <div class="w-6 h-6 rounded-full bg-teal-200 flex items-center justify-center mr-2">
                <i class="fas fa-user-tie text-teal-700 text-xs"></i>
            </div>
            <span>
                Oleh: <span class="font-bold">{{ $submission->leader->user->name ?? 'Pimpinan' }}</span> (Pimpinan)
            </span>
        </div>

        {{-- KONDISI 2: Jika Status APPROVED/REJECTED -> Biasanya dari Admin --}}
        @elseif($submission->admin)
        <div class="flex items-center font-medium">
            <div class="w-6 h-6 rounded-full bg-teal-200 flex items-center justify-center mr-2">
                <i class="fas fa-user-shield text-teal-700 text-xs"></i>
            </div>
            <span>
                Oleh: <span class="font-bold">{{ $submission->admin->name }}</span> (Admin)
            </span>
        </div>
        @endif
    </div>
</div>
@endif