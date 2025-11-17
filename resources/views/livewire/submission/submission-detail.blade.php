<div>
    {{-- Flash Messages --}}
    @if (session()->has('message'))
    <div
        class="mb-6 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl flex items-center shadow-sm">
        <i class="fas fa-circle-check text-emerald-500 text-xl mr-3"></i>
        <span class="font-medium">{{ session('message') }}</span>
    </div>
    @endif

    @if (session()->has('error'))
    <div
        class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl flex items-center shadow-sm">
        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Timeline --}}
    @include('livewire.submission.partials.common.timeline')

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column - Common Information --}}
        <div class="lg:col-span-2 space-y-6">
            @include('livewire.submission.partials.common.company-info')
            @include('livewire.submission.partials.common.team-members')
            @include('livewire.submission.partials.common.file-section')
            @include('livewire.submission.partials.common.feedback-section')
        </div>

        {{-- Right Column - Role Specific Actions --}}
        <div class="space-y-6">
            {{-- Role Specific Actions --}}
            @auth
            @if(Auth::user()->hasRole('mahasiswa'))
            @include('livewire.submission.partials.mahasiswa.actions')
            @elseif(Auth::user()->hasRole('admin'))
            @include('livewire.submission.partials.admin.actions')
            @elseif(Auth::user()->hasRole('pimpinan'))
            @include('livewire.submission.partials.pimpinan.actions')
            @endif
            @endauth

            {{-- QR Code Section --}}
            @if($submission->status === 'verified' && $submission->qr_url)
            @include('livewire.submission.partials.common.qr-section')
            @endif

            {{-- Additional Information --}}
            @include('livewire.submission.partials.common.additional-info')
        </div>
    </div>

    {{-- Role Specific Modals --}}
    @auth
    @if(Auth::user()->hasRole('admin'))
    @include('livewire.submission.partials.admin.modals')
    @endif
    @endauth
</div>