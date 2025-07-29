<?php

namespace App\Filament\User\Resources\DaftarRisikoResource\Pages;

use App\Filament\User\Resources\DaftarRisikoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDaftarRisiko extends EditRecord
{
    protected static string $resource = DaftarRisikoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
