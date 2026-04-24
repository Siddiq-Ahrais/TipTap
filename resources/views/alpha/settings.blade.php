<x-alpha-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-navy-primary/70">System Config</p>
                <h2 class="mt-1 font-display text-2xl font-semibold text-navy-primary sm:text-3xl">
                    Alpha Developer Panel
                </h2>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-lg border border-amber-300 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">
                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.486 0l6.516 11.584c.75 1.334-.213 2.992-1.742 2.992H3.483c-1.53 0-2.492-1.658-1.742-2.992L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-6a1 1 0 00-1 1v3a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                Developer Testing Environment
            </span>
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

        {{-- Company Settings Section (same as admin) --}}
        <section class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm p-8 border border-[#0B4A85]/15">
            <h3 class="font-display text-xl font-semibold text-[#0B4A85]">Company Settings</h3>
            <p class="mt-1 text-sm text-slate-500">Customize allowed company email domain and office attendance timing.</p>

            <form method="POST" action="{{ route('alpha.settings.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label for="company_email_domain" class="mb-1.5 block text-sm font-medium text-slate-600">Company Email Domain</label>
                    <input id="company_email_domain" name="company_email_domain" type="text" value="{{ old('company_email_domain', $settings->company_email_domain) }}" placeholder="tiptap.id" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 focus:ring-2 focus:ring-[#0B4A85] focus:border-[#0B4A85]" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="jam_masuk_kantor" class="mb-1.5 block text-sm font-medium text-slate-600">Standard Clock-In Time</label>
                        <input id="jam_masuk_kantor" name="jam_masuk_kantor" type="time" value="{{ old('jam_masuk_kantor', \Illuminate\Support\Str::of((string) $settings->jam_masuk_kantor)->substr(0, 5)) }}" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 focus:ring-2 focus:ring-[#0B4A85] focus:border-[#0B4A85]" />
                    </div>

                    <div>
                        <label for="jam_mulai_pulang" class="mb-1.5 block text-sm font-medium text-slate-600">Standard Clock-Out Time</label>
                        <input id="jam_mulai_pulang" name="jam_mulai_pulang" type="time" value="{{ old('jam_mulai_pulang', \Illuminate\Support\Str::of((string) $settings->jam_mulai_pulang)->substr(0, 5)) }}" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 focus:ring-2 focus:ring-[#0B4A85] focus:border-[#0B4A85]" />
                    </div>
                </div>

                <button type="submit" class="bg-[#0B4A85] text-white w-full sm:w-auto px-6 py-2 rounded-lg font-semibold transition hover:bg-blue-900">
                    Save Configuration
                </button>
            </form>
        </section>

        {{-- Reset Attendance Section --}}
        <section
            class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm p-8 border border-rose-200"
            x-data="{ showConfirm: false }"
        >
            <div class="flex items-start gap-4">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-rose-100 text-rose-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-display text-xl font-semibold text-rose-700">Reset Today's Attendance</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Delete all attendance records for <strong>today ({{ now()->format('d M Y') }})</strong> so every user can clock in again.
                        This simulates skipping to the next day for testing purposes.
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <button
                    type="button"
                    @click="showConfirm = true"
                    class="inline-flex items-center gap-2 rounded-lg border-2 border-rose-500 bg-rose-500 px-6 py-3 font-semibold text-white transition hover:bg-rose-600 hover:border-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                    </svg>
                    Reset Attendance
                </button>
            </div>

            {{-- Confirmation Modal --}}
            <div
                x-cloak
                x-show="showConfirm"
                x-transition.opacity
                class="fixed inset-0 z-[70] flex items-center justify-center bg-[#031936]/60 p-4 backdrop-blur-sm"
            >
                <div
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="w-full max-w-md overflow-hidden rounded-3xl border border-rose-300/40 bg-white shadow-[0_24px_60px_rgba(3,25,54,0.35)]"
                    @click.away="showConfirm = false"
                >
                    <div class="bg-gradient-to-r from-rose-600 to-rose-500 px-6 py-4 text-white">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/80">Destructive Action</p>
                        <h4 class="mt-1 font-display text-xl font-semibold">Reset Today's Attendance?</h4>
                    </div>

                    <div class="space-y-5 px-6 py-5">
                        <p class="text-sm leading-6 text-slate-600">
                            This will <strong class="text-rose-700">permanently delete</strong> all attendance records for today
                            (<strong>{{ now()->format('d M Y') }}</strong>). Every user will be able to clock in again as if it's a new day.
                        </p>

                        <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-medium text-amber-700">
                            ⚠️ This action cannot be undone. Only use this for testing purposes.
                        </p>

                        <div class="grid grid-cols-2 gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                                @click="showConfirm = false"
                            >
                                Cancel
                            </button>
                            <form method="POST" action="{{ route('alpha.attendance.reset') }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-500 bg-rose-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-600"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                    </svg>
                                    Yes, Reset Attendance
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-alpha-layout>
