@extends('layouts.guest')

@section('content')
    <section class="flex-grow flex flex-col items-center justify-center text-center px-4">
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900 mb-4">
            Welcome to TipTap
        </h1>

        <p class="text-lg text-gray-500 max-w-2xl mb-8">
            Streamline attendance, leave, and daily updates in one place with a clean and responsive employee portal.
        </p>

        <div class="flex flex-wrap justify-center gap-4">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-md">
                    Dashboard
                </a>
                <a href="{{ route('about') }}" class="bg-white text-gray-700 border border-gray-300 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                    About
                </a>
            @else
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-md">
                    Register
                </a>
                <a href="{{ route('login') }}" class="bg-white text-gray-700 border border-gray-300 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                    Login
                </a>
                <a href="{{ route('about') }}" class="bg-white text-gray-700 border border-gray-300 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                    About
                </a>
            @endauth
        </div>
    </section>
@endsection