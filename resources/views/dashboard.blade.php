<x-app-layout>
    @php
        $dashboardUser = $employee ?? auth()->user();

        $employeeName = data_get($dashboardUser, 'name', auth()->user()->name);
        $employeeEmail = data_get($dashboardUser, 'email', auth()->user()->email);
        $employeeRole = data_get($dashboardUser, 'role', 'Employee');
        $employeeDepartment = data_get($dashboardUser, 'department', 'Operations');
        $employeeCode = data_get($dashboardUser, 'employee_id', 'EMP-'.str_pad((string) auth()->id(), 4, '0', STR_PAD_LEFT));
        $employeeAvatar = data_get($dashboardUser, 'avatar_url');

        $normalizedStatus = strtolower((string) ($todayStatus ?? 'not_yet_clocked_in'));
        $statusAliases = [
            'checked_in' => 'checked_in',
            'hadir' => 'checked_in',
            'terlambat' => 'late',
            'late' => 'late',
            'checked_out' => 'checked_out',
            'clocked_out' => 'checked_out',
            'absent' => 'absent',
            'pending' => 'pending',
            'on_leave' => 'pending',
            'izin' => 'pending',
            'sakit' => 'pending',
            'not_yet_clocked_in' => 'not_yet_clocked_in',
            'not_clocked_in' => 'not_yet_clocked_in',
        ];

        $statusKey = $statusAliases[$normalizedStatus] ?? 'not_yet_clocked_in';

        $statusMeta = [
            'checked_in' => [
                'label' => 'Checked In',
                'tone' => 'emerald',
                'description' => 'You are currently clocked in. Use clock out when your shift ends.',
            ],
            'late' => [
                'label' => 'Late',
                'tone' => 'rose',
                'description' => 'You checked in late today. Please keep your team informed.',
            ],
            'checked_out' => [
                'label' => 'Checked Out',
                'tone' => 'slate',
                'description' => 'Attendance is complete for today. Great job wrapping up on time.',
            ],
            'pending' => [
                'label' => 'Pending',
                'tone' => 'amber',
                'description' => 'Your attendance is pending or currently marked as leave for review.',
            ],
            'absent' => [
                'label' => 'Absent',
                'tone' => 'rose',
                'description' => 'No attendance record found for today. Please check with your supervisor.',
            ],
            'not_yet_clocked_in' => [
                'label' => 'Not Yet Clocked In',
                'tone' => 'amber',
                'description' => 'Start your workday by clocking in when your shift begins.',
            ],
        ];

        $todayMeta = $statusMeta[$statusKey];

        $canClockIn = in_array($statusKey, ['not_yet_clocked_in', 'absent'], true);
        $canClockOut = in_array($statusKey, ['checked_in', 'late'], true);

        $metricCards = $metrics ?? [
            [
                'label' => 'This Month Attendance',
                'value' => data_get($stats ?? [], 'monthly_attendance', '22 Days'),
                'caption' => 'Target 24 working days',
                'trend' => '+4% vs last month',
                'trendDirection' => 'up',
            ],
            [
                'label' => 'Leave Balance',
                'value' => data_get($stats ?? [], 'leave_balance', '8 Days'),
                'caption' => 'Annual leave remaining',
                'trend' => 'On track',
                'trendDirection' => 'neutral',
            ],
            [
                'label' => 'Punctuality Score',
                'value' => data_get($stats ?? [], 'punctuality', '96%'),
                'caption' => 'On-time arrival performance',
                'trend' => '+1.5%',
                'trendDirection' => 'up',
            ],
        ];

        $attendanceRows = collect($attendanceHistory ?? [])->take(6);

        $quickActions = [
            [
                'label' => 'Request Leave',
                'description' => 'Submit annual leave or urgent absence requests.',
                'route' => 'leaves.create',
            ],
            [
                'label' => 'Manage Posts',
                'description' => 'Create and maintain internal post updates.',
                'route' => 'posts.index',
            ],
        ];
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-navy-primary/70">Employee Dashboard</p>
                <h2 class="mt-1 font-display text-2xl font-semibold text-navy-primary sm:text-3xl">
                    {{ __('Welcome back, :name', ['name' => $employeeName]) }}
                </h2>
            </div>
            <p class="text-sm font-medium text-slate-500">{{ now()->format('l, d M Y') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <section class="card-soft rounded-3xl p-6 sm:p-7">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-center gap-4">
                        @if ($employeeAvatar)
                            <img src="{{ $employeeAvatar }}" alt="{{ $employeeName }}" class="h-16 w-16 rounded-2xl object-cover ring-4 ring-white/80">
                        @else
                            <div class="grid h-16 w-16 place-items-center rounded-2xl bg-navy-primary text-2xl font-semibold text-white ring-4 ring-white/80">
                                {{ strtoupper(substr($employeeName, 0, 1)) }}
                            </div>
                        @endif

                        <div>
                            <h3 class="font-display text-2xl font-semibold text-navy-primary">{{ $employeeName }}</h3>
                            <p class="text-sm text-slate-500">{{ $employeeRole }} &middot; {{ $employeeDepartment }}</p>
                        </div>
                    </div>

                    @include('partials.dashboard.status-pill', ['label' => $todayMeta['label'], 'tone' => $todayMeta['tone']])
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-white/70 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Employee ID</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $employeeCode }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white/70 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Work Email</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $employeeEmail }}</p>
                    </div>
                </div>
            </section>

            <section class="card-soft rounded-3xl p-6 sm:p-7">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Today's Attendance Status</p>
                <div class="mt-4">
                    @include('partials.dashboard.status-pill', ['label' => $todayMeta['label'], 'tone' => $todayMeta['tone']])
                </div>

                <p class="mt-4 text-sm leading-6 text-slate-600">{{ $todayMeta['description'] }}</p>

                <div class="mt-6">
                    @if ($canClockIn)
                        <form method="POST" action="{{ url('/clock-in') }}" x-data="{ loading: false }" @submit="loading = true">
                            @csrf
                            <button
                                type="submit"
                                :disabled="loading"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-teal-primary px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white transition-all hover:scale-[1.02] hover:bg-[#0e8f71] focus:outline-none focus:ring-2 focus:ring-teal-primary/35 focus:ring-offset-2"
                            >
                                <span x-show="!loading">Clock In</span>
                                <span x-cloak x-show="loading" class="inline-flex items-center gap-2">
                                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.35" stroke-width="4" />
                                        <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                                    </svg>
                                    Processing
                                </span>
                            </button>
                        </form>
                    @elseif ($canClockOut)
                        <form method="POST" action="{{ url('/clock-out') }}" x-data="{ loading: false }" @submit="loading = true">
                            @csrf
                            @method('PUT')
                            <button
                                type="submit"
                                :disabled="loading"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-navy-primary px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white transition-all hover:scale-[1.02] hover:bg-[#083a6a] focus:outline-none focus:ring-2 focus:ring-navy-primary/30 focus:ring-offset-2"
                            >
                                <span x-show="!loading">Clock Out</span>
                                <span x-cloak x-show="loading" class="inline-flex items-center gap-2">
                                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.35" stroke-width="4" />
                                        <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                                    </svg>
                                    Processing
                                </span>
                            </button>
                        </form>
                    @else
                        <button
                            type="button"
                            disabled
                            class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-2xl bg-slate-400 px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white"
                        >
                            Attendance Completed
                        </button>
                    @endif
                </div>

                <p class="mt-3 text-xs text-slate-500">
                    Button state is rendered dynamically using Blade status checks.
                </p>
            </section>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($metricCards as $metric)
                @include('partials.dashboard.metric-card', $metric)
            @endforeach
        </div>

        <section class="card-soft rounded-3xl p-6 sm:p-7">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-display text-xl font-semibold text-navy-primary">Recent Attendance Activity</h3>
                    <p class="text-sm text-slate-500">Latest records from your attendance timeline.</p>
                </div>

                <a href="{{ route('leaves.index') }}" class="inline-flex items-center rounded-xl border border-navy-primary/30 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.14em] text-navy-primary transition hover:bg-navy-primary/5">
                    View Leave History
                </a>
            </div>

            <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white/75">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Clock In</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Clock Out</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($attendanceRows as $row)
                                @php
                                    $rowStatusRaw = strtolower((string) data_get($row, 'status', 'not_yet_clocked_in'));
                                    $rowStatusKey = $statusAliases[$rowStatusRaw] ?? 'not_yet_clocked_in';
                                    $rowMeta = $statusMeta[$rowStatusKey];
                                    $rowDate = data_get($row, 'tanggal');
                                @endphp

                                <tr class="bg-white/70">
                                    <td class="px-4 py-3 font-medium text-slate-700">
                                        {{ $rowDate ? \Illuminate\Support\Carbon::parse($rowDate)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ data_get($row, 'waktu_masuk', '-') }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ data_get($row, 'waktu_keluar', '-') }}</td>
                                    <td class="px-4 py-3">
                                        @include('partials.dashboard.status-pill', ['label' => $rowMeta['label'], 'tone' => $rowMeta['tone']])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-9 text-center text-sm text-slate-500">
                                        No attendance history yet. Your next check-in will appear in this table.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($quickActions as $action)
                @if (Route::has($action['route']))
                    <a href="{{ route($action['route']) }}" class="card-soft rounded-3xl p-5 transition-all duration-200 hover:-translate-y-1 hover:opacity-95 hover:shadow-lg hover:shadow-slate-200/70">
                        <p class="font-display text-lg font-semibold text-slate-900">{{ $action['label'] }}</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $action['description'] }}</p>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</x-app-layout>
