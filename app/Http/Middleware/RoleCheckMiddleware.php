<?php

namespace App\Http\Middleware;

use Closure;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            return Inertia::render('error/UserInactive')->toResponse($request)->setStatusCode(423);
        }

        if (!$user->hasAnyRole($roles)) {
            return Inertia::render('error/ErrorPage',['status' => 403])->toResponse($request)->setStatusCode(403);
        }

        return $next($request);
    }
}
