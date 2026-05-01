<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsVendor
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login
        // 2. Cek apakah role user adalah vendor (idrole = 3)
        if (Auth::check() && Auth::user()->idrole == 3) {
            return $next($request);
        }

        // Jika bukan vendor, arahkan ke halaman lain dengan pesan error
        return redirect()->route('welcome')->with('error', 'Akses ditolak. Anda bukan vendor.');
    }
}