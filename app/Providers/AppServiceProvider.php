<?php

namespace App\Providers;

use App\Models\Batch;
use App\Models\MedicalSupplies;
use Carbon\Carbon;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
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
                // Low stock supplies - return detailed data
                $lowStock = MedicalSupplies::with('stocks')
                    ->get()
                    ->filter(function ($supply) {
                        $critical = $supply->stocks->first()?->critical_stock ?? 10;

                        return $supply->quantity <= $critical;
                    })
                    ->map(function ($supply) {
                        return [
                            'id' => $supply->id,
                            'brand_name' => $supply->brand_name,
                            'quantity' => $supply->quantity,
                            'critical_stock' => $supply->stocks->first()?->critical_stock ?? 10,
                            'unit' => $supply->unit,
                        ];
                    })
                    ->values();

                // Nearly expired batches (within 30 days) - return detailed data
                $thresholdDate = Carbon::now()->addDays(30);
                $nearlyExpired = Batch::with('medicalSupply')
                    ->whereHas('medicalSupply', fn ($q) => $q->whereNull('deleted_at'))
                    ->whereDate('expiration_date', '<=', $thresholdDate)
                    ->whereDate('expiration_date', '>=', Carbon::today())
                    ->get()
                    ->map(function ($batch) {
                        return [
                            'id' => $batch->id,
                            'supply_name' => $batch->medicalSupply->brand_name,
                            'batch_number' => $batch->batch_number,
                            'expiration_date' => $batch->expiration_date,
                            'quantity' => $batch->quantity,
                        ];
                    });

                return [
                    'lowStock' => $lowStock,
                    'nearlyExpired' => $nearlyExpired,
                ];
            },
        ]);
    }
}
