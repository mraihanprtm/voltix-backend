<?php

// volte-backend/routes/web.php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log; // Tambahkan ini

Route::get('/{any?}', function () {
    if (File::exists(public_path('out/index.html'))) { // Arahkan ke out/index.html
        return File::get(public_path('out/index.html'));
    }
    abort(404);
})->where('any', '^(?!api\/).*$');
