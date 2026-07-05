<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\SesiController;
use Illuminate\Support\Facades\Route;

// Guest routes (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes (harus login)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', fn() => redirect('/dashboard'));
    
 // Hapus semua data peserta
    Route::delete('/peserta/hapus-semua', [PesertaController::class, 'hapusSemua'])->name('peserta.hapus-semua');
    // ===== PESERTA ROUTES =====
    // CUSTOM ROUTES HARUS DI ATAS ROUTE {id}
    Route::get('/peserta/cari/{nis}', [PesertaController::class, 'cari'])->name('peserta.cari');
    Route::get('/peserta/import-form', [PesertaController::class, 'importForm'])->name('peserta.import.form');
    Route::post('/peserta/import', [PesertaController::class, 'import'])->name('peserta.import');
    Route::get('/peserta/export-template', [PesertaController::class, 'exportTemplate'])->name('peserta.export.template');
    
    // RESOURCE ROUTES
    Route::get('/peserta', [PesertaController::class, 'index'])->name('peserta.index');
    Route::get('/peserta/create', [PesertaController::class, 'create'])->name('peserta.create');
    Route::post('/peserta', [PesertaController::class, 'store'])->name('peserta.store');
    Route::get('/peserta/{id}', [PesertaController::class, 'show'])->name('peserta.show');
    Route::get('/peserta/{id}/edit', [PesertaController::class, 'edit'])->name('peserta.edit');
    Route::put('/peserta/{id}', [PesertaController::class, 'update'])->name('peserta.update');
    Route::delete('/peserta/{id}', [PesertaController::class, 'destroy'])->name('peserta.destroy');
    
    // Sesi
    Route::resource('sesi', SesiController::class);
    Route::post('/sesi/{id}/toggle-active', [SesiController::class, 'toggleActive'])->name('sesi.toggle');
    
    // Absensi
    Route::get('/absensi/form', [AbsensiController::class, 'form'])->name('absensi.form');
    Route::post('/absensi/store', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::get('/absensi/log', [AbsensiController::class, 'log'])->name('absensi.log');
    
    // Export
    Route::get('/absensi/export-excel', [AbsensiController::class, 'exportExcel'])->name('absensi.export.excel');
    Route::get('/absensi/export-pdf', [AbsensiController::class, 'exportPdf'])->name('absensi.export.pdf');

    // Absensi Manual (untuk panitia)
    Route::get('/absensi/manual', [AbsensiController::class, 'manual'])->name('absensi.manual');
    Route::post('/absensi/manual-store', [AbsensiController::class, 'manualStore'])->name('absensi.manual.store');

    // ===== KIOSK MODE (Untuk Peserta) =====
    Route::get('/absensi/kiosk', [AbsensiController::class, 'kiosk'])->name('absensi.kiosk');
    Route::post('/absensi/kiosk/store', [AbsensiController::class, 'kioskStore'])->name('absensi.kiosk.store');
    Route::get('/absensi/counter', [AbsensiController::class, 'counter'])->name('absensi.counter');

    // ===== NOTIFICATIONS =====
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/reset', [NotificationController::class, 'resetNotifications'])->name('notifications.reset');
});


