<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $role = strtolower((string) $user?->role);

        $isAdmin = in_array($role, ['admin', 'administrator', 'superadmin', 'super admin', 'super_admin'], true);

        if ($isAdmin) {
            return view('admin.dashboard');
        }

        return view('dashboard');
    }
}
