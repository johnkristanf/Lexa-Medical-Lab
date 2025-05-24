<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware('guest')->group(function () {
    // REDIRECT TO AUTH LOGIN PAGE IN FIRST RENDER
    Route::get('/', function () {
        return redirect(route('login'));
    });

});

Route::get('/unauthorized', function () {
    return Inertia::render('Unauthorized');
})->name('unauthorized');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__. '/queue.php';
require __DIR__.'/medical.php';
require __DIR__.'/inventory.php';
require __DIR__.'/appointment.php';
