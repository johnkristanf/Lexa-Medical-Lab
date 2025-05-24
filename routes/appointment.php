<?php

use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('/services/appointment', [AppointmentController::class, 'index'])
        ->name('services.appointment');
});