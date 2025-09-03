<?php

namespace App\Filament\Actions;

use Filament\Tables;
use Filament\Forms;

class RejectRiskAction extends Tables\Actions\Action
{
    public static function create(): static
    {
        return static::make('reject')
            ->label('Tolak')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->form([
                Forms\Components\Textarea::make('alasan_penolakan')
                    ->label('Alasan Penolakan')
                    ->required()
                    ->dehydrated(true),
            ])
            ->requiresConfirmation()
            ->visible(fn () => auth()->check() && auth()->user()->hasAnyRole(['manajer', 'supervisor', 'direksi']))
            ->action(function ($record, array $data) {
    $record->forceFill([
        'status_persetujuan' => 'ditolak',
        'alasan_penolakan'   => $data['alasan_penolakan'],
        'ditolak_oleh'       => auth()->id(),
        'ditolak_pada'       => now(),
    ])->save();
});
    }
}
