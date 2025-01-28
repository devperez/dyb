<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserPlatformController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route::get('/settings', [UserPlatformController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [UserPlatformController::class, 'update'])->name('settings.update');
    Route::get('/configuration', [UserPlatformController::class, 'index'])->name('configuration');

    Route::post('/schedule', [ScheduleController::class, 'store'])->name('scheduledMessages.store');
    Route::put('/scheduledMessages/{scheduledMessage}/update', [ScheduleController::class, 'update'])->name('scheduledMessages.update');
});

require __DIR__.'/auth.php';
