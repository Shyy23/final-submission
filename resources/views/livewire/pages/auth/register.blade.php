<?php

use App\Models\User;
use App\Models\Student; // GANTI Mahasiswa -> Student (sesuai migrasi Anda)
use App\Models\StudyProgram; // IMPORT StudyProgram (sesuai migrasi Anda)
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads; // TAMBAHKAN: Untuk upload file
use Illuminate\Support\Facades\DB; // TAMBAHKAN: Untuk transaction
use Illuminate\Database\Eloquent\Collection; // TAMBAHKAN: Untuk type-hint

new #[Layout('layouts.guest')] class extends Component
{
    use WithFileUploads; // TAMBAHKAN: Gunakan trait

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // TAMBAHKAN: Properti baru
    public string $nim = '';
    public ?int $study_id = null; // Ini akan menjadi 'id' dari study_programs
    public $ktm; // Properti untuk file upload
    public Collection $prodiOptions; // Properti untuk menampung data prodi

    /**
     * TAMBAHKAN: Method mount untuk mengambil data prodi dari DB
     */
    public function mount(): void
    {
        // Mengisi dropdown dari tabel study_programs
        $this->prodiOptions = StudyProgram::orderBy('study_name')->get();
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        // Validasi
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class,
                'regex:/^[a-zA-Z0-9._%+-]+@unjani\.ac\.id$/i'
            ],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],

            'nim' => [
                'required', 
                'string', 
                'size:10', 
                'unique:' . Student::class, 
                // Regex Penjelasan:
                // ^[A-H]       : Digit 1 harus Huruf A sampai H (Fakultas)
                // [0-9]        : Digit 2 Angka (Prodi)
                // [1-2]        : Digit 3 Angka 1 atau 2 (Lokasi)
                // [1-2]        : Digit 4 Angka 1 atau 2 (Jalur)
                // [0-2]        : Digit 5 Angka 0, 1, atau 2 (Semester)
                // \d{5}$       : 5 Digit terakhir adalah angka (Tahun & No Urut)
                'regex:/^[A-H][0-9][1-2][1-2][0-2]\d{5}$/' 
            ],

            'study_id' => ['required', 'integer', 'exists:study_programs,study_id'],
            'ktm' => ['required', 'image', 'max:2048'],
        ], [
            'email.regex' => 'Pendaftaran hanya diizinkan untuk email mahasiswa (@unjani.ac.id).',
            'nim.unique' => 'NIM ini sudah terdaftar.',
            'nim.size' => 'NIM harus berjumlah tepat 10 karakter.',
            'nim.regex' => 'Format NIM tidak sesuai standar UNJANI (Cth: A311119003). Periksa kembali digit Fakultas, Lokasi, atau Jalur.',
            'study_id.required' => 'Program Studi wajib dipilih.',
            'ktm.required' => 'Harap upload scan KTM Anda.',
        ]);

        // UBAH: Logika registrasi
        DB::transaction(function () use ($validated) {
            // 1. Simpan file KTM
            $ktmPath = $this->ktm->store('ktm', 'public');

            // 2. Buat User (is_verified default-nya false dari migrasi)
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // 3. Buat Student (menggunakan relasi dari model User)
            $user->student()->create([
                'nim' => $validated['nim'],
                'study_id' => $validated['study_id'],
                'ktm_path' => $ktmPath,
            ]);

            // 4. Assign role 'mahasiswa' via Spatie (DITAMBAHKAN/AKTIFKAN)
            $user->assignRole('mahasiswa');

            // 5. Kirim event untuk verifikasi email (tetap penting)
            event(new Registered($user));
        });


        // UBAH: Redirect ke login dengan pesan sukses
        session()->flash('status', 'Pendaftaran berhasil! Silakan verifikasi email Anda. Akun Anda akan aktif setelah diverifikasi oleh Admin.');
        $this->redirect(route('login'), navigate: true);
    }
}; ?>

<!-- 
    PERUBAHAN TATA LETAK DIMULAI DI SINI 
    Wrapper diubah untuk menampung card 2 kolom di desktop
