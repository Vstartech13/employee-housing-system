<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@itci.com',
            'password' => bcrypt('admin123')
        ]);

        // Call other seeders
        $this->call([
            SettingSeeder::class,
            DepartmentSeeder::class,
            EmployeeSeeder::class,
            RoomSeeder::class,
        ]);
    }
}
