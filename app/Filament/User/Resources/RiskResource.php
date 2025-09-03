<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\RiskResource\Pages;
use App\Models\Risk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Division;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Get;           
use App\Models\DivisionYear;      
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Actions\RejectRiskAction; 




class RiskResource extends Resource
{
    protected static ?string $model = Risk::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manajemen Risiko';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Kelola Risiko';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('company_id')
    ->label('Company')
    ->relationship('company', 'name')
    ->searchable()
    ->preload()
    ->required()
    ->reactive(), // agar division_id refresh saat company berubah

Forms\Components\Select::make('division_id')
    ->label('Division')
    ->options(function (Get $get) {
        $companyId = $get('company_id');
        if (!$companyId) {
            return [];
        }

        $user = auth()->user();

        // Superadmin → semua divisi di company apa pun
        if ($user->hasRole('superadmin')) {
            return Division::where('company_id', $companyId)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        }

        // Direksi → semua divisi di company miliknya
        if ($user->hasRole('direksi') && $user->company_id == $companyId) {
            return Division::where('company_id', $companyId)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        }

        // Manajer / Supervisor → hanya divisi yang dia pegang
        if ($user->hasRole('manajer') || $user->hasRole('supervisor')) {
            return $user->divisions()
                ->where('company_id', $companyId)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        }

        // user biasa → divisi miliknya
        return $user->divisions()
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->pluck('name', 'divisions.id')
            ->toArray();
    })
    ->reactive()
    ->searchable()
    ->required(),




Forms\Components\Select::make('year')
    ->label('Tahun')
    ->options(function (Get $get) {
        $divisionId = $get('division_id');
        if (!$divisionId) {
            return [];
        }

        // Ambil daftar tahun yang tersedia untuk divisi tsb dari tabel division_year
        return DivisionYear::where('division_id', $divisionId)
            ->orderBy('year', 'desc')
            ->pluck('year', 'year')           // [year => year]
            ->toArray();
    })
    ->disabled(fn (Get $get) => ! $get('division_id'))
    ->required(),


            Forms\Components\Hidden::make('tipe_risiko')
                ->default(fn () => request()->get('tipe_risiko', 'klinis')),

            Forms\Components\TextInput::make('nama_kegiatan')->label('Nama Kegiatan')->required(),
            Forms\Components\TextInput::make('tujuan_kegiatan'),
            Forms\Components\TextInput::make('area_lokasi'),
            Forms\Components\TextInput::make('kode_risiko'),
            Forms\Components\Textarea::make('risiko'),

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

