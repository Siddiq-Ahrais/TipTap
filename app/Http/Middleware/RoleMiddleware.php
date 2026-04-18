<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $roleAliases = [
            'employee' => 'karyawan',
            'staff' => 'staf',
            'administrator' => 'admin',
        ];

        $normalize = static function (string $role) use ($roleAliases): string {
            $normalized = strtolower(trim($role));

            return $roleAliases[$normalized] ?? $normalized;
        };

        $normalizedRoles = array_map($normalize, $roles);

        if (! in_array($normalize((string) $user->role), $normalizedRoles, true)) {
            return response()->json([
                'message' => 'Forbidden: You do not have permission to access this resource.',
            ], 403);
        }

        return $next($request);
    }
}
