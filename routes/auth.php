<?php

use App\Http\Controllers\Auth\LoginController; // <-- Ganti controllernya ke yang kita buat
use Illuminate\Support\Facades\Route;

// Grup rute ini hanya untuk user yang belum login (tamu)
Route::middleware('guest')->group(function () {

    // Rute untuk menampilkan halaman form login
    Route::get('login', [LoginController::class, 'create'])
                ->name('login');

    // Rute untuk memproses data dari form login
    Route::post('login', [LoginController::class, 'store']);
});

// Grup rute ini hanya untuk user yang sudah login
Route::middleware('auth')->group(function () {

    // Rute untuk memproses logout
    Route::post('logout', [LoginController::class, 'destroy'])
                ->name('logout');
});