<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/login', [PageController::class, 'home'])->name('login');

Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

Route::middleware(['auth', 'approved'])->group(function (): void {
	Route::post('/logout', [AuthController::class, 'logout']);
	Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->middleware('contract.active');
	Route::put('/clock-out', [AttendanceController::class, 'clockOut'])->middleware('contract.active');
        
        // Leave Management Routes
        Route::get('/leaves', [App\Http\Controllers\LeaveController::class, 'index'])->name('leaves.index');
        Route::get('/leaves/create', [App\Http\Controllers\LeaveController::class, 'create'])->name('leaves.create');
        Route::post('/leaves', [App\Http\Controllers\LeaveController::class, 'store'])->name('leaves.store');
});

Route::resource('posts', PostController::class);
