<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AlphaController extends Controller
{
    /**
     * Tester account definitions.
     * Each group has a prefix, count, and behavior type.
     */
    private const TESTER_GROUPS = [
        ['prefix' => 'Atester', 'count' => 5, 'type' => 'clockin'],
        ['prefix' => 'Btester', 'count' => 5, 'type' => 'clockout'],
        ['prefix' => 'Ctester', 'count' => 5, 'type' => 'early_clockout'],
        ['prefix' => 'Dtester', 'count' => 2, 'type' => 'late'],
        ['prefix' => 'Etester', 'count' => 3, 'type' => 'no_clockin'],
    ];

    /**
     * Display the alpha system config page (settings + reset attendance).
     */
    public function settings(Request $request): View
    {
        $this->ensureAlpha($request);

        $settings = Setting::query()->firstOrCreate(
            ['id' => 1],
            [
                'jam_masuk_kantor' => '08:00',
                'jam_mulai_pulang' => '17:00',
                'company_email_domain' => (string) config('app.company_email_domain', 'tiptap.id'),
            ]
        );

        return view('alpha.settings', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update company settings (same logic as ApprovalController).
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $this->ensureAlpha($request);

        $normalizedDomain = strtolower(trim((string) $request->input('company_email_domain')));

        if (str_contains($normalizedDomain, '@')) {
            [, $normalizedDomain] = explode('@', $normalizedDomain, 2);
        }

        $request->merge([
            'company_email_domain' => $normalizedDomain,
        ]);

        $validated = $request->validate([
            'company_email_domain' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+([.-][a-z0-9]+)*\.[a-z]{2,}$/'],
            'jam_masuk_kantor' => ['required', 'date_format:H:i'],
            'jam_mulai_pulang' => ['required', 'date_format:H:i'],
        ], [
            'company_email_domain.regex' => 'Use a valid company email domain like tiptap.id.',
        ]);

        $settings = Setting::query()->firstOrCreate(['id' => 1]);
        $settings->update($validated);

        return back()->with('status', 'Company settings updated successfully.');
    }

    /**
     * Reset all attendance records for today so attendance can be tested again.
     */
    public function resetAttendance(Request $request): RedirectResponse
    {
        $this->ensureAlpha($request);

        $deletedCount = Attendance::query()
            ->whereDate('tanggal', now()->toDateString())
            ->delete();

        return back()->with('status', "Attendance reset successful! {$deletedCount} record(s) cleared for today. All users can now clock in again.");
    }

    /**
     * Display the Dev Tools page.
     */
    public function devTools(Request $request): View
    {
        $this->ensureAlpha($request);

        // Gather tester accounts info
        $testerAccounts = [];
        foreach (self::TESTER_GROUPS as $group) {
            for ($i = 1; $i <= $group['count']; $i++) {
                $name = $group['prefix'] . $i;
                $email = strtolower($name) . '@test.dev';
                $user = User::where('email', $email)->first();

                $attendanceCount = 0;
                if ($user) {
                    $attendanceCount = Attendance::where('user_id', $user->id)->count();
                }

                $testerAccounts[] = [
                    'name' => $name,
                    'email' => $email,
                    'type' => $group['type'],
                    'exists' => $user !== null,
                    'user_id' => $user?->id,
                    'attendance_days' => $attendanceCount,
                ];
            }
        }

        $typeLabels = [
            'clockin' => ['label' => 'Clock In (Present)', 'color' => 'bg-emerald-100 text-emerald-700'],
            'clockout' => ['label' => 'Checked Out', 'color' => 'bg-slate-100 text-slate-600'],
            'early_clockout' => ['label' => 'Early Checkout', 'color' => 'bg-orange-100 text-orange-700'],
            'late' => ['label' => 'Late', 'color' => 'bg-rose-100 text-rose-700'],
            'no_clockin' => ['label' => 'No Clock In', 'color' => 'bg-amber-100 text-amber-700'],
        ];

        return view('alpha.devtools', [
            'testerAccounts' => $testerAccounts,
            'typeLabels' => $typeLabels,
        ]);
    }

    /**
     * Provision tester accounts and generate N days of attendance data.
     */
    public function addDays(Request $request): RedirectResponse
    {
        $this->ensureAlpha($request);

        $validated = $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $days = (int) $validated['days'];
        $settings = Setting::first();
        $clockInTime = $settings?->jam_masuk_kantor ?? '08:00:00';
        $clockOutTime = $settings?->jam_mulai_pulang ?? '17:00:00';

        $createdUsers = 0;
        $createdRecords = 0;

        foreach (self::TESTER_GROUPS as $group) {
            for ($i = 1; $i <= $group['count']; $i++) {
                $name = $group['prefix'] . $i;
                $email = strtolower($name) . '@test.dev';

                // Create user if not exists
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => Hash::make('tester123'),
                        'role' => 'user',
                        'divisi' => 'Testing',
                        'status_pekerjaan' => 'aktif',
                        'is_approved' => true,
                    ]
                );

                if ($user->wasRecentlyCreated) {
                    $createdUsers++;
                }

                // Skip attendance generation for "no_clockin" type
                if ($group['type'] === 'no_clockin') {
                    continue;
                }

                // Generate attendance for each day
                for ($d = 0; $d < $days; $d++) {
                    $date = Carbon::today()->subDays($d)->toDateString();

                    // Skip if record already exists for this day
                    $exists = Attendance::where('user_id', $user->id)
                        ->whereDate('tanggal', $date)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    $record = $this->generateAttendanceRecord($group['type'], $date, $clockInTime, $clockOutTime, $user->id);
                    Attendance::create($record);
                    $createdRecords++;
                }
            }
        }

        return back()->with('status', "Dev Tools: {$createdUsers} tester account(s) created, {$createdRecords} attendance record(s) generated for {$days} day(s).");
    }

    /**
     * Remove all tester accounts and their attendance data.
     */
    public function resetTesters(Request $request): RedirectResponse
    {
        $this->ensureAlpha($request);

        $testerEmails = [];
        foreach (self::TESTER_GROUPS as $group) {
            for ($i = 1; $i <= $group['count']; $i++) {
                $testerEmails[] = strtolower($group['prefix'] . $i) . '@test.dev';
            }
        }

        $testerUsers = User::whereIn('email', $testerEmails)->get();
        $testerIds = $testerUsers->pluck('id')->toArray();

        $deletedAttendance = Attendance::whereIn('user_id', $testerIds)->delete();
        $deletedUsers = User::whereIn('id', $testerIds)->delete();

        return back()->with('status', "Dev Tools reset: {$deletedUsers} tester account(s) and {$deletedAttendance} attendance record(s) deleted.");
    }

    /**
     * Generate a single attendance record based on the tester type.
     */
    private function generateAttendanceRecord(string $type, string $date, string $clockInTime, string $clockOutTime, int $userId): array
    {
        // Add some randomness to times (±15 min)
        $clockInCarbon = Carbon::parse($clockInTime);
        $clockOutCarbon = Carbon::parse($clockOutTime);

        switch ($type) {
            case 'clockin':
                // Clocked in on time, hasn't clocked out yet (only for today)
                $arrivalOffset = rand(-15, 0); // early or on time
                $arrival = $clockInCarbon->copy()->addMinutes($arrivalOffset);
                return [
                    'user_id' => $userId,
                    'tanggal' => $date,
                    'waktu_masuk' => $arrival->format('H:i:s'),
                    'waktu_keluar' => ($date === Carbon::today()->toDateString()) ? null : $clockOutCarbon->copy()->addMinutes(rand(0, 30))->format('H:i:s'),
                    'status' => 'Hadir',
                ];

            case 'clockout':
                // Clocked in on time, clocked out normally
                $arrivalOffset = rand(-10, 0);
                $arrival = $clockInCarbon->copy()->addMinutes($arrivalOffset);
                $departure = $clockOutCarbon->copy()->addMinutes(rand(0, 45));
                return [
                    'user_id' => $userId,
                    'tanggal' => $date,
                    'waktu_masuk' => $arrival->format('H:i:s'),
                    'waktu_keluar' => $departure->format('H:i:s'),
                    'status' => 'Hadir',
                ];

            case 'early_clockout':
                // Clocked in on time, clocked out early
                $arrivalOffset = rand(-5, 0);
                $arrival = $clockInCarbon->copy()->addMinutes($arrivalOffset);
                $earlyDeparture = $clockOutCarbon->copy()->subMinutes(rand(60, 180));
                return [
                    'user_id' => $userId,
                    'tanggal' => $date,
                    'waktu_masuk' => $arrival->format('H:i:s'),
                    'waktu_keluar' => $earlyDeparture->format('H:i:s'),
                    'status' => 'Pulang Cepat',
                    'early_checkout_status' => 'approved',
                    'early_checkout_requested_at' => $earlyDeparture->format('Y-m-d H:i:s'),
                    'early_checkout_reviewed_at' => $earlyDeparture->copy()->addMinutes(rand(5, 30))->format('Y-m-d H:i:s'),
                ];

            case 'late':
                // Clocked in late
                $lateOffset = rand(15, 120); // 15 min to 2 hours late
                $arrival = $clockInCarbon->copy()->addMinutes($lateOffset);
                $departure = $clockOutCarbon->copy()->addMinutes(rand(0, 30));
                return [
                    'user_id' => $userId,
                    'tanggal' => $date,
                    'waktu_masuk' => $arrival->format('H:i:s'),
                    'waktu_keluar' => $departure->format('H:i:s'),
                    'status' => 'Terlambat',
                ];

            default:
                return [
                    'user_id' => $userId,
                    'tanggal' => $date,
                    'waktu_masuk' => $clockInCarbon->format('H:i:s'),
                    'status' => 'Hadir',
                ];
        }
    }

    /**
     * Ensure the current user has the "alpha" role.
     */
    private function ensureAlpha(Request $request): void
    {
        $role = strtolower((string) $request->user()?->role);

        abort_unless($role === 'alpha', 403);
    }
}

