<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DivisionYear;
use App\Models\Division;

class DivisionYearSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Division 1 ada di 2024 dan 2025
            ['division_id' => 1, 'year' => 2024],
            ['division_id' => 1, 'year' => 2025],

            // Division 2 cuma ada di 2023
            ['division_id' => 2, 'year' => 2023],

            // Division 3 cuma ada di 2025
            ['division_id' => 3, 'year' => 2025],

            // Division 4 ada di 2023 dan 2024
            ['division_id' => 4, 'year' => 2023],
            ['division_id' => 4, 'year' => 2024],

            ['division_id' => 5, 'year' => 2023],
            ['division_id' => 5, 'year' => 2024],
            ['division_id' => 5, 'year' => 2025],

            ['division_id' => 6, 'year' => 2024],

        ];

        foreach ($data as $item) {
            DivisionYear::create($item);
        }
    }
}

