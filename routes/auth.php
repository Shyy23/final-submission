<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// 1. Route untuk Guest (Belum Login)
Route::middleware('guest')->group(function () {
    Volt::route('register', 'pages.auth.register')
        ->name('register');

    Volt::route('login', 'pages.auth.login')
        ->name('login');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');
});

// 2. PERUBAHAN UTAMA DI SINI:
// Route Verifikasi dipindah KELUAR dari middleware 'auth'
// Agar user bisa klik link dari email tanpa harus login dulu.
Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1']) // Tetap pakai 'signed' agar link aman
    ->name('verification.verify');


// 3. Route yang butuh Login (Auth)
Route::middleware('auth')->group(function () {
    // Halaman "Harap Verifikasi Email" (muncul kalau user login tapi belum verif)
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    // Route confirm password
    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});