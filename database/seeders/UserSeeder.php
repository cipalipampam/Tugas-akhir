<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Super Administrator',
            'email' => 'agungalamsyah719@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'superadministrator',
        ]);
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'Firman@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
         User::factory()->create([
            'name' => 'Administrator',
            'email' => 'alamsyah@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

    }
}
