<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AI Code Education Community') }}</title>

    <style>
        [x-cloak]{display:none !important;}
        .reply-target:target{display:block !important;}
        .menu-target:focus-within [data-menu-panel]{display:block !important;}
    </style>

    <script>
        (function () {
            try {
                var stored = localStorage.getItem('theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var enabled = stored ? stored === 'dark' : prefersDark;
                document.documentElement.classList.toggle('dark', enabled);
            } catch (e) {
                // ignore
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-white text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div
        class="min-h-screen"
        x-data="{
            authOpen: false,
            authTab: 'login',
            authTabTarget: 'login',

            darkEnabled: false,

            openAuth(tab = 'login') {
                this.authTabTarget = tab;
                this.authTab = tab;
                this.authOpen = true;
                this.$nextTick(() => {
                    const id = this.authTab === 'login' ? 'auth_login_email' : 'auth_register_name';
                    const el = document.getElementById(id);
                    if (el) el.focus();
                });
            },

            switchAuthTab(tab) {
                if (this.authTabTarget === tab) return;

                this.authTabTarget = tab;
                this.authTab = tab;

                this.$nextTick(() => {
                    const id = tab === 'login' ? 'auth_login_email' : 'auth_register_name';
                    const el = document.getElementById(id);
                    if (el) el.focus();
                });
            },
            closeAuth() {
                this.authOpen = false;
            },

            applyDark() {
                document.documentElement.classList.toggle('dark', this.darkEnabled);
                try {
                    localStorage.setItem('theme', this.darkEnabled ? 'dark' : 'light');
                } catch (e) {}
            },

            init() {
                try {
                    const stored = localStorage.getItem('theme');
                    if (stored === 'dark') this.darkEnabled = true;
                    if (stored === 'light') this.darkEnabled = false;
                    if (!stored) {
                        // Prefer whatever is already applied on <html> (set by the inline script) to avoid desync after redirects.
                        this.darkEnabled = document.documentElement.classList.contains('dark');

                        // Fallback to system preference.
                        if (!this.darkEnabled) {
                            this.darkEnabled = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                        }
                    }
                } catch (e) {
                    this.darkEnabled = false;
                }

                this.applyDark();

                this.$watch('authOpen', (open) => {
                    document.documentElement.classList.toggle('overflow-hidden', open);
                    document.body.classList.toggle('overflow-hidden', open);
                });

                const hasAuthErrors = {{ $errors->any() ? 'true' : 'false' }};
                const looksLikeRegister = {{ ($errors->has('name') || $errors->has('password_confirmation')) ? 'true' : 'false' }};
                if (hasAuthErrors) {
                    this.openAuth(looksLikeRegister ? 'register' : 'login');
                }
            }
        }"
        x-init="init()"
    >
        <div class="min-h-screen">
            <x-site-header />

            @if (session('toast') && is_array(session('toast')))
                @php
                    $toast = session('toast');
                    $toastType = $toast['type'] ?? 'success';
                    $toastMessage = $toast['message'] ?? '';
                    $toastAction = $toast['action'] ?? null;
                @endphp
                <div id="server-toast" class="pointer-events-none fixed inset-x-0 top-4 z-[120] flex justify-center px-4 sm:justify-end">
                    <div class="pointer-events-auto w-full max-w-sm rounded-2xl bg-white/95 p-4 shadow-sm ring-1 ring-slate-900/10 backdrop-blur dark:bg-slate-900/90 dark:ring-white/10 {{ $toastType === 'success' ? 'border border-indigo-200/70 dark:border-indigo-500/20' : 'border border-slate-200 dark:border-white/10' }}">
                        <div class="flex items-start gap-3">
                            <div class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-indigo-600"></div>
                            <div class="min-w-0 flex-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $toastMessage }}</div>
                            <button id="server-toast-ok" type="button" class="-m-1 inline-flex rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-200 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white dark:focus-visible:ring-indigo-900/40">OK</button>
                        </div>
                    </div>
                </div>
                <script>
                    (function () {
                        var root = document.getElementById('server-toast');
                        if (!root) return;
                        var ok = document.getElementById('server-toast-ok');
                        var action = @json($toastAction);
                        function hide() { root.style.display = 'none'; }
                        if (ok) {
                            ok.addEventListener('click', function () {
                                if (action === 'reload') {
                                    window.location.reload();
                                    return;
                                }
                                hide();
                            });
                        }
                        window.setTimeout(function () {
                            if (action === 'reload') return;
                            hide();
                        }, 3200);
                    })();
                </script>
            @endif

        @if (isset($header))
            <header class="bg-white dark:bg-slate-950">
                <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main>
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>

            <x-site-footer />
        </div>

        <script>
            (function () {
                // Vanilla fallback so dark mode works even if Alpine doesn't start.
                function bind() {
                    var toggle = document.getElementById('dark-toggle');
                    if (!toggle) return;

                    try {
                        toggle.checked = document.documentElement.classList.contains('dark');
                    } catch (e) {}

                    toggle.addEventListener('change', function () {
                        var enabled = !!toggle.checked;
                        document.documentElement.classList.toggle('dark', enabled);
                        try {
                            localStorage.setItem('theme', enabled ? 'dark' : 'light');
                        } catch (e) {}
                    });
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', bind, { once: true });
                } else {
                    bind();
                }
            })();
        </script>

        <div id="post-edit-modal" class="fixed inset-0 z-[130] hidden" aria-hidden="true">
            <div class="absolute inset-0 bg-slate-900/40"></div>
            <div class="relative flex min-h-full items-center justify-center p-4">
                <div class="w-full max-w-xl rounded-3xl bg-white p-6 ring-1 ring-slate-900/10 dark:bg-slate-900/90 dark:ring-white/10">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Edit post</div>
                            <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">Update your title and body.</div>
                        </div>
                        <button id="post-edit-cancel" type="button" class="-m-1 inline-flex rounded-2xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-200 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white dark:focus-visible:ring-indigo-900/40">
                            Close
                        </button>
                    </div>

                    <form id="post-edit-form" method="POST" action="" data-ajax-post-update class="mt-5 grid gap-4">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="_render" id="post-edit-render" value="index" />
                        <input type="hidden" name="post_id" id="post-edit-post-id" value="" />

                        <p class="js-form-error hidden rounded-2xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 ring-1 ring-rose-200" aria-live="polite"></p>

                        <div>
                            <label for="post-edit-title" class="text-sm font-medium text-slate-900 dark:text-slate-100">Title</label>
                            <input
                                id="post-edit-title"
                                name="title"
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                required
                            />
                        </div>

                        <div>
                            <label for="post-edit-tags" class="text-sm font-medium text-slate-900 dark:text-slate-100">Tags</label>
                            <input
                                id="post-edit-tags"
                                name="tags"
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                placeholder="e.g. Laravel, Tailwind"
                            />
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Comma-separated topics. We auto-trim and dedupe.</p>
                        </div>

                        <div>
                            <label for="post-edit-body" class="text-sm font-medium text-slate-900 dark:text-slate-100">Body</label>
                            <textarea
                                id="post-edit-body"
                                name="body"
                                class="mt-2 min-h-[140px] w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                required
                            ></textarea>
                        </div>

                        <input type="hidden" name="is_private" value="0" />
                        <label class="inline-flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:ring-white/10">
                            <input id="post-edit-private" type="checkbox" name="is_private" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-950" />
                            <span class="text-sm text-slate-700 dark:text-slate-200">Private</span>
                        </label>

                        <div class="flex items-center justify-end gap-2">
                            <button type="button" id="post-edit-cancel-2" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 dark:bg-slate-950/60 dark:text-slate-100 dark:ring-white/10 dark:hover:bg-slate-950">
                                Cancel
                            </button>
                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-auth-modal />
    </div>
</body>
</html>
