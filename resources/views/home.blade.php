@extends('layouts.guest')

@section('content')
    <section class="flex-grow flex flex-col items-center justify-center text-center px-4">
        <p class="mb-3 rounded-full border border-teal-primary/30 bg-teal-primary/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-teal-primary">
            Smart Employee Workspace
        </p>

        <h1 class="text-4xl font-extrabold tracking-tight text-navy-primary sm:text-5xl lg:text-6xl mb-4">
            Welcome to TipTap
        </h1>

        <p class="text-lg text-dark-slate/90 max-w-2xl mb-8 leading-relaxed">
            Streamline attendance, leave, and daily updates in one place with a clean and responsive employee portal.
        </p>

        <div class="flex flex-wrap justify-center gap-4">
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-xl bg-teal-primary px-6 py-3 font-semibold text-white shadow-lg shadow-teal-primary/30 transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#0e8f71] hover:shadow-xl">
                    Dashboard
                </a>
                <a href="{{ route('about') }}" class="inline-flex items-center rounded-xl border border-navy-primary px-6 py-3 font-semibold text-navy-primary transition-colors hover:bg-navy-primary/5">
                    About
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center rounded-xl bg-teal-primary px-6 py-3 font-semibold text-white shadow-lg shadow-teal-primary/30 transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#0e8f71] hover:shadow-xl">
                    Register
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl border border-navy-primary bg-white px-6 py-3 font-semibold text-navy-primary transition-colors hover:bg-navy-primary/5">
                    Login
                </a>
                <a href="{{ route('about') }}" class="inline-flex items-center rounded-xl bg-slate-200/70 px-6 py-3 font-semibold text-dark-slate transition-colors hover:bg-slate-300/70">
                    About
                </a>
            @endauth
        </div>
    </section>
@endsection