<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('superadmin')) {
            abort(403, 'Anda tidak memiliki akses ke dashboard superadmin.');
        }

        return $next($request);
    }
}
