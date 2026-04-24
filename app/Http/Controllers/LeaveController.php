<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Leave;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LeaveController extends Controller
{
    public function apply(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'jenis_izin' => ['required', 'string', Rule::in(['Sakit', 'Cuti Tahunan', 'Keperluan Mendadak'])],
                'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
                'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
                'alasan' => ['required', 'string'],
                'bukti_file' => ['required_if:jenis_izin,Sakit', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            ],
            [
                'jenis_izin.in' => 'Leave type must be one of: Sakit, Cuti Tahunan, Keperluan Mendadak.',
                'tanggal_selesai.after_or_equal' => 'The end date must be greater than or equal to the start date.',
                'bukti_file.required_if' => 'Supporting document is required when jenis_izin is Sakit.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $storedPath = null;

        if ($request->hasFile('bukti_file')) {
            $file = $request->file('bukti_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = sprintf('user_%d_leave_%d.%s', $user->id, now()->timestamp, $extension);

            $storedPath = Storage::disk('public')->putFileAs('leave_documents', $file, $filename);
        }

        $leave = Leave::query()->create([
            'user_id' => $user->id,
            'jenis_izin' => $validated['jenis_izin'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'alasan' => $validated['alasan'],
            'bukti_file' => $storedPath,
            'status_approval' => 'Pending',
        ]);

        return response()->json([
            'message' => 'Leave application submitted successfully.',
            'data' => $leave,
        ], 201);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $history = Leave::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'message' => 'Leave history fetched successfully.',
            'data' => $history,
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Leave::query()->where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status_approval', $request->status);
        }

        $history = $query->orderByDesc('created_at')->paginate(10);

        return view('leave.index', compact('history'));
    }

    public function create()
    {
        return view('leave.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make(
            $request->all(),
            [
                'jenis_izin' => ['required', 'string', Rule::in(['Sakit', 'Cuti Tahunan', 'Keperluan Mendadak'])],
                'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
                'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
                'alasan' => ['required', 'string'],
                'bukti_file' => ['required_if:jenis_izin,Sakit', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // 5MB limit
            ],
            [
                'jenis_izin.in' => 'Leave type must be one of: Sakit, Cuti Tahunan, Keperluan Mendadak.',
                'tanggal_selesai.after_or_equal' => 'The end date must be greater than or equal to the start date.',
                'bukti_file.required_if' => 'Supporting document is required when sick (Sakit).',
                'bukti_file.max' => 'The file size must not exceed 5MB.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Check leave quota
        $startDate = \Illuminate\Support\Carbon::parse($validated['tanggal_mulai']);
        $endDate = \Illuminate\Support\Carbon::parse($validated['tanggal_selesai']);
        $requestedDays = $startDate->diffInDays($endDate) + 1;
        $remainingDays = $user->remainingLeaveDays();

        if ($requestedDays > $remainingDays) {
            return redirect()->back()
                ->withErrors(['tanggal_selesai' => "Not enough leave quota. You are requesting {$requestedDays} day(s) but only have {$remainingDays} day(s) remaining."])
                ->withInput();
        }

        $storedPath = null;

        if ($request->hasFile('bukti_file')) {
            $file = $request->file('bukti_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = sprintf('user_%d_leave_%d.%s', $user->id, now()->timestamp, $extension);

            $storedPath = Storage::disk('public')->putFileAs('leave_documents', $file, $filename);
        }

        Leave::create([
            'user_id' => $user->id,
            'jenis_izin' => $validated['jenis_izin'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'alasan' => $validated['alasan'],
            'bukti_file' => $storedPath,
            'status_approval' => 'Pending',
        ]);

        return redirect()->route('leaves.index')->with('success', 'Leave application submitted successfully.');
    }
}
