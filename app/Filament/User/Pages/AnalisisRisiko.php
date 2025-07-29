<?php

namespace App\Filament\User\Pages;

use App\Models\Risk;
use Filament\Pages\Page;

class AnalisisRisiko extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Manajemen Risiko';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.user.pages.analisis-risiko';

    // Variabel publik untuk digunakan di blade
    public $totalRisiko;
    public $perluPenanganan;
    public $persentasePenanganan;
    public $peringkatRisiko;
    public $perUnit;

    public function mount(): void
    {
        $risiko = Risk::all();

        // Hitung total dan penanganan
        $this->totalRisiko = $risiko->count();
        $this->perluPenanganan = $risiko->where('perlu_penanganan', true)->count();

        // Hitung persentase
        $this->persentasePenanganan = $this->totalRisiko > 0
            ? round(($this->perluPenanganan / $this->totalRisiko) * 100, 2)
            : 0;

        // Grup berdasarkan peringkat risiko (pie chart)
        $this->peringkatRisiko = $risiko->groupBy('peringkat_risiko')->map(function ($items) {
            return count($items);
        })->toArray();

        // Grup berdasarkan area/lokasi (bar chart)
        $this->perUnit = $risiko->groupBy('area_lokasi')->map(function ($items) {
            return count($items);
        })->toArray();
    }
}
