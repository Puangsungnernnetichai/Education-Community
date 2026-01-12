<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'game_id' => ['nullable', 'integer', Rule::exists('games', 'id')],
        ]);

        $selectedGameId = $validated['game_id'] ?? null;

        $games = Game::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $top = DB::table('game_sessions')
            ->join('users', 'users.id', '=', 'game_sessions.user_id')
            ->join('games', 'games.id', '=', 'game_sessions.game_id')
            ->when($selectedGameId, function ($q) use ($selectedGameId) {
                $q->where('game_sessions.game_id', $selectedGameId);
            })
            ->select([
                'game_sessions.user_id',
                'users.name as username',
                'game_sessions.score',
                'game_sessions.game_id',
                'games.name as game_name',
                'game_sessions.created_at',
            ])
            ->orderByDesc('game_sessions.score')
            ->orderByDesc('game_sessions.created_at')
            ->limit(20)
            ->get();

        return view('leaderboard.index', [
            'games' => $games,
            'selectedGameId' => $selectedGameId,
            'top' => $top,
        ]);
    }
}
