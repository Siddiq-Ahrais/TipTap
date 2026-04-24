@props(['variant' => 'default'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \Illuminate\Support\Str::beforeLast((string) (\App\Models\Setting::first()?->company_email_domain ?: config('app.name', 'TipTap')), '.') }}</title>

        <!-- Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen flex flex-col antialiased {{ $variant === 'admin' || request()->routeIs('admin.login') ? 'bg-[#063157] text-slate-100' : 'bg-slate-50 text-dark-slate' }}">
        @php
            $isAdminVariant = $variant === 'admin' || request()->routeIs('admin.login');

            $guestNavItems = [
                ['label' => 'Home', 'route' => 'home', 'active' => 'home'],
                ['label' => 'About', 'route' => 'about', 'active' => 'about'],
                ['label' => 'Login', 'route' => 'login', 'active' => 'login'],
                ['label' => 'Register', 'route' => 'register', 'active' => 'register'],
            ];

            $guestNavItems = array_values(array_filter($guestNavItems, fn (array $item): bool => Route::has($item['route'])));
        @endphp

        <header class="sticky top-0 z-40 border-b backdrop-blur {{ $isAdminVariant ? 'border-slate-100/10 bg-[#063157]/90' : 'border-[#0B4A85]/15 bg-white/90' }}">
            <div class="flex h-16 w-full items-center justify-between gap-3 px-4 sm:px-6 lg:px-10">
                <a href="{{ route('home') }}" class="shrink-0 text-xl font-extrabold tracking-tight {{ $isAdminVariant ? 'text-slate-100' : 'text-navy-primary' }}">TipTap</a>

                <div class="flex min-w-0 items-center justify-end gap-2 sm:gap-4">
                    <nav class="flex items-center gap-3 text-sm sm:gap-6">
                    @foreach ($guestNavItems as $item)
                        @php
                            $isActive = request()->routeIs($item['active']);
                        @endphp
                        <a
                            href="{{ route($item['route']) }}"
                            class="group relative px-1 py-2 font-medium transition-colors duration-200 {{ $isActive ? ($isAdminVariant ? 'text-white' : 'text-navy-primary') : ($isAdminVariant ? 'text-slate-200/85 hover:text-white' : 'text-dark-slate/80 hover:text-navy-primary') }}"
                        >
                            {{ $item['label'] }}
                            <span class="absolute inset-x-0 -bottom-0.5 h-0.5 rounded-full {{ $isAdminVariant ? 'bg-white' : 'bg-navy-primary' }} transition-all duration-200 {{ $isActive ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }}"></span>
                        </a>
                    @endforeach
                    </nav>

                    @if (Route::has('admin.login') && !$isAdminVariant)
                        <a
                            href="{{ route('admin.login') }}"
                            class="inline-flex shrink-0 items-center rounded-lg bg-navy-primary px-2.5 py-1.5 text-xs font-semibold text-white shadow-md shadow-navy-primary/30 transition-colors duration-200 hover:bg-navy-dark sm:px-3.5 sm:py-2 sm:text-sm"
                        >
                            Admin Login
                        </a>
                    @endif
                </div>
            </div>
        </header>

        <main class="relative flex-1 overflow-hidden">
            @if (!$isAdminVariant)
                <div class="pointer-events-none absolute -left-24 -top-20 h-64 w-64 rounded-full bg-navy-light blur-3xl"></div>
                <div class="pointer-events-none absolute -right-20 top-1/3 h-72 w-72 rounded-full bg-navy-primary/10 blur-3xl"></div>
            @endif

            <div class="relative flex min-h-[calc(100vh-4rem)] w-full flex-col px-4 py-6 sm:px-6 sm:py-8 lg:px-10">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </div>
        </main>

    </body>
</html>
