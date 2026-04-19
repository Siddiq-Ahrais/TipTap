<?php

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

require __DIR__.'/auth.php';
