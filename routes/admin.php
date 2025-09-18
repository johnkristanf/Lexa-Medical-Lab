<?php


// ADMIN ROUTE

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\MedicalSupplyController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware(['auth', 'verified', 'can:admin'])->prefix('admin')->group(function () {


    // DASHBOARD PROTECTED ROUTES
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');


    // PATIENTS PROTECTED ROUTES
    Route::get('/patients', [PatientController::class, 'renderAdminPatients'])
        ->name('admin.patients');

    Route::get('/dashboard', [MedicalSupplyController::class, 'renderAdminDashboard'])
        ->name('admin.dashboard');


    // APPOINTMENTS PROTECTED ROUTES
    Route::get('/appointments', [AppointmentController::class, 'renderAdminAppointments'])
        ->name('admin.appointments');

    Route::put('/appointment-schedules/slots/{slotId}/status', [AppointmentController::class, 'updateScheduleStatus']);


    // USER PROTECTED ROUTES
    Route::get('/user', [RegisteredUserController::class, 'renderAdminUserPanel'])
        ->name('admin.user');
        
    Route::post('/user/add', [RegisteredUserController::class, 'store'])
        ->name('admin.user.add');

    Route::put('/users/{user}', [RegisteredUserController::class, 'update'])
        ->name('admin.user.update');
    Route::delete('/user/{user}', [RegisteredUserController::class, 'destroy'])
        ->name('admin.user.destroy');
});


Route::middleware(['auth', 'verified', 'can:manage-medical'])->prefix('admin')->group(function () {
    Route::put('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);
});
