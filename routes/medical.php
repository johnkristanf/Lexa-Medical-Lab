<?php

use App\Http\Controllers\MedicalStaffController;
use App\Http\Controllers\MedicalSupplyController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth', 'verified', 'can:manage-medical'])->group(function () {

  
    //RENDER PATIENT DETAILS PAGE
    Route::get('/patient/Details/create', [MedicalSupplyController::class, 'patientDetailscreate'])
        ->name('patient.details.create');

    Route::post('/patient/Details/store', [MedicalSupplyController::class, 'patientDetailsStore'])
        ->name('patient.details.submit');

    //TEST TABLE DETAILS
    Route::get('/test/Details/create', [MedicalSupplyController::class, 'patientDetailscreate'])
        ->name('test.details.create');



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
    });


    //RENDER TEST TYPE PAGE




    // PROTECTED QUEUE ROUTES (MEDICAL STAFF SIDE)
    Route::get('/patient/queue', [MedicalStaffController::class, 'queuePage'])
        ->name('patient.queue');

    Route::put('/update/medical/queue', [MedicalStaffController::class, 'updateStatus'])
        ->name('medical.queue.update');
});
