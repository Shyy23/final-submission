@if($submission->feedback && in_array($submission->status, ['approved', 'rejected']))
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <div class="w-10 h-10 bg-gradient-to-br from-teal-100 to-cyan-100 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-comment-alt text-teal-600"></i>
        </div>
        <span>Feedback</span>
    </h3>
    <div class="p-4 bg-gradient-to-r from-teal-50 to-cyan-50 border border-teal-200 rounded-lg">
        <p class="text-teal-900">{{ $submission->feedback }}</p>
        @if($submission->admin)
        <p class="text-xs text-teal-700 mt-2">
            <i class="fas fa-user-shield mr-1"></i>
            Oleh: {{ $submission->admin->name }} (Admin)
        </p>
        @endif
    </div>
</div>
@endif