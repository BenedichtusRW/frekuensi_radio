<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'stasiun_monitor' => [
                'MSHF LAMPUNG',
            ],
            'kode_negara' => [
                'INDONESIA (INS)',
            ],
            'administrasi_termonitor' => [
                'INS',
            ],
            'kelas_stasiun' => [
                'AL', 'AM', 'AT', 'BC', 'BT', 'FA', 'FB', 'FC', 'FD', 'FG', 
                'FL', 'FP', 'FX', 'LR', 'MA', 'ML', 'MO', 'MR', 'MS', 'NL', 
                'NR', 'OD', 'OE', 'PL', 'RM', 'RN', 'SA', 'SM', 'SS', 'TC', 
                'UV', 'UW'
            ],
        ];

        foreach ($data as $category => $values) {
            foreach ($values as $value) {
                \App\Models\MasterData::updateOrCreate(
                    ['category' => $category, 'value' => $value],
                    ['is_active' => true]
                );
            }
        }
    }
}
