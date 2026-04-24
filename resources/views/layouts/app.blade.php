<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \Illuminate\Support\Str::beforeLast((string) (\App\Models\Setting::first()?->company_email_domain ?: config('app.name', 'TipTap')), '.') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#072C50] text-dark-slate antialiased">
        @php
            $isAdminNavigation = in_array(strtolower((string) auth()->user()?->role), ['admin', 'administrator', 'superadmin', 'super admin', 'super_admin'], true);

            $sidebarItems = [
                ['label' => __('Dashboard'), 'route' => 'dashboard', 'active' => ['dashboard', 'attendance.*'], 'icon' => 'home'],
                ['label' => __('Approval'), 'route' => 'approval.index', 'active' => 'approval.*', 'visible' => $isAdminNavigation, 'icon' => 'shield'],
                ['label' => __('Leaves'), 'route' => 'leaves.index', 'active' => 'leaves.*', 'icon' => 'calendar'],
                ['label' => __('Posts'), 'route' => 'posts.index', 'active' => 'posts.*', 'icon' => 'document'],
                ['label' => __('Profile'), 'route' => 'profile.edit', 'active' => 'profile.*', 'icon' => 'user'],
            ];

            $sidebarBottomItems = [
                ['label' => __('System Config'), 'route' => 'approval.settings.index', 'active' => 'approval.settings.*', 'visible' => $isAdminNavigation, 'icon' => 'settings'],
            ];

            $sidebarItems = array_values(array_filter(
                $sidebarItems,
                fn (array $item): bool => ($item['visible'] ?? true) && Route::has($item['route'])
            ));

            $sidebarBottomItems = array_values(array_filter(
                $sidebarBottomItems,
                fn (array $item): bool => ($item['visible'] ?? true) && Route::has($item['route'])
            ));
        @endphp

        <div class="workspace-shell relative min-h-screen overflow-x-clip">

            @include('layouts.navigation')

            <div class="mx-auto grid w-full max-w-[1440px] grid-cols-1 gap-6 px-4 pb-8 pt-6 sm:px-6 lg:grid-cols-[260px_minmax(0,1fr)] lg:px-8">
                <aside class="hidden lg:block">
                    <div class="card-soft sticky top-24 rounded-3xl p-4">
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.18em] text-navy-primary/70">Workspace</p>

                        <nav class="mt-3 space-y-1.5">
                            @foreach ($sidebarItems as $item)
                                <a
                                    href="{{ route($item['route']) }}"
                                    class="sidebar-link {{ request()->routeIs(...(array) $item['active']) ? 'sidebar-link-active' : '' }}"
                                >
                                    <span class="flex items-center gap-2.5">
                                        @if (($item['icon'] ?? '') === 'home')
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7A1 1 0 003 11h1v5a2 2 0 002 2h2a1 1 0 001-1v-3h2v3a1 1 0 001 1h2a2 2 0 002-2v-5h1a1 1 0 00.707-1.707l-7-7z" /></svg>
                                        @elseif (($item['icon'] ?? '') === 'shield')
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1.5l6 2.25v5.764c0 3.958-2.35 7.5-6 9.486-3.65-1.986-6-5.528-6-9.486V3.75L10 1.5zm2.207 6.793a1 1 0 00-1.414-1.414L9 8.672 8.207 7.88a1 1 0 00-1.414 1.414l1.5 1.5a1 1 0 001.414 0l2.5-2.5z" clip-rule="evenodd" /></svg>
                                        @elseif (($item['icon'] ?? '') === 'clipboard')
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7 2a2 2 0 00-2 2v1H4a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7zm1 3V4h4v1H8z" /><path d="M6 9a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h4a1 1 0 100-2H7z" /></svg>
                                        @elseif (($item['icon'] ?? '') === 'calendar')
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M6 2a1 1 0 00-2 0v1H3.5A2.5 2.5 0 001 5.5v10A2.5 2.5 0 003.5 18h13a2.5 2.5 0 002.5-2.5v-10A2.5 2.5 0 0016.5 3H16V2a1 1 0 10-2 0v1H6V2zm11 6H3v7.5c0 .276.224.5.5.5h13a.5.5 0 00.5-.5V8z" clip-rule="evenodd" /></svg>
                                        @elseif (($item['icon'] ?? '') === 'document')
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414a2 2 0 00-.586-1.414l-2.414-2.414A2 2 0 0011.586 3H6zm2 6a1 1 0 000 2h4a1 1 0 100-2H8zm0 3a1 1 0 100 2h4a1 1 0 100-2H8z" clip-rule="evenodd" /></svg>
                                        @elseif (($item['icon'] ?? '') === 'user')
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2.5a3.5 3.5 0 110 7 3.5 3.5 0 010-7z" /><path d="M4 16a6 6 0 1112 0v1H4v-1z" /></svg>
                                        @endif
                                        <span>{{ $item['label'] }}</span>
                                    </span>
                                    <svg class="h-4 w-4 opacity-70" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            @endforeach
                        </nav>

                        @if ($sidebarBottomItems !== [])
                            <div class="mt-5 space-y-1.5 border-t border-slate-200 pt-4">
                                @foreach ($sidebarBottomItems as $item)
                                    <a
                                        href="{{ route($item['route']) }}"
                                        class="sidebar-link {{ request()->routeIs(...(array) $item['active']) ? 'sidebar-link-active' : '' }}"
                                    >
                                        <span class="flex items-center gap-2.5">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.84 1.804A1 1 0 018.82 1h2.36a1 1 0 01.98.804l.19.95c.27.087.53.197.78.328l.81-.54a1 1 0 011.25.12l1.67 1.67a1 1 0 01.12 1.25l-.54.81c.13.25.24.51.33.78l.95.19a1 1 0 01.8.98v2.36a1 1 0 01-.8.98l-.95.19a5.76 5.76 0 01-.33.78l.54.81a1 1 0 01-.12 1.25l-1.67 1.67a1 1 0 01-1.25.12l-.81-.54c-.25.13-.51.24-.78.33l-.19.95a1 1 0 01-.98.8H8.82a1 1 0 01-.98-.8l-.19-.95a5.76 5.76 0 01-.78-.33l-.81.54a1 1 0 01-1.25-.12l-1.67-1.67a1 1 0 01-.12-1.25l.54-.81a5.76 5.76 0 01-.33-.78l-.95-.19a1 1 0 01-.8-.98V8.82a1 1 0 01.8-.98l.95-.19a5.76 5.76 0 01.328-.78l-.54-.81a1 1 0 01.12-1.25l1.67-1.67a1 1 0 011.25-.12l.81.54c.25-.13.51-.24.78-.33l.19-.95ZM10 13a3 3 0 100-6 3 3 0 000 6Z" clip-rule="evenodd" /></svg>
                                            <span>{{ $item['label'] }}</span>
                                        </span>
                                        <svg class="h-4 w-4 opacity-70" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                @endforeach
                            </div>
                        @endif
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
                        <p>&copy; {{ now()->year }} {{ \Illuminate\Support\Str::beforeLast((string) (\App\Models\Setting::first()?->company_email_domain ?: config('app.name', 'TipTap')), '.') }}. Built for responsive employee workflows.</p>
                    </footer>
                </section>
            </div>
        </div>
    </body>
</html>
