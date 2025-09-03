<?php

namespace App\Filament\User\Widgets;

use App\Models\Risk;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AnalisisrisikoBarChartklinis extends ChartWidget
{
    protected static ?string $heading = 'Risiko Klinis per Lokasi';

    protected function getData(): array
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {
            // superadmin → semua risiko klinis
            $risiko = Risk::where('tipe_risiko', 'klinis');
        } elseif ($user->hasRole('direksi')) {
            // direksi → semua risiko klinis di company miliknya
            $companyId = $user->company_id;

            $risiko = Risk::where('tipe_risiko', 'klinis')
                ->where('company_id', $companyId);
        } else {
            // user biasa → hanya divisi yang dimiliki
            $divisionIds = $user->divisions()->pluck('divisions.id');

            $risiko = Risk::where('tipe_risiko', 'klinis')
                ->whereIn('division_id', $divisionIds);
        }

        $data = $risiko
            ->select('area_lokasi', DB::raw('count(*) as jumlah'))
            ->groupBy('area_lokasi')
            ->pluck('jumlah', 'area_lokasi');

        return [
            'datasets' => [[
                'label' => 'Jumlah Risiko',
                'data' => $data->values(),
                'backgroundColor' => '#3b82f6',
                'barThickness' => 100,
                'maxBarThickness' => 140,
            ]],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getHeight(): ?int
    {
        return 250;
    }

    public static function getColumns(): int | array
    {
        return ['default' => 12, 'md' => 6, 'sm' => 12];
    }

    protected static ?int $sort = 5;
}
