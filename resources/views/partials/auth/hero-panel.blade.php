@php
    $highlights = [
        'Mobile-ready employee portal',
        'Secure sign in and account recovery',
        'Real-time attendance visibility',
    ];
@endphp

<div class="relative overflow-hidden rounded-[2rem] border border-white/20 bg-gradient-to-br from-sky-400/30 via-blue-500/45 to-blue-950/95 p-8 text-white shadow-2xl">
    <div class="pointer-events-none absolute -left-20 -top-20 h-64 w-64 rounded-full bg-amber-200/30 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-24 right-0 h-72 w-72 rounded-full bg-sky-200/30 blur-3xl"></div>

    <div class="relative z-10 flex min-h-[36rem] flex-col justify-between">
        <div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full border border-white/35 bg-white/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] backdrop-blur">
                {{ config('app.name', 'TipTap') }}
            </a>

            <h1 class="mt-8 max-w-sm text-4xl font-semibold leading-tight text-white">
                Workday access made clean, fast, and focused.
            </h1>
            <p class="mt-4 max-w-md text-sm leading-6 text-blue-100/95">
                Sign in to manage attendance, leave requests, and profile details in one responsive workspace.
            </p>
        </div>

        <div class="space-y-3">
            @foreach ($highlights as $item)
                <div class="flex items-center gap-3 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 backdrop-blur-sm">
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-400/20 text-emerald-100">
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.25 7.25a1 1 0 01-1.415 0l-3-3a1 1 0 111.415-1.42l2.293 2.295 6.543-6.545a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <p class="text-sm font-medium text-blue-50">{{ $item }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
