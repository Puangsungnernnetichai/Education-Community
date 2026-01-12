<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameSession;
use App\Services\Games\MathSprintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::where('is_active', true)->orderBy('name')->get();

        return view('games.index', compact('games'));
    }

    public function play(Game $game)
    {
        if (! $game->is_active) {
            abort(404);
        }

        if ($game->type === 'math_sprint') {
            $nonce = (string) Str::uuid();
            $questions = app(MathSprintService::class)->generateQuestions(50);

            Cache::put(
                $this->mathSprintCacheKey($nonce),
                [
                    'user_id' => Auth::id(),
                    'game_id' => $game->id,
                    'issued_at' => now()->timestamp,
                    'questions' => $questions,
                ],
                now()->addHours(2)
            );

            return view('games.play', [
                'game' => $game,
                'mathSprintNonce' => $nonce,
                'mathSprintQuestions' => $questions,
            ]);
        }

        return view('games.play', [
            'game' => $game,
        ]);
    }

    public function startMathSprint(Request $request, Game $game)
    {
        if (! $game->is_active || $game->type !== 'math_sprint') {
            abort(404);
        }

        $nonce = (string) Str::uuid();
        $questions = app(MathSprintService::class)->generateQuestions(50);
        $issuedAt = now()->timestamp;

        Cache::put(
            $this->mathSprintCacheKey($nonce),
            [
                'user_id' => Auth::id(),
                'game_id' => $game->id,
                'issued_at' => $issuedAt,
                'questions' => $questions,
            ],
            now()->addHours(2)
        );

        return response()->json([
            'ok' => true,
            'nonce' => $nonce,
            'issued_at' => $issuedAt,
            'duration' => 60,
            'questions' => $questions,
        ]);
    }

    public function submit(Request $request)
    {
        $gameId = $request->validate([
            'game_id' => ['required', Rule::exists('games', 'id')->where('is_active', true)],
        ])['game_id'];

        $game = Game::whereKey($gameId)->where('is_active', true)->firstOrFail();

        if ($game->type === 'math_sprint') {
            return $this->submitMathSprint($request, $game);
        }

        $data = $request->validate([
            'score' => 'required|integer|min:0|max:100000',
            'duration' => 'nullable|integer|min:0|max:86400',
        ]);

        $session = DB::transaction(function () use ($data, $request, $game) {
            $session = GameSession::create([
                'user_id' => $request->user()->id,
                'game_id' => $game->id,
                'score' => $data['score'],
                'duration' => $data['duration'] ?? null,
            ]);

            $request->user()->increment('points', $data['score']);

            return $session;
        });

        return redirect()->route('games.play', $session->game_id)->with('success', 'Score saved!');
    }

    private function submitMathSprint(Request $request, Game $game)
    {
        $data = $request->validate([
            'nonce' => 'required|uuid',
            'answers' => 'required|string',
            'duration' => 'nullable|integer|min:0|max:120',
        ]);

        $payload = Cache::get($this->mathSprintCacheKey($data['nonce']));
        if (! is_array($payload)) {
            return $request->expectsJson()
                ? response()->json(['ok' => false, 'message' => 'Game session expired. Please reload and try again.'], 419)
                : abort(419);
        }

        if (($payload['user_id'] ?? null) !== $request->user()->id) {
            return $request->expectsJson()
                ? response()->json(['ok' => false, 'message' => 'Forbidden.'], 403)
                : abort(403);
        }

        if (($payload['game_id'] ?? null) !== $game->id) {
            return $request->expectsJson()
                ? response()->json(['ok' => false, 'message' => 'Forbidden.'], 403)
                : abort(403);
        }

        $issuedAt = (int) ($payload['issued_at'] ?? 0);
        if ($issuedAt <= 0) {
            return $request->expectsJson()
                ? response()->json(['ok' => false, 'message' => 'Game session invalid. Please reload and try again.'], 419)
                : abort(419);
        }

        // Time sanity check to avoid very stale replays; align with cache TTL used in play().
        // This intentionally allows normal play + short idles without causing a confusing "save does nothing" UX.
        $maxAgeSeconds = 2 * 60 * 60; // 2 hours
        if (now()->timestamp - $issuedAt > $maxAgeSeconds) {
            return $request->expectsJson()
                ? response()->json(['ok' => false, 'message' => 'Game session expired. Please reload and try again.'], 419)
                : abort(419);
        }

        $submitted = json_decode($data['answers'], true);
        if (! is_array($submitted)) {
            return $request->expectsJson()
                ? response()->json(['ok' => false, 'message' => 'Invalid answers payload.'], 422)
                : abort(422);
        }

        $questions = $payload['questions'] ?? [];
        $byId = [];
        foreach ($questions as $q) {
            if (is_array($q) && isset($q['id'])) {
                $byId[$q['id']] = $q;
            }
        }

        $score = 0;
        $streak = 0;

        foreach ($submitted as $item) {
            if (! is_array($item)) {
                continue;
            }

            $id = $item['id'] ?? null;
            $choice = $item['choice'] ?? null;

            if (! is_string($id) || ! isset($byId[$id])) {
                continue;
            }

            if (! is_int($choice) && ! ctype_digit((string) $choice)) {
                continue;
            }

            $choice = (int) $choice;
            $question = $byId[$id];
            $correct = (int) ($question['answer'] ?? -999999);

            if ($choice === $correct) {
                $score += 10;
                $streak++;

                if ($streak % 3 === 0) {
                    $score += 5;
                }
            } else {
                $score -= 2;
                $streak = 0;
            }
        }

        if ($score < 0) {
            $score = 0;
        }

        $duration = $data['duration'] ?? null;
        if ($duration === null) {
            $duration = min(60, max(0, now()->timestamp - $issuedAt));
        } else {
            $duration = min(60, max(0, (int) $duration));
        }

        $session = DB::transaction(function () use ($request, $game, $score, $duration, $data) {
            $session = GameSession::create([
                'user_id' => $request->user()->id,
                'game_id' => $game->id,
                'score' => $score,
                'duration' => $duration,
            ]);

            $request->user()->increment('points', $score);
            Cache::forget($this->mathSprintCacheKey($data['nonce']));

            return $session;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'score' => $score,
                'points' => (int) $request->user()->fresh()->points,
                'session_id' => $session->id,
            ]);
        }

        return redirect()->route('games.play', $session->game_id)->with('success', 'Math Sprint saved!');
    }

    private function mathSprintCacheKey(string $nonce): string
    {
        return 'math_sprint:' . $nonce;
    }
}
