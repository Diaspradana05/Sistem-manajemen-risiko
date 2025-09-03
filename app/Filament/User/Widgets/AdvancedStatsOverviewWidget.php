<?php

namespace App\Filament\User\Widgets;

use App\Models\Risk;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class AdvancedStatsOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 1; // tampil paling atas

    protected function getStats(): array
{
    $user = auth()->user();

    if ($user->hasRole('superadmin')) {
        // superadmin lihat semua risiko
        $query = Risk::query();
    } elseif ($user->hasRole('direksi')) {
        // direksi lihat semua risiko di company miliknya
        $query = Risk::where('company_id', $user->company_id);
    } else {
        // staf, supervisor, manajer → lihat risiko di divisi yang dia punya
        $divisionIds = $user->divisions()->pluck('divisions.id');
        $query = Risk::whereIn('division_id', $divisionIds);
    }

    return [
        Stat::make('Risiko Klinis', (clone $query)->where('tipe_risiko','klinis')->count())
            ->icon('heroicon-o-user-group')
            ->description('Jumlah risiko klinis')
            ->iconColor('purple')
            ->backgroundColor('purple'),

        Stat::make('Risiko Non-Klinis', (clone $query)->where('tipe_risiko','non-klinis')->count())
            ->icon('heroicon-o-building-library')
            ->description('Jumlah risiko non klinis')
            ->iconColor('gray')
            ->backgroundColor('gray'),

        Stat::make('Total Risiko', (clone $query)->count())
            ->icon('heroicon-o-clipboard-document-list')
            ->description('Semua risiko total')
            ->iconColor('primary')
            ->backgroundColor('primary'),

        Stat::make('Belum Ditangani', (clone $query)->where('perlu_penanganan', true)->count())
            ->icon('heroicon-o-exclamation-circle')
            ->description('Risiko belum ditangani')
            ->iconColor('danger')
            ->backgroundColor('danger'),

        Stat::make('Rata-rata Skor Risiko', number_format((clone $query)->avg('skor'),2))
            ->icon('heroicon-o-calculator')
            ->description('Rata-rata skor risiko')
            ->iconColor('warning')
            ->backgroundColor('warning'),

        Stat::make('Peringkat Sangat Tinggi', (clone $query)->where('peringkat_risiko','Sangat Tinggi')->count())
            ->icon('heroicon-o-fire')
            ->description('Jumlah risiko dengan peringkat sangat tinggi')
            ->iconColor('danger')
            ->backgroundColor('danger'),

        Stat::make('Peringkat Tinggi', (clone $query)->where('peringkat_risiko','Tinggi')->count())
            ->icon('heroicon-o-bolt')
            ->description('Jumlah risiko peringkat tinggi')
            ->iconColor('warning')
            ->backgroundColor('warning'),

        Stat::make('Peringkat Sedang', (clone $query)->where('peringkat_risiko','Sedang')->count())
            ->icon('heroicon-o-adjustments-horizontal')
            ->description('Jumlah risiko peringkat sedang')
            ->iconColor('yellow')
            ->backgroundColor('yellow'),

        Stat::make('Peringkat Rendah', (clone $query)->where('peringkat_risiko','Rendah')->count())
            ->icon('heroicon-o-arrow-down-circle')
            ->description('Jumlah risiko peringkat rendah')
            ->iconColor('success')
            ->backgroundColor('success')
    ];
}
}