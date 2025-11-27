<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        // 1. Cari User berdasarkan ID dari URL
        $user = User::find($id);

        // 2. Jika user tidak ditemukan (misal ID asal-asalan), lempar ke login
        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Akun tidak ditemukan.');
        }

        // 3. Validasi Keamanan Link (Hash/Signature)
        // Memastikan link valid dan belum kedaluwarsa
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')
                ->with('error', 'Link verifikasi tidak valid atau rusak.');
        }

        // 4. Cek apakah sudah terverifikasi sebelumnya
        if ($user->hasVerifiedEmail()) {
            // Redirect ke login dengan pesan sukses (atau dashboard jika mau)
            return redirect()->route('login')
                ->with('status', 'Email sudah terverifikasi sebelumnya. Silakan login.');
        }

        // 5. Proses Verifikasi
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // 6. Redirect Sukses ke Halaman Login
        return redirect()->route('login')
            ->with('status', 'Email berhasil diverifikasi! Silakan login untuk melanjutkan.');
    }
}