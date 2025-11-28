<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

/**
 * Confirm the current user's password.
 */
public function confirmPassword(): void
{
    $this->validate([
        'password' => ['required', 'string'],
    ]);

    if (! Auth::guard('web')->validate([
        'email' => Auth::user()->email,
        'password' => $this->password,
    ])) {
        throw ValidationException::withMessages([
            'password' => __('auth.password'),
        ]);
    }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
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
                Konfirmasi Password
            </h2>
            <p class="text-sm text-gray-600">
                Verifikasi identitas Anda untuk melanjutkan
            </p>
        </div>

        <!-- Confirm Password Card -->
        <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">

            <!-- Info Message -->
            <div class="mb-6">
                <div class="flex items-start space-x-3 p-4 bg-amber-50 border border-amber-100 rounded-lg">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-amber-500 text-xl mt-0.5"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Ini adalah area aman dari aplikasi. Silakan konfirmasi password Anda sebelum melanjutkan.
                        </p>
                    </div>
                </div>
            </div>

            <form wire:submit="confirmPassword" class="space-y-5">

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-semibold mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <x-text-input wire:model="password" id="password"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                            type="password" name="password" placeholder="Masukkan password Anda" required
                            autocomplete="current-password" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Button -->
                <div class="pt-2">
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-lg shadow-md transition-all duration-300 transform hover:scale-[1.02] disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none">

                        <!-- Normal State -->
                        <span wire:loading.remove wire:target="confirmPassword" class="flex items-center">
                            <i class="fas fa-circle-check mr-2"></i>
                            Konfirmasi
                        </span>

                        <!-- Loading State -->
                        <span wire:loading wire:target="confirmPassword" class="flex items-center">
                            <i class="fas fa-circle-notch fa-spin mr-2"></i>
                            Memproses...
                        </span>
                    </button>
                </div>

                <!-- Cancel Link -->
                <div class="text-center pt-4 border-t border-gray-100">
                    <a href="{{ route('dashboard') }}"
                        class="text-sm text-gray-600 hover:text-gray-700 font-medium transition-colors duration-200"
                        wire:navigate>
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali ke Dashboard
                    </a>
                </div>

            </form>
        </div>

    </div>
</div>