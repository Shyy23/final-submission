<div>
    {{--
    KONDISI 1: STATUS REJECTED -> TAMPILKAN FORM EDIT (RESUBMIT)
    Ini akan menggantikan seluruh tampilan detail biasa.
    --}}
    @if($submission->status === 'rejected')

    {{-- Header Alert Revisi --}}
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-8 animate-fade-in-down">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-red-800">Pengajuan Perlu Diperbaiki</h3>
                <p class="text-red-700 mt-1 text-sm">
                    Admin telah menolak pengajuan sebelumnya dengan catatan di bawah ini. Silakan perbaiki data dan
                    ajukan ulang.
                </p>

                @if($submission->feedback)
                <div class="mt-4 bg-white p-4 rounded-lg border-l-4 border-red-500 shadow-sm">
                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-1">Catatan Admin:</h4>
                    <p class="text-gray-800 italic">"{{ $submission->feedback }}"</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <form wire:submit.prevent="resubmit" class="space-y-8 animate-fade-in-up">

        {{-- BAGIAN 1: INFORMASI PERUSAHAAN --}}
        {{-- FIX: Hapus overflow-hidden agar tooltip/dropdown aman --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            {{-- FIX: Tambah rounded-t-xl agar header tetap melengkung --}}
            <div
                class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4 border-b border-emerald-600 rounded-t-xl">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-building mr-2"></i> Edit Informasi Perusahaan
                </h3>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Perusahaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" wire:model="company_name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200"
                            placeholder="Contoh: PT. Teknologi Indonesia">
                        @error('company_name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap <span
                                class="text-red-500">*</span></label>
                        <textarea wire:model="address_company" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200"
                            placeholder="Masukkan alamat lengkap perusahaan..."></textarea>
                        @error('address_company') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Tambahan</label>
                        <textarea wire:model="note" rows="2"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200"
                            placeholder="Opsional"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: JADWAL PENELITIAN --}}
        {{-- FIX: Hapus overflow-hidden --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            {{-- FIX: Tambah rounded-t-xl --}}
            <div
                class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4 border-b border-emerald-600 rounded-t-xl">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-flask mr-2"></i> Edit Jadwal Penelitian
                </h3>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai <span
                                class="text-red-500">*</span></label>
                        <input type="date" wire:model.live="start_date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 transition duration-200">
                        @error('start_date') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Durasi (Hari) <span
                                class="text-red-500">*</span></label>
                        <input type="number" wire:model.live="duration_days" min="1" max="365"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 transition duration-200">
                        @error('duration_days') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Preview Tanggal Selesai --}}
                @if($endDate)
                <div class="bg-purple-50 border border-purple-100 p-4 rounded-lg flex items-start">
                    <i class="fas fa-calendar-check text-purple-500 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm text-purple-800 font-semibold">Estimasi Selesai:</p>
                        <p class="text-lg font-bold text-purple-900">{{ $endDate }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- BAGIAN 3: ANGGOTA KELOMPOK --}}
        {{-- FIX: Hapus overflow-hidden AGAR DROPDOWN TIDAK TERPOTONG --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            {{-- FIX: Tambah rounded-t-xl --}}
            <div
                class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4 border-b border-emerald-600 rounded-t-xl">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-users mr-2"></i> Edit Anggota Kelompok
                </h3>
            </div>

            {{-- Tambahkan relative z-20 agar berada di atas elemen lain jika tumpah --}}
            <div class="p-6 space-y-6 relative z-20">

                {{-- List Anggota Terpilih --}}
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-bold text-gray-700">Anggota Tim ({{ count($selectedMembers) + 1 }})</h4>
                        <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded">Termasuk Anda</span>
                    </div>

                    <div class="space-y-3">
                        {{-- Diri Sendiri (Ketua) --}}
                        <div
                            class="bg-white border-l-4 border-emerald-500 px-4 py-3 rounded shadow-sm flex justify-between items-center">
                            <div class="min-w-0 pr-2">
                                <div class="font-bold text-gray-800 truncate">{{ Auth::user()->name }} (Anda)</div>
                                <div class="text-xs text-gray-500 truncate">{{ Auth::user()->student->nim }}</div>
                            </div>
                            <span
                                class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2 py-1 rounded flex-shrink-0">Ketua</span>
                        </div>

                        {{-- Anggota Lain --}}
                        @foreach($selectedMembers as $index => $member)
                        <div
                            class="bg-white border border-gray-200 px-4 py-3 rounded shadow-sm flex justify-between items-center group hover:border-emerald-300 transition-colors">
                            {{-- FIX: Tambahkan min-w-0 dan truncate agar teks panjang tidak merusak layout --}}
                            <div class="min-w-0 pr-2">
                                <div class="font-semibold text-gray-800 truncate">{{ $member['name'] }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ $member['nim'] }} - {{
                                    $member['study_program'] }}</div>
                            </div>
                            <button type="button" wire:click="removeMember({{ $index }})"
                                class="text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-full transition-colors flex-shrink-0"
                                title="Hapus Anggota">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>

                    @error('selectedMembers')
                    <p class="text-red-500 text-sm mt-3"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Form Pencarian Anggota --}}
                @if(count($selectedMembers) < 5) <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tambah Anggota Baru</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="searchMember"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 transition duration-200"
                            placeholder="Cari berdasarkan Nama atau NIM...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>

                    {{-- Hasil Pencarian --}}
                    @if(!empty($availableStudents))
                    {{-- Dropdown akan muncul di atas elemen lain karena overflow-hidden sudah dihapus dari parent --}}
                    <div
                        class="absolute z-50 w-full bg-white mt-1 border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                        @foreach($availableStudents as $student)
                        <div class="px-4 py-3 hover:bg-emerald-50 cursor-pointer border-b last:border-0 transition-colors flex justify-between items-center"
                            wire:click="addMember('{{ $student['nim'] }}', '{{ $student['name'] }}')">
                            <div class="min-w-0 pr-2">
                                <div class="font-semibold text-gray-800 truncate">{{ $student['name'] }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ $student['nim'] }} • {{
                                    $student['study_program'] }}</div>
                            </div>
                            <span class="text-emerald-600 text-sm font-medium flex-shrink-0"><i
                                    class="fas fa-plus-circle"></i> Tambah</span>
                        </div>
                        @endforeach
                    </div>
                    @elseif(strlen($searchMember) >= 3)
                    <div
                        class="absolute z-50 w-full bg-white mt-1 border border-gray-200 rounded-lg shadow p-4 text-center text-gray-500 text-sm">
                        Tidak ditemukan mahasiswa dengan kata kunci tersebut.
                    </div>
                    @endif
            </div>
            @else
            <div class="bg-amber-50 text-amber-700 px-4 py-3 rounded-lg text-sm flex items-center">
                <i class="fas fa-info-circle mr-2"></i> Kuota anggota kelompok sudah penuh (Maks. 6 orang termasuk
                ketua).
            </div>
            @endif

        </div>
