<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Główna strona przekierowuje do Dashboardu
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Widok Dashboardu
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Ścieżki dla przycisków w Dashboardzie
Route::post('/dashboard/simulate', [DashboardController::class, 'simulateError'])->name('dashboard.simulate');
Route::post('/dashboard/clear', [DashboardController::class, 'clearLogs'])->name('dashboard.clear');