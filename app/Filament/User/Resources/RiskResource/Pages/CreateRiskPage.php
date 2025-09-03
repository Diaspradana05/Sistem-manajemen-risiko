<?php

namespace App\Filament\Pages;

use App\Filament\Resources\RiskResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class CreateRiskPage extends CreateRecord
{
    protected static string $resource = RiskResource::class;
    protected static ?string $navigationLabel = 'Tambah Data Risiko';
    protected static ?string $navigationGroup = 'Manajemen Risiko';

 protected function afterCreate(): void
    {
        Notification::create([
            'user_id' => auth()->id(),
            'title' => 'Risiko Baru Ditambahkan',
            'message' => 'Risiko "' . $this->record->nama_kegiatan . '" telah ditambahkan.',
        ]);

        FilamentNotification::make()
            ->title('Risiko berhasil dibuat')
            ->success()
            ->send()
            ->icon('heroicon-o-shopping-bag');
    }

        protected function mutateFormDataBeforeSave(array $data): array
    {
        // Gabungkan nilai analisa_dampak dan analisa_probabilitas
        $data['analisa_concate'] = ($data['analisa_dampak'] ?? '') . ($data['analisa_probabilitas'] ?? '');

        return $data;
    }

    public $analisa_dampak;
    public $analisa_probabilitas;
    public $analisa_concate;

    protected $listeners = [
        'updatedAnalisaDampak',
        'updatedAnalisaProbabilitas',
    ];

    // Listener method agar otomatis update analisa_concate saat input berubah
    public function updatedAnalisaDampak($value)
    {
        $this->analisa_concate = ($value ?? '') . ($this->analisa_probabilitas ?? '');
    }

    public function updatedAnalisaProbabilitas($value)
    {
        $this->analisa_concate = ($this->analisa_dampak ?? '') . ($value ?? '');
    }

}