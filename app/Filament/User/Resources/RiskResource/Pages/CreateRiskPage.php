<?php

namespace App\Filament\Pages;

use App\Filament\Resources\RiskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRiskPage extends CreateRecord
{
    protected static string $resource = RiskResource::class;
    protected static ?string $navigationLabel = 'Tambah Data Risiko';
    protected static ?string $navigationGroup = 'Manajemen Risiko';
}