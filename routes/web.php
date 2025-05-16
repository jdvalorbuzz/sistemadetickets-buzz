<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketRatingController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas para la calificación de tickets
Route::middleware('auth')->group(function () {
    Route::get('/tickets/{ticket}/rate', [TicketRatingController::class, 'show'])->name('tickets.rate.show');
    Route::post('/tickets/{ticket}/rate', [TicketRatingController::class, 'store'])->name('tickets.rate.store');
    
    // Rutas para exportación de informes (solo para super_admin)
    Route::get('/reports/export/pdf', [ReportExportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export/excel', [ReportExportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/csv', [ReportExportController::class, 'exportCsv'])->name('reports.export.csv');
});
