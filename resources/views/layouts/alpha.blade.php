@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \Illuminate\Support\Str::beforeLast((string) (\App\Models\Setting::first()?->company_email_domain ?: config('app.name', 'TipTap')), '.') }} — Alpha</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#072C50] text-dark-slate antialiased">
        @php
            $brandName = (string) (\App\Models\Setting::first()?->company_email_domain ?: config('app.name', 'TipTap'));
            $brandName = \Illuminate\Support\Str::beforeLast($brandName, '.');
        @endphp

        <div class="workspace-shell relative min-h-screen overflow-x-clip">

            {{-- Minimal navigation bar (only brand + logout) --}}
            <nav class="sticky top-0 z-40 border-b border-[#0A3E71] bg-navy-primary/95 backdrop-blur">
                <div class="mx-auto flex h-16 w-full max-w-[1440px] items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('alpha.settings.index') }}" class="grid h-10 w-10 place-items-center rounded-2xl bg-navy-primary text-white shadow-lg shadow-navy-primary/35">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 15.5A3.5 3.5 0 1012 8.5a3.5 3.5 0 000 7z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                <path d="M19.14 12.94c.04-.31.06-.63.06-.94s-.02-.63-.06-.94l2.03-1.58a.49.49 0 00.12-.61l-1.92-3.32a.488.488 0 00-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54a.484.484 0 00-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.04.31-.06.63-.06.94s.02.63.06.94l-2.03 1.58a.49.49 0 00-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                            </svg>
                        </a>

                        <div>
                            <p class="font-display text-sm font-extrabold tracking-tight text-white">{{ $brandName }}</p>
                            <p class="text-xs text-slate-100/85">Alpha Panel</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="hidden items-center gap-1.5 rounded-lg border border-amber-400/40 bg-amber-400/15 px-2.5 py-1 text-xs font-semibold text-amber-300 sm:inline-flex">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.486 0l6.516 11.584c.75 1.334-.213 2.992-1.742 2.992H3.483c-1.53 0-2.492-1.658-1.742-2.992L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-6a1 1 0 00-1 1v3a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            DEV ONLY
                        </span>

                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-2 rounded-xl border border-[#95B5D3]/55 bg-white/95 px-3 py-2 text-sm font-medium text-dark-slate transition hover:border-white hover:text-navy-primary focus:outline-none focus:ring-2 focus:ring-white/35">
                                    <span class="inline-grid h-7 w-7 place-items-center rounded-lg bg-amber-100 text-amber-700">
                                        α
                                    </span>
                                    <span>{{ Auth::user()->name }}</span>
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link
                                        :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                    >
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </nav>

            <div class="mx-auto grid w-full max-w-[1440px] grid-cols-1 gap-6 px-4 pb-8 pt-6 sm:px-6 lg:grid-cols-[260px_minmax(0,1fr)] lg:px-8">
                <aside class="hidden lg:block">
                    <div class="card-soft sticky top-24 rounded-3xl p-4">
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.18em] text-navy-primary/70">Alpha Panel</p>

                        <nav class="mt-3 space-y-1.5">
                            <a
                                href="{{ route('alpha.settings.index') }}"
                                class="sidebar-link sidebar-link-active"
                            >
                                <span class="flex items-center gap-2.5">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.84 1.804A1 1 0 018.82 1h2.36a1 1 0 01.98.804l.19.95c.27.087.53.197.78.328l.81-.54a1 1 0 011.25.12l1.67 1.67a1 1 0 01.12 1.25l-.54.81c.13.25.24.51.33.78l.95.19a1 1 0 01.8.98v2.36a1 1 0 01-.8.98l-.95.19a5.76 5.76 0 01-.33.78l.54.81a1 1 0 01-.12 1.25l-1.67 1.67a1 1 0 01-1.25.12l-.81-.54c-.25.13-.51.24-.78.33l-.19.95a1 1 0 01-.98.8H8.82a1 1 0 01-.98-.8l-.19-.95a5.76 5.76 0 01-.78-.33l-.81.54a1 1 0 01-1.25-.12l-1.67-1.67a1 1 0 01-.12-1.25l.54-.81a5.76 5.76 0 01-.33-.78l-.95-.19a1 1 0 01-.8-.98V8.82a1 1 0 01.8-.98l.95-.19a5.76 5.76 0 01.328-.78l-.54-.81a1 1 0 01.12-1.25l1.67-1.67a1 1 0 011.25-.12l.81.54c.25-.13.51-.24.78-.33l.19-.95ZM10 13a3 3 0 100-6 3 3 0 000 6Z" clip-rule="evenodd" /></svg>
                                    <span>System Config</span>
                                </span>
                                <svg class="h-4 w-4 opacity-70" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </nav>
                    </div>
                </aside>

                <section class="space-y-6">
                    @isset($header)
                        <header class="card-soft rounded-3xl p-5 sm:p-6">
                            {{ $header }}
                        </header>
                    @endisset

                    <main class="space-y-6">
                        @hasSection('content')
                            @yield('content')
                        @else
                            {{ $slot ?? '' }}
                        @endif
                    </main>

                    <footer class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs text-dark-slate/75 sm:text-sm">
                        <p>&copy; {{ now()->year }} {{ $brandName }}. Alpha developer panel — for testing purposes only.</p>
                    </footer>
                </section>
            </div>
        </div>
    </body>
</html>
