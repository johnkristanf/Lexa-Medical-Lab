<?php

use App\Http\Controllers\MedicalStaffController;
use App\Http\Controllers\MedicalSupplyController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth', 'verified', 'can:manage-medical'])->group(function () {

    Route::get('/medical/supply/request', [MedicalSupplyController::class, 'medicalSupplyRequest'])
        ->name('supply.request');

    Route::post('/medical/supply/request/create', [MedicalSupplyController::class, 'medicalSupplyRequestCreate'])
        ->name('medical.request.create');


    // PROTECTED QUEUE ROUTES (MEDICAL STAFF SIDE)
    Route::get('/patient/queue', [MedicalStaffController::class, 'queuePage'])
        ->name('patient.queue');

    Route::put('/update/medical/queue', [MedicalStaffController::class, 'updateStatus'])
        ->name('medical.queue.update');
});
