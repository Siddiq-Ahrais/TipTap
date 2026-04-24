<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceExportController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeaveController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::middleware('guest')->group(function (): void {
	Route::view('/admin/login', 'auth.admin-login')->name('admin.login');
});

Route::view('/dashboard', 'dashboard')
        ->middleware(['auth', 'approved'])
        ->name('dashboard');

Route::middleware('auth')->group(function (): void {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'approved'])->group(function (): void {
	Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->middleware('contract.active');
	Route::put('/clock-out', [AttendanceController::class, 'clockOut'])->middleware('contract.active');
	Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');

	Route::prefix('approval')->name('approval.')->group(function (): void {
		Route::get('/', [ApprovalController::class, 'index'])->name('index');
		Route::get('/settings', [ApprovalController::class, 'settings'])->name('settings.index');
		Route::get('/registrations', [ApprovalController::class, 'registrations'])->name('registrations.index');
		Route::get('/early-checkouts', [ApprovalController::class, 'earlyCheckouts'])->name('early-checkouts.index');
		Route::get('/leaves', [ApprovalController::class, 'leaves'])->name('leaves.index');
		Route::patch('/users/{user}/approve', [ApprovalController::class, 'approveUser'])->name('users.approve');
		Route::delete('/users/{user}/reject', [ApprovalController::class, 'rejectUser'])->name('users.reject');
		Route::patch('/early-checkouts/{attendance}/approve', [ApprovalController::class, 'approveEarlyCheckout'])->name('early-checkouts.approve');
		Route::patch('/early-checkouts/{attendance}/reject', [ApprovalController::class, 'rejectEarlyCheckout'])->name('early-checkouts.reject');
		Route::patch('/leaves/{leave}/approve', [ApprovalController::class, 'approveLeave'])->name('leaves.approve');
		Route::patch('/leaves/{leave}/reject', [ApprovalController::class, 'rejectLeave'])->name('leaves.reject');
		Route::patch('/settings', [ApprovalController::class, 'updateSettings'])->name('settings.update');
		Route::get('/attendance/export', [AttendanceExportController::class, 'export'])->name('attendance.export');
	});

	Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
	Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
	Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');

	Route::resource('posts', PostController::class);
});

use App\Http\Controllers\AlphaController;

Route::middleware(['auth', 'approved'])->prefix('alpha')->name('alpha.')->group(function (): void {
	Route::get('/settings', [AlphaController::class, 'settings'])->name('settings.index');
	Route::patch('/settings', [AlphaController::class, 'updateSettings'])->name('settings.update');
	Route::delete('/attendance/reset', [AlphaController::class, 'resetAttendance'])->name('attendance.reset');

	Route::get('/devtools', [AlphaController::class, 'devTools'])->name('devtools.index');
	Route::post('/devtools/add-days', [AlphaController::class, 'addDays'])->name('devtools.addDays');
	Route::delete('/devtools/reset-testers', [AlphaController::class, 'resetTesters'])->name('devtools.resetTesters');
});

require __DIR__.'/auth.php';
