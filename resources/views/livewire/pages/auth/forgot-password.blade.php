<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">

        <!-- Logo & Header Section -->
        <div class="text-center">
            <div class="flex justify-center">
                <a href="/" wire:navigate class=" mb-4">
                    <img src="{{ asset('assets/img/unjani.png') }}" alt="logo unjani" class="w-16 h-16">
                </a>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">
                Lupa Password?
            </h2>
            <p class="text-sm text-gray-600">
                Kami akan mengirimkan link reset password ke email Anda
            </p>
        </div>

        <!-- Forgot Password Card -->
        <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">

            <!-- Info Message -->
            <div class="mb-6">
                <div class="flex items-start space-x-3 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500 text-xl mt-0.5"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Tidak masalah! Masukkan alamat email Anda dan kami akan mengirimkan link untuk mereset
                            password.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form wire:submit="sendPasswordResetLink" class="space-y-5">

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-semibold mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <x-text-input wire:model="email" id="email"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                            type="email" name="email" placeholder="nama@unjani.ac.id" required autofocus />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-lg shadow-md transition-all duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Kirim Link Reset Password
                    </button>
                </div>

                <!-- Back to Login Link -->
                <div class="text-center pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-600">
                        Ingat password Anda?
                        <a href="{{ route('login') }}"
                            class="text-emerald-600 hover:text-emerald-700 font-semibold transition-colors duration-200"
                            wire:navigate>
                            Kembali ke Login
                        </a>
                    </p>
                </div>

            </form>
        </div>

    </div>
</div>