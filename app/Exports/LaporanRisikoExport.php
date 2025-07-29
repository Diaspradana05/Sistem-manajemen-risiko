<?php

namespace App\Exports;

use App\Models\Risk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanRisikoExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Risk::select(
            'id',
            'risiko',
            'kode_risiko',
            'dampak',
            'analisa_probabilitas',
            'skor',
            'peringkat_risiko'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Risiko',
            'Kode Risiko',
            'Dampak',
            'Probabilitas',
            'Skor Risiko',
            'Peringkat Risiko',
        ];
    }
}
