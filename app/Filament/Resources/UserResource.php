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
        return $form->schema([
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
                ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context): bool => $context === 'create'),

            // Company
            Forms\Components\Select::make('company_id')
                ->label('Company')
                ->relationship('company', 'name')
                ->searchable()
                ->preload()
                ->reactive(),

            // Divisions
            Forms\Components\Select::make('divisions')
    ->label('Divisions')
    ->multiple()
    ->preload()
    ->searchable()
    ->reactive()
    ->options(function (callable $get) {
        $companyId = $get('company_id'); // ambil company yang dipilih
        if (!$companyId) {
            return [];
        }

        return \App\Models\Division::where('company_id', $companyId)
            ->pluck('name', 'id');
    }),


            // Tahun (division_year)
            Forms\Components\Select::make('year')
    ->label('Tahun')
    ->options([
        2023 => 2023,
        2024 => 2024,
        2025 => 2025,
        2026 => 2026,
    ]),

            // Roles
            Forms\Components\Select::make('roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->label('Roles'),

            // Hak akses modul Risiko
            Forms\Components\Section::make('Hak Akses')
            ->schema([
        Forms\Components\Toggle::make('full_risk_access')
            ->label('Full Access Risiko'),

        Forms\Components\Section::make('Detail Akses')
            ->hidden(fn (Forms\Get $get) => $get('full_risk_access'))
            ->schema([
                Forms\Components\Toggle::make('can_create_risk')
                    ->label('Boleh Membuat Risiko'),
                Forms\Components\Toggle::make('can_update_risk')
                    ->label('Boleh Mengubah Risiko'),
                Forms\Components\Toggle::make('can_delete_risk')
                    ->label('Boleh Menghapus Risiko'),
            ]),
    ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('roles.name')
    ->label('Roles')
    ->html()
    ->formatStateUsing(fn ($state) => match ($state) {
        'superadmin' => "<span style='color:red'>{$state}</span>",
        'direksi'    => "<span style='color:green'>{$state}</span>",
        'manajer'    => "<span style='color:blue'>{$state}</span>",
        'supervisor' => "<span style='color:orange'>{$state}</span>",
        'staf'       => "<span style='color:deeppink'>{$state}</span>", // gampang pink
        default      => $state,
    }),


                Tables\Columns\TextColumn::make('company.name')->label('Company'),
                Tables\Columns\TextColumn::make('divisions.name')
                    ->label('Divisions')
                    ->listWithLineBreaks()
                    ->limit(50),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                            Tables\Actions\Action::make('removeRole')
                ->label('Remove Role')
                ->icon('heroicon-o-trash')
                ->color('danger') 
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Select::make('role')
                        ->label('Pilih Role yang akan dihapus')
                        ->options(fn (User $record) => $record->getRoleNames()
                            ->mapWithKeys(fn ($role) => [$role => $role]))
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
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus yang Dipilih')
                    ->requiresConfirmation(),
            ]),
        ]);
}


public static function mutateFormDataBeforeCreate(array $data): array
{
    $user = User::create([
        'name'       => $data['name'],
        'email'      => $data['email'],
        'password'   => $data['password'],
        'company_id' => $data['company_id'],
    ]);

    foreach ($data['divisions'] as $divisionId) {
        // Buat record division_year kalau belum ada
        $divisionYear = \App\Models\DivisionYear::firstOrCreate([
            'division_id' => $divisionId,
            'year'        => $data['division_year_id'],   // ← nilai dari form (contoh: 2025)
        ]);

        // Attach pivot dengan division_year_id (id yg baru dibuat / yg sudah ada)
        $user->divisions()->attach($divisionId, [
            'division_year_id' => $divisionYear->id,
        ]);
    }

    $user->assignRole($data['roles']);

    return [];
}


    public static function mutateFormDataBeforeSave(array $data, User $record): array
{
    // hapus relasi lama
    $record->divisions()->detach();

    foreach ($data['divisions'] as $divisionId) {
        // Buat record division_year jika belum ada
        $divisionYear = \App\Models\DivisionYear::firstOrCreate([
            'division_id' => $divisionId,
            'year'        => $data['division_year_id'],
        ]);

        // attach relasi pivot dengan id dari division_year
        $record->divisions()->attach($divisionId, [
            'division_year_id' => $divisionYear->id,
        ]);
    }

    // update user basic attributes
    return [
        'name'       => $data['name'],
        'email'      => $data['email'],
        'password'   => $data['password'],
        'company_id' => $data['company_id'],
    ];
}


    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('superadmin');
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
