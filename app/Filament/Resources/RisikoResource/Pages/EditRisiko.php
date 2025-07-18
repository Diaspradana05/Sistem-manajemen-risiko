<?php

namespace App\Filament\Resources\RisikoResource\Pages;

use App\Filament\Resources\RisikoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRisiko extends EditRecord
{
    protected static string $resource = RisikoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
