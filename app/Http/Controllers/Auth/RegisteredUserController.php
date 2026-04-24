<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Rules\CompanyEmailDomain;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $companyDomain = (string) (Setting::first()?->company_email_domain
            ?: config('app.company_email_domain', 'company.com'));

        return view('auth.register', [
            'companyDomain' => $companyDomain,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $companyDomain = (string) (Setting::first()?->company_email_domain
            ?: config('app.company_email_domain', 'company.com'));

        // Concatenate username + domain into a full email before validation
        $username = strtolower(trim(str_replace('@', '', (string) $request->input('username', ''))));
        $request->merge([
            'email' => $username . '@' . $companyDomain,
        ]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, new CompanyEmailDomain()],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'username.regex' => 'Username may only contain letters, numbers, dots, hyphens, and underscores.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_approved' => false,
        ]);

        event(new Registered($user));

        return redirect(route('login', absolute: false))
            ->with('status', 'Registration successful. Awaiting admin approval.');
    }
}
