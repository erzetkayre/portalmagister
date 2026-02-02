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
        $route = $request->route();
        $currentRoute = $route?->getName();

        // Allowed routes during first login
        $allowedRoutes = ['create.first.login', 'post.first.login', 'logout'];

        if ($user->first_login == 0) {
            if (!in_array($currentRoute, $allowedRoutes)) {
                return redirect()->route('create.first.login')
                    ->with('warning', 'Silakan ganti password terlebih dahulu');
            }
        } else {
            if (in_array($currentRoute, ['create.first.login', 'post.first.login'])) {
                return redirect()->route( 'dashboard');}
        }

        return $next($request);
    }

}
