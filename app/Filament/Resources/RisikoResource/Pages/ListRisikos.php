<?php

namespace App\Filament\Resources\RisikoResource\Pages;

use App\Filament\Resources\RisikoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRisikos extends ListRecords
{
    protected static string $resource = RisikoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
