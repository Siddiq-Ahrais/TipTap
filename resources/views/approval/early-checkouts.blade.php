<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-navy-primary/70">Approval Center</p>
                <h2 class="mt-1 font-display text-2xl font-semibold text-navy-primary sm:text-3xl">
                    Early Checkout Approval
                </h2>
            </div>
            <a href="{{ route('approval.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-[#0B4A85]/30 bg-white px-4 py-2 text-sm font-semibold text-[#0B4A85] transition hover:bg-[#0B4A85]/5">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M17 10a1 1 0 01-1 1H6.414l2.293 2.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 111.414 1.414L6.414 9H16a1 1 0 011 1z" clip-rule="evenodd" />
                </svg>
                Back to Approval Menu
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <header class="bg-[#0B4A85] text-white">
                <div class="flex flex-col gap-1 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="font-display text-lg font-semibold">Pending Early Checkout Requests</h3>
                    <p class="text-sm text-white/85">Approve or reject requests before official office checkout time</p>
                </div>
            </header>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-100 text-slate-700">
                        <tr>
                            <th class="p-4 text-left font-semibold">Employee</th>
                            <th class="p-4 text-left font-semibold">Email</th>
                            <th class="p-4 text-left font-semibold">Date</th>
                            <th class="p-4 text-left font-semibold">Clock In</th>
                            <th class="p-4 text-left font-semibold">Requested At</th>
                            <th class="p-4 text-left font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingRequests as $attendance)
                            <tr class="border-b border-gray-200 hover:bg-slate-50">
                                <td class="p-4 font-medium text-slate-800">{{ $attendance->user?->name ?? '-' }}</td>
                                <td class="p-4 text-slate-600">{{ $attendance->user?->email ?? '-' }}</td>
                                <td class="p-4 text-slate-600">{{ $attendance->tanggal?->format('d M Y') ?? '-' }}</td>
                                <td class="p-4 text-slate-600">{{ $attendance->waktu_masuk ? $attendance->waktu_masuk->format('H:i') : '-' }}</td>
                                <td class="p-4 text-slate-600">{{ $attendance->early_checkout_requested_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td class="p-4">
                                    <div class="flex flex-wrap gap-2">
                                        <form method="POST" action="{{ route('approval.early-checkouts.approve', $attendance) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-md px-4 py-2 font-medium text-white shadow-sm transition" style="background-color:#0B4A85;border:1px solid #0B4A85;min-width:132px;justify-content:center;">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3.75-3.75a1 1 0 011.414-1.414l3.043 3.043 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                Approve
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('approval.early-checkouts.reject', $attendance) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-md px-4 py-2 font-medium transition" style="background-color:#FFF1F2;color:#BE123C;border:1px solid #FDA4AF;min-width:132px;justify-content:center;">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-slate-500">No early checkout requests pending right now.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
