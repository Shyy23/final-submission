Mount the component.Update the profile information for the currently authenticated user.Send an email verification notification to the current user.<?php

                                                                                                                                                    use App\Models\Leader;
                                                                                                                                                    use App\Models\Position;
                                                                                                                                                    use App\Models\Student;
                                                                                                                                                    use App\Models\StudyProgram;
                                                                                                                                                    use App\Models\User;
                                                                                                                                                    use Illuminate\Support\Facades\Auth;
                                                                                                                                                    use Illuminate\Support\Facades\Session;
                                                                                                                                                    use Illuminate\Validation\Rule;
                                                                                                                                                    use Livewire\Volt\Component;

                                                                                                                                                    new class extends Component
                                                                                                                                                    {
                                                                                                                                                        public string $name = '';
                                                                                                                                                        public string $email = '';

                                                                                                                                                        // Properti untuk mahasiswa
                                                                                                                                                        public string $nim = '';
                                                                                                                                                        public $study_id = null;
                                                                                                                                                        public $studyPrograms = [];

                                                                                                                                                        // Properti untuk pimpinan
                                                                                                                                                        public string $nid = '';
                                                                                                                                                        public $position_id = null;
                                                                                                                                                        public $positions = [];

                                                                                                                                                        // simpan data asli
                                                                                                                                                        private ?Student $existingStudent = null;
                                                                                                                                                        private ?Leader $existingLeader = null;

                                                                                                                                                        /**
                                                                                                                                                         * Mount the component.
                                                                                                                                                         */
                                                                                                                                                        public function mount(): void
                                                                                                                                                        {
                                                                                                                                                            $user = Auth::user();

                                                                                                                                                            $this->name = Auth::user()->name;
                                                                                                                                                            $this->email = Auth::user()->email;

                                                                                                                                                            // update profile mahasiswa
                                                                                                                                                            if ($user->hasRole('mahasiswa')) {
                                                                                                                                                                $this->studyPrograms = StudyProgram::orderBy('study_name')->get();

                                                                                                                                                                $this->existingStudent = Student::where('user_id', $user->id)->first();
                                                                                                                                                                if ($this->existingStudent) {
                                                                                                                                                                    $this->nim = $this->existingStudent->nim;
                                                                                                                                                                    $this->study_id = $this->existingStudent->study_id;
                                                                                                                                                                }
                                                                                                                                                            }

                                                                                                                                                            // update profile pimpinan
                                                                                                                                                            if ($user->hasRole('pimpinan')) {
                                                                                                                                                                $this->positions = Position::orderBy('position_name')->get();

                                                                                                                                                                $this->existingLeader = Leader::where('user_id', $user->id)->first();
                                                                                                                                                                if ($this->existingLeader) {
                                                                                                                                                                    $this->nid = $this->existingLeader->nid;
                                                                                                                                                                    $this->position_id = $this->existingLeader->position_id;
                                                                                                                                                                }
                                                                                                                                                            }
                                                                                                                                                        }

                                                                                                                                                        /**
                                                                                                                                                         * Update the profile information for the currently authenticated user.
                                                                                                                                                         */
                                                                                                                                                        public function updateProfileInformation(): void
                                                                                                                                                        {
                                                                                                                                                            $user = Auth::user();

                                                                                                                                                            $rules = [
                                                                                                                                                                'name' => ['required', 'string', 'max:255'],
                                                                                                                                                                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
                                                                                                                                                            ];

                                                                                                                                                            // validasi tambahan untuk mahasiswa
                                                                                                                                                            if ($user->hasRole('mahasiswa')) {
                                                                                                                                                                $rules = array_merge($rules, [
                                                                                                                                                                    'nim' => ['required', 'string', 'min:10', 'max:11', Rule::unique(Student::class, 'nim')->ignore($this->existingStudent?->nim, 'nim')],
                                                                                                                                                                    'study_id' => ['required', 'exists:study_programs,study_id']
                                                                                                                                                                ]);
                                                                                                                                                            }

                                                                                                                                                            if ($user->hasRole('pimpinan')) {
                                                                                                                                                                $rules = array_merge($rules, [
                                                                                                                                                                    'nid' => ['required', 'string', 'min:10', 'max:11', Rule::unique(Leader::class, 'nid')->ignore($this->existingLeader?->nid, 'nid')],
                                                                                                                                                                    'position_id' => ['required', 'exists:positions,position_id'],
                                                                                                                                                                ]);
                                                                                                                                                            }

                                                                                                                                                            $validated = $this->validate($rules);

                                                                                                                                                            $user->fill($validated);

                                                                                                                                                            if ($user->isDirty('email')) {
                                                                                                                                                                $user->email_verified_at = null;
                                                                                                                                                            }

                                                                                                                                                            $user->save();

                                                                                                                                                            if ($user->hasRole('mahasiswa')) {
                                                                                                                                                                Student::updateOrCreate([
                                                                                                                                                                    'user_id' => $user->id
                                                                                                                                                                ], [
                                                                                                                                                                    'nim' => $validated['nim'],
                                                                                                                                                                    'study_id' => $validated['study_id']
                                                                                                                                                                ]);
                                                                                                                                                            }

                                                                                                                                                            if ($user->hasRole('pimpinan')) {
                                                                                                                                                                Leader::updateOrCreate([
                                                                                                                                                                    'user_id' => $user->id
                                                                                                                                                                ], [
                                                                                                                                                                    'nid' => $validated['nid'],
                                                                                                                                                                    'position_id' => $validated['position_id']
                                                                                                                                                                ]);
                                                                                                                                                            }

                                                                                                                                                            $this->dispatch('profile-updated', name: $user->name);
                                                                                                                                                        }

                                                                                                                                                        /**
                                                                                                                                                         * Send an email verification notification to the current user.
                                                                                                                                                         */
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
                    <i class="fas fa-user text-gray-400 mr-2"></i>
                    Nama Lengkap
                </label>
                <input wire:model="name" id="name" name="name" type="text"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                    Email Address
                </label>
                <input wire:model="email" id="email" name="email" type="email"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-sm text-amber-800 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Email Anda belum diverifikasi.
                        <button wire:click.prevent="sendVerification" class="ml-2 underline text-amber-900 hover:text-amber-700 font-medium">
                            Kirim ulang email verifikasi
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-sm text-emerald-700 flex items-center">
                        <i class="fas fa-circle-check mr-2"></i>
                        Link verifikasi baru telah dikirim ke email Anda.
                    </p>
                    @endif
                </div>
                @endif
            </div>
        </div>

        @role('mahasiswa')
        {{-- Mahasiswa Data Section --}}
        <div class="pt-6 border-t border-gray-200">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-graduation-cap text-blue-600"></i>
                </div>
                <div>
                    <h4 class="text-md font-semibold text-gray-800">Data Mahasiswa</h4>
                    <p class="text-sm text-gray-600">Lengkapi data kemahasiswaan Anda</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nim" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-id-card text-gray-400 mr-2"></i>
                        NIM (Nomor Induk Mahasiswa)
                    </label>
                    <input wire:model="nim" id="nim" name="nim" type="text"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        placeholder="Contoh: 1234567890"
                        required />
                    <x-input-error class="mt-2" :messages="$errors->get('nim')" />
                </div>

                <div>
                    <label for="study_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-book text-gray-400 mr-2"></i>
                        Program Studi
                    </label>
                    <select wire:model="study_id" id="study_id" name="study_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all text-gray-800"
                        required>
                        <option value="">-- Pilih Program Studi --</option>
                        @foreach($studyPrograms as $program)
                        <option value="{{ $program->study_id }}">{{ $program->study_name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('study_id')" />
                </div>
            </div>
        </div>
        @endrole

        @role('pimpinan')
        {{-- Pimpinan Data Section --}}
        <div class="pt-6 border-t border-gray-200">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-pink-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-user-tie text-purple-600"></i>
                </div>
                <div>
                    <h4 class="text-md font-semibold text-gray-800">Data Pimpinan</h4>
                    <p class="text-sm text-gray-600">Lengkapi data kepemimpinan Anda</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nid" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-id-badge text-gray-400 mr-2"></i>
                        NID (Nomor Induk Dosen/Kepegawaian)
                    </label>
                    <input wire:model="nid" id="nid" name="nid" type="text"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        placeholder="Contoh: 1234567890"
                        required />
                    <x-input-error class="mt-2" :messages="$errors->get('nid')" />
                </div>

                <div>
                    <label for="position_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-briefcase text-gray-400 mr-2"></i>
                        Jabatan
                    </label>
                    <select wire:model="position_id" id="position_id" name="position_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all text-gray-800"
                        required>
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($positions as $position)
                        <option value="{{ $position->position_id }}">{{ $position->position_name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('position_id')" />
                </div>
            </div>
        </div>
        @endrole

        {{-- Action Buttons --}}
        <div class="flex items-center gap-4 pt-6">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-lg shadow-sm transition-all duration-300">
                <i class="fas fa-save mr-2"></i>
                Simpan Perubahan
            </button>

            <div x-data="{ shown: false }"
                x-on:profile-updated.window="shown = true; setTimeout(() => shown = false, 3000)"
                x-show="shown"
                x-transition
                class="flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg border border-emerald-200">
                <i class="fas fa-circle-check mr-2"></i>
                <span class="text-sm font-medium">Tersimpan!</span>
            </div>
        </div>
    </form>
</section>