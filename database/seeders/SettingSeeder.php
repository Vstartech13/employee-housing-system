<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'key' => 'employee_id_prefix',
            'value' => 'KT',
            'description' => 'Prefix untuk ID Karyawan (contoh: KT akan menghasilkan KT001, KT002, dst)'
        ]);
    }
}