</div>

{{-- TOMBOL AKSI --}}
<div class="flex flex-col sm:flex-row justify-end gap-4 pt-4 border-t border-gray-200">
    {{-- Tombol Batal (Opsional, jika ingin membatalkan pengajuan) --}}
    <button type="button" wire:click="confirmCancelSubmission"
        class="px-6 py-3 bg-white border border-red-200 text-red-600 font-semibold rounded-lg hover:bg-red-50 transition duration-200 shadow-sm">
        <i class="fas fa-times mr-2"></i> Batalkan Pengajuan
    </button>

    <button type="submit" wire:loading.attr="disabled"
        class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold rounded-lg shadow-lg transform hover:scale-105 transition duration-200 flex items-center justify-center min-w-[200px]">
        <span wire:loading.remove wire:target="resubmit">
            <i class="fas fa-paper-plane mr-2"></i> Kirim Perbaikan
        </span>
        <span wire:loading wire:target="resubmit">
            <i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...
        </span>
    </button>
</div>
</form>

@else
{{--
KONDISI 2: STATUS NORMAL (PENDING / APPROVED / VERIFIED)
Tampilan Sidebar Kanan Biasa
--}}
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Mahasiswa</h3>

    {{-- Jika Pending --}}
    @if($submission->status === 'pending')
    <div class="text-gray-500 text-sm italic text-center py-4 bg-gray-50 rounded-lg border border-gray-100">
        <i class="fas fa-hourglass-half mb-2 text-xl block text-gray-400"></i>
        Pengajuan sedang diperiksa oleh Admin.<br>Mohon menunggu persetujuan.
    </div>
    @endif

    {{-- Jika Approved (Belum Verified) --}}
    @if($submission->status === 'approved')
    <div class="text-blue-600 text-sm text-center py-4 bg-blue-50 rounded-lg border border-blue-100">
        <i class="fas fa-file-signature mb-2 text-xl block"></i>
        <strong>Surat Disetujui!</strong><br>
        Menunggu tanda tangan digital Pimpinan.
    </div>
    @endif

    {{-- Jika Verified --}}
    @if($submission->status === 'verified')
    <div class="space-y-4">
        <div class="p-3 bg-emerald-50 text-emerald-800 text-sm rounded-lg border border-emerald-100 flex items-start">
            <i class="fas fa-check-circle mt-0.5 mr-2 text-emerald-600"></i>
            <span>Selamat! Surat tugas Anda telah terbit dan sah.</span>
        </div>

        {{-- Tombol Download (Memanggil fungsi di Parent) --}}
        <button wire:click="$dispatch('downloadDocument')"
            class="w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-md transition-all flex items-center justify-center group">
            <i class="fas fa-download mr-2 group-hover:animate-bounce"></i> Download Surat Tugas
        </button>
    </div>
    @endif
</div>
@endif
</div>