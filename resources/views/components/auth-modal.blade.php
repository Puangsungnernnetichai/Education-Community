@php
    $isRegisterError = $errors->has('name') || $errors->has('password_confirmation');
@endphp

<div
    x-cloak
    x-show="authOpen"
    x-on:keydown.escape.window="closeAuth()"
    class="fixed inset-0 z-[100]"
    aria-labelledby="auth-modal-title"
    role="dialog"
    aria-modal="true"
>
    <div
        class="absolute inset-0 bg-slate-900/65 dark:bg-black/70"
        x-on:click="closeAuth()"
        x-transition.opacity
    ></div>

    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div
            class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-slate-900/10 dark:bg-slate-950 dark:ring-white/10 max-h-[85vh] overflow-y-auto"
            x-on:click.stop
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="flex items-center justify-between gap-4 px-5 pt-5 sm:px-6 sm:pt-6">
                <div>
                    <h2 id="auth-modal-title" class="text-base font-semibold text-slate-900 dark:text-slate-100 sm:text-lg">Account</h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Log in or create an account to participate.</p>
                </div>

                <button
                    type="button"
                    x-on:click="closeAuth()"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-50 text-slate-700 ring-1 ring-slate-900/10 transition hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-slate-700"
                    aria-label="Close"
                >
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-5 pt-4 sm:px-6 sm:pt-5">
                <div class="grid grid-cols-2 rounded-2xl bg-slate-50 p-1 ring-1 ring-slate-900/5 dark:bg-slate-800/50 dark:ring-white/10">
                    <button
                        type="button"
                        x-on:click="switchAuthTab('login')"
                        class="rounded-2xl px-4 py-2 text-sm font-semibold transition"
                        x-bind:class="authTabTarget === 'login' ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-900/10 dark:bg-slate-900 dark:text-slate-100 dark:ring-white/10' : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'"
                    >
                        Login
                    </button>
                    <button
                        type="button"
                        x-on:click="switchAuthTab('register')"
                        class="rounded-2xl px-4 py-2 text-sm font-semibold transition"
                        x-bind:class="authTabTarget === 'register' ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-900/10 dark:bg-slate-900 dark:text-slate-100 dark:ring-white/10' : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'"
                    >
                        Register
                    </button>
                </div>

                @if ($errors->any())
                    <div class="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200 dark:bg-rose-950/40 dark:text-rose-200 dark:ring-rose-900/40">
                        Please fix the errors below.
                    </div>
                @endif
            </div>

            <div class="px-5 pb-5 pt-4 sm:px-6 sm:pb-6 sm:pt-5">
                <div class="relative min-h-[420px]">
                    <div
                        class="absolute inset-0 transition-all duration-300 ease-out"
                        x-bind:class="authTab === 'login' ? 'opacity-100 translate-y-0 pointer-events-auto' : 'opacity-0 translate-y-2 pointer-events-none'"
                        x-bind:aria-hidden="authTab !== 'login'"
                        style="will-change: transform, opacity"
                    >
                        <form method="POST" action="{{ route('login') }}" class="grid gap-4">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ url()->full() }}" />

                        <div>
                            <label class="text-sm font-medium text-slate-900 dark:text-slate-100" for="auth_login_email">Email</label>
                            <input
                                id="auth_login_email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                placeholder="you@example.com"
                            />
                            @if (! $isRegisterError)
                                @error('email')
                                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-900 dark:text-slate-100" for="auth_login_password">Password</label>
                            <input
                                id="auth_login_password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                placeholder="••••••••"
                            />
                            @if (! $isRegisterError)
                                @error('password')
                                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>

                        <label class="inline-flex items-center gap-3 text-sm text-slate-700 dark:text-slate-200">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-900" />
                            Remember me
                        </label>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white"
                            >
                                Log in
                            </button>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-slate-700 underline hover:text-slate-900 dark:text-slate-200 dark:hover:text-white">Forgot password?</a>
                            @endif
                        </div>
                        </form>
                    </div>

                    <div
                        class="absolute inset-0 transition-all duration-300 ease-out"
                        x-bind:class="authTab === 'register' ? 'opacity-100 translate-y-0 pointer-events-auto' : 'opacity-0 translate-y-2 pointer-events-none'"
                        x-bind:aria-hidden="authTab !== 'register'"
                        style="will-change: transform, opacity"
                    >
                        <form method="POST" action="{{ route('register') }}" class="grid gap-4">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ url()->full() }}" />

                        <div>
                            <label class="text-sm font-medium text-slate-900 dark:text-slate-100" for="auth_register_name">Name</label>
                            <input
                                id="auth_register_name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                autocomplete="name"
                                required
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                placeholder="Your name"
                            />
                            @if ($isRegisterError)
                                @error('name')
                                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-900 dark:text-slate-100" for="auth_register_email">Email</label>
                            <input
                                id="auth_register_email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                placeholder="you@example.com"
                            />
                            @if ($isRegisterError)
                                @error('email')
                                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-900 dark:text-slate-100" for="auth_register_password">Password</label>
                            <input
                                id="auth_register_password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                required
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                placeholder="••••••••"
                            />
                            @if ($isRegisterError)
                                @error('password')
                                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-900 dark:text-slate-100" for="auth_register_password_confirmation">Confirm password</label>
                            <input
                                id="auth_register_password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                required
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                placeholder="••••••••"
                            />
                            @if ($isRegisterError)
                                @error('password_confirmation')
                                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white"
                        >
                            Create account
                        </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
