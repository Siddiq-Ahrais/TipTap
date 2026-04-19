<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
<<<<<<< HEAD
use App\Rules\CompanyEmailDomain;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
=======
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
>>>>>>> refs/remotes/origin/main
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
<<<<<<< HEAD
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, new CompanyEmailDomain()],
=======
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
>>>>>>> refs/remotes/origin/main
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
<<<<<<< HEAD
            'is_approved' => false,
=======
>>>>>>> refs/remotes/origin/main
        ]);

        event(new Registered($user));

<<<<<<< HEAD
        return redirect(route('login', absolute: false))
            ->with('status', 'Registration successful. Awaiting admin approval.');
=======
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
>>>>>>> refs/remotes/origin/main
    }
}
