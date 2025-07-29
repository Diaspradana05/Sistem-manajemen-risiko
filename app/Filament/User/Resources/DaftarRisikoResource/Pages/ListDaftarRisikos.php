<?php

namespace App\Filament\User\Resources\DaftarRisikoResource\Pages;

use App\Filament\User\Resources\DaftarRisikoResource;
use Filament\Resources\Pages\ListRecords;

class ListDaftarRisikos extends ListRecords
{
    protected static string $resource = DaftarRisikoResource::class;

    protected function getHeaderActions(): array
    {
        return []; // tidak ada tombol tambah/edit
    }
}
