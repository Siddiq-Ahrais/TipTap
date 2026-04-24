<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-navy-primary/70">Approval Center</p>
                <h2 class="mt-1 font-display text-2xl font-semibold text-navy-primary sm:text-3xl">
                    Leave Approval
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

    <div class="space-y-6" x-data="{ reviewOpen: false, selected: null }">
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

        <section class="rounded-2xl border border-[#0B4A85]/20 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#0B4A85]">Today's Attendance Stat</p>
                    <h3 class="mt-1 font-display text-2xl font-bold text-slate-900">{{ $clockedInTodayCount }}/{{ $totalEmployeeCount }}</h3>
                </div>
                <p class="text-sm text-slate-600">Employees clocked in today out of total registered employees.</p>
            </div>
        </section>

        <section class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <header class="bg-[#0B4A85] text-white">
                <div class="flex flex-col gap-1 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="font-display text-lg font-semibold">Pending Leave & Sick Requests</h3>
                    <p class="text-sm text-white/85">Review employee requests and process approval decisions</p>
                </div>
            </header>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-slate-100 text-slate-700">
                        <tr>
                            <th class="p-4 text-left font-semibold">Employee Name</th>
                            <th class="p-4 text-left font-semibold">Date</th>
                            <th class="p-4 text-left font-semibold">Type</th>
                            <th class="p-4 text-left font-semibold">Reason</th>
                            <th class="p-4 text-left font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingLeaves as $leave)
                            @php
                                $ext = strtolower(pathinfo((string) $leave->bukti_file, PATHINFO_EXTENSION));
                                $imageExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                                $attachmentUrl = $leave->bukti_file ? Storage::url($leave->bukti_file) : null;
                                $reviewPayload = [
                                    'id' => $leave->id,
                                    'employeeName' => $leave->user?->name ?? '-',
                                    'employeeEmail' => $leave->user?->email ?? '-',
                                    'type' => $leave->jenis_izin,
                                    'date' => $leave->tanggal_mulai?->format('d M Y').($leave->tanggal_selesai ? ' - '.$leave->tanggal_selesai->format('d M Y') : ''),
                                    'reason' => $leave->alasan,
                                    'attachmentUrl' => $attachmentUrl,
                                    'attachmentName' => $leave->bukti_file ? basename($leave->bukti_file) : null,
                                    'attachmentIsImage' => in_array($ext, $imageExt, true),
                                ];
                            @endphp
                            <tr class="border-b border-gray-200 align-top hover:bg-slate-50">
                                <td class="p-4">
                                    <p class="font-medium text-slate-800">{{ $leave->user?->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-500">{{ $leave->user?->email ?? '-' }}</p>
                                </td>
                                <td class="p-4 text-slate-700">
                                    {{ $leave->tanggal_mulai?->format('d M Y') ?? '-' }}
                                    @if ($leave->tanggal_selesai)
                                        <span class="text-slate-500">-</span>
                                        {{ $leave->tanggal_selesai?->format('d M Y') }}
                                    @endif
                                </td>
                                <td class="p-4 text-slate-700">{{ $leave->jenis_izin }}</td>
                                <td class="p-4 text-slate-700 max-w-sm">
                                    <p class="line-clamp-3 whitespace-pre-wrap">{{ $leave->alasan }}</p>
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            type="button"
                                            @click="selected = @js($reviewPayload); reviewOpen = true"
                                            class="inline-flex items-center rounded-md border border-[#0B4A85]/30 px-3 py-2 text-xs font-semibold text-[#0B4A85] transition hover:bg-[#0B4A85]/5"
                                        >
                                            Review
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-500">No pending leave or sick forms right now.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div
            x-cloak
            x-show="reviewOpen && selected"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            role="dialog"
            aria-modal="true"
            @click.self="reviewOpen = false"
        >
            <div class="w-full max-w-2xl max-h-[90vh] flex flex-col rounded-xl bg-white shadow-xl overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3 shrink-0">
                    <div>
                        <h3 class="font-display text-lg font-semibold text-slate-900">Review Leave Request</h3>
                        <p class="text-sm text-slate-500" x-text="selected?.employeeName"></p>
                    </div>
                    <button type="button" @click="reviewOpen = false" class="rounded-md p-2 text-slate-500 hover:bg-slate-100">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4 px-5 py-4 overflow-y-auto flex-1">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Type</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800" x-text="selected?.type"></p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Date</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800" x-text="selected?.date"></p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-700">Reason</p>
                        <p class="mt-1 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700 whitespace-pre-wrap" x-text="selected?.reason"></p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-700">Medical Certificate / Attachment</p>
                        <div class="mt-2 rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <template x-if="selected?.attachmentUrl && selected?.attachmentIsImage">
                                <img :src="selected?.attachmentUrl" alt="Medical certificate" class="w-full max-h-64 rounded-md border border-slate-200 object-contain" />
                            </template>

                            <template x-if="selected?.attachmentUrl && !selected?.attachmentIsImage">
                                <a :href="selected?.attachmentUrl" target="_blank" class="inline-flex items-center rounded-md border border-[#0B4A85]/30 px-3 py-2 text-xs font-semibold text-[#0B4A85] hover:bg-[#0B4A85]/5" x-text="selected?.attachmentName ?? 'View attachment'"></a>
                            </template>

                            <template x-if="!selected?.attachmentUrl">
                                <p class="text-sm text-slate-500">No attachment uploaded for this request.</p>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label for="admin_note" class="mb-1 block text-sm font-medium text-slate-700">Admin Notes (optional)</label>
                        <textarea id="admin_note" x-ref="adminNote" rows="3" placeholder="Add optional notes (for example: reason for rejection)..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0B4A85]"></textarea>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 px-5 py-3 shrink-0">
                    <form method="POST" :action="'{{ route('approval.leaves.approve', ['leave' => '__ID__']) }}'.replace('__ID__', selected?.id ?? '')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-md bg-[#11A582] px-4 py-2 font-semibold text-white transition hover:bg-teal-700">
                            Approve
                        </button>
                    </form>

                    <form method="POST" :action="'{{ route('approval.leaves.reject', ['leave' => '__ID__']) }}'.replace('__ID__', selected?.id ?? '')">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="note" :value="$refs.adminNote ? $refs.adminNote.value : ''">
                        <button type="submit" class="rounded-md border border-[#FF6B6B] px-4 py-2 font-semibold text-[#FF6B6B] transition hover:bg-red-50">
                            Reject
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
