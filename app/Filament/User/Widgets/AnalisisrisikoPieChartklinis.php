<?php

namespace App\Filament\User\Widgets;

use App\Models\Risk;
use App\Models\Division;
use Filament\Widgets\ChartWidget;

class AnalisisrisikoPieChartklinis extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Risiko Klinis';

    protected function getData(): array
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {
            // superadmin → semua risiko klinis
            $risiko = Risk::where('tipe_risiko', 'klinis')->get();
        } elseif ($user->hasRole('direksi')) {
            // direksi → semua divisi dalam company miliknya
            $divisionIds = Division::where('company_id', $user->company_id)->pluck('id');
            $risiko = Risk::where('tipe_risiko', 'klinis')
                ->whereIn('division_id', $divisionIds)
                ->get();
        } else {
            // role lain → hanya divisi yang dimiliki user
            $divisionIds = $user->divisions()->pluck('divisions.id');
            $risiko = Risk::where('tipe_risiko', 'klinis')
                ->whereIn('division_id', $divisionIds)
                ->get();
        }

        // mapping warna sesuai peringkat risiko
        $warnaMapping = [
            'Sangat Rendah' => '#3b82f6', // biru
            'Rendah'        => '#22c55e', // hijau
            'Sedang'        => '#facc15', // kuning
            'Tinggi'        => '#f97316', // oranye
            'Sangat Tinggi' => '#ef4444', // merah
        ];

        // hitung jumlah risiko per peringkat
        $peringkatRisiko = $risiko
            ->groupBy('peringkat_risiko')
            ->map(fn($items) => $items->count())
            ->toArray();

        // urutkan sesuai mapping di atas
        $labels = array_keys($warnaMapping);
        $data   = [];
        $colors = [];

        foreach ($labels as $label) {
            $data[]   = $peringkatRisiko[$label] ?? 0; // kalau tidak ada = 0
            $colors[] = $warnaMapping[$label];
        }

        return [
            'datasets' => [[
                'label' => 'Jumlah Risiko',
                'data' => $data,
                'backgroundColor' => $colors,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): ?array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }

    protected function getHeight(): ?int
    {
        return 200;
    }

    public static function getColumns(): int | array
    {
        return ['default' => 12, 'md' => 6, 'sm' => 12];
    }

    protected static ?int $sort = 3;
}
