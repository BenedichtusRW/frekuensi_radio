<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MonitoringController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    
    // Route untuk menangani link reset password dari email
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Monitoring Routes
Route::middleware(['auth', 'no-cache', 'check-active'])->group(function () {
    
    // Halaman Lengkapi Profil (Bebas dari Check-Profile Middleware untuk mencegah Infinite Loop)
    Route::get('/complete-profile', [\App\Http\Controllers\ProfileController::class, 'showCompleteProfile'])->name('profile.complete');
    Route::post('/complete-profile', [\App\Http\Controllers\ProfileController::class, 'storeCompleteProfile'])->name('profile.store');

    Route::middleware(['check-profile'])->group(function () {
        Route::get('/', function () {
            return redirect()->route('dashboard');
        });

        Route::get('/dashboard', [MonitoringController::class, 'dashboard'])->name('dashboard');
        Route::get('/pengaturan', [MonitoringController::class, 'settings'])->name('settings');
        Route::post('/2fa/generate', [MonitoringController::class, 'generate2faQr'])->name('2fa.generate');
        Route::post('/2fa/enable', [MonitoringController::class, 'enable2fa'])->name('2fa.enable');
        Route::post('/2fa/toggle', [MonitoringController::class, 'toggle2fa'])->name('2fa.toggle');
        Route::post('/2fa/reset', [MonitoringController::class, 'reset2fa'])->name('2fa.reset');
        Route::post('/update-security', [MonitoringController::class, 'updateSecurity'])->name('security.update');
        
        Route::prefix('laporan')->name('monitoring.')->group(function () {
            Route::get('/', [MonitoringController::class, 'index'])->name('index');
            Route::get('/export', [MonitoringController::class, 'exportLaporan'])->name('export');
        });

        Route::prefix('input')->name('monitoring.')->group(function () {
            Route::get('/', [MonitoringController::class, 'create'])->name('create');
            Route::post('/', [MonitoringController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [MonitoringController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MonitoringController::class, 'update'])->name('update');
            Route::delete('/bulk-delete', [MonitoringController::class, 'bulkDestroy'])->name('bulk-destroy');
            Route::delete('/delete-all', [MonitoringController::class, 'deleteAll'])->name('delete-all')->middleware('role:super_admin');
            Route::delete('/{id}', [MonitoringController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('role:super_admin')->group(function () {
            Route::prefix('users')->name('users.')->group(function () {
                Route::post('/', [\App\Http\Controllers\UserController::class, 'store'])->name('store');
                Route::put('/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('update');
                Route::delete('/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('destroy');
                Route::post('/{user}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->name('toggle-status');
                Route::post('/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'sendResetLink'])->name('reset-password');
            });

            Route::prefix('master-data')->name('master-data.')->group(function () {
                Route::get('/', [\App\Http\Controllers\MasterDataController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\MasterDataController::class, 'store'])->name('store');
                Route::put('/{id}', [\App\Http\Controllers\MasterDataController::class, 'update'])->name('update');
                Route::delete('/{id}', [\App\Http\Controllers\MasterDataController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/toggle-status', [\App\Http\Controllers\MasterDataController::class, 'toggleStatus'])->name('toggle-status');
            });
        });
    });
});
