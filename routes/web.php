<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\SesiController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// ================================================================
// GUEST ROUTES (BELUM LOGIN)
// ================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// ================================================================
// LOGOUT
// ================================================================
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================================================================
// PROTECTED ROUTES (HARUS LOGIN)
// ================================================================
Route::middleware(['auth'])->group(function () {
    
    // ===== DASHBOARD =====
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', fn() => redirect('/dashboard'));
    
    // ================================================================
    // PESERTA ROUTES - CUSTOM ROUTES HARUS DI ATAS RESOURCE!
    // ================================================================
    Route::get('/peserta/cari/{nis}', [PesertaController::class, 'cari'])->name('peserta.cari');
    Route::get('/peserta/import-form', [PesertaController::class, 'importForm'])->name('peserta.import.form');
    Route::post('/peserta/import', [PesertaController::class, 'import'])->name('peserta.import');
    Route::get('/peserta/export-template', [PesertaController::class, 'exportTemplate'])->name('peserta.export.template');
    Route::post('/peserta/cepat', [PesertaController::class, 'cepat'])->name('peserta.cepat');
    Route::delete('/peserta/hapus-semua', [PesertaController::class, 'hapusSemua'])->name('peserta.hapus-semua');
    
    // RESOURCE ROUTES (HARUS DI BAWAH CUSTOM ROUTES)
    Route::resource('peserta', PesertaController::class);
    
    // ================================================================
    // SESI ROUTES
    // ================================================================
    Route::resource('sesi', SesiController::class);
    Route::post('/sesi/{id}/toggle-active', [SesiController::class, 'toggleActive'])->name('sesi.toggle');
    
    // ================================================================
    // ABSENSI DIGITAL
    // ================================================================
    Route::get('/absensi/form', [AbsensiController::class, 'form'])->name('absensi.form');
    Route::post('/absensi/store', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::get('/absensi/log', [AbsensiController::class, 'log'])->name('absensi.log');
    
    // ================================================================
    // ABSENSI MANUAL (UNTUK PANITIA)
    // ================================================================
    Route::get('/absensi/manual', [AbsensiController::class, 'manual'])->name('absensi.manual');
    Route::post('/absensi/manual-store', [AbsensiController::class, 'manualStore'])->name('absensi.manual.store');
    
    // ================================================================
    // KIOSK MODE (UNTUK PESERTA)
    // ================================================================
    Route::get('/absensi/kiosk', [AbsensiController::class, 'kiosk'])->name('absensi.kiosk');
    Route::post('/absensi/kiosk/store', [AbsensiController::class, 'kioskStore'])->name('absensi.kiosk.store');
    Route::get('/absensi/counter', [AbsensiController::class, 'counter'])->name('absensi.counter');
    
    // ================================================================
    // EXPORT
    // ================================================================
    Route::get('/absensi/export-excel', [AbsensiController::class, 'exportExcel'])->name('absensi.export.excel');
    Route::get('/absensi/export-pdf', [AbsensiController::class, 'exportPdf'])->name('absensi.export.pdf');
    
    // ================================================================
    // EXPORT WORD (.docx) - BARU!
    // ================================================================
    Route::get('/absensi/export-word', [AbsensiController::class, 'exportWord'])->name('absensi.export.word');
});

// ================================================================
// NOTIFICATIONS
// ================================================================
Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

// ================================================================
// CLEAR CACHE (UNTUK DEBUG)
// ================================================================
Route::get('/clear-cache', function() {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize:clear');
    return '✅ Cache cleared!';
});

// ================================================================
// TEST ROUTE (UNTUK DEBUG - BISA DIHAPUS NANTI)
// ================================================================
Route::get('/test-import', function() {
    try {
        return view('peserta.import');
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine();
    }
});