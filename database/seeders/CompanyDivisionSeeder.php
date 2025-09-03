<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Division;

class CompanyDivisionSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan tabel
        DB::statement('TRUNCATE TABLE divisions RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE companies RESTART IDENTITY CASCADE');

        // Data perusahaan
        $companies = [
            'Rumah Sakit Semen Gresik',
            'PT Cipta Nirmala',
            'PT Nale Integration'
        ];

        // Data divisi unik (6 divisi)
        $divisions = [
            'Divisi Teknologi Informasi',
            'Divisi Keuangan & Akuntansi',
            'Divisi Sumber Daya Manusia',
            'Divisi Pengadaan & Logistik',
            'Divisi Humas & Pemasaran',
            'Divisi Pengembangan Layanan'
        ];

        // Bagi divisi ke perusahaan (2 divisi per perusahaan)
        $index = 0;
        foreach ($companies as $companyName) {
            $company = Company::create(['name' => $companyName]);

            for ($i = 0; $i < 2; $i++) {
                Division::create([
                    'name' => $divisions[$index],
                    'company_id' => $company->id
                ]);
                $index++;
            }
        }
    }
}
