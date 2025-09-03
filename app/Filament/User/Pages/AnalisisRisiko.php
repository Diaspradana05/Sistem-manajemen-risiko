<?php

namespace App\Filament\User\Pages;

use App\Models\Risk;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;

class AnalisisRisiko extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Manajemen Risiko';
    protected static ?int    $navigationSort  = 3;

    protected static string $view = 'filament.user.pages.analisis-risiko';

    // Variabel untuk blade
    public $totalRisiko = 0;
    public $perluPenanganan = 0;
    public $persentasePenanganan = 0;
    public $peringkatRisiko = [];
    public $perUnit = [];
    public $peringkatTipeRisiko = [];
    public $lokasiTipeRisiko = [];

    public $filterCompany;
    public $filterDivision;
    public $filterYear;

    public $availableDivisions = [];

    public function mount(): void
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {
            $this->filterCompany  = null;
            $this->filterDivision = null;
            $this->availableDivisions = [];
        } elseif ($user->hasRole('direksi')) {
            $this->filterCompany = $user->company_id;
            $this->availableDivisions = $user->company->divisions->pluck('name', 'id')->toArray();
            $this->filterDivision = null;
        } else {
            $this->filterCompany      = $user->company_id;
            $this->availableDivisions = $user->divisions->pluck('name', 'id')->toArray();
            $this->filterDivision     = null;
        }

        $this->filterYear = null;

        $this->applyFilter();
    }

    public function updatedFilterCompany(): void
    {
        $this->filterDivision = null;
    }

    /**
     * Terapkan filter ke query risiko
     */
    public function applyFilter(): void
    {
        $query = $this->buildFilteredQuery();

        $risiko = $query->get();

        $this->hitungStatistik($risiko);
        $this->hitungPeringkatRisiko($risiko);
        $this->hitungTipeDanLokasi($risiko);

        // Kirim data terbaru ke browser (Livewire v3)
        $this->dispatch(
            'risk-charts-update',
            peringkatTipeRisiko: $this->peringkatTipeRisiko,
            lokasiTipeRisiko:    $this->lokasiTipeRisiko,
        );
    }

    /**
     * @return Builder
     */
    private function buildFilteredQuery(): Builder
    {
        $user  = auth()->user();
        $query = Risk::query();

        if ($user->hasRole('superadmin')) {
            if ($this->filterCompany)  $query->where('company_id',  $this->filterCompany);
            if ($this->filterDivision) $query->where('division_id', $this->filterDivision);
        } elseif ($user->hasRole('direksi')) {
            $query->where('company_id', $user->company_id);
            if ($this->filterDivision) {
                $query->where('division_id', $this->filterDivision);
            }
        } else {
            $query->where('company_id', $user->company_id);
            if ($this->filterDivision) {
                $query->where('division_id', $this->filterDivision);
            } else {
                $query->whereIn('division_id', $user->divisions->pluck('id'));
            }
        }

        if ($this->filterYear) {
            $query->where('year', $this->filterYear);
        }

        return $query;
    }

    /**
     * Hitung statistik risiko umum
     */
    private function hitungStatistik($risiko): void
    {
        $this->totalRisiko          = $risiko->count();
        $this->perluPenanganan      = $risiko->where('perlu_penanganan', true)->count();
        $this->persentasePenanganan = $this->totalRisiko
            ? round(($this->perluPenanganan / $this->totalRisiko) * 100, 2)
            : 0;
    }

    /**
     * Hitung peringkat risiko
     */
    private function hitungPeringkatRisiko($risiko): void
    {
        $peringkatList = $risiko->pluck('peringkat_risiko')
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->unique()
            ->values();

        $this->peringkatRisiko = $peringkatList->mapWithKeys(
            fn ($p) => [$p => $risiko->where('peringkat_risiko', $p)->count()]
        )->toArray();

        $this->peringkatTipeRisiko = $risiko
            ->groupBy('tipe_risiko')
            ->map(function ($items) use ($peringkatList) {
                return $peringkatList->mapWithKeys(
                    fn ($p) => [$p => $items->where('peringkat_risiko', $p)->count()]
                );
            })
            ->toArray();

        $this->peringkatTipeRisiko = collect($this->peringkatTipeRisiko)
            ->sortByDesc(fn ($value, $key) => $key === 'klinis')
            ->toArray();
    }

    /**
     * Hitung lokasi per tipe risiko
     */
    private function hitungTipeDanLokasi($risiko): void
    {
        $this->lokasiTipeRisiko = $risiko
            ->groupBy('tipe_risiko')
            ->map(fn ($items) => $items->groupBy('area_lokasi')->map->count())
            ->toArray();

        $this->lokasiTipeRisiko = collect($this->lokasiTipeRisiko)
            ->sortByDesc(fn ($value, $key) => $key === 'klinis')
            ->toArray();


    // Kirim data terbaru ke browser (Livewire v3)
    $this->dispatch(
        'risk-charts-update',
        peringkatTipeRisiko: $this->peringkatTipeRisiko,
        lokasiTipeRisiko:    $this->lokasiTipeRisiko,
    );
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

public static function canAccess(): bool
{
    $user = auth()->user();

    // Staff tidak boleh akses halaman ini
    if ($user->hasRole('staf')) {
        return false;
    }

    return true;
}

}
