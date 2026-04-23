<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function clockIn(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return $this->errorResponse($request, 'Unauthenticated.', 401);
        }

        $now = Carbon::now();
        $today = $now->toDateString();

        $alreadyClockedIn = Attendance::query()
            ->where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($alreadyClockedIn) {
            return $this->errorResponse($request, 'User has already clocked in today.', 422);
        }

        $officeStartTime = Setting::query()->value('jam_masuk_kantor') ?? '08:00:00';

        $status = Carbon::parse($now->format('H:i:s'))->gt(Carbon::parse($officeStartTime))
            ? 'Terlambat'
            : 'Hadir';

        $attendance = Attendance::query()->create([
            'user_id' => $user->id,
            'tanggal' => $today,
            'waktu_masuk' => $now->format('H:i:s'),
            'status' => $status,
        ]);

        return $this->successResponse($request, 'Clock-in successful.', 201, $attendance);
    }

    public function clockOut(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return $this->errorResponse($request, 'Unauthenticated.', 401);
        }

        $now = Carbon::now();
        $today = $now->toDateString();

        $attendance = Attendance::query()
            ->where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (! $attendance) {
            return $this->errorResponse($request, 'No active clock-in record found for today.', 404);
        }

        if ($attendance->waktu_keluar) {
            return $this->errorResponse($request, 'User has already clocked out.', 400);
        }

        $role = strtolower((string) $user->role);
        $isAdmin = in_array($role, ['admin', 'administrator', 'superadmin', 'super admin', 'super_admin'], true);

        if ($isAdmin) {
            $attendance->update([
                'waktu_keluar' => $now->format('H:i:s'),
                'early_checkout_status' => null,
                'early_checkout_requested_at' => null,
                'early_checkout_reviewed_at' => null,
                'early_checkout_reviewed_by' => null,
                'early_checkout_note' => null,
            ]);

            return $this->successResponse($request, 'Clock-out successful.', 200, $attendance->fresh());
        }

        if ($attendance->early_checkout_status === 'pending') {
            return $this->errorResponse($request, 'Your clock-out request is already waiting for admin approval.', 422);
        }

        if ($attendance->early_checkout_status === 'approved') {
            return $this->errorResponse($request, 'Your clock-out request has already been approved.', 422);
        }

        $attendance->update([
            'early_checkout_status' => 'pending',
            'early_checkout_requested_at' => $now,
            'early_checkout_reviewed_at' => null,
            'early_checkout_reviewed_by' => null,
            'early_checkout_note' => null,
        ]);

        return $this->successResponse($request, 'Clock-out request submitted. Please wait for admin approval.', 202, $attendance->fresh());
    }

    private function successResponse(Request $request, string $message, int $statusCode = 200, mixed $data = null): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        }

        return back()->with('status', $message);
    }

    private function errorResponse(Request $request, string $message, int $statusCode): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ], $statusCode);
        }

        return back()->withErrors(['attendance' => $message]);
    }
}
