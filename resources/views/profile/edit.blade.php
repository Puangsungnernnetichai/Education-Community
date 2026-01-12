<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-slate-900 dark:text-slate-100">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow-sm ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10 sm:rounded-3xl">
                <div>
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Game stats</div>
                    <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">Your activity across all games.</div>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl bg-slate-50 px-5 py-4 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:ring-white/10">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Total played</div>
                        <div class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ $gameStats['total_played'] ?? 0 }}</div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-5 py-4 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:ring-white/10">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Highest score</div>
                        <div class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ $gameStats['highest_score'] ?? 0 }}</div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-5 py-4 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:ring-white/10">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Average score</div>
                        <div class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ number_format((float)($gameStats['average_score'] ?? 0), 1) }}</div>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow-sm ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10 sm:rounded-3xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow-sm ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10 sm:rounded-3xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow-sm ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10 sm:rounded-3xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
