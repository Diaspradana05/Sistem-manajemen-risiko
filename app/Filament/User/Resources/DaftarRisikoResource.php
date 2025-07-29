<?php

namespace App\Filament\User\Resources;


use App\Filament\User\Resources\DaftarRisikoResource\Pages;
use App\Models\Risk;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DaftarRisikoResource extends Resource
{
    protected static ?string $model = Risk::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationGroup = 'Manajemen Risiko';
    protected static ?string $navigationLabel = 'Daftar Risiko';
    protected static ?string $pluralLabel = 'Daftar Risiko';
    protected static ?int $navigationSort = 2; // posisinya di urutan ke-2

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kegiatan')->searchable()->label('Kegiatan'), 
                Tables\Columns\TextColumn::make('kode_risiko')->searchable()->label('Kode'),
                Tables\Columns\TextColumn::make('risiko')->limit(30),
                Tables\Columns\TextColumn::make('skor')->label('Skor'),
                Tables\Columns\TextColumn::make('peringkat_risiko')->label('Peringkat')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'High' => 'danger',
                        'Medium' => 'warning',
                        'Low' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\BooleanColumn::make('perlu_penanganan'),
            ])

             ->filters([
            Tables\Filters\SelectFilter::make('peringkat_risiko')
                ->label('Peringkat Risiko')
                ->options([
                    'High' => 'High',
                    'Medium' => 'Medium',
                    'Low' => 'Low',
                ]),

            Tables\Filters\TernaryFilter::make('perlu_penanganan')
                ->label('Perlu Penanganan')
                ->placeholder('Semua')
                ->trueLabel('Ya')
                ->falseLabel('Tidak'),
        ])
            ->defaultSort('id', 'desc');
    }

   public static function getPages(): array
{
    return [
        'index' => Pages\ListDaftarRisikos::route('/'),
    ];
}
}
