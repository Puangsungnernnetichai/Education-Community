<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-md border border-slate-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-700 shadow-sm transition ease-in-out duration-150 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 dark:border-white/10 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:focus:ring-offset-slate-950']) }}>
    {{ $slot }}
</button>
