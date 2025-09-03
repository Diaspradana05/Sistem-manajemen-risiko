<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use App\Models\Risk;
use App\Models\Division;

class LaporanRisiko extends Page
{
    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationLabel  = 'Laporan Risiko';
    protected static ?string $navigationGroup  = 'Manajemen Risiko';
    protected static ?int    $navigationSort   = 4;

    protected static string $view = 'filament.user.pages.laporan-risiko';

    // ---------------------------
    // FILTER PROPERTIES
    // ---------------------------
    public $filterCompany = null;
    public $filterDivision = null;
    public $filterYear     = null;

    public $availableDivisions = [];

    public $risikos = [];

    public function mount(): void
{
    $user = auth()->user();

    if ($user->hasRole('superadmin')) {
        // Superadmin → semua divisi dan semua company
        $this->availableDivisions = Division::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    } elseif ($user->hasRole('direksi')) {
        // Direksi → semua divisi di company yang dimiliki
        $this->availableDivisions = Division::where('company_id', $user->company_id)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $this->filterCompany = $user->company_id;
    } else {
        // User biasa → hanya divisi miliknya
        $this->filterCompany = $user->company_id;

        $this->availableDivisions = $user->divisions()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    $this->applyFilter();
}


    public function updatedFilterCompany($companyId): void
{
    // Reset filter divisi saat company berubah
    $this->filterDivision = null;

    $user = auth()->user();

    if ($user->hasRole('superadmin')) {
        if ($companyId) {
            // Tampilkan divisi hanya untuk company yang dipilih
            $this->availableDivisions = Division::where('company_id', $companyId)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        } else {
            // Semua divisi
            $this->availableDivisions = Division::orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        }
    } elseif ($user->hasRole('direksi')) {
        // Divisi untuk company milik direksi
        $this->availableDivisions = Division::where('company_id', $user->company_id)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    } else {
        // User biasa → hanya divisi miliknya
        $this->availableDivisions = $user->divisions()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
}

public function applyFilter(): void
{
    $user  = auth()->user();
    $query = Risk::query();

    if ($user->hasRole('superadmin')) {
        // Filter superadmin berdasarkan company/divisi jika dipilih
        if ($this->filterCompany) {
            $query->where('company_id', $this->filterCompany);
        }
        if ($this->filterDivision) {
            $query->where('division_id', $this->filterDivision);
        }
    } elseif ($user->hasRole('direksi')) {
        // Direksi → semua divisi di company miliknya
        $query->where('company_id', $user->company_id);

        if ($this->filterDivision) {
            $query->where('division_id', $this->filterDivision);
        } else {
            $divisionIds = Division::where('company_id', $user->company_id)->pluck('id');
            $query->whereIn('division_id', $divisionIds);
        }
    } else {
        // User biasa → hanya divisi miliknya
        $query->where('company_id', $user->company_id);

        if ($this->filterDivision) {
            $query->where('division_id', $this->filterDivision);
        } else {
            $query->whereIn('division_id', $user->divisions->pluck('id'));
        }
    }

    // Filter tahun jika ada
    if ($this->filterYear) {
        $query->where('year', $this->filterYear);
    }

    // Ambil data risiko
    $this->risikos = $query->get();
}

public static function canAccess(): bool
{
    $user = auth()->user();

    // Staff tidak boleh akses halaman ini
    if ($user->hasRole('staf')) {
        return false;
    }

    return true;
}
}