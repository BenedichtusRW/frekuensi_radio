<?php

use App\Http\Controllers\MonitoringController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MonitoringController::class, 'dashboard'])->name('dashboard');
Route::get('/laporan', [MonitoringController::class, 'index'])->name('monitoring.index');
Route::get('/laporan/export', [MonitoringController::class, 'exportLaporan'])->name('monitoring.export');

Route::get('/input', [MonitoringController::class, 'create'])->name('monitoring.create');
Route::post('/input', [MonitoringController::class, 'store'])->name('monitoring.store');
Route::get('/input/{id}/edit', [MonitoringController::class, 'edit'])->name('monitoring.edit');
Route::put('/input/{id}', [MonitoringController::class, 'update'])->name('monitoring.update');
Route::delete('/input/{id}', [MonitoringController::class, 'destroy'])->name('monitoring.destroy');
