<?php

namespace App\Filament\User\Resources\RiskResource\Pages;

use App\Filament\User\Resources\RiskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRisks extends ListRecords
{
    protected static string $resource = RiskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create_klinis')
                ->label('Buat Risiko Klinis')
                ->color('success')
                ->url(RiskResource::getUrl('create', ['tipe_risiko' => 'klinis'])),

            Actions\Action::make('create_non_klinis')
                ->label('Buat Risiko Non-Klinis')
                ->color('warning')
                ->url(RiskResource::getUrl('create', ['tipe_risiko' => 'non-klinis'])),
        ];
    }
}
