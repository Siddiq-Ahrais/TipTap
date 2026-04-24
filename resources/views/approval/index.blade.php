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

        {{-- Today's Attendance Table --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <header class="bg-[#0B4A85] text-white">
                <div class="flex flex-col gap-1 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-display text-lg font-semibold">Today's Attendance</h3>
                        <p class="text-sm text-white/85">{{ $clockedInTodayCount }}/{{ $totalEmployeeCount }} employees clocked in &middot; ranked by earliest clock-in time</p>
                    </div>
                    <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold text-white">
                        {{ $todayAttendances->total() }} {{ Str::plural('record', $todayAttendances->total()) }}
                    </span>
                </div>
            </header>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Employee ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Clock In</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Clock Out</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($todayAttendances as $index => $attendance)
                            @php
                                $globalIndex = ($todayAttendances->currentPage() - 1) * $todayAttendances->perPage() + $index;
                                $empUser = $attendance->user;
                                $empId = 'EMP-' . str_pad((string) ($empUser?->id ?? 0), 4, '0', STR_PAD_LEFT);
                                $empName = $empUser?->name ?? '-';

                                $rawStatus = strtolower((string) $attendance->status);
                                $clockOut = $attendance->waktu_keluar;
                                $earlyStatus = strtolower((string) $attendance->early_checkout_status);

                                // Determine display status
                                if ($earlyStatus === 'pending') {
                                    $displayStatus = 'Pending Checkout';
                                    $statusColor = 'bg-amber-100 text-amber-700';
                                } elseif ($clockOut) {
                                    if ($rawStatus === 'pulang cepat') {
                                        $displayStatus = 'Early Checkout';
                                        $statusColor = 'bg-orange-100 text-orange-700';
                                    } else {
                                        $displayStatus = 'Checked Out';
                                        $statusColor = 'bg-slate-100 text-slate-600';
                                    }
                                } elseif ($rawStatus === 'terlambat') {
                                    $displayStatus = 'Late';
                                    $statusColor = 'bg-rose-100 text-rose-700';
                                } else {
                                    $displayStatus = 'Checked In';
                                    $statusColor = 'bg-emerald-100 text-emerald-700';
                                }
                            @endphp

                            <tr class="hover:bg-slate-50/80 transition-colors {{ $globalIndex === 0 ? 'bg-emerald-50/40' : '' }}">
                                <td class="px-4 py-3 font-semibold text-slate-500">
                                    @if ($globalIndex === 0)
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-amber-400 text-xs font-bold text-white" title="Fastest clock-in">🥇</span>
                                    @elseif ($globalIndex === 1)
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-300 text-xs font-bold text-white" title="2nd fastest">🥈</span>
                                    @elseif ($globalIndex === 2)
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-amber-600 text-xs font-bold text-white" title="3rd fastest">🥉</span>
                                    @else
                                        {{ $globalIndex + 1 }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-md bg-[#0B4A85]/10 px-2 py-0.5 text-xs font-semibold text-[#0B4A85]">{{ $empId }}</span>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $empName }}</td>
                                <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                                    {{ $attendance->waktu_masuk ? \Illuminate\Support\Carbon::parse($attendance->waktu_masuk)->format('H:i:s') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                                    {{ $clockOut ? \Illuminate\Support\Carbon::parse($clockOut)->format('H:i:s') : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusColor }}">
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                    No employees have clocked in yet today.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($todayAttendances->hasPages())
                <div class="flex items-center justify-between px-5 py-3 border-t border-slate-200 bg-slate-50/60">
                    <p class="text-xs text-slate-500">
                        Showing <span class="font-semibold text-slate-700">{{ $todayAttendances->firstItem() }}–{{ $todayAttendances->lastItem() }}</span> of <span class="font-semibold text-slate-700">{{ $todayAttendances->total() }}</span>
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($todayAttendances->onFirstPage())
                            <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-300 cursor-not-allowed">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Prev
                            </span>
                        @else
                            <a href="{{ $todayAttendances->previousPageUrl() }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-[#0B4A85] hover:text-white hover:border-[#0B4A85] transition-all duration-200">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Prev
                            </a>
                        @endif

                        @foreach ($todayAttendances->getUrlRange(1, $todayAttendances->lastPage()) as $page => $url)
                            @if ($page == $todayAttendances->currentPage())
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#0B4A85] text-xs font-bold text-white shadow-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-600 hover:bg-[#0B4A85] hover:text-white hover:border-[#0B4A85] transition-all duration-200">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($todayAttendances->hasMorePages())
                            <a href="{{ $todayAttendances->nextPageUrl() }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-[#0B4A85] hover:text-white hover:border-[#0B4A85] transition-all duration-200">
                                Next
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-300 cursor-not-allowed">
                                Next
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
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
