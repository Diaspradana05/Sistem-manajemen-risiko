<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan role sudah ada
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);

        // Buat user dan tetapkan role superadmin
        $user = User::firstOrCreate(
            ['email' => 'pradanadias601@gmail.com'],
            [
                'name' => 'Dias',
                'password' => bcrypt('Pradana123'), // Ganti dengan password aman
            ]
        );

        $user->assignRole($superadminRole);
    }
}
