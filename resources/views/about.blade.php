@extends('layouts.guest')

@section('content')
    <section class="flex-grow flex items-center justify-center">
        <div class="w-full max-w-3xl rounded-3xl border border-slate-200 bg-white/95 p-8 text-center shadow-xl shadow-slate-200/50 sm:p-10">
            <h1 class="text-4xl font-extrabold tracking-tight text-navy-primary sm:text-5xl">About TipTap</h1>

            <p class="mt-4 text-base leading-7 text-dark-slate/90 sm:text-lg">
                TipTap helps teams stay in sync by combining attendance tracking, leave management,
                and daily communication in one responsive interface that is easy to use on desktop and mobile.
            </p>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center rounded-xl border border-navy-primary bg-white px-5 py-2.5 text-sm font-semibold text-navy-primary transition-colors hover:bg-navy-primary/5">
                    Home
                </a>

                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-xl bg-teal-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-teal-primary/30 transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#0e8f71]">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-xl bg-teal-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-teal-primary/30 transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#0e8f71]">
                        Register
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl bg-slate-200/70 px-5 py-2.5 text-sm font-semibold text-dark-slate transition-colors hover:bg-slate-300/70">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </section>
@endsection