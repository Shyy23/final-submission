<?php

                                                                                                                                                                                                use App\Livewire\Actions\Logout;
                                                                                                                                                                                                use Illuminate\Support\Facades\Auth;
                                                                                                                                                                                                use Illuminate\Support\Facades\Session;
                                                                                                                                                                                                use Livewire\Attributes\Layout;
                                                                                                                                                                                                use Livewire\Volt\Component;

                                                                                                                                                                                                new #[Layout('layouts.guest')] class extends Component
                                                                                                                                                                                                {
                                                                                                                                                                                                    /**
                                                                                                                                                                                                     * Send an email verification notification to the user.
                                                                                                                                                                                                     */
                                                                                                                                                                                                    public function sendVerification(): void
                                                                                                                                                                                                    {
                                                                                                                                                                                                        if (Auth::user()->hasVerifiedEmail()) {
                                                                                                                                                                                                            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
                                                                                                                                                                                                            return;
                                                                                                                                                                                                        }

                                                                                                                                                                                                        Auth::user()->sendEmailVerificationNotification();

                                                                                                                                                                                                        Session::flash('status', 'verification-link-sent');
                                                                                                                                                                                                    }

                                                                                                                                                                                                    /**
                                                                                                                                                                                                     * Log the current user out of the application.
                                                                                                                                                                                                     */
                                                                                                                                                                                                    public function logout(Logout $logout): void
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $logout();

                                                                                                                                                                                                        $this->redirect('/', navigate: true);
                                                                                                                                                                                                    }
                                                                                                                                                                                                }; ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">

        <!-- Logo & Header Section -->
        <div class="text-center">
            <div class="flex justify-center">
                <a href="/" wire:navigate
                    class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-lg mb-4 hover:shadow-xl transition-shadow duration-300">
                    <span class="text-white font-bold text-2xl">UNJ</span>
                </a>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">
                Verifikasi Email
            </h2>
            <p class="text-sm text-gray-600">
                Konfirmasi alamat email Anda untuk melanjutkan
            </p>
        </div>

        <!-- Verify Email Card -->
        <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">

            <!-- Info Message -->
            <div class="mb-6">
                <div class="flex items-start space-x-3 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500 text-xl mt-0.5"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan
                            mengklik link yang baru saja kami kirimkan. Jika Anda tidak menerima email tersebut, kami
                            dengan senang hati akan mengirimkan yang lain.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('status') == 'verification-link-sent')
            <div class="mb-6">
                <div class="flex items-start space-x-3 p-4 bg-emerald-50 border border-emerald-100 rounded-lg">
                    <div class="flex-shrink-0">
                        <i class="fas fa-circle-check text-emerald-500 text-xl mt-0.5"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-emerald-700 font-medium leading-relaxed">
                            Link verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="space-y-4">
                <!-- Resend Button -->
                <button wire:click="sendVerification" type="button"
                    class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-lg shadow-md transition-all duration-300 transform hover:scale-[1.02]">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Kirim Ulang Email Verifikasi
                </button>

                <!-- Logout Button -->
                <button wire:click="logout" type="button"
                    class="w-full inline-flex items-center justify-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg border border-gray-300 transition-all duration-200">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Keluar
                </button>
            </div>

        </div>

        <!-- Footer Text -->
        <p class="text-center text-xs text-gray-500">
            © {{ date('Y') }} Submission System - Tugas Akhir Mahasiswa
        </p>

    </div>
</div>