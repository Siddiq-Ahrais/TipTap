<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
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

	Route::prefix('approval')->name('approval.')->group(function (): void {
		Route::get('/', [ApprovalController::class, 'index'])->name('index');
		Route::patch('/users/{user}/approve', [ApprovalController::class, 'approveUser'])->name('users.approve');
		Route::delete('/users/{user}/reject', [ApprovalController::class, 'rejectUser'])->name('users.reject');
		Route::patch('/settings', [ApprovalController::class, 'updateSettings'])->name('settings.update');
	});

	Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
	Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
	Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');

	Route::resource('posts', PostController::class);
});

require __DIR__.'/auth.php';
