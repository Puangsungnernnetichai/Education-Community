<footer id="about" class="border-t border-slate-200 bg-white dark:border-white/10 dark:bg-slate-950">
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <div class="grid gap-8 sm:grid-cols-2">
            <div>
                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">AI Code Education Community</div>
                <p class="mt-2 max-w-prose text-sm leading-6 text-slate-600 dark:text-slate-300">
                    A friendly place for students to learn together, share knowledge, ask questions, and grow real-world coding skills.
                </p>
            </div>

            <div class="sm:text-right">
                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Links</div>
                <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-600 dark:text-slate-300 sm:justify-end">
                    <a class="transition hover:text-slate-900 dark:hover:text-white" href="#top">Home</a>
                    <a class="transition hover:text-slate-900 dark:hover:text-white" href="#community">Community</a>
                    <a class="transition hover:text-slate-900 dark:hover:text-white" href="#topics">Topics</a>
                </div>
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-3 border-t border-slate-200 pt-6 text-xs text-slate-500 dark:border-white/10 dark:text-slate-400 sm:flex-row sm:items-center sm:justify-between">
            <div>© {{ date('Y') }} AI Code Education Community</div>
            <div>Built with Laravel + TailwindCSS</div>
        </div>
    </div>
</footer>
