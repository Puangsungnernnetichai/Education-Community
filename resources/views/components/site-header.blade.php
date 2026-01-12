<header
    data-site-header
    class="header-shell sticky top-0 z-50 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 dark:bg-slate-950/75 dark:supports-[backdrop-filter]:bg-slate-950/55"
>
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <a href="{{ route('home') }}#top" class="group flex items-center gap-3">
            <span class="grid h-10 w-10 place-items-center rounded-2xl bg-gradient-to-br from-indigo-500 to-emerald-400 text-white shadow-sm">
                <span class="text-sm font-semibold">AI</span>
            </span>
            <div class="leading-tight">
                <div class="text-sm font-semibold tracking-tight text-slate-900 dark:text-slate-100">AI Code Education</div>
                <div class="text-xs text-slate-600 dark:text-slate-300">Community</div>
            </div>
        </a>

        <nav class="ml-auto flex flex-wrap items-center justify-end gap-x-5 gap-y-2 text-sm text-slate-700 dark:text-slate-200">
            <a class="transition hover:text-slate-900 dark:hover:text-white" href="{{ route('home') }}#top">Home</a>
            <a class="transition hover:text-slate-900 dark:hover:text-white" href="{{ route('home') }}#community">Community</a>
            <a class="transition hover:text-slate-900 dark:hover:text-white" href="{{ route('home') }}#topics">Topics</a>
            <a class="transition hover:text-slate-900 dark:hover:text-white" href="{{ route('home') }}#about">About</a>

            <a class="transition hover:text-slate-900 dark:hover:text-white" href="{{ route('games.index') }}">Games</a>
            <a class="transition hover:text-slate-900 dark:hover:text-white" href="{{ route('leaderboard.index') }}">Leaderboard</a>

            <div class="ml-2 flex items-center">
                <label class="inline-flex cursor-pointer items-center" for="dark-toggle">
                    <span class="mr-2 hidden text-xs font-medium text-slate-700 dark:text-slate-200 sm:inline">Dark mode</span>
                    <span class="sr-only">Toggle dark mode</span>
                    <input id="dark-toggle" type="checkbox" class="peer sr-only" x-model="darkEnabled" x-on:change="applyDark()" />
                    <span class="relative h-7 w-12 rounded-full bg-slate-300 shadow-sm ring-2 ring-slate-900/20 transition dark:bg-slate-700 dark:ring-white/10 peer-checked:bg-slate-900 peer-checked:[&_.toggle-sun]:opacity-100 peer-checked:[&_.toggle-moon]:opacity-0 peer-checked:[&_.toggle-thumb]:translate-x-5">
                        <span class="toggle-sun absolute inset-y-0 left-1 flex items-center text-slate-900 opacity-0 transition-opacity dark:text-slate-100">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M7.05 16.95l-1.414 1.414m0-11.314 1.414 1.414m11.314 11.314 1.414 1.414" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12z" />
                            </svg>
                        </span>

                        <span class="toggle-moon absolute inset-y-0 right-1 flex items-center text-slate-900 transition-opacity dark:text-slate-100">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 0 1 11.21 3a7 7 0 1 0 9.79 9.79z" />
                            </svg>
                        </span>

                        <span class="toggle-thumb absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow transition-transform dark:bg-slate-50"></span>
                    </span>
                </label>
            </div>

            @guest
                <a
                    href="{{ route('login') }}"
                    x-on:click.prevent="openAuth('login')"
                    class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-800"
                >
                    Login
                </a>

                @if (Route::has('register'))
                    <a
                        href="{{ route('register') }}"
                        x-on:click.prevent="openAuth('register')"
                        class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white"
                    >
                        Register
                    </a>
                @endif
            @endguest

            @auth
                <details class="relative">
                    <summary class="list-none cursor-pointer rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-800">
                        <span class="inline-flex items-center gap-2">
                            <span class="max-w-[12rem] truncate">{{ Auth::user()->name }}</span>
                            <svg viewBox="0 0 20 20" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </summary>

                    <div class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-900/10 dark:bg-slate-900 dark:ring-white/10">
                        <div class="px-4 py-3">
                            <div class="text-xs font-medium text-slate-500 dark:text-slate-300">Signed in as</div>
                            <div class="mt-1 truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ Auth::user()->email }}</div>
                        </div>

                        <div class="border-t border-slate-100 dark:border-white/10">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">Profile</a>
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">Dashboard</a>

                            @if (method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin() && Route::has('admin.dashboard'))
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">Admin Panel</a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 dark:border-white/10">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">Logout</button>
                            </form>
                        </div>
                    </div>
                </details>
            @endauth
        </nav>
    </div>
</header>
