<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use App\Models\Risk;

class LaporanRisiko extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Laporan Risiko';
    protected static ?string $navigationGroup = 'Manajemen Risiko';
    protected static ?int $navigationSort = 4; 

    protected static string $view = 'filament.pages.laporan-risiko';

    public function getRisikosProperty()
    {
        return Risk::all();
    }
}
