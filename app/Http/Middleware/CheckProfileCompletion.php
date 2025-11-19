<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileCompletion
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // ============================================================
        // FIX: PENGECUALIAN LIVEWIRE HARUS LEBIH LUAS
        // ============================================================
        if (
            $request->routeIs('dashboard') ||
            $request->routeIs('profile') ||
            $request->routeIs('logout') ||
            $request->routeIs('verification.*') ||
            
            // GANTI INI: Jangan hanya livewire.update, tapi SEMUA jalur Livewire
            // Agar '/livewire/upload-file' (upload) dan '/livewire/preview-file' (preview) lolos
            $request->is('livewire/*') || 
            $request->routeIs('livewire.*')
        ) {
            return $next($request);
        }
        // ============================================================

        if (!$user->is_profile_complete) {

            $message = 'Harap lengkapi profil Anda sebelum melanjutkan.';
            if ($user->hasRole('mahasiswa')) {
                $message = 'Harap lengkapi data NIM dan Program Studi Anda sebelum melanjutkan.';
            } elseif ($user->hasRole('pimpinan')) {
                $message = 'Harap lengkapi data NID dan Jabatan Anda sebelum melanjutkan.';
            }

            // Redirect paksa ke profile jika belum lengkap
            return redirect()->route('profile')->with('warning', $message);
        }

        return $next($request);
    }
}