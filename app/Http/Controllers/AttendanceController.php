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

        if ($attendance->early_checkout_status === 'pending') {
            return $this->errorResponse($request, 'Your early checkout request is already waiting for admin approval.', 422);
        }

        if ($attendance->early_checkout_status === 'rejected') {
            return $this->errorResponse($request, 'Your early checkout request was rejected by admin.', 422);
        }

        if ($attendance->early_checkout_status === 'approved') {
            return $this->errorResponse($request, 'Your early checkout request has already been approved.', 422);
        }

        $officeCheckoutTime = Setting::query()->value('jam_mulai_pulang') ?? '17:00:00';
        $officeCheckoutAt = Carbon::parse($today.' '.$officeCheckoutTime);
        $confirmedEarlyLeave = filter_var($request->input('confirm_early_leave', false), FILTER_VALIDATE_BOOL);

        if ($now->lt($officeCheckoutAt)) {
            if (! $confirmedEarlyLeave) {
                return $this->errorResponse($request, 'Are you sure you want to leave early?', 422);
            }

            $attendance->update([
                'early_checkout_status' => 'pending',
                'early_checkout_requested_at' => $now,
                'early_checkout_reviewed_at' => null,
                'early_checkout_reviewed_by' => null,
                'early_checkout_note' => null,
            ]);

            return $this->successResponse($request, 'Early checkout request submitted. Please wait for admin approval.', 202, $attendance->fresh());
        }

        $clockInValue = $attendance->waktu_masuk;

        $clockInAt = $clockInValue instanceof Carbon
            ? $clockInValue->copy()
            : Carbon::parse((string) $clockInValue);

        $minutesWorked = max(0, $clockInAt->diffInMinutes($now, false));

        if ($minutesWorked < 360) {
            $remainingMinutes = 360 - $minutesWorked;

            return $this->errorResponse(
                $request,
                'Clock-out is available only after 6 hours from clock-in. Remaining time: '.$remainingMinutes.' minutes.',
                422
            );
        }

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
