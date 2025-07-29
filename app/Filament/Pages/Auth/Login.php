<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Filament\Actions\Action;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Placeholder;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('login')
                    ->label('Nama / Email')
                    ->required()
                    ->autocomplete()
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->revealable()
                    ->autocomplete('current-password')
                    ->extraInputAttributes(['tabindex' => 2]),

                       Placeholder::make('lupaPasswordLink')
                ->content(new HtmlString('
                    <div style="text-align: right; margin-top: -10px; margin-bottom: 10px;">
                        <a href="' . route('password.request') . '" font-size: 14px; text-decoration: underline;">
                            Lupa Password?
                        </a>
                    </div>
                '))
                ->disableLabel(),


                Checkbox::make('remember')
                    ->label(__('Ingat saya')),
                    
            ]);
    }

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
            'login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
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

        if ($user->hasRole('superadmin')) {
            Filament::setCurrentPanel(Filament::getPanel('admin'));
        } elseif ($user->hasRole('user')) {
            Filament::setCurrentPanel(Filament::getPanel('user'));
        }

        return app(LoginResponse::class);
    }

    public function getTitle(): string
    {
        return 'Sistem Manajemen Risiko';
    }

    public function getHeading(): string
    {
        return 'Sistem Manajemen Risiko';
    }


}
