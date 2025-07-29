<?php

namespace App\Exports;

use App\Models\Risk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RisikoExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Risk::select('id', 'nama', 'kategori', 'dampak', 'probabilitas', 'skor', 'status')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Risiko',
            'Kategori',
            'Dampak',
            'Probabilitas',
            'Skor Risiko',
            'Status',
        ];
    }
}
