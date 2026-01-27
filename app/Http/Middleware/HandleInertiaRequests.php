<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use Illuminate\Http\Request;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Gate;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => $this->getUserInfo($request),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    public function getUserInfo(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            return [
                'user' => null,
                'can' => [],
                'program' => null,
            ];
        }

        $user->loadMissing(['roles', 'studyProgram']);
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'study_program' => $user->studyProgram->program_name
            ],
            'can' => [
                'admin' => Gate::allows('admin'),
                'koordinator' => Gate::allows('koordinator'),
                'dosen' => Gate::allows('dosen'),
                'mahasiswa' => Gate::allows('mahasiswa'),
            ],
            'program' => $user->studyProgram->db_connection,
        ];
    }
}
