<?php

use App\Http\Controllers\MedicalSupplyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/medical/supply/inventory', [MedicalSupplyController::class, 'inventory'])
        ->name('medical.inventory');

    Route::post('/supply/add', [MedicalSupplyController::class, 'store'])
        ->name('supply.add');


    Route::get('/medical/supply/request', [MedicalSupplyController::class, 'medicalSupplyRequest'])
        ->name('medical.request');


    Route::post('/medical/supply/request/create', [MedicalSupplyController::class, 'medicalSupplyRequestCreate'])
        ->name('medical.request.create');
});