            Forms\Components\Section::make('Analisa Risiko Inheren')->schema([

                // ANALISA DAMPAK
Forms\Components\Select::make('analisa_dampak')
    ->label('Analisa Dampak')
    ->options(fn (callable $get) => $get('tipe_risiko') === 'klinis'
        ? [
            '1' => '1 - Insignificant; Tidak ada cedera',
            '2' => '2 - Minor; Cedera ringan, Dapat diatasi dengan pertolongan pertama,',
            '3' => '3 - Cedera sedang; Berkurangnya fungsi motorik / sensorik / psikologis atau intelektual secara semipermanent / reversibel / tidak berhubungan dengan penyakit Setiap kasus yang memperpanjang perawatan"',
            '4' => '4 - Cedera berat / luas ; a) Kehilangan fungsi utama permanent (motorik, sensorik, psikologis, intelektual), permanen/irreversibel/tidak berhubungan dengan penyakit b) Kerugian keuangan besar',
            '5' => '5 - Kematian yang tidak berhubungan dengan perjalanan penyakit',
        ]
        : [
            '1' => '1 - Sangat rendah; <1jt, <1 HRK, diketahui seisi kantor, tidak patuh thd 1 UU, KPI tercapai 100%',
            '2' => '2 - Rendah; >1-5jt, >1-2 HRK, Dimuat oleh media massa lokal namun cepat dilupakan, tidak patuh 2 UU, KPI tercapai 90%',
            '3' => '3 - Sedang; >5-25jt, >2-3 HRK, Dimuat oleh media massa lokal & media sosial namun cepat dilupakan masyarakat, tidak patuh 3 UU, KPI tercapai 80%',
            '4' => '4 - Tinggi; >25-50jt, >3-5 HRK, Dimuat di media nasional dan media online dan diingat sementara oleh masyarakat, tidak patuh 4 UU, KPI tercapai 70%',
            '5' => '5 - Sangat Tinggi; >50jt, >5 HRK, Dimuat oleh media nasional/ internasional dan media sosial/media online diingat lama oleh masyarakat, tidak patuh 5 UU, KPI tercapai 60%',
        ]
    )
    ->searchable()
    ->placeholder('Pilih tingkat dampak')
    ->reactive()
->extraAttributes(function (callable $get) {
    $tooltipKlinis = "1 - Insignificant; Tidak ada cedera\n"
        . "2 - Minor; Cedera ringan, Dapat diatasi dengan pertolongan pertama,\n"
        . "3 - Cedera sedang; Berkurangnya fungsi motorik / sensorik / psikologis atau intelektual secara semipermanent / reversibel / tidak berhubungan dengan penyakit Setiap kasus yang memperpanjang perawatan\n"
        . "4 - Cedera berat / luas ; a) Kehilangan fungsi utama permanent (motorik, sensorik, psikologis, intelektual), permanen/irreversibel/tidak berhubungan dengan penyakit b) Kerugian keuangan besar\n"
        . "5 - Kematian yang tidak berhubungan dengan perjalanan penyakit";

    $tooltipNonKlinis = "1 - Sangat rendah; <1jt, <1 HRK, diketahui seisi kantor, tidak patuh thd 1 UU, KPI tercapai 100%\n"
        . "2 - Rendah; >1-5jt, >1-2 HRK, Dimuat oleh media massa lokal namun cepat dilupakan, tidak patuh 2 UU, KPI tercapai 90%\n"
        . "3 - Sedang; >5-25jt, >2-3 HRK, Dimuat oleh media massa lokal & media sosial namun cepat dilupakan masyarakat, tidak patuh 3 UU, KPI tercapai 80%\n"
        . "4 - Tinggi; >25-50jt, >3-5 HRK, Dimuat di media nasional dan media online dan diingat sementara oleh masyarakat, tidak patuh 4 UU, KPI tercapai 70%\n"
        . "5 - Sangat Tinggi; >50jt, >5 HRK, Dimuat oleh media nasional/ internasional dan media sosial/media online diingat lama oleh masyarakat, tidak patuh 5 UU, KPI tercapai 60%";

    return [
        'title' => $get('tipe_risiko') === 'klinis'
            ? $tooltipKlinis
            : $tooltipNonKlinis,
        'style' => 'cursor: help;', // Biar ada icon pointer
    ];
})
    ->afterStateUpdated(function ($state, callable $set, callable $get) {
        self::updateAnalisaConcate($set, $get);
    }),

                // ANALISA PROBABILITAS
                Forms\Components\Select::make('analisa_probabilitas')
    ->label('Analisa Probabilitas')
    ->options(fn (callable $get) => $get('tipe_risiko') === 'klinis'
    ? [
        '1' => '1 - Sangat Jarang; Dapat terjadi dalam lebih dari 5 tahun',
        '2' => '2 - Jarang; Dapat terjadi dalam 2 – 5 tahun',
        '3' => '3 - Mungkin; Dapat terjadi tiap 1 – 2 tahun',
        '4' => '4 - Sering; Dapat terjadi beberapa kali dalam setahun',
        '5' => '5 - Sangat Sering; Terjadi dalam minggu / bulan',
    ]
    : [
        '1' => '1 - Sangat Jarang; Dapat terjadi dalam lebih dari 5 tahun',
        '2' => '2 - Jarang; Dapat terjadi dalam 2 – 5 tahun',
        '3' => '3 - Mungkin; Dapat terjadi tiap 1 – 2 tahun',
        '4' => '4 - Sering; Dapat terjadi beberapa kali dalam setahun',
        '5' => '5 - Sangat Sering; Terjadi dalam minggu / bulan',
    ]
    )

    ->searchable()
    ->placeholder('Pilih tingkat probabilitas')
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set, callable $get) {
        self::updateAnalisaConcate($set, $get);
    })
    ->extraAttributes(function (callable $get) {
        // Tooltip Klinis
        $tooltipKlinis = "1 - Sangat Jarang; Dapat terjadi dalam lebih dari 5 tahun\n"
            . "2 - Jarang; Dapat terjadi dalam 2 – 5 tahun\n"
            . "3 - Mungkin; Dapat terjadi tiap 1 – 2 tahun\n"
            . "4 - Sering; Dapat terjadi beberapa kali dalam setahun\n"
            . "5 - Sangat Sering; Terjadi dalam minggu / bulan";

        // Tooltip Non-Klinis
        $tooltipNonKlinis = "1 - Sangat Jarang; Dapat terjadi dalam lebih dari 5 tahun\n"
            . "2 - Jarang; Dapat terjadi dalam 2 – 5 tahun\n"
            . "3 - Mungkin; Dapat terjadi tiap 1 – 2 tahun\n"
            . "4 - Sering; Dapat terjadi beberapa kali dalam setahun\n"
            . "5 -  Sangat Sering; Terjadi dalam minggu / bulan";

        return [
            'title' => $get('tipe_risiko') === 'klinis'
                ? $tooltipKlinis
                : $tooltipNonKlinis,
            'style' => 'cursor: help;',
        ];
    }),

