<div> {{-- ROOT ELEMENT --}}

    {{-- Flash Messages --}}
    @if (session()->has('message'))
    <div
        class="mb-4 sm:mb-6 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 text-emerald-800 px-4 py-3 sm:px-5 sm:py-4 rounded-xl flex items-center shadow-sm animate-fade-in-down">
        <i class="fas fa-circle-check text-emerald-500 text-lg sm:text-xl mr-3"></i>
        <span class="font-medium text-sm sm:text-base">{{ session('message') }}</span>
    </div>
    @endif

    @if (session()->has('error'))
    <div
        class="mb-4 sm:mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 px-4 py-3 sm:px-5 sm:py-4 rounded-xl flex items-center shadow-sm animate-fade-in-down">
        <i class="fas fa-exclamation-circle text-red-500 text-lg sm:text-xl mr-3"></i>
        <span class="font-medium text-sm sm:text-base">{{ session('error') }}</span>
    </div>
    @endif

    {{-- ====================================================================== --}}
    {{-- 1. STATUS DISPLAY (TIMELINE / CARD) --}}
    {{-- ====================================================================== --}}

    @if(Auth::user()->hasRole('mahasiswa'))
    {{-- Mahasiswa melihat TIMELINE (Agar tetap tahu progress) --}}
    @include('livewire.submission.partials.common.timeline')
    @else
    {{-- Admin/Pimpinan melihat STATUS CARD RINGKAS --}}
    @php
    $statusStyles = [
    'pending' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-700', 'icon_bg' =>
    'bg-amber-100', 'icon_color' => 'text-amber-600', 'icon' => 'fa-hourglass-half', 'label' => 'Menunggu Persetujuan'],
    'approved' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700', 'icon_bg' =>
    'bg-blue-100', 'icon_color' => 'text-blue-600', 'icon' => 'fa-thumbs-up', 'label' => 'Disetujui Admin (Draft
    Siap)'],
    'rejected' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-700', 'icon_bg' =>
    'bg-red-100', 'icon_color' => 'text-red-600', 'icon' => 'fa-times-circle', 'label' => 'Ditolak / Perlu Revisi'],
    'verified' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700', 'icon_bg' =>
    'bg-emerald-100', 'icon_color' => 'text-emerald-600', 'icon' => 'fa-check-double', 'label' => 'Terverifikasi &
    Ditandatangani'],
    ];
    $currentStyle = $statusStyles[$submission->status] ?? $statusStyles['pending'];
    @endphp

    <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 mb-6 border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            {{-- Kiri: Icon & Status --}}
            <div class="flex items-center">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0 {{ $currentStyle['icon_bg'] }}">
                    <i
                        class="fas {{ $currentStyle['icon'] }} {{ $currentStyle['icon_color'] }} text-xl sm:text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-800">Status Pengajuan</h3>
                    <div
                        class="mt-1 inline-flex items-center px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-lg text-xs sm:text-sm font-semibold {{ $currentStyle['bg'] }} {{ $currentStyle['text'] }} border {{ $currentStyle['border'] }}">
                        {{ $currentStyle['label'] }}
                    </div>
                </div>
            </div>

            {{-- Kanan: Waktu Update --}}
            <div class="text-left sm:text-right mt-2 sm:mt-0 pl-0 sm:pl-0">
                <p class="text-xs text-gray-500 mb-1">Terakhir diperbarui</p>
                <div class="flex items-center sm:justify-end text-gray-700 font-medium text-sm sm:text-base">
                    <i class="far fa-clock mr-2 text-gray-400"></i>
                    {{ $submission->updated_at->format('d M Y, H:i') }}
                </div>
            </div>
        </div>
    </div>
    @endif


    {{-- ====================================================================== --}}
    {{-- 2. CONTENT SWITCHER BERDASARKAN STATUS --}}
    {{-- ====================================================================== --}}

    {{-- KASUS A: REJECTED (Mahasiswa) -> Form Resubmit --}}
    @if($submission->status === 'rejected' && Auth::user()->hasRole('mahasiswa'))

    <div class="mt-6 animate-fade-in-up">
        <livewire:submission.mahasiswa.action-panel :submission="$submission"
            :key="'mhs-resubmit-'.$submission->submission_id" />
    </div>

    {{--
    KASUS B: FOKUS DOKUMEN (Approved/Verified)
    Kondisi:
    1. Status Verified (Semua User)
    2. ATAU Status Approved TAPI User BUKAN Mahasiswa (Admin/Pimpinan)
    --}}
    @elseif($submission->status === 'verified' || ($submission->status === 'approved' &&
    !Auth::user()->hasRole('mahasiswa')))

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6 animate-fade-in">
        {{-- KOLOM KIRI: DOKUMEN UTAMA, FEEDBACK & QR (FOKUS UTAMA) --}}
        <div class="lg:col-span-2 space-y-6 order-2 lg:order-1">

            {{-- Banner Status --}}
            @if($submission->status === 'verified')
            <div
                class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl shadow-lg p-4 sm:p-6 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold mb-1 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i> Surat Tugas Selesai!
                    </h2>
                    <p class="text-emerald-50 text-xs sm:text-sm opacity-90">Dokumen telah diverifikasi dan
                        ditandatangani secara digital.</p>
                </div>
                <div class="hidden sm:block">
                    <i class="fas fa-file-contract text-5xl text-white opacity-20"></i>
                </div>
            </div>
            @elseif($submission->status === 'approved')
            <div
                class="bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl shadow-lg p-4 sm:p-6 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold mb-1 flex items-center">
                        <i class="fas fa-file-signature mr-2"></i> Dokumen Siap Diproses
                    </h2>
                    <p class="text-blue-50 text-xs sm:text-sm opacity-90">
                        Data telah disetujui. Dokumen draft telah dibuat untuk diverifikasi.
                    </p>
                </div>
                <div class="hidden sm:block">
                    <i class="fas fa-pen-nib text-5xl text-white opacity-20"></i>
                </div>
            </div>
            @endif

            {{-- 1. File Section (Preview Dokumen) --}}
            {{-- Kita paksa tampilkan file section di sini meskipun logic internal component membatasi --}}
            @if($submission->document_path)
            @include('livewire.submission.partials.common.file-section')
            @else
            <div class="p-6 bg-gray-50 border border-dashed border-gray-300 rounded-xl text-center text-gray-500">
                <i class="fas fa-file-pdf text-3xl mb-2 text-gray-300"></i>
                <p>Dokumen sedang digenerate...</p>
            </div>
            @endif

            {{-- 2. QR Section (Hanya jika verified) --}}
            @if($submission->qr_url)
            @include('livewire.submission.partials.common.qr-section')
            @endif

            {{-- 3. Feedback (Penting) --}}
            @include('livewire.submission.partials.common.feedback-section')

            {{--
            DATA DATA SEPERTI COMPANY INFO / TEAM MEMBERS DIHILANGKAN
            SESUAI REQUEST UTAMA AGAR FOKUS KE DOKUMEN & AKSI
            --}}
        </div>

        {{-- KOLOM KANAN: AKSI DOWNLOAD & VALIDATOR --}}
        <div class="space-y-6 order-1 lg:order-2">

            {{-- Action Panel (Tombol Aksi Utama) --}}
            @auth
            @if(Auth::user()->hasRole('mahasiswa'))
            <livewire:submission.mahasiswa.action-panel :submission="$submission"
                :key="'mhs-action-'.$submission->submission_id" />
            @elseif(Auth::user()->hasRole('admin'))
            <livewire:submission.admin.action-panel :submission="$submission"
                :key="'adm-action-'.$submission->submission_id" />
            @elseif(Auth::user()->hasRole('pimpinan'))
            <livewire:submission.pimpinan.action-panel :submission="$submission"
                :key="'pim-action-'.$submission->submission_id" />
            @endif
            @endauth

            {{-- Info Validator (Siapa yang setuju & TTD) --}}
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-shield-alt text-emerald-600 mr-2"></i> Validator
                </h3>
                <div class="space-y-4 text-sm">
                    {{-- Admin --}}
                    <div class="flex items-center justify-between pb-3 border-b border-gray-50">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-3 flex-shrink-0">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-gray-500 text-xs">Disetujui Admin</p>
                                <p class="font-semibold text-gray-800 truncate max-w-[150px]">
                                    {{ $submission->admin->name ?? 'Menunggu...' }}
                                </p>
                            </div>
                        </div>
                        @if($submission->admin_id)
                        <i class="fas fa-check text-emerald-500"></i>
                        @endif
                    </div>

                    {{-- Pimpinan --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 mr-3 flex-shrink-0">
                                <i class="fas fa-signature"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-gray-500 text-xs">Ditandatangani Pimpinan</p>
                                <p class="font-semibold text-gray-800 truncate max-w-[150px]">
                                    {{ $submission->leader->user->name ?? 'Menunggu...' }}
                                </p>
                            </div>
                        </div>
                        @if($submission->leader_id)
                        <i class="fas fa-check text-emerald-500"></i>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{--
    KASUS C: DATA MODE
    Kondisi:
    1. Pending (Semua User)
    2. ATAU Approved DAN User ADALAH Mahasiswa (Agar bisa cek data sebelum ttd)
    --}}
    @else

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        {{-- [KOLOM KIRI] Detail Data Lengkap (Inputan) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Data-data mentah ditampilkan di sini --}}
            @include('livewire.submission.partials.common.company-info')
            @include('livewire.submission.partials.common.team-members')

            {{-- Feedback jika ada (misal revisi sebelumnya) --}}
            @include('livewire.submission.partials.common.feedback-section')
        </div>

        {{-- [KOLOM KANAN] Sidebar Aksi --}}
        <div class="space-y-6">
            @auth
            @if(Auth::user()->hasRole('mahasiswa'))
            <livewire:submission.mahasiswa.action-panel :submission="$submission"
                :key="'mhs-action-'.$submission->submission_id" />
            @elseif(Auth::user()->hasRole('admin'))
            <livewire:submission.admin.action-panel :submission="$submission"
                :key="'adm-action-'.$submission->submission_id" />
            @elseif(Auth::user()->hasRole('pimpinan'))
            <livewire:submission.pimpinan.action-panel :submission="$submission"
                :key="'pim-action-'.$submission->submission_id" />
            @endif
            @endauth

            @include('livewire.submission.partials.common.additional-info')
        </div>
    </div>

    @endif

</div>