<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthUser
{
    public function handle(Request $request, Closure $next)
    {
        // cek apakah session 'user' ada
        if (!$request->session()->has('user')) {
            return redirect('/login'); // redirect ke login kalau belum ada session
        }

        return $next($request);
    }
}
