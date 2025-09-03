<?php

namespace App\Filament\Widgets;

use App\Models\Risk;
use Filament\Widgets\Widget;

class RiskMatrixWidget extends Widget
{
    protected static string $view = 'filament.widgets.risk-matrix-widget';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    // Filter properties
    public $filterCompany;
    public $filterDivision;
    public $filterYear;

    public $matrix = []; // matriks yang akan ditampilkan di Blade

    public function mount(): void
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {
            // superadmin bebas pilih semua
            $this->filterCompany  = null;
            $this->filterDivision = null;
        } elseif ($user->hasRole('direksi')) {
            // direksi fix di company_id miliknya, bisa lihat semua divisi
            $this->filterCompany  = $user->company_id;
            $this->filterDivision = null;
        } else {
            // user biasa → fix company_id dan divisi pilihannya
            $this->filterCompany  = $user->company_id;
            $this->filterDivision = null;
        }

        $this->filterYear = null;

        $this->loadMatrix();
    }

    // Load matriks berdasarkan filter
    public function loadMatrix(): void
    {
        $matrix = [];

        // Inisialisasi 5x5 matrix
        for ($p = 1; $p <= 5; $p++) {
            for ($i = 1; $i <= 5; $i++) {
                $matrix[$p][$i] = [
                    'total' => 0,
                    'skor' => $p * $i,
                    'peringkat_risiko' => '-',
                ];
            }
        }

        $user = auth()->user();

        $query = Risk::selectRaw('
                analisa_probabilitas,
                analisa_dampak,
                peringkat_risiko,
                (analisa_probabilitas * analisa_dampak) as skor,
                COUNT(*) as total
            ')
            ->groupBy('analisa_probabilitas', 'analisa_dampak', 'peringkat_risiko');

        // 🔹 Filter company/division berdasarkan role
        if ($user->hasRole('superadmin')) {
            if ($this->filterCompany) {
                $query->where('company_id', $this->filterCompany);
            }
            if ($this->filterDivision) {
                $query->where('division_id', $this->filterDivision);
            }
        } elseif ($user->hasRole('direksi')) {
            // direksi fix ke company miliknya
            $query->where('company_id', $user->company_id);

            if ($this->filterDivision) {
                // bisa filter per divisi
                $query->where('division_id', $this->filterDivision);
            }
            // kalau tidak pilih divisi → otomatis semua divisi di company itu
        } else {
            // user biasa fix ke company + divisi miliknya
            $query->where('company_id', $user->company_id);

            if ($this->filterDivision) {
                $query->where('division_id', $this->filterDivision);
            } else {
                $query->whereIn('division_id', $user->divisions->pluck('id'));
            }
        }

        // 🔹 Filter tahun
        if ($this->filterYear) {
            if (\Schema::hasColumn('risks', 'year')) {
                $query->where('year', $this->filterYear);
            } else {
                $query->whereRaw('EXTRACT(YEAR FROM created_at)::int = ?', [$this->filterYear]);
            }
        }

        $risks = $query->get();

        // Masukkan data ke matriks
        foreach ($risks as $risk) {
            $matrix[$risk->analisa_probabilitas][$risk->analisa_dampak] = [
                'total' => $risk->total,
                'skor' => $risk->skor,
                'peringkat_risiko' => $risk->peringkat_risiko,
            ];
        }

        $this->matrix = $matrix;
    }

    // Hooks Livewire
    public function updatedFilterCompany()
    {
        $this->filterDivision = null;
        $this->loadMatrix();
    }

    public function updatedFilterDivision()
    {
        $this->loadMatrix();
    }

    public function updatedFilterYear()
    {
        $this->loadMatrix();
    }

    // Dropdown options
    public function getCompaniesProperty()
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {
            return \App\Models\Company::all();
        }

        // direksi & user biasa → hanya company miliknya
        return \App\Models\Company::where('id', $user->company_id)->get();
    }

    public function getDivisionsProperty()
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {
            $query = \App\Models\Division::query();

            if ($this->filterCompany) {
                $query->where('company_id', $this->filterCompany);
            }

            return $query->get();
        }

        if ($user->hasRole('direksi')) {
            // direksi → semua divisi di company miliknya
            return \App\Models\Division::where('company_id', $user->company_id)->get();
        }

        // user biasa → hanya divisi yang dia punya
        return $user->divisions;
    }

    public function getYearsProperty()
    {
        if (\Schema::hasColumn('risks', 'year')) {
            return Risk::select('year')
                       ->distinct()
                       ->orderByDesc('year')
                       ->pluck('year')
                       ->map(fn($y) => (int) $y);
        }

        return Risk::selectRaw("EXTRACT(YEAR FROM created_at)::int as year")
                   ->distinct()
                   ->orderByDesc('year')
                   ->pluck('year')
                   ->map(fn($y) => (int) $y);
    }
}
