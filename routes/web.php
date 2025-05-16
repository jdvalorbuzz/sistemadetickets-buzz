<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketRatingController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\SupportDashboardController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas de autenticación
Route::post('/logout', function() {
    Auth::logout();
    return redirect('/admin/login');
})->middleware('auth')->name('logout');

// Rutas para la calificación de tickets
Route::middleware('auth')->group(function () {
    Route::get('/tickets/{ticket}/rate', [TicketRatingController::class, 'show'])->name('tickets.rate.show');
    Route::post('/tickets/{ticket}/rate', [TicketRatingController::class, 'store'])->name('tickets.rate.store');
    
    // Rutas para exportación de informes (solo para super_admin)
    Route::get('/reports/export/pdf', [ReportExportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export/excel', [ReportExportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/csv', [ReportExportController::class, 'exportCsv'])->name('reports.export.csv');
});

// Rutas protegidas para personal de soporte
Route::middleware(['auth'])->prefix('support')->name('support.')->group(function () {
    // Dashboard principal para soporte
    Route::get('/dashboard', [SupportDashboardController::class, 'index'])->name('dashboard');
    
    // Gestión de tickets para soporte
    Route::get('/tickets/{ticket}/preview', [SupportTicketController::class, 'preview'])->name('ticket.preview');
    Route::post('/tickets/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('ticket.reply');
    Route::post('/tickets/{ticket}/take', [SupportTicketController::class, 'takeTicket'])->name('ticket.take');
    Route::post('/tickets/{ticket}/release', [SupportTicketController::class, 'releaseTicket'])->name('ticket.release');
    Route::post('/tickets/{ticket}/close', [SupportTicketController::class, 'closeTicket'])->name('ticket.close');
    Route::post('/tickets/{ticket}/reopen', [SupportTicketController::class, 'reopenTicket'])->name('ticket.reopen');
});
