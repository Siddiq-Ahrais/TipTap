<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-navy-primary/70">Admin Dashboard</p>
                <h2 class="mt-1 font-display text-2xl font-semibold text-navy-primary sm:text-3xl">
                    Clean Overview
                </h2>
            </div>
            <p class="text-sm font-medium text-slate-500">{{ now()->format('l, d M Y') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="card-soft rounded-3xl p-6 sm:p-7">
            <h3 class="font-display text-xl font-semibold text-navy-primary">Today Focus</h3>
            <p class="mt-2 text-sm text-slate-600">Approvals and leave operations are separated into their own menus to keep dashboard concise.</p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:max-w-3xl">
                <a href="{{ route('approval.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                    <p class="text-sm font-semibold uppercase tracking-[0.12em] text-navy-primary/70">Approval</p>
                    <p class="mt-2 font-display text-lg font-semibold text-slate-900">Review Registrations</p>
                    <p class="mt-1 text-sm text-slate-500">Approve or reject newly registered employees.</p>
                </a>

                <a href="{{ route('leaves.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                    <p class="text-sm font-semibold uppercase tracking-[0.12em] text-navy-primary/70">Leaves</p>
                    <p class="mt-2 font-display text-lg font-semibold text-slate-900">Review Leave Requests</p>
                    <p class="mt-1 text-sm text-slate-500">Process employee leave submissions in the Leave menu.</p>
                </a>
            </div>
        </section>
    </div>
</x-app-layout>
