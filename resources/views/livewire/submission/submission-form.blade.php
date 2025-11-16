<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">

        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center mb-4">

                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Form Pengajuan Surat</h1>
                    <p class="text-sm text-gray-600 mt-1">Lengkapi formulir di bawah untuk mengajukan surat tugas akhir</p>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 flex items-start shadow-sm">
            <i class="fas fa-circle-check text-emerald-500 mr-3 mt-0.5 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-start shadow-sm">
            <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5 flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <form wire:submit.prevent="submit" class="space-y-6">

            <!-- Informasi Perusahaan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-building mr-2"></i>
                        Informasi Perusahaan
                    </h3>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Perusahaan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="company_name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200"
                            placeholder="Masukkan nama perusahaan">
                        @error('company_name')
                        <span class="text-red-500 text-sm mt-1.5 flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>
                            {{ $message }}
                        </span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Alamat Perusahaan <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="address_company" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200"
                            placeholder="Masukkan alamat lengkap perusahaan"></textarea>
                        @error('address_company')
                        <span class="text-red-500 text-sm mt-1.5 flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>
                            {{ $message }}
                        </span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Catatan <span class="text-gray-500 text-xs">(Opsional)</span>
                        </label>
                        <textarea wire:model="note" rows="2"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200"
                            placeholder="Tambahkan catatan jika diperlukan"></textarea>
                        @error('note')
                        <span class="text-red-500 text-sm mt-1.5">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            File Submission <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="file" wire:model="file_submission"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200 file:cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                        </div>
                        <p class="text-xs text-gray-500 mt-2 flex items-center">
                            <i class="fas fa-info-circle mr-1 text-gray-400"></i>
                            Format: PDF, DOC, DOCX (Maksimal 10MB)
                        </p>
                        @error('file_submission')
                        <span class="text-red-500 text-sm mt-1.5 flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pemilihan Anggota -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-users mr-2"></i>
                        Anggota Kelompok
                    </h3>
                </div>

                <div class="p-6 space-y-5">

                    <!-- Filter Section -->
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-lg border border-gray-200">
                        <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-filter mr-2 text-emerald-600"></i>
                            Filter Mahasiswa
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Program Studi</label>
                                <select wire:model="selectedStudy" wire:change="loadAvailableStudents"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                                    <option value="">Semua Program Studi</option>
                                    @foreach($studyPrograms as $study)
                                    <option value="{{ $study->study_id }}">{{ $study->study_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Mahasiswa</label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        wire:model="searchTerm"
                                        wire:keydown.debounce.500ms="loadAvailableStudents"
                                        placeholder="Cari berdasarkan NIM atau Nama"
                                        class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200 bg-white">
                                    <div class="absolute inset-y-0 right-1 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-search text-gray-400 text-sm"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Members -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 p-5 rounded-lg border border-emerald-200">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-semibold text-gray-700 flex items-center">
                                <i class="fas fa-user-friends mr-2 text-emerald-600"></i>
                                Anggota Terpilih
                            </h4>
                            <span class="text-sm font-semibold text-emerald-700 bg-white px-3 py-1 rounded-full shadow-sm">
                                {{ count($selectedMembers) }}/6
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($selectedMembers as $member)
                            <div class="bg-white border border-emerald-200 text-gray-800 px-4 py-2.5 rounded-lg text-sm flex items-center shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-white text-xs"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900">{{ $member['name'] }}</span>
                                    <span class="text-xs text-gray-600">{{ $member['nim'] }}</span>
                                </div>
                                @if($member['is_representative'])
                                <span class="ml-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-2.5 py-1 rounded-md text-xs font-semibold shadow-sm">
                                    Perwakilan
                                </span>
                                @else
                                <button type="button" wire:click="removeMember('{{ $member['nim'] }}')"
                                    class="ml-3 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full p-1 transition duration-200"
                                    title="Hapus anggota">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @if(count($selectedMembers) === 1)
                        <p class="text-sm text-gray-600 mt-3 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-emerald-600"></i>
                            Anda dapat menambahkan maksimal 5 anggota lainnya
                        </p>
                        @endif
                    </div>

                    <!-- Available Students -->
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-semibold text-gray-700 flex items-center">
                                <i class="fas fa-list mr-2 text-gray-600"></i>
                                Daftar Mahasiswa
                            </h4>
                            @if($isLoading)
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-spinner fa-spin text-emerald-500 mr-2"></i>
                                Memuat...
                            </div>
                            @endif
                        </div>

                        @if(count($availableStudents) > 0)
                        <div class="grid grid-cols-1 gap-3 max-h-96 overflow-y-auto pr-2">
                            @foreach($availableStudents as $student)
                            <div class="flex items-center justify-between p-4 bg-white hover:bg-gray-50 rounded-lg border border-gray-200 hover:border-emerald-300 transition-all duration-200 shadow-sm hover:shadow-md">
                                <div class="flex items-center flex-1">
                                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                        <i class="fas fa-user-graduate text-emerald-600 text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">{{ $student['name'] }}</div>
                                        <div class="text-sm text-gray-600 mt-0.5">
                                            <span class="font-medium">{{ $student['nim'] }}</span>
                                            <span class="mx-1.5">•</span>
                                            <span>{{ $student['study_program'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button"
                                    wire:click="toggleMember('{{ $student['nim'] }}', '{{ $student['name'] }}')"
                                    class="{{ $student['is_selected'] ? 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white shadow-md' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }} px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center">
                                    @if($student['is_selected'])
                                    <i class="fas fa-circle-check mr-2"></i>
                                    Terpilih
                                    @else
                                    <i class="fas fa-plus mr-2"></i>
                                    Pilih
                                    @endif
                                </button>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-12">
                            <i class="fas fa-users-slash text-5xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium">Tidak ada mahasiswa yang ditemukan</p>
                            <p class="text-sm text-gray-400 mt-1">Coba ubah filter atau kata kunci pencarian</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('dashboard') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Ajukan Surat Tugas
                </button>
            </div>
        </form>
    </div>
    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-sm text-gray-500">
                © {{ date('Y') }} Submission System. All rights reserved.
            </p>
        </div>
    </footer>
</div>