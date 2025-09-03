<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateDivisionUserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua data dari division_year
        $divisionYears = DB::table('division_year')->get();

        foreach ($divisionYears as $dy) {
            // Update division_user berdasarkan division_id
            DB::table('division_user')
                ->where('division_id', $dy->division_id)
                ->update([
                    'division_year_id' => $dy->id
                ]);
        }
    }
}
