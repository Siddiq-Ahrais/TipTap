@props(['variant' => 'default'])

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
    <body class="min-h-screen flex flex-col bg-gray-50 text-gray-900 antialiased">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6">
            <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">TipTap</a>

            <nav class="flex items-center gap-4 sm:gap-6 text-sm">
                @if (Route::has('home'))
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 transition-colors">Home</a>
                @endif
                @if (Route::has('about'))
                    <a href="{{ route('about') }}" class="text-gray-600 hover:text-gray-900 transition-colors">About</a>
                @endif
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 transition-colors">Login</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-900 transition-colors">Register</a>
                @endif
            </nav>
        </header>

        <main class="flex-grow flex flex-col">
            <div class="w-full max-w-7xl mx-auto flex-grow flex flex-col px-4 sm:px-6 py-6 sm:py-8">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </div>
        </main>
    </body>
</html>
