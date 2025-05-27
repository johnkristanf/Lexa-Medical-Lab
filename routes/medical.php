<?php

use App\Http\Controllers\MedicalStaffController;
use App\Http\Controllers\MedicalSupplyController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth', 'verified', 'can:manage-medical'])->group(function () {


    Route::get('/medical/supply/request', [MedicalSupplyController::class, 'medicalSupplyRequest'])
        ->name('supply.request');

    Route::post('/medical/supply/request/create', [MedicalSupplyController::class, 'medicalSupplyRequestCreate'])
        ->name('medical.request.create');

    //RENDER PATIENT DETAILS PAGE
    Route::get('/patient/Details/create', [MedicalStaffController::class, 'patientDetailscreate'])
        ->name('patient.details.create');

    Route::post('/patient/Details/store', [MedicalStaffController::class, 'patientDetailsStore'])
        ->name('patient.details.submit');



    //RENDER  TEST CATEGORY PAGE
    Route::prefix('test/Category')->group(function () {

        Route::get('/create', [MedicalStaffController::class, 'testCategoryCreate'])
            ->name('test.category.create');

        Route::post('/store', [MedicalStaffController::class, 'testCategoryStore'])
            ->name('test.category.submit');

        Route::delete('/delete{id}', [MedicalStaffController::class, 'testCategoryDelete'])
            ->name('test.category.delete');

        Route::post('/test/types/store', [MedicalStaffController::class, 'testTypeStore'])
            ->name('test.types.submit');

        Route::post('/test/tore', [MedicalStaffController::class, 'testStore'])
            ->name('test.submit');

        Route::get('/test-details/create', [MedicalStaffController::class, 'testDetailsCreate'])
            ->name('test.details.create');

        Route::get('/test-details/{id}/print', [MedicalStaffController::class, 'print'])
            ->name('print.test.details');
    });


    //RENDER TEST TYPE PAGE




    // PROTECTED QUEUE ROUTES (MEDICAL STAFF SIDE)
    Route::get('/patient/queue', [MedicalStaffController::class, 'queuePage'])
        ->name('patient.queue');

    Route::put('/update/medical/queue', [MedicalStaffController::class, 'updateStatus'])
        ->name('medical.queue.update');
});
