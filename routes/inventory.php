<?php

use App\Http\Controllers\MedicalSupplyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:manage-inventory-supplies'])->group(function () {

    // RENDER INVETORY SUPPLIES PAGE
    Route::get('/inventory/supplies', [MedicalSupplyController::class, 'inventory'])
        ->name('inventory.supplies');

    // RENDER INVETORY SUPPLIES REQUEST PAGE
    Route::get('/inventory/supply/requests', [MedicalSupplyController::class, 'requests'])
        ->name('inventory.supply.request');


    // SUPPLY INVENTORY ADDITION ENDPOINT
    Route::post('/supply/add', [MedicalSupplyController::class, 'store'])
        ->name('supply.add');


    // UPDATE REQUEST ENDPOINT
    Route::put('/update/supply/request', [MedicalSupplyController::class, 'update'])
        ->name('update.supply.request');
});
