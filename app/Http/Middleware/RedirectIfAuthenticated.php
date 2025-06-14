<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Jika yang sudah login adalah seorang admin, arahkan ke dashboard admin
                if (Auth::user()->role === 'admin') {
                    return redirect('/admin/dashboard');
                }

                // Untuk user biasa, 
                // bisa diarahkan ke halaman utama SPA.
                return redirect('/'); 
            }
        }

        return $next($request);
    }
}