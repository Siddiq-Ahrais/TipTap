<?php

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (): void {
    $today = now()->toDateString();
    $timestamp = now();

    $activeUserIds = User::query()
        ->where('status_pekerjaan', 'aktif')
        ->pluck('id');

    if ($activeUserIds->isEmpty()) {
        return;
    }

    $approvedLeaveUserIds = Leave::query()
        ->where('status_approval', 'approved')
        ->whereDate('tanggal_mulai', '<=', $today)
        ->whereDate('tanggal_selesai', '>=', $today)
        ->pluck('user_id');

    $attendanceUserIds = Attendance::query()
        ->whereDate('tanggal', $today)
        ->pluck('user_id');

    $excludedUserIds = $approvedLeaveUserIds
        ->merge($attendanceUserIds)
        ->unique();

    $alphaUserIds = $activeUserIds
        ->diff($excludedUserIds)
        ->values();

    if ($alphaUserIds->isEmpty()) {
        return;
    }

    $rows = $alphaUserIds
        ->map(fn ($userId): array => [
            'user_id' => $userId,
            'tanggal' => $today,
            'waktu_masuk' => null,
            'waktu_keluar' => null,
            'status' => 'Alpha',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ])
        ->all();

    Attendance::query()->insert($rows);
})->dailyAt('23:59');
