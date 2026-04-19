@extends('layouts.guest')

@section('content')
    <div class="space-y-4">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Halaman Home</h1>
        <p class="text-gray-700 dark:text-gray-300">
            Selamat datang di TipTap. Halaman ini sekarang memakai Blade layout yang sama dengan Breeze
            supaya struktur frontend tetap konsisten dan mudah dirawat.
        </p>

        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('about') }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">About</a>

            @auth
                <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Login</a>
                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Register</a>
            @endauth
        </div>
    </div>
@endsection