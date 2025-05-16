<?php

use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

// Rutas protegidas para super_admin
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Gestión de permisos
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::post('/permissions/role', [PermissionController::class, 'updateRolePermissions'])->name('permissions.update-role');
});
