<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class EnsureContractIsActiveForAttendance
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $employmentStatus = strtolower(trim((string) $user->status_pekerjaan));
        $permanentStatuses = ['tetap', 'permanent', 'karyawan tetap'];

        if (! in_array($employmentStatus, $permanentStatuses, true) && $user->tgl_habis_kontrak) {
            $today = Carbon::today();
            $contractEndDate = Carbon::parse($user->tgl_habis_kontrak)->startOfDay();

            if ($today->gt($contractEndDate)) {
                return response()->json([
                    'message' => 'Contract has expired. Attendance access is blocked.',
                ], 403);
            }
        }

        return $next($request);
    }
}
