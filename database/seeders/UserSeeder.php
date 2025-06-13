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
        // Create superadministrator if not exists
        if (!User::where('email', 'superadmin@example.com')->exists()) {
            User::create([
                'name' => 'Super Administrator',
                'email' => 'agungalamsyah719@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'superadministrator',
            ]);
        }

        // Create admin if not exists
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]);
        }
    }
}
