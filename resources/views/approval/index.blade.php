<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-navy-primary/70">Approval Center</p>
                <h2 class="mt-1 font-display text-2xl font-semibold text-navy-primary sm:text-3xl">
                    Employee Approval Menu
                </h2>
            </div>
            <p class="text-sm font-medium text-slate-500">{{ now()->format('l, d M Y') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="rounded-2xl border border-[#0B4A85]/20 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#0B4A85]">Today's Attendance Stat</p>
                    <h3 class="mt-1 font-display text-2xl font-bold text-slate-900">{{ $clockedInTodayCount }}/{{ $totalEmployeeCount }}</h3>
                </div>
                <p class="text-sm text-slate-600">Employees clocked in today out of total registered employees.</p>
            </div>
        </section>

        <section class="grid gap-5 lg:grid-cols-3">
            <a href="{{ route('approval.registrations.index') }}" class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#0B4A85]/60 hover:shadow-lg">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-[#0B4A85]/10 transition group-hover:bg-[#0B4A85]/20"></div>
                <div class="relative">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#0B4A85]">Approval Category</p>
                    <h3 class="mt-2 font-display text-2xl font-bold text-slate-900">Pending Registrations</h3>
                    <p class="mt-2 text-sm text-slate-600">Review new account registrations and approve official employees.</p>
                    <div class="mt-5 flex items-center justify-between">
                        <span class="rounded-full bg-[#0B4A85]/10 px-3 py-1 text-xs font-semibold text-[#0B4A85]">
                            {{ $pendingUsersCount }} Pending
                        </span>
                        <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#0B4A85]">
                            Open Menu
                            <svg class="h-4 w-4 transition group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h9.586l-2.293-2.293a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L13.586 11H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </div>
                </div>
            </a>

            <a href="{{ route('approval.early-checkouts.index') }}" class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#0B4A85]/60 hover:shadow-lg">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-[#0B4A85]/10 transition group-hover:bg-[#0B4A85]/20"></div>
                <div class="relative">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#0B4A85]">Approval Category</p>
                    <h3 class="mt-2 font-display text-2xl font-bold text-slate-900">Early Checkout Approval</h3>
                    <p class="mt-2 text-sm text-slate-600">Handle employee requests to clock out before office checkout time.</p>
                    <div class="mt-5 flex items-center justify-between">
                        <span class="rounded-full bg-[#0B4A85]/10 px-3 py-1 text-xs font-semibold text-[#0B4A85]">
                            {{ $pendingEarlyCheckoutCount }} Pending
                        </span>
                        <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#0B4A85]">
                            Open Menu
                            <svg class="h-4 w-4 transition group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h9.586l-2.293-2.293a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L13.586 11H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </div>
                </div>
            </a>

            <a href="{{ route('approval.leaves.index') }}" class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#0B4A85]/60 hover:shadow-lg">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-[#0B4A85]/10 transition group-hover:bg-[#0B4A85]/20"></div>
                <div class="relative">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#0B4A85]">Approval Category</p>
                    <h3 class="mt-2 font-display text-2xl font-bold text-slate-900">Leave Approval</h3>
                    <p class="mt-2 text-sm text-slate-600">Review employee leave and sick forms, then approve or reject each request.</p>
                    <div class="mt-5 flex items-center justify-between">
                        <span class="rounded-full bg-[#0B4A85]/10 px-3 py-1 text-xs font-semibold text-[#0B4A85]">
                            {{ $pendingLeaveCount }} Pending
                        </span>
                        <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#0B4A85]">
                            Open Menu
                            <svg class="h-4 w-4 transition group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h9.586l-2.293-2.293a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L13.586 11H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </div>
                </div>
            </a>
        </section>

    </div>
</x-app-layout>
