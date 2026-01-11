<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-slate-900 dark:text-slate-100">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10">
                <div class="p-6 text-slate-900 dark:text-slate-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
