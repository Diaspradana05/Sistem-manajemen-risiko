<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getLoginFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label(__('Nama / Email'))
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }
    
    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        return [
            $login_type => $data['login'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

      public function getTitle(): string
    {
        return 'Sistem Manajemen Risiko'; // Ganti sesuai kebutuhan
    }

    public function getHeading(): string
{
    return 'Sistem Manajemen Risiko'; // Ganti dengan nama sistem kamu
}
}

