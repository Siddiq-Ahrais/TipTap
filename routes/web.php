<?php

<<<<<<< HEAD
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeaveController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

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

	Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
	Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
	Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');

	Route::resource('posts', PostController::class);
});
=======
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::middleware(['auth', 'approved'])->group(function (): void {
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->middleware('contract.active');
    Route::put('/clock-out', [AttendanceController::class, 'clockOut'])->middleware('contract.active');

    // Leave Management Routes
    Route::get('/leaves', [App\Http\Controllers\LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [App\Http\Controllers\LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [App\Http\Controllers\LeaveController::class, 'store'])->name('leaves.store');
});

Route::resource('posts', PostController::class);
>>>>>>> refs/remotes/origin/main

require __DIR__.'/auth.php';
