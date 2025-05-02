<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'roles' => $request->user()->roles,

                    // Add permissions to be accessed on the frontend
                    'permissions' => [
                        'is_admin' => $request->user()->can('admin'),
                        'can_manage_medical' => $request->user()->can('manage-medical'),
                        'can_manage_inventory_supplies' => $request->user()->can('manage-inventory-supplies'),
                    ],
                ] : null,
            ],

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'queueData' => fn () => $request->session()->get('queueData'),
            ],
        ];
    }
}
