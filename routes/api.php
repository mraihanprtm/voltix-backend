<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\PerangkatController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\PerangkatController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\SyncController;

// Rute untuk login/register awal (yang menghasilkan token Sanctum)
Route::post('/v1/auth/firebase-login-or-register', [AuthController::class, 'handleFirebaseLoginOrRegister']);

// Grup rute yang memerlukan autentikasi Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // RUTE UNTUK MENGAMBIL DATA USER YANG SEDANG LOGIN
    Route::get('/v1/user', function (Request $request) {
        return $request->user(); // Ini akan mengembalikan model User yang terautentikasi
    });

    // Rute untuk update profil
    Route::put('/v1/user/profile', [UserController::class, 'updateProfile']);

    // ... rute lain yang terproteksi ...

    Route::post('/sync', [SyncController::class, 'syncData']);
    Route::post('/push-changes', [SyncController::class, 'pushChanges']);
});
// Route untuk mendapatkan semua ruangan milik user
// Route::middleware('auth:sanctum')->group(function () {
    // Route untuk mendapatkan semua ruangan milik user
    Route::get('/ruangan', [RuanganController::class, 'index']);
    // Route untuk membuat ruangan baru
    Route::post('/ruangan', [RuanganController::class, 'store']);
    // Route untuk mendapatkan detail ruangan tertentu
    Route::get('/ruangan/{ruangan}', [RuanganController::class, 'show']);
    // Route untuk memperbarui ruangan tertentu
    Route::put('/ruangan/{ruangan}', [RuanganController::class, 'update']);
    // Route untuk menghapus ruangan tertentu
    Route::delete('/ruangan/{ruangan}', [RuanganController::class, 'destroy']);
    // Route::post('/sync', [SyncController::class, 'syncData']);
    // Route::post('/push-changes', [SyncController::class, 'pushChanges']);
// });

Route::apiResource('perangkat', PerangkatController::class);
Route::get('/ruangan/{ruangan}/perangkat', [RuanganController::class, 'getPerangkatByRuangan']);
Route::post('perangkat/{perangkat}/attach-ruangan', [PerangkatController::class, 'attachPerangkatToRuangan']);
