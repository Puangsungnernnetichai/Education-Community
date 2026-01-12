<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Game::updateOrCreate(
            ['name' => 'Lucky Click'],
            ['type' => 'casual', 'is_active' => true]
        );

        Game::updateOrCreate(
            ['name' => 'Score Rush'],
            ['type' => 'arcade', 'is_active' => true]
        );

        Game::updateOrCreate(
            ['name' => 'Math Sprint'],
            ['type' => 'math_sprint', 'is_active' => true]
        );
    }
}
