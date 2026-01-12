<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MiniSudokuController extends Controller
{
    public function show(Request $request)
    {
        return view('games.mini-sudoku');
    }

    public function finish(Request $request)
    {
        $data = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:100000'],
            'duration' => ['nullable', 'integer', 'min:0', 'max:86400'],
        ]);

        $game = Game::updateOrCreate(
            ['name' => 'Mini Sudoku'],
            ['slug' => 'mini-sudoku', 'type' => 'game_sudoku4', 'is_active' => true]
        );

        $user = $request->user();
        $score = (int) $data['score'];
        $duration = array_key_exists('duration', $data) ? $data['duration'] : null;
        $duration = $duration === null ? null : (int) $duration;

        $session = DB::transaction(function () use ($user, $game, $score, $duration) {
            $session = GameSession::create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'score' => $score,
                'duration' => $duration,
            ]);

            if ($score > 0) {
                $user->increment('points', $score);
            }

            return $session;
        });

        return response()->json([
            'ok' => true,
            'score' => $score,
            'points' => (int) $user->fresh()->points,
            'session_id' => $session->id,
        ]);
    }
}
