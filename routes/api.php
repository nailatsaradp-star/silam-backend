<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\TrackingController;
use Illuminate\Support\Facades\Route;

// Publik (tanpa login)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::get('/tracking', TrackingController::class);

// Harus login (token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Semua role: lihat pesanan dan ubah status pengerjaan
    Route::get('/pesanan', [PesananController::class, 'index']);
    Route::get('/pesanan/{pesanan}', [PesananController::class, 'show']);
    Route::patch('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus']);

    // Administrator + kasir
    Route::middleware('role:administrator,kasir')->group(function () {
        Route::get('/layanan', [DataController::class, 'layanan']);
        Route::get('/pelanggan', [DataController::class, 'pelanggan']);
        Route::post('/pesanan', [PesananController::class, 'store']);
        Route::patch('/pesanan/{pesanan}/bayar', [PesananController::class, 'bayar']);
    });
});
