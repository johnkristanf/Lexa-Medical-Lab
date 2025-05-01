<?php

use App\Http\Controllers\MedicalStaffController;
use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/queue/create', [QueueController::class, 'create'])->name('queue.create');

    Route::post('/queue/store', [QueueController::class, 'store'])->name('queue.store');
    Route::get('/queue/dashboard', [QueueController::class, 'dashboard'])->name('queue.dashboard');
});
