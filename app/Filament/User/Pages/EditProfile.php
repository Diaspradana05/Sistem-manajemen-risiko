<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Actions\ButtonAction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class EditProfile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.user.pages.edit-profile';
    protected static ?string $title = 'Edit Profil';

    public $name, $email, $password, $photo;

    public function mount(): void
    {
        $user = auth()->user();

        $this->form->fill([
            'name'  => $user->name,
            'email' => $user->email,
            'photo' => $user->photo,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Profil')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email')
                        ->required()
                        ->email()
                        ->maxLength(255),

                    FileUpload::make('photo')
                        ->label('Foto Profil')
                        ->directory('profile-photos')
                        ->image()
                        ->imageEditor()
                        ->imagePreviewHeight('150')
                        ->maxSize(1024)
                        ->nullable(),
                ]),

            Section::make('Ganti Password')
                ->schema([
                    TextInput::make('password')
                        ->label('Password Baru')
                        ->password()
                        ->nullable()
                        ->minLength(6)
                        ->maxLength(255),
                ]),
        ];
    }

    public function submit(): void
    {
        $user = Auth::user();
        $data = $this->form->getState();

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password']) && is_string($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if (!empty($data['photo'])) {
            $user->photo = $data['photo']; // gunakan kolom `photo` di database
        }

        $user->save();

        $this->notify('success', 'Profil berhasil diperbarui.');
    }

    protected function getFormActions(): array
    {
        return [
            ButtonAction::make('submit')
                ->label('Simpan')
                ->submit('submit')
                ->color('primary'),
        ];
    }
}
