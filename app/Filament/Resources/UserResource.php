<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Kelola User';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email', ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->dehydrateStateUsing(fn($state) => !empty($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state)) // hanya update jika diisi
                    ->required(fn (string $context): bool => $context === 'create'),

                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Roles'),
            ]);
    }

   public static function table(Table $table): Table
{
    return $table
        ->columns([
            // Kolom tabel, misal:
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('email'),
            Tables\Columns\TextColumn::make('roles.name')->label('Roles'),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\Action::make('removeRole')
                ->label('Remove Role')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Select::make('role')
                        ->label('Pilih Role yang akan dihapus')
                        ->options(fn (User $record) => $record->getRoleNames()->mapWithKeys(fn ($role) => [$role => $role]))
                        ->required(),
                ])
                ->action(function (User $record, array $data): void {
                    if ($record->roles()->count() > 1) {
                        $record->removeRole($data['role']);
                        \Filament\Notifications\Notification::make()
                            ->title('Role dihapus!')
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal, user hanya punya 1 role!')
                            ->body('User harus memiliki minimal 1 role.')
                            ->danger()
                            ->send();
                    }
                }),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('superadmin'); // hanya superadmin bisa akses
    }

    protected static ?string $navigationGroup = 'Manajemen Risiko';
    protected static ?int $navigationSort = 1; 

    public static function getModelLabel(): string
    {
        return 'User';
    }
    public static function getPluralModelLabel(): string
    {
        return 'User';
    }
}


