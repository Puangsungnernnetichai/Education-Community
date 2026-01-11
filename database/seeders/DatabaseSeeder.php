<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Normal User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
            ]
        );
    }
}
