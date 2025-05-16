<?php

use App\Http\Controllers\SupportDashboardController;
use Illuminate\Support\Facades\Route;

// Rutas protegidas para personal de soporte
Route::middleware(['auth', 'role:support,admin,super_admin'])->prefix('support')->name('support.')->group(function () {
    // Dashboard principal para soporte
    Route::get('/dashboard', [SupportDashboardController::class, 'index'])->name('dashboard');
});
