<?php

namespace App\Http\Responses\Auth;

use Laravel\Fortify\Contracts\PasswordResetResponse;
use Illuminate\Http\RedirectResponse;

class CustomPasswordResetResponse implements PasswordResetResponse
{
    public function toResponse($request): RedirectResponse
    {
        return redirect()->route('login')->with('status', trans('passwords.reset'));
    }
}
