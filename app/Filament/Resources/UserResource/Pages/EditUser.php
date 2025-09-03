<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\DivisionYear;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Override schema form dari UserResource
     */
    protected function getFormSchema(): array
    {
        return UserResource::form($this->form)->getSchema();
    }

    /**
     * Jalan setelah user berhasil disimpan
     */
    protected function afterSave(): void
    {
        // Ambil data form
        $state = $this->form->getState();

        $year  = $state['year'] ?? null;
        $divisionIds = $state['divisions'] ?? [];

        if ($year && ! empty($divisionIds)) {
            // hapus relasi lama
            $this->record->divisions()->detach();

            foreach ($divisionIds as $divisionId) {
                $divisionYear = DivisionYear::firstOrCreate([
                    'division_id' => $divisionId,
                    'year'        => $year,
                ]);

                $this->record->divisions()->attach($divisionId, [
                    'division_year_id' => $divisionYear->id,
                ]);
            }
        }
    }
}
