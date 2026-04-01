<?php

use App\Http\Controllers\Guardian\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Guardian\DashboardController;
use App\Http\Controllers\Guardian\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('guardian')->name('guardian.')->group(function () {

    // مسارات الضيف
    Route::middleware('guest:guardian')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    });

    // مسارات ولي الأمر المسجّل
    Route::middleware('auth.guardian')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});

