<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 text-dark-slate antialiased">
        @php
            $isAdminNavigation = in_array(strtolower((string) auth()->user()?->role), ['admin', 'administrator', 'superadmin', 'super admin', 'super_admin'], true);

            $sidebarItems = [
                ['label' => __('Dashboard'), 'route' => 'dashboard', 'active' => 'dashboard'],
                ['label' => __('Approval'), 'route' => 'approval.index', 'active' => 'approval.*', 'visible' => $isAdminNavigation],
                ['label' => __('Leaves'), 'route' => 'leaves.index', 'active' => 'leaves.*'],
                ['label' => __('Posts'), 'route' => 'posts.index', 'active' => 'posts.*'],
                ['label' => __('Profile'), 'route' => 'profile.edit', 'active' => 'profile.*'],
            ];

            $sidebarItems = array_values(array_filter(
                $sidebarItems,
                fn (array $item): bool => ($item['visible'] ?? true) && Route::has($item['route'])
            ));
        @endphp

        <div class="relative min-h-screen overflow-x-clip">

            @include('layouts.navigation')

            <div class="mx-auto grid w-full max-w-[1440px] grid-cols-1 gap-6 px-4 pb-8 pt-6 sm:px-6 lg:grid-cols-[260px_minmax(0,1fr)] lg:px-8">
                <aside class="hidden lg:block">
                    <div class="card-soft sticky top-24 rounded-3xl p-4">
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.18em] text-navy-primary/70">Workspace</p>

                        <nav class="mt-3 space-y-1.5">
                            @foreach ($sidebarItems as $item)
                                <a
                                    href="{{ route($item['route']) }}"
                                    class="sidebar-link {{ request()->routeIs($item['active']) ? 'sidebar-link-active' : '' }}"
                                >
                                    <span>{{ $item['label'] }}</span>
                                    <svg class="h-4 w-4 opacity-70" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            @endforeach
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
                        <p>&copy; {{ now()->year }} {{ config('app.name', 'TipTap') }}. Built for responsive employee workflows.</p>
                    </footer>
                </section>
            </div>
        </div>
    </body>
</html>
