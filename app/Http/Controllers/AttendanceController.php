<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function clockIn(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $now = Carbon::now();
        $today = $now->toDateString();

        $alreadyClockedIn = Attendance::query()
            ->where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($alreadyClockedIn) {
            return response()->json([
                'message' => 'User has already clocked in today.',
            ], 422);
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

        return response()->json([
            'message' => 'Clock-in successful.',
            'data' => $attendance,
        ], 201);
    }

    public function clockOut(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $now = Carbon::now();
        $today = $now->toDateString();

        $attendance = Attendance::query()
            ->where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (! $attendance) {
            return response()->json([
                'message' => 'No active clock-in record found for today.',
            ], 404);
        }

        if ($attendance->waktu_keluar) {
            return response()->json([
                'message' => 'User has already clocked out.',
            ], 400);
        }

        $attendance->update([
            'waktu_keluar' => $now->format('H:i:s'),
        ]);

        return response()->json([
            'message' => 'Clock-out successful.',
            'data' => $attendance->fresh(),
        ]);
    }
}
