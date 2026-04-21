@props(['variant' => 'default'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen flex flex-col bg-slate-50 text-dark-slate antialiased">
        @php
            $guestNavItems = [
                ['label' => 'Home', 'route' => 'home', 'active' => 'home'],
                ['label' => 'About', 'route' => 'about', 'active' => 'about'],
                ['label' => 'Login', 'route' => 'login', 'active' => 'login'],
                ['label' => 'Register', 'route' => 'register', 'active' => 'register'],
            ];

            $guestNavItems = array_values(array_filter($guestNavItems, fn (array $item): bool => Route::has($item['route'])));
        @endphp

        <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-4 sm:px-6">
                <a href="{{ route('home') }}" class="text-xl font-extrabold tracking-tight text-navy-primary">TipTap</a>

                <nav class="flex items-center gap-4 text-sm sm:gap-6">
                    @foreach ($guestNavItems as $item)
                        @php
                            $isActive = request()->routeIs($item['active']);
                        @endphp
                        <a
                            href="{{ route($item['route']) }}"
                            class="group relative px-1 py-2 font-medium transition-colors duration-200 {{ $isActive ? 'text-teal-primary' : 'text-dark-slate/80 hover:text-teal-primary' }}"
                        >
                            {{ $item['label'] }}
                            <span class="absolute inset-x-0 -bottom-0.5 h-0.5 rounded-full bg-teal-primary transition-all duration-200 {{ $isActive ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }}"></span>
                        </a>
                    @endforeach
                </nav>
            </div>
        </header>

        <main class="relative flex-grow overflow-hidden">
            <div class="pointer-events-none absolute -left-24 -top-20 h-64 w-64 rounded-full bg-teal-primary/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-20 top-1/3 h-72 w-72 rounded-full bg-navy-primary/10 blur-3xl"></div>

            <div class="relative mx-auto flex w-full max-w-7xl flex-grow flex-col px-4 py-6 sm:px-6 sm:py-8">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </div>
        </main>
    </body>
</html>
