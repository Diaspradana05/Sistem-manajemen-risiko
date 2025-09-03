<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotUserRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        // daftar role yang boleh akses panel user
        $allowedRoles = ['manajer', 'direksi', 'staf', 'supervisor'];

        if (!Auth::user()->hasAnyRole($allowedRoles)) {
            return redirect('/');
        }

        return $next($request);
    }
}
