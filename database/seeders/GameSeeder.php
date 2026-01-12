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
        $ensure = function (string $name, array $attrs): void {
            $updated = Game::where('name', $name)->update($attrs);

            if ($updated === 0) {
                Game::create(array_merge(['name' => $name], $attrs));
            }
        };

        $ensure('Lucky Click', ['slug' => 'lucky-click', 'type' => 'casual', 'is_active' => false]);
        $ensure('Score Rush', ['slug' => 'score-rush', 'type' => 'arcade', 'is_active' => false]);

        $ensure('Word Ladder', ['slug' => 'word-ladder', 'type' => 'game_word_ladder', 'is_active' => true]);
        $ensure('Mini Sudoku', ['slug' => 'mini-sudoku', 'type' => 'game_sudoku4', 'is_active' => true]);
        $ensure('Math Sprint', ['slug' => 'math-sprint', 'type' => 'math_sprint', 'is_active' => true]);
        $ensure('Logic Rush', ['slug' => 'logic-rush', 'type' => 'game_logic', 'is_active' => true]);
        $ensure('Memory Rush', ['slug' => 'memory-rush', 'type' => 'game_memory', 'is_active' => true]);
    }
}
