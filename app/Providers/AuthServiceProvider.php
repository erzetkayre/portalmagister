<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('admin', fn ($user) => $user->hasRole('admin'));
        Gate::define('koordinator', fn ($user) => $user->hasRole('koordinator'));
        Gate::define('dosen', fn ($user) => $user->hasRole('dosen'));
        Gate::define('mahasiswa', fn ($user) => $user->hasRole('mahasiswa'));
    }
}
