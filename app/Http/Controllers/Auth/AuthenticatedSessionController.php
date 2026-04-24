<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        if (! $user?->is_approved) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Account pending Admin approval.',
            ]);
        }

        $role = strtolower((string) $user->role);
        $loginType = $request->input('login_type', 'employee');
        $adminRoles = ['admin', 'administrator', 'superadmin', 'super admin', 'super_admin'];

        // Admin/alpha accounts must use the admin login page
        if ($loginType === 'employee' && (in_array($role, $adminRoles, true) || $role === 'alpha')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Admin accounts must use the Admin Login page.',
            ]);
        }

        // Employee accounts must use the employee login page
        if ($loginType === 'admin' && ! in_array($role, $adminRoles, true) && $role !== 'alpha') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Employee accounts must use the Employee Login page.',
            ]);
        }

        if ($role === 'alpha') {
            return redirect()->route('alpha.settings.index');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
