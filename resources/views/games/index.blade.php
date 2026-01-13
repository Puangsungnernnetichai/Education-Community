@extends('layouts.app')

@section('content')
@php
    $mathSprint = $games->firstWhere('type', 'math_sprint');
    $initials = function (?string $name): string {
        $name = trim((string) $name);
        if ($name === '') return 'G';
        $parts = preg_split('/\s+/', $name) ?: [];
        $letters = '';
        foreach ($parts as $p) {
            if ($p === '') continue;
            $first = function_exists('mb_substr') ? mb_substr($p, 0, 1) : substr($p, 0, 1);
            $letters .= function_exists('mb_strtoupper') ? mb_strtoupper($first) : strtoupper($first);
            $len = function_exists('mb_strlen') ? mb_strlen($letters) : strlen($letters);
            if ($len >= 2) break;
        }
        return $letters !== '' ? $letters : 'G';
    };
    $difficulty = function ($type) {
        return match ($type) {
            'math_sprint' => ['label' => 'Medium', 'classes' => 'bg-indigo-600 text-white'],
            'game_word_ladder' => ['label' => 'Medium', 'classes' => 'bg-indigo-600 text-white'],
            'game_sudoku4' => ['label' => 'Medium', 'classes' => 'bg-indigo-600 text-white'],
            'game_memory' => ['label' => 'Easy', 'classes' => 'bg-emerald-600 text-white'],
            'game_logic' => ['label' => 'Hard', 'classes' => 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900'],
            'arcade' => ['label' => 'Hard', 'classes' => 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900'],
            'casual' => ['label' => 'Easy', 'classes' => 'bg-emerald-600 text-white'],
            default => ['label' => 'Easy', 'classes' => 'bg-slate-700 text-white'],
        };
    };
    $description = function ($type) {
        return match ($type) {
            'math_sprint' => 'Solve as many questions as you can in 60 seconds. Fast, focused practice.',
            'game_logic' => 'Answer fast under pressure. One mistake (or timeout) ends the run.',
            'game_memory' => 'Watch the pattern, then repeat it. Builds focus and recall.',
            'game_word_ladder' => 'Transform a word to the target by changing one letter each step.',
            'game_sudoku4' => 'Fill a 4x4 mini-sudoku fast: rows, columns, and 2x2 blocks.',
            'arcade' => 'High-energy challenges designed to build speed and confidence.',
            'casual' => 'Quick, low-pressure mini challenges. Great for warm-ups.',
            default => 'Practice, play, and earn points.',
        };
    };
@endphp

<style>
    @keyframes gh-float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    @keyframes gh-float-soft {
        0%, 100% { transform: translateY(0px) rotate(-1deg); }
        50% { transform: translateY(-8px) rotate(1deg); }
    }
    @keyframes gh-pulse {
        0%, 100% { transform: scale(1); opacity: .55; }
        50% { transform: scale(1.06); opacity: .75; }
    }

    .gh-blob { animation: gh-pulse 2.6s ease-in-out infinite; }
    .gh-card-1 { animation: gh-float 2.4s ease-in-out infinite; }
    .gh-card-2 { animation: gh-float-soft 2.8s ease-in-out infinite; }
    .gh-card-3 { animation: gh-float 3.1s ease-in-out infinite; }
</style>

<div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
    <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10">
        <div class="grid gap-6 p-6 sm:p-10 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <div class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:text-slate-200 dark:ring-white/10">
                    Educational • Modern • Points
                </div>
                <h1 class="mt-4 text-3xl font-black tracking-tight text-slate-900 dark:text-slate-100 sm:text-4xl">Game Hub</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                    Short, focused mini-games inspired by the best learning platforms — built to help you practice consistently and earn points.
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <div class="inline-flex items-center rounded-2xl bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:text-slate-200 dark:ring-white/10">
                        Your points:
                        <span class="ml-2 font-black text-slate-900 dark:text-slate-100">{{ auth()->user()->points ?? 0 }}</span>
                    </div>
                    <a href="{{ route('leaderboard.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-950">
                        View leaderboard
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="relative mx-auto h-44 w-full max-w-sm overflow-hidden rounded-3xl bg-slate-50 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:ring-white/10 sm:h-56">
                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-indigo-200/70 blur-2xl dark:bg-indigo-500/20 gh-blob"></div>
                    <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-emerald-200/60 blur-2xl dark:bg-emerald-500/15 gh-blob" style="animation-delay: .35s"></div>

                    <div class="relative h-full w-full">
                        <div class="absolute left-6 top-7 gh-card-1">
                            <div class="w-40 rounded-2xl bg-white/95 p-4 shadow-sm ring-1 ring-slate-900/10 backdrop-blur dark:bg-slate-900/70 dark:ring-white/10">
                                <div class="text-xs font-semibold text-slate-500 dark:text-slate-300">Math Sprint</div>
                                <div class="mt-1 text-2xl font-black tracking-tight text-slate-900 dark:text-slate-100">7 + 5 = ?</div>
                                <div class="mt-3 inline-flex items-center rounded-full bg-indigo-600 px-2 py-0.5 text-xs font-semibold text-white">+10 points</div>
                            </div>
                        </div>

                        <div class="absolute right-6 top-16 gh-card-2">
                            <div class="w-36 rounded-2xl bg-white/95 p-4 shadow-sm ring-1 ring-slate-900/10 backdrop-blur dark:bg-slate-900/70 dark:ring-white/10">
                                <div class="text-xs font-semibold text-slate-500 dark:text-slate-300">Streak</div>
                                <div class="mt-1 text-3xl font-black text-slate-900 dark:text-slate-100">x3</div>
                                <div class="mt-2 text-xs font-semibold text-emerald-700 dark:text-emerald-300">Combo bonus</div>
                            </div>
                        </div>

                        <div class="absolute bottom-6 left-16 gh-card-3">
                            <div class="w-44 rounded-2xl bg-white/95 p-4 shadow-sm ring-1 ring-slate-900/10 backdrop-blur dark:bg-slate-900/70 dark:ring-white/10">
                                <div class="flex items-center justify-between">
                                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-300">Progress</div>
                                    <div class="text-xs font-semibold text-slate-700 dark:text-slate-200">60s</div>
                                </div>
                                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-white/10">
                                    <div class="h-full w-2/3 rounded-full bg-indigo-600"></div>
                                </div>
                                <div class="mt-3 text-xs text-slate-600 dark:text-slate-300">Short practice. Big impact.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        @if ($mathSprint)
            @php($badge = $difficulty($mathSprint->type))
            <div class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-900/5 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-900/40 dark:ring-white/10">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="h-11 w-11 overflow-hidden rounded-2xl bg-slate-50 ring-1 ring-slate-900/10 dark:bg-slate-950/60 dark:ring-white/10">
                                @if (!empty($mathSprint->logo_path))
                                    <img src="{{ asset($mathSprint->logo_path) }}" alt="{{ $mathSprint->name }} logo" class="h-full w-full object-cover" loading="lazy" />
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-sm font-black text-slate-700 dark:text-slate-100">
                                        {{ $initials($mathSprint->name) }}
                                    </div>
                                @endif
                            </div>

                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Featured</div>
                                <div class="mt-1 text-xl font-black tracking-tight text-slate-900 dark:text-slate-100">{{ $mathSprint->name }}</div>
                            </div>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badge['classes'] }}">{{ $badge['label'] }}</span>
                    </div>

                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">{{ $description($mathSprint->type) }}</p>

                    <div class="mt-5">
                        <a href="{{ route('games.play', $mathSprint) }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                            Play
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-900/5 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-900/40 dark:ring-white/10">
            <div class="p-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Coming soon</div>
                        <div class="mt-1 text-xl font-black tracking-tight text-slate-900 dark:text-slate-100">Daily Review</div>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-slate-700 px-2.5 py-1 text-xs font-semibold text-white">Easy</span>
                </div>
                <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">A quick, guided session that adapts to what you practiced.</p>
                <div class="mt-5">
                    <button type="button" disabled class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-200 px-5 py-3 text-sm font-semibold text-slate-500 dark:bg-white/10 dark:text-slate-300">
                        Coming soon
                    </button>
                </div>
            </div>
        </div>

        <div class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-900/5 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-900/40 dark:ring-white/10">
            <div class="p-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Coming soon</div>
                        <div class="mt-1 text-xl font-black tracking-tight text-slate-900 dark:text-slate-100">Memory Match</div>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-indigo-600 px-2.5 py-1 text-xs font-semibold text-white">Medium</span>
                </div>
                <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">Build recall with short, playful matching rounds.</p>
                <div class="mt-5">
                    <button type="button" disabled class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-200 px-5 py-3 text-sm font-semibold text-slate-500 dark:bg-white/10 dark:text-slate-300">
                        Coming soon
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-10">
        <div class="flex items-end justify-between gap-4">
            <div>
                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">All games</div>
                <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">Pick a game to start earning points.</div>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($games as $game)
                @continue($mathSprint && $game->id === $mathSprint->id)
                @php($badge = $difficulty($game->type))

                <div class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-900/5 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-900/40 dark:ring-white/10">
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="h-10 w-10 overflow-hidden rounded-2xl bg-slate-50 ring-1 ring-slate-900/10 dark:bg-slate-950/60 dark:ring-white/10">
                                    @if (!empty($game->logo_path))
                                        <img src="{{ asset($game->logo_path) }}" alt="{{ $game->name }} logo" class="h-full w-full object-cover" loading="lazy" />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-xs font-black text-slate-700 dark:text-slate-100">
                                            {{ $initials($game->name) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-lg font-black tracking-tight text-slate-900 dark:text-slate-100">{{ $game->name }}</div>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badge['classes'] }}">{{ $badge['label'] }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $description($game->type) }}</p>
                        <div class="mt-5">
                            <a href="{{ route('games.play', $game) }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                Play
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
