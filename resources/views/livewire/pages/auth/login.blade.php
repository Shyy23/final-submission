<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        $this->dispatch('login-success');
    }
}; ?>

<div
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-teal-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">

        <!-- Logo & Header Section -->
        <div class="text-center">
            <div class="flex justify-center">
                <a href="/" wire:navigate class=" mb-4">
                    <img src="{{ asset('assets/img/unjani.png') }}" alt="logo unjani" class="w-16 h-16">
                </a>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">
                Selamat Datang Kembali
            </h2>
            <p class="text-sm text-gray-600">
                Login untuk mengakses Submission System
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form wire:submit="login" class="space-y-6">

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-semibold mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <x-text-input wire:model="form.email" id="email"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                            type="email" name="email" placeholder="nama@unjani.ac.id" required autofocus
                            autocomplete="username" />
                    </div>
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-semibold mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <x-text-input wire:model="form.password" id="password"
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                            type="password" name="password" placeholder="Masukkan password" required
                            autocomplete="current-password" />
                    </div>
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label for="remember" class="inline-flex items-center cursor-pointer">
                        <input wire:model="form.remember" id="remember" type="checkbox"
                            class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500 cursor-pointer"
                            name="remember">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>

                    @if (Route::has('password.request'))
                    <a class="text-sm text-emerald-600 hover:text-emerald-700 font-medium transition-colors duration-200"
                        href="{{ route('password.request') }}" wire:navigate>
                        Lupa password?
                    </a>
                    @endif
                </div>

                <!-- Login Button -->
                <div>
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-lg shadow-md transition-all duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk
                    </button>
                </div>

                <!-- Register Link -->
                @if (Route::has('register'))
                <div class="text-center pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-600">
                        Belum punya akun?
                        <a href="{{ route('register') }}"
                            class="text-emerald-600 hover:text-emerald-700 font-semibold transition-colors duration-200"
                            wire:navigate>
                            Daftar Sekarang
                        </a>
                    </p>
                </div>
                @endif

            </form>
        </div>

        <!-- Footer Text -->
        <p class="text-center text-xs text-gray-500">
            © {{ date('Y') }} Submission System - Tugas Akhir Mahasiswa
        </p>

    </div>
</div>