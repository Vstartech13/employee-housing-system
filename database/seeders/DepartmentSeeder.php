<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['code' => 'HR', 'name' => 'Human Resources'],
            ['code' => 'FIN', 'name' => 'Finance'],
            ['code' => 'PROD', 'name' => 'Produksi'],
            ['code' => 'SAR', 'name' => 'Sarana'],
            ['code' => 'SAFE', 'name' => 'Safety'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
