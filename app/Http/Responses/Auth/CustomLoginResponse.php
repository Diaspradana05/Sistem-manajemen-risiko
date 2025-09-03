<?php

namespace App\Http\Responses\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {
            return redirect()->intended(Filament::getPanel('admin')->getUrl());
        }

        return redirect()->intended(Filament::getPanel('user')->getUrl());
    }
}