                // ANALISA CONCATENATE
                Forms\Components\TextInput::make('analisa_concate')
                    ->label('Analisa Concate')
                    ->disabled()
                    ->dehydrated(),

                // SKOR
                Forms\Components\TextInput::make('skor')
                    ->numeric()
                    ->disabled()
                    ->hint('Skor dihitung otomatis'),

                // PERINGKAT RISIKO
                Forms\Components\TextInput::make('peringkat_risiko')
                    ->disabled()
                    ->hint('Peringkat muncul otomatis setelah skor dihitung'),
            ]),

            Forms\Components\Toggle::make('perlu_penanganan')->label('Perlu Penanganan Risiko?'),

            Forms\Components\Section::make('Opsi Pengendalian')->schema([
                Forms\Components\Toggle::make('hindari_risiko'),
                Forms\Components\Toggle::make('cegah_kerugian'),
                Forms\Components\Toggle::make('reduksi_kerugian'),
                Forms\Components\Toggle::make('segregasi'),
                Forms\Components\Toggle::make('contractual_transfer'),
            ]),

            Forms\Components\Textarea::make('rencana_penanganan'),
            Forms\Components\Textarea::make('pembiayaan_risiko'),

            Forms\Components\Select::make('status_persetujuan')
    ->label('Status Persetujuan')
    ->options([
        'draf'      => 'Draf',
        'ditinjau'  => 'Ditinjau',
        'disetujui' => 'Disetujui',
        'ditolak'   => 'Ditolak',
    ])
    ->default('draf')
    ->visible(fn () =>
        auth()->user()->hasRole('supervisor') ||
        auth()->user()->hasRole('manajer') ||
        auth()->user()->hasRole('direksi')
    ),

Forms\Components\Hidden::make('status_persetujuan')
    ->default('draf')
    ->visible(fn () =>
        auth()->user()->hasRole('staf') ||
        auth()->user()->hasRole('superadmin') 
    ),
]); 
    }
    

    // Fungsi untuk update analisa_concate + skor + peringkat
    protected static function updateAnalisaConcate(callable $set, callable $get): void
    {
        $dampak = $get('analisa_dampak') ?? '';
        $prob = $get('analisa_probabilitas') ?? '';

        if ($dampak && $prob) {
            $set('analisa_concate', "{$dampak}{$prob}");

            // Hitung skor
            $skor = (int) $dampak * (int) $prob;
            $set('skor', $skor);

            // Tentukan peringkat risiko
            if ($skor >= 15) {
                $set('peringkat_risiko', 'Sangat Tinggi');
            } elseif ($skor >= 10) {
                $set('peringkat_risiko', 'Tinggi');
            } elseif ($skor >= 5) {
                $set('peringkat_risiko', 'Sedang');
            } elseif ($skor >= 3) {
                $set('peringkat_risiko', 'Rendah');
            } elseif ($skor >= 1) {
                $set('peringkat_risiko', 'Sangat Rendah');
            } else {
                $set('peringkat_risiko', '-');
            }
        } else {
            $set('analisa_concate', null);
            $set('skor', null);
            $set('peringkat_risiko', null);
        }
    }

    

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRisks::route('/'),
            'create' => Pages\CreateRisk::route('/create'),
            'edit'   => Pages\EditRisk::route('/{record}/edit'),
        ];
    }

        private static function getRiskActions(): array
    {
        return [
            Tables\Actions\EditAction::make()
                ->visible(fn ($record) => auth()->user()?->can('update', $record)),

            Tables\Actions\DeleteAction::make()
                ->visible(fn ($record) => auth()->user()?->can('delete', $record)),

            Tables\Actions\Action::make('approve')
                ->label('Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn ($record) => auth()->user()?->can('approve', $record))
                ->action(function ($record) {
                    $record->update([
                        'status_persetujuan' => 'disetujui',
                        'ditinjau_oleh'      => auth()->id(),
                        'ditinjau_pada'      => now(),
                    ]);
                }),

            RejectRiskAction::create(),

            Tables\Actions\Action::make('review')
                ->label('Tinjau')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn ($record) => auth()->user()?->can('review', $record))
                ->action(function ($record) {
                    $record->update([
                        'status_persetujuan' => 'ditinjau',
                        'ditinjau_oleh'      => auth()->id(),
                        'ditinjau_pada'      => now(),
                    ]);
                }),
        ];
    }