-->
<div
    class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50 flex items-center justify-center p-4 sm:p-6 lg:p-8">

    <!-- Card Wrapper Utama -->
    <!-- max-w-sm di mobile, max-w-4xl di lg, dan lg:grid-cols-2 -->
    <div class="w-full max-w-sm lg:max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden lg:grid lg:grid-cols-2">

        <!-- [KOLOM 1: BRANDING/INFO - HANYA DESKTOP] -->
        <div class="hidden lg:flex flex-col justify-center p-12 bg-gray-50 border-r border-gray-100">
            <div class="flex justify-center mb-6">
                <a href="/" wire:navigate>
                    <img src="{{ asset('assets/img/unjani.png') }}" alt="logo unjani" class="w-24 h-24">
                </a>
            </div>
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-3">
                Selamat Datang
            </h2>
            <p class="text-center text-gray-600 text-sm mb-6">
                Satu langkah lagi untuk mengelola pengajuan tugas akhir Anda.
            </p>
            <!-- Info tambahan untuk memandu user -->
            <ul class="space-y-3 text-gray-600 text-sm">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-emerald-500 mt-1 mr-3 flex-shrink-0"></i>
                    <span>Pastikan data (NIM, Nama, Prodi) sesuai dengan KTM.</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-emerald-500 mt-1 mr-3 flex-shrink-0"></i>
                    <span>Gunakan email <code class="text-xs bg-gray-200 p-0.5 rounded">@unjani.ac.id</code> yang
                        aktif.</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-emerald-500 mt-1 mr-3 flex-shrink-0"></i>
                    <span>Akun akan diverifikasi oleh Admin sebelum dapat digunakan.</span>
                </li>
            </ul>
        </div>

        <!-- [KOLOM 2: FORMULIR - MOBILE & DESKTOP] -->
        <!-- Diberi padding lebih besar di desktop -->
        <div class="p-8 sm:p-12">

            <!-- Logo & Header (HANYA MOBILE) -->
            <div class="text-center lg:hidden">
                <a href="/" wire:navigate class="inline-block mb-4">
                    <img src="{{ asset('assets/img/unjani.png') }}" alt="logo unjani" class="w-16 h-16 mx-auto">
                </a>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">
                    Daftar Akun Mahasiswa
                </h2>
                <p class="text-sm text-gray-600">
                    Buat akun untuk mengakses Submission System
                </p>
            </div>

            <!-- Header (HANYA DESKTOP) -->
            <div class="hidden lg:block mb-6">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">
                    Daftar Akun
                </h2>
                <p class="text-sm text-gray-600">
                    Buat akun untuk mengakses Submission System
                </p>
            </div>

            <!-- Formulir Registrasi -->
            <!-- Diberi margin-top di mobile (mt-6), tapi tidak di desktop (lg:mt-0) -->
            <form wire:submit="register" class="space-y-5 mt-6 lg:mt-0">

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap (sesuai KTM)')"
                        class="text-gray-700 font-semibold mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <x-text-input wire:model="name" id="name"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                            type="text" placeholder="Masukkan nama lengkap" required autofocus autocomplete="name" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- NIM -->
                <div>
                    <x-input-label for="nim" :value="__('NIM (Nomor Induk Mahasiswa)')"
                        class="text-gray-700 font-semibold mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-id-badge text-gray-400"></i>
                        </div>
                        <x-text-input wire:model="nim" id="nim"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                            type="text" placeholder="Masukkan NIM (Cth: A311119003)" required autocomplete="off" />
                    </div>
                    <x-input-error :messages="$errors->get('nim')" class="mt-2" />
                </div>

                <!-- Prodi (Dropdown) -->
                <div>
                    <x-input-label for="study_id" :value="__('Program Studi')"
                        class="text-gray-700 font-semibold mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-graduation-cap text-gray-400"></i>
                        </div>
                        <select wire:model="study_id" id="study_id" name="study_id"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white"
                            required>
                            <option value="">Pilih program studi...</option>
                            @foreach($prodiOptions as $prodi)
                            <option value="{{ $prodi->study_id }}">{{ $prodi->study_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-input-error :messages="$errors->get('study_id')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email Unjani')" class="text-gray-700 font-semibold mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <x-text-input wire:model="email" id="email"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                            type="email" placeholder="nama@unjani.ac.id" required autocomplete="username" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-semibold mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <x-text-input wire:model="password" id="password"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                            type="password" placeholder="Minimal 8 karakter" required autocomplete="new-password" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')"
                        class="text-gray-700 font-semibold mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <x-text-input wire:model="password_confirmation" id="password_confirmation"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                            type="password" placeholder="Ulangi password" required autocomplete="new-password" />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- KTM File Upload -->
                <div>
                    <x-input-label for="ktm" :value="__('Upload Scan KTM')" class="text-gray-700 font-semibold mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-id-card text-gray-400"></i>
                        </div>
                        <input wire:model="ktm" id="ktm"
                            class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 file:cursor-pointer file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
                            type="file" required />
                    </div>
                    <div wire:loading wire:target="ktm" class="mt-2 text-sm text-emerald-600">
                        <i class="fas fa-spinner fa-spin mr-1"></i>
                        Sedang mengupload...
                    </div>
                    @if ($ktm && !$errors->has('ktm'))
                    <div class="mt-3">
                        <span class="text-sm text-gray-600 block mb-2">Preview KTM:</span>
                        <img src="{{ $ktm->temporaryUrl() }}" class="w-full rounded-lg border border-gray-200">
                    </div>
                    @endif
                    <x-input-error :messages="$errors->get('ktm')" class="mt-2" />
                </div>

                <!-- Register Button -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-lg shadow-md transition-all duration-300 transform hover:scale-[1.02]">
                        <span wire:loading.remove wire:target="register">
                            <i class="fas fa-user-plus mr-2"></i>
                            Daftar Sekarang
                        </span>
                        <span wire:loading wire:target="register">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Memproses...
                        </span>
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun?
                        <a href="{{ route('login') }}"
                            class="text-emerald-600 hover:text-emerald-700 font-semibold transition-colors duration-200"
                            wire:navigate>
                            Login di sini
                        </a>
                    </p>
                </div>

            </form>
        </div>
    </div> <!-- Akhir dari Card Wrapper Utama -->

    <!-- Footer Text (Diposisikan di luar card) -->

</div>