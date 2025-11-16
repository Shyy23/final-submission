<?php

namespace App\Http\Middleware;

use App\Models\Leader;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class CheckProfileCompletion
{
    // File: App\Http\Middleware\CheckProfileCompletion.php

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // 1. Jika user tidak login, atau adalah admin, biarkan saja.
        if (!$user || $user->hasRole('admin')) {
            return $next($request);
        }

        // 2. Izinkan akses ke semua rute yang wajib diizinkan (GET/POST)
        // Termasuk: profile (GET), logout (POST), verifikasi (GET/POST), dan update Livewire (POST).
        if (
            $request->routeIs('profile') ||
            $request->routeIs('logout') ||
            $request->routeIs('verification.*') ||
            $request->routeIs('livewire.update') // 🔥 Tambahkan pengecualian untuk rute POST Livewire
        ) {
            return $next($request);
        }

        // Logika pengecekan kelengkapan data HANYA berjalan jika rute yang diminta BUKAN rute pengecualian di atas.

        // Cek Mahasiswa
        if ($user->hasRole('mahasiswa')) {
            $isComplete = Student::where('user_id', $user->id)->exists();
            if (!$isComplete) {
                // Redirect paksa ke profile jika belum lengkap
                return redirect()->route('profile')->with('warning', 'Harap lengkapi data mahasiswa Anda sebelum melanjutkan.');
            }
        }

        // Cek Pimpinan
        if ($user->hasRole('pimpinan')) {
            $isComplete = Leader::where('user_id', $user->id)->exists();
            if (!$isComplete) {
                // Redirect paksa ke profile jika belum lengkap
                return redirect()->route('profile')->with('warning', 'Harap lengkapi data pimpinan Anda sebelum melanjutkan.');
            }
        }

        return $next($request);
    }
}
