<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\RiskResource\Pages;
use App\Filament\User\Resources\RiskResource\RelationManagers;
use App\Models\Risk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiskResource extends Resource
{
    protected static ?string $model = Risk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->required(),

                Forms\Components\TextInput::make('tujuan_kegiatan'),
                Forms\Components\TextInput::make('area_lokasi'),
                Forms\Components\TextInput::make('kode_risiko'),
                Forms\Components\Textarea::make('risiko'),

                // Penyebab Risiko
                Forms\Components\Section::make('Penyebab Risiko')->schema([
                    Forms\Components\TextInput::make('sebab_1'),
                    Forms\Components\TextInput::make('sebab_2'),
                    Forms\Components\TextInput::make('sebab_3'),
                    Forms\Components\TextInput::make('sebab_4'),
                    Forms\Components\TextInput::make('sebab_5'),
                ]),

                Forms\Components\Textarea::make('dampak'),
                Forms\Components\Textarea::make('pernyataan_risiko'),
                Forms\Components\Textarea::make('pengendalian_saat_ini'),

                // Analisa Risiko
                Forms\Components\Section::make('Analisa Risiko Inheren')->schema([
                    Forms\Components\TextInput::make('analisa_dampak')->numeric(),
                    Forms\Components\TextInput::make('analisa_probabilitas')->numeric(),
                    Forms\Components\TextInput::make('analisa_conate')->numeric(),
                    Forms\Components\TextInput::make('skor')->numeric()->disabled(),
                    Forms\Components\TextInput::make('peringkat_risiko')->disabled(),
                ]),

                Forms\Components\Toggle::make('perlu_penanganan')->label('Perlu Penanganan Risiko?'),

                // Opsi Teknik Pengendalian Risiko
                Forms\Components\Section::make('Opsi Pengendalian')->schema([
                    Forms\Components\Toggle::make('hindari_risiko'),
                    Forms\Components\Toggle::make('cegah_kerugian'),
                    Forms\Components\Toggle::make('reduksi_kerugian'),
                    Forms\Components\Toggle::make('segregasi'),
                    Forms\Components\Toggle::make('contractual_transfer'),
                ]),

                Forms\Components\Textarea::make('rencana_penanganan'),
                Forms\Components\Textarea::make('pembiayaan_risiko'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kegiatan'),
                Tables\Columns\TextColumn::make('kode_risiko'),
                Tables\Columns\TextColumn::make('skor'),
                Tables\Columns\TextColumn::make('peringkat_risiko'),
                Tables\Columns\BooleanColumn::make('perlu_penanganan'),
            ])
            
             ->filters([
                //
             ])
            ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
            ]);
    }
           

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRisks::route('/'),
            'create' => Pages\CreateRisk::route('/create'),
            'edit' => Pages\EditRisk::route('/{record}/edit'),
        ];
    }

    protected static ?string $navigationGroup = 'Manajemen Risiko';
    protected static ?int $navigationSort = 1; 

    protected static ?string $navigationLabel = 'Kelola Risiko';
    public static function getModelLabel(): string
    {
        return 'Risiko';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Risiko';
    }
}
