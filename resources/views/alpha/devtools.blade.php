<x-alpha-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-navy-primary/70">Developer Tools</p>
                <h2 class="mt-1 font-display text-2xl font-semibold text-navy-primary sm:text-3xl">
                    Tester Accounts & Data
                </h2>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-lg border border-amber-300 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">
                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.486 0l6.516 11.584c.75 1.334-.213 2.992-1.742 2.992H3.483c-1.53 0-2.492-1.658-1.742-2.992L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-6a1 1 0 00-1 1v3a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                Developer Testing Environment
            </span>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ showResetConfirm: false }">
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

        {{-- Add Days Section --}}
        <section class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm p-6 border border-[#0B4A85]/15">
            <div class="flex items-start gap-4">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-[#0B4A85]/10 text-[#0B4A85]">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-display text-xl font-semibold text-[#0B4A85]">Generate Tester Data</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Creates 20 tester accounts and generates attendance records going back N days from today.
                        Each account type has different attendance behavior.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('alpha.devtools.addDays') }}" class="mt-5">
                @csrf
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="flex-1 max-w-xs">
                        <label for="days" class="mb-1.5 block text-sm font-medium text-slate-600">Number of Days</label>
                        <input
                            id="days"
                            name="days"
                            type="number"
                            min="1"
                            max="365"
                            value="{{ old('days', 10) }}"
                            placeholder="e.g. 10"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 focus:ring-2 focus:ring-[#0B4A85] focus:border-[#0B4A85] outline-none transition-all"
                        />
                        <p class="mt-1 text-xs text-slate-400">1–365 days of attendance history will be generated</p>
                    </div>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#0B4A85] px-6 py-3 font-semibold text-white transition hover:bg-[#063157] focus:outline-none focus:ring-2 focus:ring-[#0B4A85] focus:ring-offset-2"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                        Generate Data
                    </button>
                </div>
            </form>
        </section>

        {{-- Tester Accounts Table --}}
        <section class="max-w-4xl mx-auto rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <header class="bg-[#0B4A85] text-white">
                <div class="flex flex-col gap-1 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-display text-lg font-semibold">Tester Accounts</h3>
                        <p class="text-sm text-white/85">20 accounts across 5 behavior groups</p>
                    </div>
                    <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold text-white">
                        Password: tester123
                    </span>
                </div>
            </header>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Behavior</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Days</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($testerAccounts as $index => $account)
                            @php
                                $meta = $typeLabels[$account['type']] ?? ['label' => $account['type'], 'color' => 'bg-slate-100 text-slate-600'];
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 font-semibold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $account['name'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-md bg-[#0B4A85]/10 px-2 py-0.5 text-xs font-semibold text-[#0B4A85]">{{ $account['email'] }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $meta['color'] }}">
                                        {{ $meta['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($account['exists'])
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-400">
                                            <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                                            Not Created
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">
                                        {{ $account['attendance_days'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Behavior Legend --}}
            <div class="border-t border-slate-200 bg-slate-50/80 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500 mb-2">Behavior Legend</p>
                <div class="flex flex-wrap gap-3">
                    @foreach ($typeLabels as $type => $meta)
                        <div class="inline-flex items-center gap-1.5">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $meta['color'] }}">{{ $meta['label'] }}</span>
                            <span class="text-xs text-slate-500">
                                @if ($type === 'clockin')
                                    — Clocked in on time (A1-5)
                                @elseif ($type === 'clockout')
                                    — Clocked in & out normally (B1-5)
                                @elseif ($type === 'early_clockout')
                                    — Early checkout approved (C1-5)
                                @elseif ($type === 'late')
                                    — Late arrivals (D1-2)
                                @elseif ($type === 'no_clockin')
                                    — No attendance records (E1-3)
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Reset Testers Section --}}
        <section class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm p-6 border border-rose-200">
            <div class="flex items-start gap-4">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-rose-100 text-rose-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-display text-xl font-semibold text-rose-700">Reset All Testers</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Delete all 20 tester accounts and their attendance records. This is a destructive action.
                    </p>
                </div>
            </div>

            <div class="mt-5">
                <button
                    type="button"
                    @click="showResetConfirm = true"
                    class="inline-flex items-center gap-2 rounded-lg border-2 border-rose-500 bg-rose-500 px-6 py-3 font-semibold text-white transition hover:bg-rose-600 hover:border-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Delete All Testers
                </button>
            </div>

            {{-- Reset Confirmation Modal --}}
            <div
                x-cloak
                x-show="showResetConfirm"
                x-transition.opacity
                class="fixed inset-0 z-[70] flex items-center justify-center bg-[#031936]/60 p-4 backdrop-blur-sm"
            >
                <div
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="w-full max-w-md overflow-hidden rounded-3xl border border-rose-300/40 bg-white shadow-[0_24px_60px_rgba(3,25,54,0.35)]"
                    @click.away="showResetConfirm = false"
                >
                    <div class="bg-gradient-to-r from-rose-600 to-rose-500 px-6 py-4 text-white">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/80">Destructive Action</p>
                        <h4 class="mt-1 font-display text-xl font-semibold">Delete All Tester Data?</h4>
                    </div>

                    <div class="space-y-5 px-6 py-5">
                        <p class="text-sm leading-6 text-slate-600">
                            This will <strong class="text-rose-700">permanently delete</strong> all 20 tester accounts
                            and every attendance record associated with them.
                        </p>

                        <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-medium text-amber-700">
                            ⚠️ This action cannot be undone. You can regenerate the data using "Generate Data" afterwards.
                        </p>

                        <div class="grid grid-cols-2 gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                                @click="showResetConfirm = false"
                            >
                                Cancel
                            </button>
                            <form method="POST" action="{{ route('alpha.devtools.resetTesters') }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-500 bg-rose-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-600"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Yes, Delete All
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-alpha-layout>
