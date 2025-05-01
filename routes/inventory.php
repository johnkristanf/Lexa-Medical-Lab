<?php

use App\Http\Controllers\MedicalSupplyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:manage-inventory-supplies'])->group(function () {

    Route::get('/inventory/supplies', [MedicalSupplyController::class, 'inventory'])
        ->name('inventory.supplies');

    Route::get('/inventory/supply/requests', [MedicalSupplyController::class, 'requests'])
        ->name('inventory.supply.request');

    Route::post('/supply/add', [MedicalSupplyController::class, 'store'])
        ->name('supply.add');
});
