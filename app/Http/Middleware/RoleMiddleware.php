<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan user sudah login
        if (!auth()->check()) {
            return redirect('login');
        }

        // 2. Cek apakah role user saat ini ada di dalam daftar role yang diizinkan
        if (!in_array(auth()->user()->role, $roles)) {
            // Jika tidak punya izin, tendang dengan error 403 (Forbidden)
            abort(403, 'Akses Ditolak! Anda tidak memiliki izin untuk melihat halaman ini.');
        }

        return $next($request);
    }
}
