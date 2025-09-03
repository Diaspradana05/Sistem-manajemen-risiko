<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;        // user yang baru dibuat
        $data = $this->data;

        // roles
        if (! empty($data['roles'])) {
            $user->syncRoles($data['roles']); // atau assignRole()
        }

        // divisions + division_year (pakai field form 'year')
        $divisions = $data['divisions'] ?? [];
        $year      = $data['year'] ?? null;

        foreach ($divisions as $divisionId) {
            $divisionYear = \App\Models\DivisionYear::firstOrCreate([
                'division_id' => $divisionId,
                'year'        => $year,
            ]);

            $user->divisions()->attach($divisionId, [
                'division_year_id' => $divisionYear->id,
            ]);
        }
    }
}
