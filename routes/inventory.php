<?php

use App\Http\Controllers\MedicalSupplyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:manage-inventory-supplies'])->group(function () {

    // RENDER INVETORY SUPPLIES PAGE
    Route::get('/inventory/supplies', [MedicalSupplyController::class, 'inventory'])
        ->name('inventory.supplies');


    // INSERT SUPPLY REQUEST
    Route::post('/medical/supply/request/create', [MedicalSupplyController::class, 'medicalSupplyRequestCreate'])
        ->name('medical.request.create');

    // RENDER BATCH NUMBER PAGE
    Route::get('/medical/supply/batches', [MedicalSupplyController::class, 'batchNumbercreate'])
        ->name('inventory.supply.batches');

    // STORED DATA BATCH NUMBER
    // Route::post('/medical/supply/batch/store', [MedicalSupplyController::class, 'storeBatchNumber'])
    //     ->name('add.batch');

    // RENDER SUPPLY REQUEST PAGE
    Route::get('/medical/supply/request', [MedicalSupplyController::class, 'inventorySupplyRequest'])
        ->name('inventory.supply.request');


    // UPDATE SUPPLY REQUEST STATUS
    Route::post('/update/supply/request/status', [MedicalSupplyController::class, 'updateRequestStatus'])
        ->name('update.request.status');


    // SUPPLY INVENTORY ADDITION ENDPOINT
    Route::post('/supply/add', [MedicalSupplyController::class, 'store'])
        ->name('supply.add');


    // UPDATE REQUEST ENDPOINT
    Route::put('/update/supply/request', [MedicalSupplyController::class, 'update'])
        ->name('update.supply.request');
    //RENDER TO DASHBOARD PAGE
    Route::get('/medical/supply/dashboard/create', [MedicalSupplyController::class, 'dashboardSupplyCreate'])
        ->name('dashboard.supply.create');
});
