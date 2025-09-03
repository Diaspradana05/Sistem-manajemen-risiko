<?php

namespace App\Filament\User\Resources\RiskResource\Pages;

use App\Filament\User\Resources\RiskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRisk extends CreateRecord
{
    protected static string $resource = RiskResource::class;

    public $analisa_dampak;
    public $analisa_probabilitas;
    public $analisa_concate;

    // Livewire sudah otomatis memanggil ini tanpa listeners[]
    public function updatedAnalisaDampak($value)
    {
        $this->updateConcate();
    }

    public function updatedAnalisaProbabilitas($value)
    {
        $this->updateConcate();
    }

    public function updateConcate()
    {
        $this->analisa_concate = ($this->analisa_dampak ?? '') . ($this->analisa_probabilitas ?? '');
    }
}
