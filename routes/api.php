<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;

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
});