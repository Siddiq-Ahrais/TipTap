<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-navy-primary/70">Attendance</p>
                <h2 class="mt-1 font-display text-2xl font-semibold text-navy-primary sm:text-3xl">
                    Attendance History
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-xl border border-navy-primary/30 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.14em] text-navy-primary transition hover:bg-navy-primary/5">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                    Back to Dashboard
                </a>
                <p class="text-sm font-medium text-slate-500">{{ now()->format('l, d M Y') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Per-page selector --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-display text-lg font-semibold text-navy-primary">Your Full Attendance Record</h3>
                    <p class="text-sm text-slate-500">Complete history of your clock-in and clock-out activity.</p>
                </div>

                <form method="GET" action="{{ route('attendance.history') }}" class="flex items-center gap-2">
                    <label for="per_page" class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Show</label>
                    <select
                        id="per_page"
                        name="per_page"
                        onchange="this.form.submit()"
                        class="rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 focus:border-[#0B4A85] focus:ring-2 focus:ring-[#0B4A85] outline-none transition-all cursor-pointer"
                    >
                        @foreach ([10, 20, 30, 50, 100] as $option)
                            <option value="{{ $option }}" {{ $perPage === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    <span class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">entries</span>
                </form>
            </div>
        </section>

        {{-- Attendance Table --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <header class="bg-[#0B4A85] text-white">
                <div class="flex flex-col gap-1 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-display text-lg font-semibold">Attendance Log</h3>
                        <p class="text-sm text-white/85">Sorted by most recent first</p>
                    </div>
                    <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold text-white">
                        {{ $attendances->total() }} {{ Str::plural('record', $attendances->total()) }} total
                    </span>
                </div>
            </header>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Clock In</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Clock Out</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($attendances as $index => $attendance)
                            @php
                                $rawStatus = strtolower((string) $attendance->status);
                                $clockOut = $attendance->waktu_keluar;
                                $earlyStatus = strtolower((string) $attendance->early_checkout_status);

                                if ($earlyStatus === 'pending') {
                                    $displayStatus = 'Pending Checkout';
                                    $statusTone = 'amber';
                                } elseif ($clockOut) {
                                    if ($rawStatus === 'pulang cepat') {
                                        $displayStatus = 'Early Checkout';
                                        $statusTone = 'amber';
                                    } else {
                                        $displayStatus = 'Checked Out';
                                        $statusTone = 'slate';
                                    }
                                } elseif ($rawStatus === 'terlambat') {
                                    $displayStatus = 'Late';
                                    $statusTone = 'rose';
                                } elseif ($rawStatus === 'hadir') {
                                    $displayStatus = 'Present';
                                    $statusTone = 'emerald';
                                } else {
                                    $displayStatus = 'Checked In';
                                    $statusTone = 'emerald';
                                }

                                $rowNumber = ($attendances->currentPage() - 1) * $attendances->perPage() + $index + 1;
                            @endphp

                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 font-semibold text-slate-400">{{ $rowNumber }}</td>
                                <td class="px-4 py-3 font-medium text-slate-700">
                                    {{ $attendance->tanggal ? \Illuminate\Support\Carbon::parse($attendance->tanggal)->format('D, d M Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                                    {{ $attendance->waktu_masuk ? \Illuminate\Support\Carbon::parse($attendance->waktu_masuk)->format('H:i:s') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                                    {{ $clockOut ? \Illuminate\Support\Carbon::parse($clockOut)->format('H:i:s') : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    @include('partials.dashboard.status-pill', ['label' => $displayStatus, 'tone' => $statusTone])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">
                                    No attendance records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($attendances->hasPages())
                <div class="border-t border-slate-200 bg-slate-50/80 px-4 py-3">
                    <div class="flex flex-col items-center gap-3 sm:flex-row sm:justify-between">
                        <p class="text-xs text-slate-500">
                            Showing <span class="font-semibold text-slate-700">{{ $attendances->firstItem() }}</span>
                            to <span class="font-semibold text-slate-700">{{ $attendances->lastItem() }}</span>
                            of <span class="font-semibold text-slate-700">{{ $attendances->total() }}</span> records
                        </p>

                        <nav class="flex items-center gap-1">
                            {{-- Previous --}}
                            @if ($attendances->onFirstPage())
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-300 cursor-not-allowed">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                </span>
                            @else
                                <a href="{{ $attendances->previousPageUrl() }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-[#0B4A85] hover:text-white hover:border-[#0B4A85] transition-colors">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                </a>
                            @endif

                            {{-- Page Numbers --}}
                            @foreach ($attendances->getUrlRange(max(1, $attendances->currentPage() - 2), min($attendances->lastPage(), $attendances->currentPage() + 2)) as $page => $url)
                                @if ($page === $attendances->currentPage())
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#0B4A85] text-xs font-bold text-white">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white border border-slate-200 text-xs font-medium text-slate-600 hover:bg-[#0B4A85] hover:text-white hover:border-[#0B4A85] transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach

                            {{-- Next --}}
                            @if ($attendances->hasMorePages())
                                <a href="{{ $attendances->nextPageUrl() }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-[#0B4A85] hover:text-white hover:border-[#0B4A85] transition-colors">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                </a>
                            @else
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-300 cursor-not-allowed">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                </span>
                            @endif
                        </nav>
                    </div>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
