<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-users text-blue-600"></i>
        </div>
        <span>Daftar Anggota Kelompok</span>
    </h3>
    <div class="space-y-3">
        @foreach($submission->memberStudents as $member)
        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg border border-gray-100 hover:shadow-sm transition-shadow duration-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                    <span class="text-white font-bold">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
                </div>
                <div>
                    <div class="font-semibold text-gray-800">{{ $member->user->name }}</div>
                    <div class="text-sm text-gray-500 flex items-center">
                        <i class="fas fa-id-card text-gray-400 mr-1"></i>
                        {{ $member->nim }}
                    </div>
                </div>
            </div>
            @if($member->nim === $submission->representative_nim)
            <span class="px-3 py-1.5 bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700 text-sm font-semibold rounded-full border border-emerald-200 flex items-center">
                <i class="fas fa-crown mr-1"></i>
                Perwakilan
            </span>
            @endif
        </div>
        @endforeach
    </div>
</div>