<?php

namespace App\Livewire\Forms;

use App\Models\User; // TAMBAHKAN: Import User
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        // UBAH: Logika authenticate() diganti total
        $this->ensureIsNotRateLimited();

        // 1. Cek kredensial (username & password) tanpa login
        if (! Auth::validate($this->only(['email', 'password']))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        // 2. Kredensial benar, ambil data user
        $user = User::where('email', $this->email)->first();

        // 3. Cek apakah user sudah diverifikasi oleh Admin
        if (! $user->is_verified) {
            // Kita bisa beri pesan error yang lebih spesifik
            
            // Cek apakah email-nya sudah diverifikasi
            if (! $user->hasVerifiedEmail()) {
                throw ValidationException::withMessages([
                    'form.email' => 'Email Anda belum diverifikasi. Silakan cek inbox Anda.',
                ]);
            }

            // Jika email sudah, tapi admin belum
            throw ValidationException::withMessages([
                'form.email' => 'Akun Anda sedang menunggu persetujuan Admin.',
            ]);
        }
        
        // 4. Jika user ada, password benar, DAN is_verified = true
        // Baru kita login-kan
        Auth::attempt($this->only(['email', 'password']), $this->remember);

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
// ... (Sisa file ini tetap sama) ...
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}