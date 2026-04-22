@php
    $navItems = [
        ['label' => __('Dashboard'), 'route' => 'dashboard', 'active' => 'dashboard'],
        ['label' => __('Leaves'), 'route' => 'leaves.index', 'active' => 'leaves.*'],
        ['label' => __('Posts'), 'route' => 'posts.index', 'active' => 'posts.*'],
    ];

    $navItems = array_values(array_filter($navItems, fn (array $item): bool => Route::has($item['route'])));
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex h-16 w-full max-w-[1440px] items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="grid h-10 w-10 place-items-center rounded-2xl bg-navy-primary text-white shadow-lg shadow-navy-primary/35">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M3 9.5L12 4L21 9.5V20H3V9.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                    <path d="M9 20V12H15V20" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                </svg>
            </a>

            <div>
                <p class="font-display text-sm font-extrabold tracking-tight text-navy-primary">{{ config('app.name', 'TipTap') }}</p>
                <p class="text-xs text-dark-slate/70">Employee Workspace</p>
            </div>
        </div>

        <div class="hidden items-center gap-1 md:flex">
            @foreach ($navItems as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs($item['active']) ? 'bg-navy-primary text-white shadow-md shadow-navy-primary/25' : 'text-dark-slate/80 hover:bg-navy-light hover:text-navy-primary' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>

        <div class="hidden items-center gap-3 md:flex">
            <x-dropdown align="right" width="56">
                <x-slot name="trigger">
                    <button class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-dark-slate transition hover:border-[#0B4A85]/40 hover:text-navy-primary focus:outline-none focus:ring-2 focus:ring-[#0B4A85]/30">
                        <span class="inline-grid h-7 w-7 place-items-center rounded-lg bg-[#E7EFF6] text-[#0B4A85]">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                        <span>{{ Auth::user()->name }}</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link
                            :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                        >
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>

        <button
            @click="open = !open"
            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-dark-slate/80 transition hover:border-[#0B4A85]/40 hover:text-navy-primary focus:outline-none focus:ring-2 focus:ring-[#0B4A85]/30 md:hidden"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M6 18L18 6" />
            </svg>
        </button>
    </div>

    <div
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="border-t border-slate-200 bg-white p-4 md:hidden"
    >
        <div class="space-y-1">
            @foreach ($navItems as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="block rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs($item['active']) ? 'bg-navy-primary text-white' : 'text-dark-slate/80 hover:bg-navy-light hover:text-navy-primary' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>

        <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-3">
            <p class="text-sm font-semibold text-navy-primary">{{ Auth::user()->name }}</p>
            <p class="text-xs text-dark-slate/70">{{ Auth::user()->email }}</p>

            <div class="mt-3 flex flex-wrap items-center gap-2">
                <a href="{{ route('profile.edit') }}" class="rounded-lg border border-navy-primary/30 px-3 py-1.5 text-xs font-medium text-navy-primary transition hover:bg-navy-primary/5">
                    {{ __('Profile') }}
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-600 transition hover:bg-rose-50">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
