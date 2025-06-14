<?php

namespace App\Http\Middleware; // <-- PASTIKAN NAMESPACE INI BENAR

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Pastikan Auth di-import
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login DAN rolenya adalah admin
        if (Auth::check() && Auth::user()->role == 'admin') {
            // Jika ya, lanjutkan request
            return $next($request);
        }

        // Jika tidak, tendang ke halaman login dengan pesan error
        return redirect('/login')->withErrors(['email' => 'You do not have administrative access.']);
    }
}