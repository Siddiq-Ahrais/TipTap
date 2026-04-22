@extends('layouts.guest')

@section('content')
    <section class="flex flex-grow items-center justify-center px-4">
        <div class="w-full max-w-3xl rounded-3xl border border-navy-primary/20 bg-white/95 p-8 text-center shadow-xl shadow-slate-200/50 sm:p-10">
            <p class="mb-3 inline-flex rounded-full border border-navy-primary/25 bg-navy-light px-4 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-navy-primary">
                Smart Employee Workspace
            </p>

            <h1 class="mb-4 text-4xl font-extrabold tracking-tight text-navy-primary sm:text-5xl lg:text-6xl">
                Welcome to TipTap
            </h1>

            <p class="mx-auto mb-8 max-w-2xl text-lg leading-relaxed text-dark-slate/90">
                Streamline attendance, leave, and daily updates in one place with a clean and responsive employee portal.
            </p>

            <div class="mt-1 flex flex-wrap items-center justify-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-xl bg-navy-primary px-6 py-3 font-semibold text-white shadow-lg shadow-navy-primary/35 transition-transform duration-200 hover:scale-105 hover:bg-navy-dark">
                        Dashboard
                    </a>
                    <a href="{{ route('about') }}" class="inline-flex items-center rounded-xl border-2 border-navy-primary bg-white px-6 py-3 font-semibold text-navy-primary transition-transform duration-200 hover:scale-105 hover:bg-navy-light">
                        About
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-xl bg-navy-primary px-6 py-3 font-semibold text-white shadow-lg shadow-navy-primary/35 transition-transform duration-200 hover:scale-105 hover:bg-navy-dark">
                        Register
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl border-2 border-navy-primary bg-white px-6 py-3 font-semibold text-navy-primary transition-transform duration-200 hover:scale-105 hover:bg-navy-light">
                        Login
                    </a>
                    <a href="{{ route('about') }}" class="inline-flex items-center rounded-xl px-6 py-3 font-semibold text-dark-slate/70 transition-transform duration-200 hover:scale-105 hover:text-navy-primary">
                        About
                    </a>
                @endauth
            </div>
        </div>
    </section>
@endsection