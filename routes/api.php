<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'approved', 'contract.active'])->group(function (): void {
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
    Route::put('/attendance/clock-out', [AttendanceController::class, 'clockOut']);
});

Route::middleware(['auth', 'approved'])->group(function (): void {
    Route::post('/leaves/apply', [LeaveController::class, 'apply']);
    Route::get('/leaves/history', [LeaveController::class, 'history']);
});
