<?php

namespace App\Filament\User\Widgets;

use App\Models\Risk;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RingkasanRisiko extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Risiko', Risk::count())
                ->icon('heroicon-o-clipboard-document-list'),

            Stat::make('Risiko Belum Ditangani', Risk::where('perlu_penanganan', true)->count())
                ->icon('heroicon-o-exclamation-circle')
                ->color('danger'),

            Stat::make('Rata-rata Skor Risiko', number_format(Risk::avg('skor'), 2))
                ->icon('heroicon-o-calculator'),

            Stat::make('Risiko Peringkat Tinggi', Risk::where('peringkat_risiko', 'High')->count())
                ->icon('heroicon-o-fire')
                ->color('warning'),

            Stat::make('Risiko dengan Strategi Hindari', Risk::where('hindari_risiko', true)->count())
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('info'),

            Stat::make('Risiko dengan Strategi Cegah', Risk::where('cegah_kerugian', true)->count())
                ->icon('heroicon-o-shield-check')
                ->color('success'),
        ];
    }
}
