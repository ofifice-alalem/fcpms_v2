<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

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
        if ($request->user()) {
            try {
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'web']);
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'consultant', 'guard_name' => 'web']);
            } catch (\Throwable $e) {
                // Ignore if DB temporary locked or migrating
            }
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'username' => $request->user()->username ?? null,
                    'email' => $request->user()->email,
                    'roles' => method_exists($request->user(), 'getRoleNames') ? $request->user()->getRoleNames() : [],
                    'permissions' => method_exists($request->user(), 'getAllPermissions') ? $request->user()->getAllPermissions()->pluck('name') : [],
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
        ]);
    }
}
