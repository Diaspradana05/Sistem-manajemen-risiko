<?php

namespace App\Http\Controllers;

use App\Exports\LaporanRisikoExport;
use App\Models\Risk;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanRisikoController extends Controller
{
    // Export Excel dengan filter
    public function exportExcel(Request $request)
    {
        $risikos = $this->getFilteredRisks($request);

        return Excel::download(new LaporanRisikoExport($risikos), 'laporan-risiko.xlsx');
    }

    // Export PDF dengan filter
    public function exportPDF(Request $request)
    {
        $risikos = $this->getFilteredRisks($request);

        $pdf = PDF::loadView('filament.pages.laporan-risiko-pdf', compact('risikos'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-risiko.pdf');
    }

    /**
     * Ambil data risiko sesuai filter dan role user
     */
    private function getFilteredRisks(Request $request)
    {
        $user = auth()->user();

        $query = Risk::with(['company', 'division']);

        // Role-based access
        if ($user->hasRole('superadmin')) {
            // Superadmin → semua risiko
        } elseif ($user->hasRole('direksi')) {
            // Direksi → semua risiko di company miliknya
            $query->where('company_id', $user->company_id);
        } else {
            // User biasa → hanya divisi miliknya
            $userDivisionIds = $user->divisions()->pluck('divisions.id');
            $query->whereIn('division_id', $userDivisionIds);
        }

        // Filter dari request
        if ($request->query('company_id')) {
            $query->where('company_id', $request->query('company_id'));
        }
        if ($request->query('division_id')) {
            $query->where('division_id', $request->query('division_id'));
        }
        if ($request->query('year')) {
            $query->where('year', $request->query('year'));
        }

        return $query->get();
    }
}
