@extends('layouts.app')

@section('content')
    <section id="top" class="bg-motion w-full min-h-screen overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-emerald-50 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
        <div class="grid min-h-screen grid-cols-1 md:grid-cols-2">
            <div class="flex items-center justify-center p-6 md:p-10">
                <div
                    class="h-[320px] w-full max-w-[520px] sm:h-[380px] md:h-[420px]"
                    data-lottie
                    data-src="{{ asset('lottie/left.json') }}?v={{ filemtime(public_path('lottie/left.json')) }}"
                    aria-label="Coding animation"
                    role="img"
                ></div>
            </div>

            <div class="flex items-center justify-center p-6 md:p-10">
                <div
                    class="h-[320px] w-full max-w-[520px] sm:h-[380px] md:h-[420px]"
                    data-lottie
                    data-src="{{ asset('lottie/right.json') }}?v={{ filemtime(public_path('lottie/right.json')) }}"
                    aria-label="Learning animation"
                    role="img"
                ></div>
            </div>
        </div>
    </section>

    <section id="intro" class="bg-motion relative min-h-screen overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-emerald-50 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
        <div class="mx-auto flex min-h-screen max-w-6xl flex-col items-center justify-center px-4 py-24 text-center sm:px-6">
            <div class="animate-on-load max-w-3xl">
                <p class="inline-flex items-center gap-2 rounded-full bg-white/70 px-4 py-2 text-xs font-medium text-slate-700 ring-1 ring-slate-900/5 dark:bg-slate-900/60 dark:text-slate-200 dark:ring-white/10">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Learn together. Build together. Grow together.
                </p>

                <h1 class="mt-6 text-balance text-4xl font-semibold tracking-tight text-slate-900 dark:text-slate-100 sm:text-6xl">
                    A modern community for students learning to code
                </h1>

                <p class="mt-5 text-pretty text-base leading-7 text-slate-700 dark:text-slate-200 sm:text-lg">
                    Share what you’re building, ask questions without fear, and get support from learners who are on the same journey.
                </p>

                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a
                        href="#create"
                        class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto"
                    >
                        Start Learning
                    </a>
                    <a
                        href="#community"
                        class="inline-flex w-full items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-semibold text-slate-900 ring-1 ring-slate-900/10 transition hover:bg-slate-50 sm:w-auto"
                    >
                        Join Discussion
                    </a>
                </div>
            </div>

            <div class="mt-16 grid w-full max-w-4xl grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="reveal rounded-3xl bg-white/70 p-6 ring-1 ring-slate-900/5 dark:bg-slate-900/50 dark:ring-white/10" data-reveal>
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Friendly learning space</div>
                    <div class="mt-2 text-sm text-slate-600 dark:text-slate-300">Clear explanations, kind feedback, and practical help.</div>
                </div>
                <div class="reveal rounded-3xl bg-white/70 p-6 ring-1 ring-slate-900/5 dark:bg-slate-900/50 dark:ring-white/10" data-reveal>
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Build real projects</div>
                    <div class="mt-2 text-sm text-slate-600 dark:text-slate-300">Post progress, get ideas, and learn by shipping.</div>
                </div>
                <div class="reveal rounded-3xl bg-white/70 p-6 ring-1 ring-slate-900/5 dark:bg-slate-900/50 dark:ring-white/10" data-reveal>
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Grow your skills</div>
                    <div class="mt-2 text-sm text-slate-600 dark:text-slate-300">From fundamentals to advanced topics—step by step.</div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white dark:bg-slate-950">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="reveal" data-reveal>
                <h2 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100 sm:text-3xl">How this community helps</h2>
                <p class="mt-3 max-w-prose text-sm leading-6 text-slate-600 dark:text-slate-300">
                    A clean, student-first space designed to keep you learning consistently—with support and momentum.
                </p>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $features = [
                        ['title' => 'Learn together', 'body' => 'Study buddies, accountability, and shared progress.'],
                        ['title' => 'Share knowledge', 'body' => 'Teach what you learn and reinforce concepts.'],
                        ['title' => 'Ask questions', 'body' => 'Get unstuck with friendly, practical answers.'],
                        ['title' => 'Grow skills', 'body' => 'Build confidence through projects and feedback.'],
                    ];
                @endphp

                @foreach ($features as $feature)
                    <div class="reveal group rounded-3xl bg-white p-6 ring-1 ring-slate-900/5 transition hover:-translate-y-0.5 hover:bg-slate-50 dark:bg-slate-900/40 dark:ring-white/10 dark:hover:bg-slate-900/60" data-reveal>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 h-10 w-10 shrink-0 rounded-2xl bg-gradient-to-br from-indigo-100 to-emerald-100 ring-1 ring-slate-900/5 dark:from-indigo-500/20 dark:to-emerald-500/20 dark:ring-white/10"></div>
                            <div>
                                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $feature['title'] }}</div>
                                <div class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $feature['body'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="create" class="bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="reveal flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end" data-reveal>
                <div>
                    <h2 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100 sm:text-3xl">Create a post</h2>
                    <p class="mt-3 max-w-prose text-sm leading-6 text-slate-600 dark:text-slate-300">
                        Share what you’re learning, a small win, or a question you’re stuck on. Short posts are welcome.
                    </p>
                </div>

                @if (session('status'))
                    <div class="rounded-2xl bg-white px-4 py-3 text-sm text-slate-700 ring-1 ring-slate-900/5 dark:bg-slate-900/50 dark:text-slate-200 dark:ring-white/10">
                        {{ session('status') }}
                    </div>
                @endif
            </div>

            <div class="mt-10 rounded-3xl bg-white p-6 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10">
                @auth
                    <form method="POST" action="{{ route('posts.store') }}" data-ajax-post data-stay data-render="feed" data-posts-root="home-posts-root" class="grid gap-4">
                        @csrf
                        <input type="hidden" name="_redirect" value="home" />

                        <p class="js-form-error hidden rounded-2xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 ring-1 ring-rose-200" aria-live="polite"></p>

                        <div>
                            <label class="text-sm font-medium text-slate-900 dark:text-slate-100" for="title">Title</label>
                            <input
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                id="title"
                                name="title"
                                value="{{ old('title') }}"
                                placeholder="What are you learning today?"
                                required
                            />
                            @if ($errors->post->has('title'))
                                <p class="mt-2 text-sm text-rose-600 dark:text-rose-300">{{ $errors->post->first('title') }}</p>
                            @endif
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-900 dark:text-slate-100" for="tags">Tags</label>
                            <input
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                id="tags"
                                name="tags"
                                value="{{ old('tags') }}"
                                placeholder="e.g. Laravel, Tailwind, Machine Learning"
                            />
                            @if ($errors->post->has('tags'))
                                <p class="mt-2 text-sm text-rose-600 dark:text-rose-300">{{ $errors->post->first('tags') }}</p>
                            @endif
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Comma-separated topics. We auto-trim and dedupe.</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-900 dark:text-slate-100" for="body">Body</label>
                            <textarea
                                class="mt-2 min-h-[140px] w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-indigo-900/40"
                                id="body"
                                name="body"
                                placeholder="Share context, what you tried, and where you're stuck..."
                                required
                            >{{ old('body') }}</textarea>
                            @if ($errors->post->has('body'))
                                <p class="mt-2 text-sm text-rose-600 dark:text-rose-300">{{ $errors->post->first('body') }}</p>
                            @endif
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Tip: add what you expected vs what happened. You’ll get better answers.</p>
                        </div>

                        <label class="inline-flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:ring-white/10">
                            <input type="checkbox" name="is_private" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-950" />
                            <span class="text-sm text-slate-700 dark:text-slate-200">Make this post private (only you and admins can see it)</span>
                        </label>

                        <div class="flex items-center justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white"
                            >
                                Submit Post
                            </button>
                        </div>
                    </form>
                @endauth

                @guest
                    <div class="rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-700 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:text-slate-200 dark:ring-white/10">
                        Please <a href="{{ route('login') }}" class="font-semibold text-slate-900 underline dark:text-white">log in</a> (or <a href="{{ route('register') }}" class="font-semibold text-slate-900 underline dark:text-white">create an account</a>) to create a post.
                    </div>
                @endguest
            </div>
        </div>
    </section>

    <section id="community" class="bg-white dark:bg-slate-950">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="reveal flex items-end justify-between gap-6" data-reveal>
                <div>
                    <h2 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100 sm:text-3xl">Community feed</h2>
                    <p class="mt-3 max-w-prose text-sm leading-6 text-slate-600 dark:text-slate-300">
                        Recent posts from learners. Expand a post to see comments and replies.
                    </p>
                </div>
            </div>

            <div id="home-posts-root" class="mt-10 grid gap-5">
                @forelse ($posts as $post)
                    <x-feed-post :post="$post" />
                @empty
                    <div class="reveal rounded-3xl bg-slate-50 p-10 text-center ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10" data-reveal>
                        <div class="text-lg font-semibold text-slate-900 dark:text-slate-100">No posts yet</div>
                        <div class="mt-2 text-sm text-slate-600 dark:text-slate-300">Create the first post above to start the discussion.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="reveal flex items-end justify-between gap-6" data-reveal>
                <div>
                    <h2 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100 sm:text-3xl">Sponsors & partners</h2>
                    <p class="mt-3 max-w-prose text-sm leading-6 text-slate-600 dark:text-slate-300">Community-backed learning, supported by great people.</p>
                </div>
            </div>

            <div class="mt-10 rounded-3xl bg-white p-6 ring-1 ring-slate-900/5 dark:bg-slate-900/40 dark:ring-white/10">
                <div class="marquee">
                    <div class="marquee-track flex w-max items-center gap-4">
                        @foreach ($sponsors as $sponsor)
                            <div class="shrink-0 rounded-2xl bg-slate-50 px-5 py-3 text-sm font-semibold text-slate-600 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:text-slate-300 dark:ring-white/10">
                                {{ $sponsor['name'] }}
                            </div>
                        @endforeach
                        @foreach ($sponsors as $sponsor)
                            <div class="shrink-0 rounded-2xl bg-slate-50 px-5 py-3 text-sm font-semibold text-slate-600 ring-1 ring-slate-900/5 dark:bg-slate-950/60 dark:text-slate-300 dark:ring-white/10">
                                {{ $sponsor['name'] }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="topics" class="bg-white dark:bg-slate-950">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="reveal" data-reveal>
                <h2 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100 sm:text-3xl">Knowledge corner</h2>
                <p class="mt-3 max-w-prose text-sm leading-6 text-slate-600 dark:text-slate-300">
                    Browse education-focused posts.
                </p>
            </div>

            <div class="mt-10">
                <div class="marquee marquee-scroll select-none" data-marquee data-marquee-speed="0.55">
                    <div class="marquee-track-scroll flex w-max items-stretch gap-4">
                        @foreach ($blogCards as $card)
                            <a
                                href="{{ $card['href'] ?? '#topics' }}"
                                class="reveal group shrink-0 w-[85%] rounded-3xl bg-slate-100 p-6 ring-1 ring-slate-300/70 transition hover:-translate-y-0.5 hover:bg-slate-200 hover:ring-slate-400/60 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-200 dark:bg-slate-900/40 dark:ring-white/15 dark:hover:bg-slate-900/60 dark:hover:ring-white/25 dark:focus-visible:ring-indigo-900/40 sm:w-[360px]"
                                data-reveal
                            >
                                <div class="inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-300/70 transition group-hover:bg-slate-300/70 dark:bg-slate-950/60 dark:text-slate-200 dark:ring-white/10 dark:group-hover:bg-slate-950/80">
                                    {{ $card['tag'] }}
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $card['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $card['excerpt'] }}</p>
                                <div class="mt-6 text-sm font-semibold text-slate-900 dark:text-slate-100">Read more →</div>
                            </a>
                        @endforeach

                        @foreach ($blogCards as $card)
                            <a
                                href="{{ $card['href'] ?? '#topics' }}"
                                class="reveal group shrink-0 w-[85%] rounded-3xl bg-slate-100 p-6 ring-1 ring-slate-300/70 transition hover:-translate-y-0.5 hover:bg-slate-200 hover:ring-slate-400/60 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-200 dark:bg-slate-900/40 dark:ring-white/15 dark:hover:bg-slate-900/60 dark:hover:ring-white/25 dark:focus-visible:ring-indigo-900/40 sm:w-[360px]"
                                data-reveal
                            >
                                <div class="inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-300/70 transition group-hover:bg-slate-300/70 dark:bg-slate-950/60 dark:text-slate-200 dark:ring-white/10 dark:group-hover:bg-slate-950/80">
                                    {{ $card['tag'] }}
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $card['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $card['excerpt'] }}</p>
                                <div class="mt-6 text-sm font-semibold text-slate-900 dark:text-slate-100">Read more →</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-motion relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-900">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="reveal rounded-3xl bg-white/5 p-8 ring-1 ring-white/10" data-reveal>
                <div class="max-w-2xl">
                    <h2 class="text-2xl font-semibold tracking-tight text-white sm:text-3xl">Ready to learn with people who get it?</h2>
                    <p class="mt-3 text-sm leading-6 text-white/80">
                        Start small. Post your question, your win, or your next goal—and keep the streak alive.
                    </p>

                    <div class="mt-8">
                        <a
                            href="#create"
                            class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-50"
                        >
                            Join the Community
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
