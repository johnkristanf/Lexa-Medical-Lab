<?php

namespace App\Providers;

use App\Models\User;
use App\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use App\Models\MedicalSupplies;
use App\Models\Batch;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        //  Share global notifications (low stock + nearly expired) for all dashboards
        Inertia::share([
            'notifications' => function () {
                // Low stock supplies
                $lowStock = MedicalSupplies::with('stocks')
                    ->get()
                    ->filter(function ($supply) {
                        $critical = $supply->stocks->first()?->critical_stock ?? 10;
                        return $supply->quantity <= $critical;
                    })
                    ->values();

                // Nearly expired batches (within 30 days)
                $thresholdDate = Carbon::now()->addDays(30);
                $nearlyExpired = Batch::with('medicalSupply')
                    ->whereHas('medicalSupply', fn($q) => $q->whereNull('deleted_at'))
                    ->whereDate('expiration_date', '<=', $thresholdDate)
                    ->whereDate('expiration_date', '>=', Carbon::today())
                    ->get();

                return [
                    'lowStock' => $lowStock->count(),
                    'nearlyExpired' => $nearlyExpired->count(),
                ];
            },
        ]);
    }
}
