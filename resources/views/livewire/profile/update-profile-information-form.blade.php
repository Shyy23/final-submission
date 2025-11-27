<?php

use App\Models\Leader;
use App\Models\Position;
use App\Models\Student;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';

    // Properti Mahasiswa
    public string $nim = '';
    public $study_id = null;
    public $ktm; 
    public $old_ktm_path; 

    // Properti Pimpinan
    public string $nid = '';
    public $position_id = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;

        if ($user->hasRole('mahasiswa')) {
            $student = Student::where('user_id', $user->id)->first();

            if ($student) {
                $this->nim = $student->nim;
                $this->study_id = $student->study_id;
                $this->old_ktm_path = $student->ktm_path;
            }
        }

        if ($user->hasRole('pimpinan')) {
            $leader = Leader::where('user_id', $user->id)->first();
            
            if ($leader) {
                $this->nid = $leader->nid;
                $this->position_id = $leader->position_id;
            }
        }
    }

    #[Computed]
    public function studyPrograms()
    {
        return StudyProgram::orderBy('study_name')->get();
    }

    #[Computed]
    public function positions()
    {
        return Position::orderBy('position_name')->get();
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ];
        
        // Custom messages untuk validasi NIM
        $messages = [];

        if ($user->hasRole('mahasiswa')) {
            $currentStudentNim = Student::where('user_id', $user->id)->value('nim');

            $rules = array_merge($rules, [
                'nim' => [
                    'required', 
                    'string', 
                    'size:10', // FIX: Wajib 10 karakter sesuai PDF
                    Rule::unique(Student::class, 'nim')->ignore($currentStudentNim, 'nim'),
                    // FIX: Regex sesuai aturan SK Rektor UNJANI
                    // Digit 1: A-H (Fakultas)
                    // Digit 2: 0-9 (Jurusan)
                    // Digit 3: 1-2 (Kampus)
                    // Digit 4: 1-2 (Jalur)
                    // Digit 5: 0-2 (Semester)
                    // Digit 6-10: Angka (Tahun & Urut)
                    'regex:/^[A-H][0-9][1-2][1-2][0-2]\d{5}$/' 
                ],
                'study_id' => ['required', 'exists:study_programs,study_id'],
                'ktm' => ['nullable', 'image', 'max:2048'], 
            ]);

            // Tambahkan pesan error spesifik
            $messages = [
                'nim.size' => 'NIM harus berjumlah tepat 10 karakter.',
                'nim.regex' => 'Format NIM tidak valid (Cth: A311119003). Periksa kode Fakultas/Lokasi/Jalur.',
            ];
        }

        if ($user->hasRole('pimpinan')) {
            $currentLeaderNid = Leader::where('user_id', $user->id)->value('nid');

            $rules = array_merge($rules, [
                'nid' => [
                    'required', 
                    'string', 
                    'min:5', // Sesuaikan jika ada aturan NID
                    'max:20', 
                    Rule::unique(Leader::class, 'nid')->ignore($currentLeaderNid, 'nid')
                ],
                'position_id' => ['required', 'exists:positions,position_id'],
            ]);
        }

        // Jalankan validasi dengan custom messages
        $validated = $this->validate($rules, $messages);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($user->hasRole('mahasiswa')) {
            $ktmPath = $this->old_ktm_path;

            if ($this->ktm) {
                if ($this->old_ktm_path && Storage::disk('public')->exists($this->old_ktm_path)) {
                    Storage::disk('public')->delete($this->old_ktm_path);
                }
                $ktmPath = $this->ktm->store('ktm', 'public');
            }

            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nim' => $validated['nim'],
                    'study_id' => $validated['study_id'],
                    'ktm_path' => $ktmPath,
                ]
            );
            
            $this->old_ktm_path = $ktmPath;
            $this->ktm = null; 
        }

        if ($user->hasRole('pimpinan')) {
            Leader::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nid' => $validated['nid'],
                    'position_id' => $validated['position_id']
                ]
            );
        }

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function sendVerification(): void
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }
        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <form wire:submit="updateProfileInformation" class="space-y-6">
        {{-- Basic Information --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user text-gray-400 mr-2"></i> Nama Lengkap
                </label>
                <input wire:model="name" id="name" name="name" type="text"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-envelope text-gray-400 mr-2"></i> Email Address
                </label>
                <input wire:model="email" id="email" name="email" type="email"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                {{-- Area Verifikasi Email (DIPERBAIKI) --}}
                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !
                auth()->user()->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-sm text-amber-800 flex flex-col sm:flex-row sm:items-center gap-2">
                        <span>Email belum diverifikasi.</span>

                        {{-- TOMBOL DIPERBAIKI: Loading State & Disabled --}}
                        <button wire:click.prevent="sendVerification" wire:loading.attr="disabled"
                            wire:target="sendVerification"
                            class="underline font-bold hover:text-amber-900 disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center">

                            {{-- Teks normal --}}
                            <span wire:loading.remove wire:target="sendVerification">Kirim ulang link</span>

                            {{-- Teks saat loading --}}
                            <span wire:loading wire:target="sendVerification" class="flex items-center">
                                <i class="fas fa-circle-notch fa-spin mr-1"></i> Mengirim...
                            </span>
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                    <div class="mt-2 text-sm text-emerald-700 font-bold flex items-center animate-pulse">
                        <i class="fas fa-check-circle mr-1.5"></i> Link verifikasi baru telah dikirim!
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        @role('mahasiswa')
        {{-- Mahasiswa Data --}}
        <div class="pt-6 border-t border-gray-200">
            <div class="flex items-center mb-6">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-graduation-cap text-blue-600"></i>
                </div>
                <div>
                    <h4 class="text-md font-semibold text-gray-800">Data Mahasiswa</h4>
                    <p class="text-sm text-gray-600">Lengkapi data kemahasiswaan Anda</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nim" class="block text-sm font-semibold text-gray-700 mb-2">NIM</label>
                    {{-- PLACEHOLDER DIPERBAIKI --}}
                    <input wire:model="nim" id="nim" type="text"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 transition-all"
                        placeholder="Contoh: A311119003" required />
                    <x-input-error class="mt-2" :messages="$errors->get('nim')" />
                </div>

                <div>
                    <label for="study_id" class="block text-sm font-semibold text-gray-700 mb-2">Program Studi</label>
                    <select wire:model="study_id" id="study_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 transition-all"
                        required>
                        <option value="">-- Pilih Prodi --</option>
                        @foreach($this->studyPrograms as $program)
                        <option value="{{ $program->study_id }}">{{ $program->study_name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('study_id')" />
                </div>

                {{-- UPLOAD KTM --}}
                <div class="md:col-span-2" x-data="{ 
                        uploading: false, 
                        progress: 0, 
                        errorMessage: null,
                        uploadFile(event) {
                            const file = event.target.files[0];
                            if (!file) return;

                            if (file.size > 2 * 1024 * 1024) {
                                this.errorMessage = 'File terlalu besar! Maksimal 2MB.';
                                event.target.value = ''; 
                                return;
                            }

                            this.uploading = true;
                            this.errorMessage = null;

                            $wire.upload('ktm', file, 
                                () => {
                                    this.uploading = false;
                                    this.errorMessage = null;
                                },
                                () => {
                                    this.uploading = false;
                                    this.errorMessage = 'Gagal upload. Silakan coba lagi.';
                                    event.target.value = '';
                                },
                                (event) => {
                                    this.progress = event.detail.progress;
                                }
                            );
                        }
                     }">

                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kartu Tanda Mahasiswa (KTM)</label>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 relative">
                            @if ($ktm)
                            <img src="{{ $ktm->temporaryUrl() }}"
                                class="w-32 h-20 object-cover rounded-lg border shadow-sm">
                            <div class="text-xs text-emerald-600 mt-1 text-center">Preview Baru</div>
                            @elseif ($old_ktm_path)
                            <img src="{{ asset('storage/' . $old_ktm_path) }}"
                                class="w-32 h-20 object-cover rounded-lg border shadow-sm">
                            <div class="text-xs text-gray-500 mt-1 text-center">Saat Ini</div>
                            @else
                            <div
                                class="w-32 h-20 bg-gray-100 rounded-lg border-2 border-dashed flex items-center justify-center text-gray-400 text-xs">
                                Kosong</div>
                            @endif

                            {{-- Progress Bar Overlay --}}
                            <div x-show="uploading"
                                class="absolute inset-0 bg-white/90 flex flex-col items-center justify-center rounded-lg z-10"
                                style="display: none;">
                                <i class="fas fa-circle-notch fa-spin text-emerald-500 text-xl mb-1"></i>
                                <span class="text-xs font-bold text-emerald-600" x-text="progress + '%'"></span>
                            </div>
                        </div>

                        <div class="flex-1">
                            <input type="file" accept="image/png, image/jpeg, image/jpg"
                                x-on:change="uploadFile($event)"
                                class="block w-full text-sm text-gray-500 file:cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-gray-300 rounded-lg" />

                            <p class="mt-1 text-xs text-gray-500">Format: JPG/PNG. Max 2MB.</p>

                            <x-input-error class="mt-2" :messages="$errors->get('ktm')" />
                            <div x-show="errorMessage" x-cloak
                                class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-xs text-red-600 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span x-text="errorMessage"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endrole

        @role('pimpinan')
        {{-- Pimpinan Data --}}
        <div class="pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NID</label>
                    <input wire:model="nid" type="text"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 transition-all"
                        required />
                    <x-input-error class="mt-2" :messages="$errors->get('nid')" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jabatan</label>
                    <select wire:model="position_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 transition-all"
                        required>
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($this->positions as $pos)
                        <option value="{{ $pos->position_id }}">{{ $pos->position_name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('position_id')" />
                </div>
            </div>
        </div>
        @endrole

        <div class="flex items-center gap-4 pt-6">
            <button type="submit"
                class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg shadow-sm transition-all flex items-center disabled:opacity-70 disabled:cursor-not-allowed"
                wire:loading.attr="disabled" wire:target="updateProfileInformation">

                <span wire:loading.remove wire:target="updateProfileInformation">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </span>
                <span wire:loading wire:target="updateProfileInformation">
                    <i class="fas fa-circle-notch fa-spin mr-2"></i> Menyimpan...
                </span>
            </button>

            <div x-data="{ shown: false }"
                x-on:profile-updated.window="shown = true; setTimeout(() => shown = false, 3000)" x-show="shown"
                x-transition style="display: none;"
                class="flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg border border-emerald-200">
                <i class="fas fa-circle-check mr-2"></i> <span class="text-sm font-medium">Tersimpan!</span>
            </div>
        </div>
    </form>
</section>