@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-10">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Leaderboard</h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Top 20 scores{{ $selectedGameId ? ' for selected game' : '' }}.</p>
        </div>

        <form method="GET" action="{{ route('leaderboard.index') }}" class="flex items-center gap-2">
            <label for="game_id" class="text-sm font-medium text-slate-700 dark:text-slate-200">Game</label>
            <select
                id="game_id"
                name="game_id"
                class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm ring-1 ring-transparent transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                onchange="this.form.submit()"
            >
                <option value="">All games</option>
                @foreach ($games as $g)
                    <option value="{{ $g->id }}" {{ (string)$selectedGameId === (string)$g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                @endforeach
            </select>
            <noscript>
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">Filter</button>
            </noscript>
        </form>
    </div>

    <div class="mt-6 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-slate-950/60">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">User</th>
                        @if (!$selectedGameId)
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Game</th>
                        @endif
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Score</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                    @forelse ($top as $i => $row)
                        @php
                            $isMe = auth()->check() && (int)auth()->id() === (int)$row->user_id;
                        @endphp
                        <tr class="{{ $isMe ? 'bg-indigo-50/70 dark:bg-indigo-500/10' : '' }}">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $i + 1 }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-900 dark:text-slate-100">
                                <span class="font-semibold">{{ $row->username }}</span>
                                @if ($isMe)
                                    <span class="ml-2 inline-flex items-center rounded-full bg-indigo-600 px-2 py-0.5 text-xs font-semibold text-white">You</span>
                                @endif
                            </td>
                            @if (!$selectedGameId)
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700 dark:text-slate-200">{{ $row->game_name }}</td>
                            @endif
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-black text-slate-900 dark:text-slate-100">{{ $row->score }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $selectedGameId ? 3 : 4 }}" class="px-6 py-10 text-center text-sm text-slate-600 dark:text-slate-300">
                                No scores yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
