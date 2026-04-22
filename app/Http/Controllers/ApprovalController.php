<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function index(Request $request): View
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

        $pendingUsers = User::query()
            ->where('is_approved', false)
            ->orderBy('created_at')
            ->get();

        return view('approval.index', [
            'pendingUsers' => $pendingUsers,
            'settings' => $settings,
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

    private function ensureAdmin(Request $request): void
    {
        $role = strtolower((string) $request->user()?->role);

        $isAdmin = in_array($role, ['admin', 'administrator', 'superadmin', 'super admin', 'super_admin'], true);

        abort_unless($isAdmin, 403);
    }
}
