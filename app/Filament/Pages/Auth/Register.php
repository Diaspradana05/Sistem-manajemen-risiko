<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Filament\Forms\Components\View;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;

class Register extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form->schema([
            View::make('filament.pages.auth.logoblade'),

            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(User::class, 'email'),

            Forms\Components\TextInput::make('password')
                ->password()
                ->revealable()
                ->required()
                ->confirmed(),

            Forms\Components\TextInput::make('password_confirmation')
                ->password()
                ->revealable()
                ->label('Konfirmasi Password')
                ->required(),
        ]);
    }

    protected function handleRegistration(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('staf'); // Set default role

        return $user;
    }

    public function authenticate(User $user): void
    {
        // Kosongkan agar tidak login otomatis
    }

    public function register(): RegistrationResponse
    {
        $this->handleRegistration($this->form->getState());

        session()->flash('success', 'Registrasi berhasil! Silakan login.');

        return app(RegistrationResponse::class); // ✅ FIXED: jangan pakai ->to() atau redirect manual
    }

    public function getTitle(): string
    {
        return 'Sistem Manajemen Risiko';
    }

    public function getHeading(): string
    {
        return 'Buat akun';
    }
}
