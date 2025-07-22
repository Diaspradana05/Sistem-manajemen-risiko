<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleOrPermissionMiddleware
{
    public function handle($request, Closure $next, $roleOrPermission)
    {
        if (!Auth::check() || (!Auth::user()->hasRole($roleOrPermission) && !Auth::user()->can($roleOrPermission))) {
            abort(403);
        }
        return $next($request);
    }
}
