<?php

namespace App\Http\Controllers;

use App\Exports\RisikoExport;
use App\Models\Risk;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LaporanRisikoExport;


class LaporanRisikoController extends Controller
{
   public function exportExcel()
{
    return Excel::download(new LaporanRisikoExport, 'laporan-risiko.xlsx');
}

    public function exportPDF()
    {
        $risikos = Risk::all();
        $pdf = PDF::loadView('filament.pages.laporan-risiko-pdf', compact('risikos'));
        return $pdf->download('laporan-risiko.pdf');
    }
}
