<?php


// ADMIN ROUTE

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalStaffController;
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

    Route::put('/appointment-schedules/{schedule}/status', [AppointmentController::class, 'updateScheduleStatus']);


    // USER PROTECTED ROUTES
    Route::get('/user', function () {
        return Inertia::render('Admin/User');
    })->name('admin.user');
});


Route::middleware(['auth', 'verified', 'can:manage-medical'])->prefix('admin')->group(function () {
    Route::put('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);
});