public static function table(Table $table): Table
{
    return $table
        ->filtersFormColumns(2)
        ->filters([
            Tables\Filters\SelectFilter::make('company_id')
                ->label('Company')
                ->relationship('company', 'name')
                ->searchable()
                ->preload(),

            Tables\Filters\Filter::make('division')
                ->label('Divisi')
                ->form([
                    Forms\Components\Select::make('division_id')
                        ->label('Pilih Divisi')
                        ->options(function (callable $get) {
                            $companyId = $get('company_id');
                            $user = auth()->user();

                            $query = $user->divisions()->orderBy('name');

                            if ($companyId) {
                                $query->where('company_id', $companyId);
                            }

                            return $query
                                ->pluck('name', 'divisions.id')
                                ->toArray();
                        })
                        ->reactive()
                        ->searchable()
                        ->placeholder('Pilih company dulu'),
                ])
                ->query(fn (Builder $query, array $data): Builder =>
                    $query->when(
                        $data['division_id'] ?? null,
                        fn ($q, $divisionId) => $q->where('division_id', $divisionId)
                    )
                ),

            Tables\Filters\Filter::make('year')
                ->label('Tahun')
                ->form([
                    Forms\Components\Select::make('year')
                        ->label('Pilih Tahun')
                        ->options(
                            Risk::query()
                                ->select('year')
                                ->distinct()
                                ->orderBy('year', 'desc')
                                ->pluck('year', 'year')
                                ->toArray()
                        )
                        ->placeholder('Semua Tahun'),
                ])
                ->query(fn (Builder $query, array $data): Builder =>
                    $query->when($data['year'], fn ($q, $year) => $q->where('year', $year))
                ),

            Tables\Filters\SelectFilter::make('tipe_risiko')
                ->label('Pilih Risiko')
                ->options([
                    'klinis' => 'Klinis',
                    'non-klinis' => 'Non-Klinis',
                ])
        ], layout: Tables\Enums\FiltersLayout::AboveContent)
        ->columns([
            Tables\Columns\TextColumn::make('year')->label('Tahun')
                ->alignment('center'),
            Tables\Columns\TextColumn::make('company.name')
                ->label('Company')
                ->sortable()
                ->searchable()
                ->alignment('center'),
            Tables\Columns\TextColumn::make('division.name')
                ->label('Divisi')
                ->sortable()
                ->searchable()
                ->alignment('center'),

            Tables\Columns\TextColumn::make('nama_kegiatan'),
            Tables\Columns\TextColumn::make('kode_risiko')
            ->alignment('center'),
            Tables\Columns\TextColumn::make('skor')
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
            ->alignment('center'),
            Tables\Columns\BadgeColumn::make('tipe_risiko')
                ->colors([
                    'success' => 'klinis',
                    'warning' => 'non-klinis',
                ])
                ->label('Tipe Risiko')
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
                ->visible(fn () => Auth::check() && Auth::user()->hasAnyRole(['superadmin', 'manajer', 'supervisor', 'direksi']))
                ->alignment('center'),

            Tables\Columns\TextColumn::make('ditinjauOleh.name')
                ->label('Ditinjau / Disetujui / Ditolak Oleh')
                ->alignment('center'),

            Tables\Columns\TextColumn::make('ditinjau_pada')
                ->label('Tanggal Tinjau')
                ->dateTime('d M Y H:i'),
        ])

                    ->actions(self::getRiskActions());
            }
        
    public static function canCreate(): bool
{
    $user = auth()->user();
    return $user->full_risk_access || $user->can_create_risk;
}

public static function canEdit($record): bool
{
    $user = auth()->user();

    // jika punya full access risk => langsung boleh
    if ($user->full_risk_access) {
        return true;
    }

    // kalau tidak punya hak update => simpan false
    if (! $user->can_update_risk) {
        return false;
    }

    // Pastikan data ini adalah milik divisi yg dimiliki user
    return $user->divisions()
    ->where('divisions.id', $record->division_id)
    ->where('divisions.company_id', $record->company_id)
    ->exists();
}


public static function canDelete($record): bool
{
    $user = auth()->user();

    if ($user->full_risk_access) {
        return true;
    }

    if (! $user->can_delete_risk) {
        return false;
    }
    return $user->divisions()
    ->where('divisions.id', $record->division_id)
    ->where('divisions.company_id', $record->company_id)
    ->exists();
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


    public static function getModelLabel(): string
    {
        return 'Risiko';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Risiko';
    }

protected function mutateFormDataBeforeSave(array $data): array
{
    // Set user yang buat
    if (! isset($data['dibuat_oleh'])) {
        $data['dibuat_oleh'] = auth()->id();
    }

    // Kalau role staf → paksa status jadi draf
    if (auth()->user()->hasRole('staf')) {
        $data['status_persetujuan'] = 'draf';
    }

    // Kalau status sudah disetujui / ditolak → isi user & timestamp
    if (in_array($data['status_persetujuan'] ?? 'draf', ['disetujui', 'ditolak'])) {
        $data['disetujui_oleh'] = auth()->id();
        $data['disetujui_pada'] = now();
    }

    return $data;
}

}