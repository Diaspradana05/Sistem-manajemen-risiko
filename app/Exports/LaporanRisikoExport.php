<?php

namespace App\Exports;

use App\Models\Risk;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LaporanRisikoExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithCustomStartCell,
    ShouldAutoSize
{
    protected $risikos;

    // Constructor menerima data filter
    public function __construct($risikos)
    {
        $this->risikos = $risikos;
    }

    public function collection(): Collection
    {
        return $this->risikos;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
{
    return [
        'No',
        'Company',
        'Divisi',
        'Tahun',
        'Nama Risiko',
        'Kode Risiko',
        'Dampak',
        'Probabilitas',
        'Skor Risiko',
        'Peringkat Risiko',
        'Tipe Risiko',
        'Status Persetujuan',
        'Dibuat Oleh',
        'Ditinjau / Disetujui / Ditolak Oleh',
        'Tanggal Tinjau',
    ];
}

public function map($risk): array
{
    return [
        $risk->id,
        optional($risk->company)->name,
        optional($risk->division)->name,
        $risk->year,
        $risk->risiko,
        $risk->kode_risiko,
        $risk->dampak,
        $risk->analisa_probabilitas,
        $risk->skor,
        $risk->peringkat_risiko,
        ucfirst($risk->tipe_risiko),
        ucfirst($risk->status_persetujuan), // Pastikan kolom ini ada di DB
        optional($risk->dibuatOleh)->name ?? '-', // relasi user pembuat
        optional($risk->ditinjauOleh)->name ?? '-', // relasi user peninjau
        $risk->ditinjau_pada ? $risk->ditinjau_pada->format('d-m-Y') : '-', // pastikan tipe date
    ];
}

    public function styles(Worksheet $sheet)
{
    // --- (1) Logo ---
    $drawing = new Drawing();
    $drawing->setName('Logo PT Cipta Nirmala');
    $drawing->setDescription('Logo');
    $drawing->setPath(public_path('image/PT Cipta Nirmala.png'));
    $drawing->setHeight(40);
    $drawing->setCoordinates('A1');
    $drawing->setOffsetX(5);
    $drawing->setOffsetY(15);
    $drawing->setWorksheet($sheet);

    // --- (2) Judul laporan ---
    $lastColumn = $sheet->getHighestColumn(); // otomatis ambil kolom terakhir
    $sheet->mergeCells("B2:{$lastColumn}2");
    $sheet->setCellValue('B2', 'Laporan Risiko - ' . Carbon::now()->format('d/m/Y'));
    $sheet->getStyle("B2:{$lastColumn}2")->applyFromArray([
        'font' => ['bold' => true, 'size' => 14],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);

    // --- (3) Style header ---
    $sheet->getStyle("A4:{$lastColumn}4")->applyFromArray([
        'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
        'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F81BD']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    ]);

    // --- (4) Border & zebra ---
    $highestRow = $sheet->getHighestRow();
    $sheet->getStyle("A4:{$lastColumn}{$highestRow}")->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ]);

    for ($row = 5; $row <= $highestRow; $row++) {
        if ($row % 2 === 0) {
            $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFE8F3FF']],
            ]);
        }
    }

    return [];
}
}