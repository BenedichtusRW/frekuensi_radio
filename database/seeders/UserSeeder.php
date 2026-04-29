<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Super Admin
        User::updateOrCreate(
            ['email' => 'adminsuper@balmon.go.id'],
            [
                'name' => 'Super Admin Balmon',
                'password' => Hash::make('BalmonLampung25'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        // 2. Akun Petugas Admin (User Biasa)
        User::updateOrCreate(
            ['email' => 'adminaja@balmon.go.id'],
            [
                'name' => 'Petugas Balmon',
                'password' => Hash::make('BalmonLampung23'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
