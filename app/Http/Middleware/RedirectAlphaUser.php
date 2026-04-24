<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects alpha-role users to their dedicated panel.
 * Alpha users should only access /alpha/* routes and auth routes (logout).
 */
class RedirectAlphaUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && strtolower((string) $user->role) === 'alpha') {
            // Allow alpha routes and auth routes (logout)
            if (! $request->routeIs('alpha.*') && ! $request->routeIs('logout')) {
                return redirect()->route('alpha.settings.index');
            }
        }

        return $next($request);
    }
}
