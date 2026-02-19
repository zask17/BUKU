<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|int  $roleId
     */
    public function handle(Request $request, Closure $next, $roleId): Response
    {
        // 1. Pastikan user sudah login 
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Cek idrole user sesuai dengan route 
        // Admin (1) atau Visitor (2)
        if (Auth::user()->idrole == $roleId) {
            return $next($request);
        }

        // 3. Jika tidak sesuai, kembalikan ke halaman home 
        return redirect('/home')->with('error', 'Anda tidak memiliki izin akses.');
    }
}
