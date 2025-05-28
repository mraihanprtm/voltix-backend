<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\ItemController; // Jika masih digunakan
use App\Http\Controllers\Api\UserController; // Atau AuthController
use App\Http\Controllers\Api\RuanganController; // Import RuanganController

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth.firebase')->group(function () {
    // Route::apiResource('items', ItemController::class);

    Route::match(['GET', 'POST'], '/auth/user/sync', [UserController::class, 'syncOrGetCurrentUser'])->name('auth.user.sync');
    
    // Rute untuk Ruangan Resource
    Route::apiResource('ruangan', RuanganController::class);

    // Rute untuk fitur lain (Perangkat, Simulasi, dll.) akan ditambahkan di sini
});