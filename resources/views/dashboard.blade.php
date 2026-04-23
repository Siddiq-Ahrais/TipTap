<x-app-layout>
    @php
        $dashboardUser = $employee ?? auth()->user();

        $employeeName = data_get($dashboardUser, 'name', auth()->user()->name);
        $employeeEmail = data_get($dashboardUser, 'email', auth()->user()->email);
        $employeeRole = data_get($dashboardUser, 'role', 'Employee');
        $employeeDepartment = data_get($dashboardUser, 'department', 'Operations');
        $employeeCode = data_get($dashboardUser, 'employee_id', 'EMP-'.str_pad((string) auth()->id(), 4, '0', STR_PAD_LEFT));
        $employeeAvatar = data_get($dashboardUser, 'avatar_url');
        $isAdminDashboard = in_array(strtolower((string) $employeeRole), ['admin', 'administrator', 'super admin', 'super_admin'], true);

        $todayAttendanceRecord = \App\Models\Attendance::query()
            ->where('user_id', auth()->id())
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        $effectiveTodayStatus = $todayStatus ?? null;

        if ($effectiveTodayStatus === null) {
            if (! $todayAttendanceRecord) {
                $effectiveTodayStatus = 'not_yet_clocked_in';
            } elseif (data_get($todayAttendanceRecord, 'waktu_keluar')) {
                $effectiveTodayStatus = 'checked_out';
            } elseif (strtolower((string) data_get($todayAttendanceRecord, 'early_checkout_status')) === 'pending') {
                $effectiveTodayStatus = 'pending';
            } else {
                $effectiveTodayStatus = strtolower((string) data_get($todayAttendanceRecord, 'status', 'checked_in'));
            }
        }

        $normalizedStatus = strtolower((string) ($effectiveTodayStatus ?? 'not_yet_clocked_in'));
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
        $canAttemptClockOut = in_array($statusKey, ['checked_in', 'late'], true);

        $clockOutMinimumMinutes = 360;
        $clockInTimeValue = data_get($todayAttendanceRecord, 'waktu_masuk');
        $clockInAt = null;

        if ($clockInTimeValue instanceof \Illuminate\Support\Carbon) {
            $clockInAt = $clockInTimeValue->copy();
        } elseif (! empty($clockInTimeValue)) {
            $clockInAt = \Illuminate\Support\Carbon::parse((string) $clockInTimeValue);
        }

        $officeCheckIn = old('office_check_in', data_get($globalSettings ?? [], 'office_check_in', data_get($settings ?? [], 'office_check_in', '08:00')));
        $officeCheckOut = old('office_check_out', data_get($globalSettings ?? [], 'office_check_out', data_get($settings ?? [], 'office_check_out', '17:00')));

        $minutesSinceClockIn = $clockInAt ? max(0, $clockInAt->diffInMinutes(now(), false)) : 0;
        $remainingClockOutMinutes = $canAttemptClockOut ? max(0, $clockOutMinimumMinutes - $minutesSinceClockIn) : 0;
        $canClockOut = $canAttemptClockOut && $remainingClockOutMinutes === 0;
        $officeCheckoutAt = \Illuminate\Support\Carbon::parse(now()->toDateString().' '.$officeCheckOut.':00');
        $canRequestEarlyClockOut = $canAttemptClockOut && now()->lt($officeCheckoutAt);

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

        $attendanceRows = collect($attendanceHistory ?? []);

        if ($attendanceRows->isEmpty()) {
            $attendanceRows = \App\Models\Attendance::query()
                ->where('user_id', auth()->id())
                ->latest('tanggal')
                ->limit(6)
                ->get();
        }

        $attendanceRows = $attendanceRows->take(6);

        $adminSummaryCards = [
            [
                'label' => 'Present',
                'value' => data_get($adminSummary ?? [], 'present', data_get($stats ?? [], 'present', 128)),
                'helper' => 'Employees clocked in this month',
                'icon' => 'check',
            ],
            [
                'label' => 'Late',
                'value' => data_get($adminSummary ?? [], 'late', data_get($stats ?? [], 'late', 17)),
                'helper' => 'Late arrivals this month',
                'icon' => 'clock',
            ],
            [
                'label' => 'On Leave',
                'value' => data_get($adminSummary ?? [], 'on_leave', data_get($stats ?? [], 'on_leave', 9)),
                'helper' => 'Approved leave records',
                'icon' => 'calendar',
            ],
            [
                'label' => 'Absent',
                'value' => data_get($adminSummary ?? [], 'absent', data_get($stats ?? [], 'absent', 5)),
                'helper' => 'Unexcused attendance gaps',
                'icon' => 'warning',
            ],
        ];

        $pendingRegistrations = collect($pendingRegistrations ?? [
            [
                'id' => 1,
                'name' => 'Nadia Putri',
                'email' => 'nadia.putri@tiptap.id',
                'department' => 'Finance',
                'registered_at' => now()->subHours(3),
            ],
            [
                'id' => 2,
                'name' => 'Rafi Mahendra',
                'email' => 'rafi.mahendra@tiptap.id',
                'department' => 'Product',
                'registered_at' => now()->subHours(7),
            ],
            [
                'id' => 3,
                'name' => 'Gina Ananta',
                'email' => 'gina.ananta@tiptap.id',
                'department' => 'Operations',
                'registered_at' => now()->subDay(),
            ],
        ]);

        $leaveApprovalRequests = collect($leaveApprovalRequests ?? [
            [
                'id' => 101,
                'employee_name' => 'Aldi Saputra',
                'type' => 'Annual Leave',
                'date_start' => now()->addDays(2)->toDateString(),
                'date_end' => now()->addDays(4)->toDateString(),
                'reason' => 'Family event outside city.',
            ],
            [
                'id' => 102,
                'employee_name' => 'Mira Lestari',
                'type' => 'Sick Leave',
                'date_start' => now()->addDay()->toDateString(),
                'date_end' => now()->addDays(2)->toDateString(),
                'reason' => 'Medical recovery and doctor follow-up.',
            ],
        ]);

        $monthOptions = [
            ['value' => '01', 'label' => 'January'],
            ['value' => '02', 'label' => 'February'],
            ['value' => '03', 'label' => 'March'],
            ['value' => '04', 'label' => 'April'],
            ['value' => '05', 'label' => 'May'],
            ['value' => '06', 'label' => 'June'],
            ['value' => '07', 'label' => 'July'],
            ['value' => '08', 'label' => 'August'],
            ['value' => '09', 'label' => 'September'],
            ['value' => '10', 'label' => 'October'],
            ['value' => '11', 'label' => 'November'],
            ['value' => '12', 'label' => 'December'],
        ];

        $currentYear = (int) now()->format('Y');
        $yearOptions = [$currentYear - 1, $currentYear, $currentYear + 1];
        $selectedMonth = now()->format('m');
        $selectedYear = (string) $currentYear;


    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-navy-primary/70">{{ $isAdminDashboard ? 'Admin Dashboard' : 'Employee Dashboard' }}</p>
                <h2 class="mt-1 font-display text-2xl font-semibold text-navy-primary sm:text-3xl">
                    {{ $isAdminDashboard ? __('Monthly Operations Overview') : __('Welcome back, :name', ['name' => $employeeName]) }}
                </h2>
            </div>
            <p class="text-sm font-medium text-slate-500">{{ now()->format('l, d M Y') }}</p>
        </div>
    </x-slot>

    @if ($isAdminDashboard)
        <div class="space-y-6" x-data="{ activeLeaveId: null }">
            <section class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h3 class="font-display text-xl font-bold text-[#0B4A85]">Payroll Export Utility</h3>
                        <p class="mt-1 text-sm text-slate-500">Generate attendance recap for payroll processing.</p>
                    </div>

                    <div class="grid w-full gap-3 sm:grid-cols-3 lg:w-auto">
                        <label class="text-sm font-medium text-slate-600">
                            <span class="mb-1.5 block">Month</span>
                            <select class="w-full rounded-lg border border-[#0B4A85]/35 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-[#0B4A85] focus:outline-none focus:ring-2 focus:ring-[#0B4A85]/25">
                                @foreach ($monthOptions as $month)
                                    <option value="{{ $month['value'] }}" @selected($month['value'] === $selectedMonth)>{{ $month['label'] }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="text-sm font-medium text-slate-600">
                            <span class="mb-1.5 block">Year</span>
                            <select class="w-full rounded-lg border border-[#0B4A85]/35 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-[#0B4A85] focus:outline-none focus:ring-2 focus:ring-[#0B4A85]/25">
                                @foreach ($yearOptions as $year)
                                    <option value="{{ $year }}" @selected((string) $year === $selectedYear)>{{ $year }}</option>
                                @endforeach
                            </select>
                        </label>

                        <button type="button" class="mt-auto inline-flex items-center justify-center gap-2 rounded-lg bg-[#0B4A85] px-5 py-2.5 font-medium text-white transition-all hover:shadow-lg hover:bg-[#063157]">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M3 14.5A1.5 1.5 0 014.5 13h1a1 1 0 000-2h-1A3.5 3.5 0 001 14.5v1A3.5 3.5 0 004.5 19h11a3.5 3.5 0 003.5-3.5v-1a3.5 3.5 0 00-3.5-3.5h-1a1 1 0 000 2h1a1.5 1.5 0 011.5 1.5v1a1.5 1.5 0 01-1.5 1.5h-11A1.5 1.5 0 013 15.5v-1z" />
                                <path d="M9 1.5a1 1 0 112 0V10l2.3-2.3a1 1 0 111.4 1.4l-4 4a1 1 0 01-1.4 0l-4-4a1 1 0 011.4-1.4L9 10V1.5z" />
                            </svg>
                            Export to Payroll (CSV/Excel)
                        </button>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 bg-slate-50 rounded-2xl p-2">
                @foreach ($adminSummaryCards as $card)
                    <article class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-[#0B4A85] p-6">
                        <div class="absolute right-4 top-4 rounded-full bg-[#0B4A85]/10 p-3 text-[#0B4A85]/35">
                            @if ($card['icon'] === 'check')
                                <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3.75-3.75a1 1 0 011.414-1.414l3.043 3.043 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @elseif ($card['icon'] === 'clock')
                                <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4c0 .265.105.52.293.707l2.5 2.5a1 1 0 001.414-1.414L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            @elseif ($card['icon'] === 'calendar')
                                <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 012 0v1h4V2a1 1 0 112 0v1h1.5A2.5 2.5 0 0118 5.5v9A2.5 2.5 0 0115.5 17h-11A2.5 2.5 0 012 14.5v-9A2.5 2.5 0 014.5 3H6V2zm10 5H4v7.5c0 .276.224.5.5.5h11a.5.5 0 00.5-.5V7z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.486 0l6.516 11.584c.75 1.334-.213 2.992-1.742 2.992H3.483c-1.53 0-2.492-1.658-1.742-2.992L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-6a1 1 0 00-1 1v3a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>

                        <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">{{ $card['label'] }}</p>
                        <p class="mt-3 text-4xl font-extrabold text-[#0B4A85]">{{ $card['value'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $card['helper'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <header class="bg-[#0B4A85] text-white">
                    <div class="flex flex-col gap-1 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="font-display text-lg font-semibold">Pending Registrations</h3>
                        <p class="text-sm text-white/85">Review new employee account requests</p>
                    </div>
                </header>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-100 text-slate-700">
                            <tr>
                                <th class="p-4 text-left font-semibold">Name</th>
                                <th class="p-4 text-left font-semibold">Email</th>
                                <th class="p-4 text-left font-semibold">Department</th>
                                <th class="p-4 text-left font-semibold">Registered</th>
                                <th class="p-4 text-left font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingRegistrations as $user)
                                @php
                                    $registeredAtRaw = data_get($user, 'registered_at');
                                    $registeredAtDisplay = $registeredAtRaw ? \Illuminate\Support\Carbon::parse($registeredAtRaw)->format('d M Y H:i') : '-';
                                @endphp
                                <tr class="border-b border-gray-200 hover:bg-slate-50">
                                    <td class="p-4 font-medium text-slate-800">{{ data_get($user, 'name', '-') }}</td>
                                    <td class="p-4 text-slate-600">{{ data_get($user, 'email', '-') }}</td>
                                    <td class="p-4 text-slate-600">{{ data_get($user, 'department', '-') }}</td>
                                    <td class="p-4 text-slate-600">{{ $registeredAtDisplay }}</td>
                                    <td class="p-4">
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" class="bg-[#0B4A85] text-white px-4 py-2 rounded shadow-sm hover:bg-[#063157] transition">Approve</button>
                                            <button type="button" class="border border-[#0B4A85] text-[#0B4A85] px-4 py-2 rounded hover:bg-slate-100 transition">Reject</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-500">No pending registrations right now.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
                <article class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="font-display text-xl font-semibold text-[#0B4A85]">Leave Request Approval</h3>
                            <p class="mt-1 text-sm text-slate-500">Review and decide employee leave submissions.</p>
                        </div>
                        <span class="rounded-full bg-[#0B4A85]/10 px-3 py-1 text-xs font-semibold text-[#0B4A85]">{{ $leaveApprovalRequests->count() }} Requests</span>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse ($leaveApprovalRequests as $request)
                            @php
                                $requestStart = data_get($request, 'date_start');
                                $requestEnd = data_get($request, 'date_end');
                                $requestDateRange = ($requestStart && $requestEnd)
                                    ? \Illuminate\Support\Carbon::parse($requestStart)->format('d M Y').' - '.\Illuminate\Support\Carbon::parse($requestEnd)->format('d M Y')
                                    : '-';
                            @endphp
                            <div class="rounded-lg border border-slate-200 p-4 hover:border-[#0B4A85]/40 transition-colors">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ data_get($request, 'employee_name', '-') }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ data_get($request, 'type', '-') }} &middot; {{ $requestDateRange }}</p>
                                        <p class="mt-2 text-sm text-slate-600">{{ data_get($request, 'reason', '-') }}</p>
                                    </div>
                                    <button type="button" @click="activeLeaveId = {{ data_get($request, 'id') }}" class="bg-[#0B4A85] text-white px-4 py-2 rounded shadow-sm hover:bg-[#063157] transition">Review</button>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-slate-300 p-6 text-center text-slate-500">No leave requests pending review.</div>
                        @endforelse
                    </div>
                </article>

                <article class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-display text-xl font-semibold text-[#0B4A85]">Global Settings</h3>
                    <p class="mt-1 text-sm text-slate-500">Configure office-wide attendance timing.</p>

                    <form class="mt-6 space-y-5" x-data="{ checkIn: '{{ $officeCheckIn }}', checkOut: '{{ $officeCheckOut }}' }">
                        <div>
                            <label for="office_check_in" class="mb-1.5 block text-sm font-medium text-slate-600">Office Check-In Time</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-dark-slate/70">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4c0 .265.105.52.293.707l2.5 2.5a1 1 0 001.414-1.414L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <input id="office_check_in" type="time" x-model="checkIn" class="w-full px-4 py-3 pl-10 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-[#0B4A85] focus:border-[#0B4A85] outline-none" />
                            </div>
                        </div>

                        <div>
                            <label for="office_check_out" class="mb-1.5 block text-sm font-medium text-slate-600">Office Check-Out Time</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-dark-slate/70">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4c0 .265.105.52.293.707l2.5 2.5a1 1 0 001.414-1.414L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <input id="office_check_out" type="time" x-model="checkOut" class="w-full px-4 py-3 pl-10 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-[#0B4A85] focus:border-[#0B4A85] outline-none" />
                            </div>
                        </div>

                        <button type="button" class="w-full rounded-lg bg-[#0B4A85] px-4 py-3 font-semibold text-white transition hover:bg-[#063157]">Save Changes</button>
                    </form>
                </article>
            </section>

            <div x-cloak x-show="activeLeaveId !== null" class="fixed inset-0 z-50" aria-modal="true" role="dialog">
                <div class="absolute inset-0 bg-slate-900/40" @click="activeLeaveId = null"></div>
                <div class="absolute inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl">
                    <div class="flex h-full flex-col">
                        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                            <h3 class="font-display text-lg font-semibold text-[#0B4A85]">Review Leave Request</h3>
                            <button type="button" @click="activeLeaveId = null" class="rounded-md p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto px-6 py-5">
                            @foreach ($leaveApprovalRequests as $request)
                                @php
                                    $modalStart = data_get($request, 'date_start');
                                    $modalEnd = data_get($request, 'date_end');
                                    $modalDateRange = ($modalStart && $modalEnd)
                                        ? \Illuminate\Support\Carbon::parse($modalStart)->format('d M Y').' - '.\Illuminate\Support\Carbon::parse($modalEnd)->format('d M Y')
                                        : '-';
                                @endphp
                                <div x-show="activeLeaveId === {{ data_get($request, 'id') }}" class="space-y-4">
                                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-sm font-semibold text-slate-700">Employee</p>
                                        <p class="mt-1 text-base text-slate-900">{{ data_get($request, 'employee_name', '-') }}</p>
                                    </div>

                                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-sm font-semibold text-slate-700">Leave Dates</p>
                                        <p class="mt-1 text-base text-slate-900">{{ $modalDateRange }}</p>
                                    </div>

                                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-sm font-semibold text-slate-700">Reason</p>
                                        <p class="mt-1 text-base text-slate-900">{{ data_get($request, 'reason', '-') }}</p>
                                    </div>

                                    <div>
                                        <label for="admin_note_{{ data_get($request, 'id') }}" class="mb-1.5 block text-sm font-medium text-slate-600">Optional Notes</label>
                                        <textarea id="admin_note_{{ data_get($request, 'id') }}" rows="5" placeholder="Write optional feedback for employee..." class="focus:ring-2 focus:ring-[#0B4A85] focus:border-[#0B4A85] outline-none rounded-md border-gray-300 w-full"></textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-slate-200 px-6 py-4">
                            <div class="flex flex-wrap justify-end gap-2">
                                <button type="button" class="border border-[#0B4A85] text-[#0B4A85] px-4 py-2 rounded hover:bg-slate-100 transition">Reject Request</button>
                                <button type="button" class="bg-[#0B4A85] text-white px-4 py-2 rounded shadow-sm hover:bg-[#063157] transition">Approve Request</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="space-y-6">
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('attendance'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                    {{ $errors->first('attendance') }}
                </div>
            @endif

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
                            <form method="POST" action="{{ url('/clock-in') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-2xl border border-[#0B4A85] px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] transition-all hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-[#0B4A85]/35 focus:ring-offset-2"
                                    style="background-color: #0B4A85; color: #FFFFFF;"
                                >
                                    Clock In
                                </button>
                            </form>
                        @elseif ($canAttemptClockOut)
                            <form
                                method="POST"
                                action="{{ url('/clock-out') }}"
                                x-data="{ loading: false, earlyConfirmed: false }"
                                @submit.prevent="
                                    if (loading || {{ ($canClockOut || $canRequestEarlyClockOut) ? 'false' : 'true' }}) return;
                                    const [endHour, endMinute] = '{{ $officeCheckOut }}'.split(':').map(Number);
                                    const nowTime = new Date();
                                    const cutoffTime = new Date();
                                    cutoffTime.setHours(endHour, endMinute || 0, 0, 0);
                                    if (nowTime < cutoffTime && !earlyConfirmed) {
                                        if (!window.confirm('are you sure want to leave early?')) return;
                                        earlyConfirmed = true;
                                    }
                                    loading = true;
                                    $el.submit();
                                "
                            >
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="confirm_early_leave" :value="earlyConfirmed ? 1 : 0">
                                <button
                                    type="submit"
                                    :disabled="loading || {{ ($canClockOut || $canRequestEarlyClockOut) ? 'false' : 'true' }}"
                                    class="inline-flex w-full items-center justify-center rounded-2xl border px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white transition-all focus:outline-none focus:ring-2 focus:ring-navy-primary/30 focus:ring-offset-2 {{ ($canClockOut || $canRequestEarlyClockOut) ? 'border-[#0B4A85] hover:scale-[1.02]' : 'cursor-not-allowed border-slate-400 bg-slate-400' }}"
                                    style="{{ ($canClockOut || $canRequestEarlyClockOut) ? 'background-color: #0B4A85; color: #FFFFFF;' : '' }}"
                                >
                                    <span x-show="!loading" class="inline-block">
                                        {{ $canRequestEarlyClockOut ? 'Request Early Clock Out' : ($canClockOut ? 'Clock Out' : 'Clock Out (Locked)') }}
                                    </span>
                                    <span x-cloak x-show="loading" class="inline-flex items-center gap-2" style="display: none;">
                                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.35" stroke-width="4" />
                                            <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                                        </svg>
                                        {{ $canRequestEarlyClockOut ? 'Submitting Request' : 'Processing' }}
                                    </span>
                                </button>
                            </form>
                        @elseif ($statusKey === 'pending')
                            <button
                                type="button"
                                disabled
                                class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-2xl border border-amber-300 bg-amber-100 px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-amber-800"
                            >
                                Waiting Admin Approval
                            </button>
                        @else
                            <button
                                type="button"
                                disabled
                                class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-2xl border border-slate-400 bg-slate-400 px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white"
                            >
                                Attendance Completed
                            </button>
                        @endif
                    </div>

                    <p class="mt-3 text-xs text-slate-500">
                        @if ($statusKey === 'pending')
                            Your early clock-out request has been submitted and is waiting for admin decision.
                        @elseif ($canRequestEarlyClockOut)
                            You can submit an early clock-out request now. Admin approval is required before leaving.
                        @elseif ($canAttemptClockOut && ! $canClockOut)
                            Clock Out becomes available after 6 hours from your clock-in. Remaining: {{ $remainingClockOutMinutes }} minutes.
                        @else
                            Button state is rendered dynamically using Blade status checks.
                        @endif
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

        </div>
    @endif
</x-app-layout>
