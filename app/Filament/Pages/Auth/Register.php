<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Register extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form->schema([
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

        // Berikan role default "user"
        $user->assignRole('user');

        return $user;
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
