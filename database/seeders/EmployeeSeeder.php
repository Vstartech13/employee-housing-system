<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Department;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::all();
        $names = [
            'Budi Santoso', 'Siti Nurhaliza', 'Ahmad Wijaya', 'Rina Wati', 'Dedi Suryanto',
            'Maya Sari', 'Agus Pratama', 'Dewi Lestari', 'Hendra Gunawan', 'Lina Marlina',
            'Bambang Susilo', 'Fitri Handayani', 'Rudi Hartono', 'Nina Kartika', 'Yudi Setiawan',
            'Ani Rahmawati', 'Eko Prasetyo', 'Sri Mulyani', 'Tono Sugiarto', 'Dian Pertiwi',
            'Andi Wijaya', 'Ratna Sari', 'Hadi Purnomo', 'Endang Susilowati', 'Fajar Nugroho',
            'Wahyu Hidayat', 'Lia Permata', 'Irwan Setiadi', 'Siska Amelia', 'Joko Widodo',
            'Putri Ayu', 'Dadang Sudrajat', 'Nita Anggraini', 'Arief Rahman', 'Mega Puspita',
            'Yanto Kusuma', 'Lia Safitri', 'Hari Wijayanto', 'Dina Marlina', 'Joni Iskandar',
            'Retno Wulandari', 'Iwan Setyawan', 'Eka Novita', 'Ridwan Kamil', 'Yuni Shara',
            'Firman Utina', 'Ria Ricis', 'Bayu Skak', 'Desy Ratnasari', 'Indra Bekti',
            'Nunung Srimulat', 'Parto Patrio', 'Soimah Pancawati', 'Cak Lontong', 'Tessy',
            'Cak Budi', 'Mbak Tutut', 'Pak Tarno', 'Bu Tejo', 'Denny Cagur',
            'Vincent', 'Desta', 'Gading Marten', 'Raffi Ahmad', 'Baim Wong',
            'Ruben Onsu', 'Ayu Ting Ting', 'Iis Dahlia', 'Dewi Perssik', 'Saipul Jamil',
            'Ridho Rhoma', 'Reza Zakarya', 'Zaskia Gotik', 'Siti Badriah', 'Via Vallen'
        ];

        // Create 60 active employees (employee_id will be auto-generated)
        for ($i = 0; $i < 60; $i++) {
            Employee::create([
                'name' => $names[$i],
                'department_id' => $departments->random()->id,
                'status' => 'Aktif'
            ]);
        }

        // Create 12 non-active employees
        for ($i = 60; $i < 72; $i++) {
            Employee::create([
                'name' => $names[$i] ?? 'Employee ' . ($i + 1),
                'department_id' => $departments->random()->id,
                'status' => 'Non-aktif'
            ]);
        }
    }
}
