<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\DaftarRisikoResource\Pages;
use App\Models\Risk;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DaftarRisikoResource extends Resource
{
    protected static ?string $model = Risk::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationGroup = 'Manajemen Risiko';
    protected static ?string $navigationLabel = 'Daftar Risiko';
    protected static ?string $pluralLabel = 'Daftar Risiko';
    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                ->label('Tahun')
                ->alignment('center'),
                Tables\Columns\TextColumn::make('company.name')
                ->label('Company')
                ->alignment('center'),
                Tables\Columns\TextColumn::make('division.name')
                ->label('Divisi')
                ->alignment('center'),

                Tables\Columns\TextColumn::make('nama_kegiatan')
                    ->searchable()
                    ->label('Kegiatan')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('kode_risiko')
                    ->searchable()
                    ->label('Kode')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('risiko')
                    ->limit(30)
                    ->label('Risiko')
                    ->alignment('center'),

                Tables\Columns\BadgeColumn::make('tipe_risiko')
                ->colors([
                    'success' => 'klinis',
                    'warning' => 'non-klinis',
                ])
                ->label('Tipe Risiko')
                ->alignment('center'),


                Tables\Columns\TextColumn::make('skor')
                    ->label('Skor')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('peringkat_risiko')
    ->label('Peringkat')
    ->badge()
    ->color(fn (string $state): string => match ($state) {
    'Sangat Tinggi' => 'danger',   // merah
    'Tinggi'        => 'warning',  // kuning/orange
    'Sedang'        => 'info',     // biru
    'Rendah'        => 'success',  // hijau
    'Sangat Rendah' => 'gray',     // abu-abu
    default         => 'secondary',

    })
    ->alignment('center'),


                Tables\Columns\BooleanColumn::make('perlu_penanganan')
                    ->label('Perlu Penanganan')
                    ->alignment('center'),

                   Tables\Columns\BadgeColumn::make('status_persetujuan')
    ->label('Status Persetujuan')
    ->colors([
        'warning' => 'draf',
        'info'    => 'ditinjau',
        'success' => 'disetujui',
        'danger'  => 'ditolak',
    ])
    ->formatStateUsing(fn ($state) => match ($state) {
        'draf'      => 'Draf',
        'ditinjau'  => 'Ditinjau',
        'disetujui' => 'Disetujui',
        'ditolak'   => 'Ditolak',
        default     => $state,
    })

        ->tooltip(function ($record) {
        $user = auth()->user();

        if ($record->status_persetujuan === 'ditolak' && $user?->hasRole('staf')) {
            return $record->alasan_penolakan; // tampilkan alasan hanya untuk staf
        }

        return null; // user lain tidak melihat tooltip
    })
        ->alignment('center'),

        Tables\Columns\TextColumn::make('dibuatOleh.name')
    ->label('Dibuat Oleh')
    ->visible(fn () => Auth::check() && Auth::user()->hasAnyRole(roles: ['superadmin', 'manajer', 'supervisor','direksi']))
    ->alignment('center'),


        Tables\Columns\TextColumn::make('ditinjauOleh.name')
            ->label('Ditinjau / Disetujui / Ditolak Oleh')
            ->alignment('center'),

        Tables\Columns\TextColumn::make('ditinjau_pada')
            ->label('Tanggal Tinjau')
            ->dateTime('d M Y H:i')
             ->alignment('center'),
                    
    
            ])
        ->filters([
    // Filter Company
    Tables\Filters\SelectFilter::make('company_id')
        ->label('Company')
        ->options(function () {
            if (auth()->user()->hasRole('superadmin')) {
                return \App\Models\Company::pluck('name','id')->toArray();
            }
            return \App\Models\Company::where('id', auth()->user()->company_id)
                    ->pluck('name','id')->toArray();
        }),

    // Filter Divisi
    Tables\Filters\SelectFilter::make('division_id')
        ->label('Divisi')
        ->options(function () {
            $user = auth()->user();
            if ($user->hasRole('superadmin')) {
                return \App\Models\Division::pluck('name','id')->toArray();
            }
            return $user->divisions->pluck('name','id')->toArray();
        }),

    // Filter Tahun
    Tables\Filters\SelectFilter::make('year')
        ->label('Tahun')
        ->options(
            Risk::query()
                ->select('year')
                ->distinct()
                ->orderBy('year','desc')
                ->pluck('year', 'year')
                ->toArray()
        ),

    // Filter Peringkat Risiko
    Tables\Filters\SelectFilter::make('peringkat_risiko')
        ->label('Peringkat Risiko')
        ->options([
            'High' => 'High',
            'Medium' => 'Medium',
            'Low' => 'Low',
        ]),

    // Filter Tipe Risiko
    Tables\Filters\SelectFilter::make('tipe_risiko')
        ->label('Tipe Risiko')
        ->options([
            'klinis' => 'Klinis',
            'non-klinis' => 'Non-Klinis',
        ]),

    // Filter Perlu Penanganan
    Tables\Filters\TernaryFilter::make('perlu_penanganan')
        ->label('Perlu Penanganan')
        ->placeholder('Semua')
        ->trueLabel('Ya')
        ->falseLabel('Tidak'),

    // ✅ Filter Status Persetujuan (semua role boleh)
    Tables\Filters\SelectFilter::make('status_persetujuan')
        ->label('Status Persetujuan')
        ->options([
            'draf'      => 'Draf',
            'ditinjau'  => 'Ditinjau',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
        ]),

    // ✅ Filter Dibuat Oleh (hanya untuk superadmin, direksi, manajer, supervisor)
    Tables\Filters\SelectFilter::make('dibuat_oleh')
        ->label('Dibuat Oleh')
        ->options(fn () => \App\Models\User::pluck('name', 'id'))
        ->visible(fn () => auth()->user()->hasAnyRole(['superadmin','direksi','manajer','supervisor'])),

    // ✅ Filter Ditinjau / Disetujui / Ditolak Oleh (hanya untuk superadmin, direksi, manajer)
    Tables\Filters\SelectFilter::make('ditinjau_oleh')
        ->label('Ditinjau / Disetujui / Ditolak Oleh')
        ->options(fn () => \App\Models\User::pluck('name', 'id'))
        ->visible(fn () => auth()->user()->hasAnyRole(['superadmin','direksi','manajer'])),

    // ✅ Filter Tanggal Tinjau (range date)
    Tables\Filters\Filter::make('ditinjau_pada')
        ->label('Tanggal Tinjau')
        ->form([
            \Filament\Forms\Components\DatePicker::make('from')->label('Dari'),
            \Filament\Forms\Components\DatePicker::make('until')->label('Sampai'),
        ])
        ->query(function (Builder $query, array $data): Builder {
            return $query
                ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('ditinjau_pada', '>=', $date))
                ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('ditinjau_pada', '<=', $date));
        })
])
->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDaftarRisikos::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
{
    $user = auth()->user();
    $query = parent::getEloquentQuery();

    // Superadmin → akses penuh lintas company & divisi
    if ($user->hasRole('superadmin')) {
        return $query;
    }

    // Direksi → akses semua divisi, tapi hanya dalam company dia
    if ($user->hasRole('direksi')) {
        return $query->where('company_id', $user->company_id);
    }

    // Manajer / Supervisor → hanya divisi yang dia pegang (dalam company dia)
    if ($user->hasRole('manajer') || $user->hasRole('supervisor')) {
        return $query->where('company_id', $user->company_id)
                     ->whereIn('division_id', $user->divisions->pluck('id'));
    }

    // Staff → hanya risiko yang dia buat (dalam company dia)
    if ($user->hasRole('staf')) {
        return $query->where('company_id', $user->company_id)
                     ->where('dibuat_oleh', $user->id);
    }

    // default fallback
    return $query;
}
}
