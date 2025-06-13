<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Middleware\IsAdmin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// RUTE OTENTIKASI
require __DIR__.'/auth.php';


// RUTE KHUSUS ADMIN
// Hanya user yang sudah login (auth) dan admin (IsAdmin::class) yang bisa mengakses ini.
Route::middleware(['auth', IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});


// RUTE CATCH-ALL UNTUK SPA (FRONTEND WEB)
Route::get('/{any?}', function () {
    if (File::exists(public_path('out/index.html'))) {
        return File::get(public_path('out/index.html'));
    }
    abort(404);
})->where('any', '^(?!api\/).*$');