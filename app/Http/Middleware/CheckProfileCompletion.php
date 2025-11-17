<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response; // Import Response

class CheckProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. Jika user tidak login, biarkan saja.
        if (!$user) {
            return $next($request);
        }

        // 2. Izinkan akses ke rute-rute 'wajib'
        // Rute-rute ini harus bisa diakses bahkan jika profil belum lengkap.
        if (
            $request->routeIs('dashboard') || // Dashboard untuk menampilkan peringatan
            $request->routeIs('profile') ||    // Halaman untuk melengkapi profil
            $request->routeIs('logout') ||
            $request->routeIs('verification.*') ||
            $request->routeIs('livewire.update') // Penting untuk Livewire
        ) {
            return $next($request);
        }

        // 3. Gunakan accessor yang sudah kita buat
        // Pengecekan ini HANYA berjalan untuk rute selain yang diizinkan di atas
        // (Contoh: 'submission.create', 'submission.index', dll)
        if (!$user->is_profile_complete) {

            // Tentukan pesan peringatan berdasarkan role
            $message = 'Harap lengkapi profil Anda sebelum melanjutkan.';
            if ($user->hasRole('mahasiswa')) {
                $message = 'Harap lengkapi data NIM dan Program Studi Anda sebelum melanjutkan.';
            } elseif ($user->hasRole('pimpinan')) {
                $message = 'Harap lengkapi data NID dan Jabatan Anda sebelum melanjutkan.';
            }

            // Redirect paksa ke profile jika belum lengkap
            return redirect()->route('profile')->with('warning', $message);
        }

        // 4. Jika semua lolos, lanjutkan ke rute yang dituju
        return $next($request);
    }
}