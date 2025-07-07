<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FirstLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $currentRoute = $request->route()->getName();
        $role = $user->role->nama_role ?? 'mahasiswa';

        // Route first login yang diizinkan
        $firstLoginRoutes = ['index.first.login', 'post.first.login'];
        $isFirstLoginRoute = in_array($currentRoute, haystack: $firstLoginRoutes);

        if ($user->first_login == 0) {
            // Belum first login - paksa ke halaman ganti password (kecuali logout)
            if (!$isFirstLoginRoute && $currentRoute !== 'logout') {
                return redirect()->route('index.first.login')
                    ->with('warning', 'Silakan ganti password terlebih dahulu');
            }
        } else {
            // Sudah first login - tidak boleh akses halaman first login lagi
            if ($isFirstLoginRoute) {
                return redirect()->route($role . '.dashboard')
                    ->with('info', 'Anda sudah pernah mengganti password');
            }
        }

        return $next($request);
    }
}
