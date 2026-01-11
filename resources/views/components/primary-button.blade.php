<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-md border border-transparent bg-slate-900 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition ease-in-out duration-150 hover:bg-slate-800 focus:bg-slate-800 active:bg-slate-950 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white dark:focus:bg-white dark:active:bg-slate-200 dark:focus:ring-offset-slate-950']) }}>
    {{ $slot }}
</button>
