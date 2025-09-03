<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Actions\Action;

class Login extends BaseLogin
{
    // Hilangkan heading & subheading bawaan Filament
    protected ?string $heading = '';
    protected ?string $subheading = '';
    
    public function form(Form $form): Form { 
        return $form->schema([ // Inject custom CSS
    Placeholder::make('')->content(new HtmlString('
    <style>
       body {
    position: relative;
    background: url("' . asset("image/rumahsakitsemen.jpg") . '") center/cover no-repeat fixed !important;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
}

/* Overlay */
body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        rgba(0, 0, 0, 0.5),   /* atas lebih gelap */
        rgba(0, 0, 0, 0.2)    /* bawah lebih terang */
    );
    z-index: 0;
}



        .particle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    animation: float 20s linear infinite;
    z-index: 0;
}

.particle:nth-child(1) {
    width: 80px; height: 80px;
    top: 20%; left: 10%;
    animation-duration: 25s;
}
.particle:nth-child(2) {
    width: 120px; height: 120px;
    top: 60%; left: 70%;
    animation-duration: 30s;
}

@keyframes float {
    0% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-50px) rotate(180deg); }
    100% { transform: translateY(0) rotate(360deg); }
}


        /* Hilangkan heading bawaan */
        .fi-simple-header {
            display: none !important;
        }

        /* ===== Animasi Card/Login Container ===== */
        .fi-simple-main {
            animation: fadeSlideUp 1s ease-out;
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.7) !important;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            transform: translateY(30px);
        }

        @keyframes fadeSlideUp {
            0% {
                opacity: 0;
                transform: translateY(60px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Input field */
        .fi-simple-main input {
            background: rgba(255,255,255,0.08) !important;
            border: 1px solid rgba(255,255,255,0.3) !important;
            color: #000 !important;          
            border-radius: 10px !important;
            font-size: 1.05rem !important;
            padding: 0.6rem 1rem !important;
            height: 52px !important;
            transition: all 0.3s ease;
        }

        /* Hover + Focus efek glowing */
        .fi-simple-main input:focus {
            outline: none !important;
            border-color: #2563eb !important;
            box-shadow: 0 0 10px rgba(37, 99, 235, 0.6) !important;
            transform: scale(1.02);
        }

        /* Label */
        .fi-simple-main label {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: black !important;
            margin-bottom: 0.5rem !important;
            display: block;
        }

        .fi-simple-main input[type="checkbox"] { 
        appearance: auto !important; 
        -webkit-appearance: checkbox !important; 
        -moz-appearance: checkbox !important; 
        background-color: #fff !important; /* background putih */
        border: 1px solid #ccc !important; 
        width: 18px !important; 
        height: 18px !important; 
        cursor: pointer; }

        /* Tombol login */
        .fi-simple-main .filament-button {
            background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
            color: #fff !important;
            font-weight: bold;
            border-radius: 10px !important;
            width: 100%;
            height: 52px !important;
            font-size: 1.1rem !important;
            transition: all 0.3s ease;
        }

        .fi-simple-main .filament-button:hover {
            background: linear-gradient(135deg, #1d4ed8, #2563eb) !important;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(37, 99, 235, 0.4);
        }

        /* Link lupa password */
        .fi-simple-card a {
            color: #fff !important;
            text-decoration: underline;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .fi-simple-card a:hover {
            color: #2563eb !important;
        }

        /* Logo */
        .login-logo {
    display: flex;
    justify-content: center;  /* posisikan horizontal center */
    align-items: center;      /* posisikan vertical center */
    margin-bottom: 1rem;
}

        .login-logo img {
            max-width: 20%;
            height: auto;
            animation: fadeZoom 1.2s ease;
        }

        @keyframes fadeZoom {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Heading */
        .login-heading {
            font-weight: bold;
            font-size: 1.5rem;
            color: black;
            margin-bottom: 0.5rem;
            text-align: center;
            animation: fadeSlideUp 1.2s ease-out;
        }

        .login-subheading {
            font-size: 1.5rem;
            color: rgba(0,0,0,0.8);
            text-align: center;
            margin-bottom: 1rem;
            font-weight: bold;
            animation: fadeSlideUp 1.4s ease-out;
        }
    </style>
')),

Placeholder::make('')->content(new HtmlString('
    <div style="position: absolute; top: 1rem; left: 1rem;">
        <a href="' . url('/') . '" style="color: #2563eb; font-size: 0.9rem; text-decoration: none;">
            ← Kembali
        </a>
    </div>
')),





            // Logo + Judul custom
            Placeholder::make('')->content(new HtmlString('
                <h2 class="login-heading">
        Sistem Manajemen Risiko
    </h2>
    <p class="login-subheading">
        Login
    </p>

                <div class="login-logo">
                    <img src="' . asset("image/PT Cipta Nirmala.png") . '" alt="Logo">
                </div>
                
            ')),

            TextInput::make('login')
                    ->label(new HtmlString('<span style="font-size:1.2rem; font-weight:700;">Nama / Email</span>'))

                ->required()
                ->autofocus(),

            TextInput::make('password')
    ->label(new HtmlString('<span style="font-size:1.2rem; font-weight:700;">Password</span>'))
    ->password()
    ->required()
    ->revealable()
    ->autofocus(),


Checkbox::make('remember')
    ->label(new HtmlString('<span style="font-size:1.2rem; font-weight:600;">Ingat saya</span>'))
    ->extraAttributes([
        'style' => 'font-size:1rem;'
    ]),

Placeholder::make('')
    ->content(new HtmlString('
        <div style="text-align: right; margin-top: 0.5rem;">
            <a href="' . route('password.request') . '" 
               style="font-size:1.2rem; text-decoration:none; color: #000;; font-weight:600;">
                Lupa Password?
            </a>
        </div>
    ')),

        ]);
    }

    

    // === Authentication tetap sama ===
    protected function getCredentialsFromFormData(array $data): array
    {
        $loginType = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        return [
            $loginType => $data['login'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
{
    throw ValidationException::withMessages([
        'data.login' => 'Username atau password salah, silakan coba lagi.',
    ]);
}


        protected function getFormActions(): array
{
    return [
        Action::make('login')
            ->label('Masuk')
            ->submit('authenticate')
            ->extraAttributes([
                'class' => 'w-full py-3 text-xl font-bold',
            ]),
    ];
}

public function authenticate(): LoginResponse
{
    $data = $this->form->getState();

    $loginType = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
    $user = User::where($loginType, $data['login'])->first();

    if (! $user || ! Hash::check($data['password'], (string) $user->password)) {
        $this->throwFailureValidationException();
    }

    auth()->login($user, $data['remember'] ?? false);

    // Arahkan panel berdasarkan role
    if ($user->hasRole('superadmin')) {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    } elseif ($user->hasAnyRole(['staf', 'manajer', 'direksi', 'supervisor'])) {
        Filament::setCurrentPanel(Filament::getPanel('user'));
    }

    session()->flash('success', 'Login berhasil. Selamat datang, ' . $user->name . '!');

    return app(LoginResponse::class);
}
}
