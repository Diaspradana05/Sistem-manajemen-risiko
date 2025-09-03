<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // Tampilkan halaman login (Blade custom)
    public function showLoginForm()
    {
        return view('filament.pages.auth.background'); // Blade custom glassmorphism
    }

    // Logic autentikasi dari Login.php
    public function login(Request $request)
    {
        $data = $request->validate([
            'login'    => ['required'],
            'password' => ['required'],
        ]);

        $loginType = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $user = User::where($loginType, $data['login'])->first();

        if (! $user || ! Hash::check($data['password'], (string) $user->password)) {
            return back()->withErrors([
                'login' => 'Login gagal. Email/Nama atau password salah.',
            ])->onlyInput('login');
        }

        // Login user
        Auth::login($user, $request->filled('remember'));

        // Set panel Filament sesuai role
        if ($user->hasRole('superadmin')) {
            Filament::setCurrentPanel(Filament::getPanel('admin'));
            return redirect('/admin');
        } elseif ($user->hasRole('user')) {
            Filament::setCurrentPanel(Filament::getPanel('user'));
            return redirect('/user');
        }

        // fallback
        return redirect('/'); 
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
