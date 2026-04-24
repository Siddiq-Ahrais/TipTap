<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlphaController extends Controller
{
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
     * This effectively "skips" to a new day by deleting today's attendance records.
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
     * Ensure the current user has the "alpha" role.
     */
    private function ensureAlpha(Request $request): void
    {
        $role = strtolower((string) $request->user()?->role);

        abort_unless($role === 'alpha', 403);
    }
}
