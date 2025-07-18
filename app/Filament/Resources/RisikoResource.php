<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RisikoResource\Pages;
use App\Filament\Resources\RisikoResource\RelationManagers;
use App\Models\Risiko;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class RisikoResource extends Resource
{
    protected static ?string $model = Risiko::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_risiko'),
                TextInput::make('deskripsi'),
                TextInput::make('tingkat_risiko'),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRisikos::route('/'),
            'create' => Pages\CreateRisiko::route('/create'),
            'edit' => Pages\EditRisiko::route('/{record}/edit'),
        ];
    }
}
