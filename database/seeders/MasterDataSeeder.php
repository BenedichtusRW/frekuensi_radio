<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterData;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'kategori' => [
                'MF', 'HF RUTIN', 'HF NELAYAN'
            ],
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
            'system_config' => [
                'kelas_emisi_manual'
            ],
        ];

        foreach ($data as $category => $values) {
            foreach ($values as $value) {
                MasterData::updateOrCreate(
                    ['category' => $category, 'value' => strtoupper($value)],
                    ['is_active' => ($category === 'system_config' ? false : true)]
                );
            }
        }
    }
}
