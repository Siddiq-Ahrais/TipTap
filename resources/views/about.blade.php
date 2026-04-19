@extends('layouts.guest')

@section('content')
    <div class="space-y-4">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">About TipTap</h1>
        <p class="text-gray-700 dark:text-gray-300">
            Halaman About sudah dipindahkan ke Blade agar konsisten dengan Breeze dan mempermudah
            pengelolaan komponen UI ke depannya.
        </p>

        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Home</a>

            @auth
                <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Login</a>
            @endauth
        </div>
    </div>
@endsection