<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Proses reset password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Redirect sesuai role setelah reset berhasil
        if ($status === Password::PASSWORD_RESET) {
            $user = User::where('email', $request->email)->first();

        // Sesuaikan ini dengan role yang kamu gunakan
if ($user?->hasRole('superadmin')) {
    return redirect('/admin/login')->with('status', __($status));
} elseif ($user?->hasAnyRole(['staf', 'direksi', 'manajer', 'supervisor'])) {
    return redirect('/user/login')->with('status', __($status));
}

// Fallback jika tidak ada role
return redirect('/login')->with('status', __($status));
        }


        // Jika gagal reset
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}

