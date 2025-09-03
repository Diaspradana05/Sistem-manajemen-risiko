<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotSuperadmin
{
    public function handle(Request $request, Closure $next)
    {


        // Kalau sudah login tapi bukan superadmin
        if (!Auth::user()->hasRole('superadmin')) {
            return redirect('/');
        }

        return $next($request);
    }
}
