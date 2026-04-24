<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        $clockInStats = $this->getTodayClockInStats();

        $settings = Setting::query()->firstOrCreate(
            ['id' => 1],
            [
                'jam_masuk_kantor' => '08:00',
                'jam_mulai_pulang' => '17:00',
                'company_email_domain' => (string) config('app.company_email_domain', 'tiptap.id'),
            ]
        );

        $pendingUsersCount = User::query()
            ->where('is_approved', false)
            ->count();

        $pendingEarlyCheckoutCount = Attendance::query()
            ->where('early_checkout_status', 'pending')
            ->count();

        $pendingLeaveCount = Leave::query()
            ->whereRaw('LOWER(status_approval) = ?', ['pending'])
            ->count();

        $todayAttendances = Attendance::query()
            ->with('user:id,name,email,role,divisi')
            ->whereDate('tanggal', now()->toDateString())
            ->orderBy('waktu_masuk', 'asc')
            ->get();

        return view('approval.index', [
            'pendingUsersCount' => $pendingUsersCount,
            'pendingEarlyCheckoutCount' => $pendingEarlyCheckoutCount,
            'pendingLeaveCount' => $pendingLeaveCount,
            'clockedInTodayCount' => $clockInStats['clockedInTodayCount'],
            'totalEmployeeCount' => $clockInStats['totalEmployeeCount'],
            'settings' => $settings,
            'todayAttendances' => $todayAttendances,
        ]);
    }

    public function leaves(Request $request): View
    {
        $this->ensureAdmin($request);

        $clockInStats = $this->getTodayClockInStats();

        $pendingLeaves = Leave::query()
            ->with('user:id,name,email')
            ->whereRaw('LOWER(status_approval) = ?', ['pending'])
            ->orderByDesc('created_at')
            ->get();

        return view('approval.leaves', [
            'pendingLeaves' => $pendingLeaves,
            'clockedInTodayCount' => $clockInStats['clockedInTodayCount'],
            'totalEmployeeCount' => $clockInStats['totalEmployeeCount'],
        ]);
    }

    public function registrations(Request $request): View
    {
        $this->ensureAdmin($request);

        $pendingUsers = User::query()
            ->where('is_approved', false)
            ->orderBy('created_at')
            ->get();

        return view('approval.registrations', [
            'pendingUsers' => $pendingUsers,
        ]);
    }

    public function settings(Request $request): View
    {
        $this->ensureAdmin($request);

        $settings = Setting::query()->firstOrCreate(
            ['id' => 1],
            [
                'jam_masuk_kantor' => '08:00',
                'jam_mulai_pulang' => '17:00',
                'company_email_domain' => (string) config('app.company_email_domain', 'tiptap.id'),
            ]
        );

        return view('approval.settings', [
            'settings' => $settings,
        ]);
    }

    public function earlyCheckouts(Request $request): View
    {
        $this->ensureAdmin($request);

        $pendingRequests = Attendance::query()
            ->with('user:id,name,email')
            ->where('early_checkout_status', 'pending')
            ->orderByDesc('early_checkout_requested_at')
            ->get();

        return view('approval.early-checkouts', [
            'pendingRequests' => $pendingRequests,
        ]);
    }

    public function approveUser(Request $request, User $user): RedirectResponse
    {
        $this->ensureAdmin($request);

        if ($user->is_approved) {
            return back()->with('status', 'User is already approved.');
        }

        $user->forceFill([
            'is_approved' => true,
            'role' => $user->role ?: 'user',
        ])->save();

        return back()->with('status', 'Employee account approved successfully.');
    }

    public function rejectUser(Request $request, User $user): RedirectResponse
    {
        $this->ensureAdmin($request);

        if ((int) $request->user()->id === (int) $user->id) {
            return back()->withErrors(['approval' => 'You cannot reject your own account.']);
        }

        if ($user->is_approved) {
            return back()->withErrors(['approval' => 'Approved users cannot be rejected from this queue.']);
        }

        $user->delete();

        return back()->with('status', 'Employee registration request rejected.');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $this->ensureAdmin($request);

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

    public function approveEarlyCheckout(Request $request, Attendance $attendance): RedirectResponse
    {
        $this->ensureAdmin($request);

        if ($attendance->early_checkout_status !== 'pending' || ! $attendance->early_checkout_requested_at) {
            return back()->withErrors(['approval' => 'This early checkout request is no longer pending.']);
        }

        $approvedAt = Carbon::parse($attendance->early_checkout_requested_at);

        $attendance->update([
            'waktu_keluar' => $approvedAt->format('H:i:s'),
            'status' => 'Pulang Cepat',
            'early_checkout_status' => 'approved',
            'early_checkout_reviewed_at' => now(),
            'early_checkout_reviewed_by' => $request->user()->id,
            'early_checkout_note' => null,
        ]);

        return back()->with('status', 'Early checkout request approved successfully.');
    }

    public function rejectEarlyCheckout(Request $request, Attendance $attendance): RedirectResponse
    {
        $this->ensureAdmin($request);

        if ($attendance->early_checkout_status !== 'pending') {
            return back()->withErrors(['approval' => 'This early checkout request is no longer pending.']);
        }

        $attendance->update([
            'early_checkout_status' => 'rejected',
            'early_checkout_reviewed_at' => now(),
            'early_checkout_reviewed_by' => $request->user()->id,
            'early_checkout_note' => (string) $request->input('note', ''),
        ]);

        return back()->with('status', 'Early checkout request rejected.');
    }

    public function approveLeave(Request $request, Leave $leave): RedirectResponse
    {
        $this->ensureAdmin($request);

        if (strtolower((string) $leave->status_approval) !== 'pending') {
            return back()->withErrors(['approval' => 'This leave request is no longer pending.']);
        }

        $leave->update([
            'status_approval' => 'Approved',
        ]);

        return back()->with('status', 'Leave request approved successfully.');
    }

    public function rejectLeave(Request $request, Leave $leave): RedirectResponse
    {
        $this->ensureAdmin($request);

        if (strtolower((string) $leave->status_approval) !== 'pending') {
            return back()->withErrors(['approval' => 'This leave request is no longer pending.']);
        }

        $leave->update([
            'status_approval' => 'Rejected',
        ]);

        return back()->with('status', 'Leave request rejected.');
    }

    private function ensureAdmin(Request $request): void
    {
        $role = strtolower((string) $request->user()?->role);

        $isAdmin = in_array($role, ['admin', 'administrator', 'superadmin', 'super admin', 'super_admin'], true);

        abort_unless($isAdmin, 403);
    }

    private function getTodayClockInStats(): array
    {
        $adminRoles = ['admin', 'administrator', 'superadmin', 'super admin', 'super_admin'];

        $totalEmployeeCount = User::query()
            ->where('is_approved', true)
            ->whereRaw('LOWER(COALESCE(role, ?)) NOT IN (?, ?, ?, ?, ?)', [
                'user',
                $adminRoles[0],
                $adminRoles[1],
                $adminRoles[2],
                $adminRoles[3],
                $adminRoles[4],
            ])
            ->count();

        $clockedInTodayCount = Attendance::query()
            ->whereDate('tanggal', now()->toDateString())
            ->whereHas('user', function ($query) use ($adminRoles): void {
                $query
                    ->where('is_approved', true)
                    ->whereRaw('LOWER(COALESCE(role, ?)) NOT IN (?, ?, ?, ?, ?)', [
                        'user',
                        $adminRoles[0],
                        $adminRoles[1],
                        $adminRoles[2],
                        $adminRoles[3],
                        $adminRoles[4],
                    ]);
            })
            ->distinct('user_id')
            ->count('user_id');

        return [
            'clockedInTodayCount' => $clockedInTodayCount,
            'totalEmployeeCount' => $totalEmployeeCount,
        ];
    }
}
