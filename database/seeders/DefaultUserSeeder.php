<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@itci.com'],
            [
                'name' => 'Admin ITCI',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
