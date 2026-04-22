@extends('layouts.guest')

@section('content')
    <section class="flex-grow flex items-center justify-center px-4">
        <div class="w-full max-w-3xl rounded-3xl border border-navy-primary/20 bg-white/95 p-8 text-center shadow-xl shadow-slate-200/50 sm:p-10">
            <h1 class="text-4xl font-extrabold tracking-tight text-navy-primary sm:text-5xl">About TipTap</h1>

            <p class="mt-4 text-base leading-7 text-dark-slate/90 sm:text-lg">
                TipTap helps teams stay in sync by combining attendance tracking, leave management,
                and daily communication in one responsive interface that is easy to use on desktop and mobile.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-xl bg-navy-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-navy-primary/35 transition-transform duration-200 hover:scale-105 hover:bg-navy-dark">
                        Dashboard
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center rounded-xl border-2 border-navy-primary bg-white px-5 py-2.5 text-sm font-semibold text-navy-primary transition-transform duration-200 hover:scale-105 hover:bg-navy-light">
                        Home
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-xl bg-navy-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-navy-primary/35 transition-transform duration-200 hover:scale-105 hover:bg-navy-dark">
                        Register
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl border-2 border-navy-primary bg-white px-5 py-2.5 text-sm font-semibold text-navy-primary transition-transform duration-200 hover:scale-105 hover:bg-navy-light">
                        Login
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center rounded-xl px-5 py-2.5 text-sm font-semibold text-dark-slate/70 transition-transform duration-200 hover:scale-105 hover:text-navy-primary">
                        Home
                    </a>
                @endauth
            </div>
        </div>
    </section>
@endsection