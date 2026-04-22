<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-navy-primary/70">Approval Center</p>
                <h2 class="mt-1 font-display text-2xl font-semibold text-navy-primary sm:text-3xl">
                    Employee Approval Menu
                </h2>
            </div>
            <p class="text-sm font-medium text-slate-500">{{ now()->format('l, d M Y') }}</p>
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
                    <h3 class="font-display text-lg font-semibold">Pending Registrations</h3>
                    <p class="text-sm text-white/85">Approve official employees before they can log in</p>
                </div>
            </header>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-100 text-slate-700">
                        <tr>
                            <th class="p-4 text-left font-semibold">Name</th>
                            <th class="p-4 text-left font-semibold">Email</th>
                            <th class="p-4 text-left font-semibold">Registered</th>
                            <th class="p-4 text-left font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingUsers as $user)
                            <tr class="border-b border-gray-200 hover:bg-slate-50">
                                <td class="p-4 font-medium text-slate-800">{{ $user->name }}</td>
                                <td class="p-4 text-slate-600">{{ $user->email }}</td>
                                <td class="p-4 text-slate-600">{{ $user->created_at?->format('d M Y H:i') }}</td>
                                <td class="p-4">
                                    <div class="flex flex-wrap gap-2">
                                        <form method="POST" action="{{ route('approval.users.approve', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[#0B4A85] px-4 py-2 font-medium text-white transition hover:bg-[#063157]">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3.75-3.75a1 1 0 011.414-1.414l3.043 3.043 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                Approve
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('approval.users.reject', $user) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-md border border-rose-300 px-4 py-2 font-medium text-rose-700 transition hover:bg-rose-50">
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
                                <td colspan="4" class="p-6 text-center text-slate-500">No pending registrations right now.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-display text-xl font-semibold text-[#0B4A85]">Company Settings</h3>
            <p class="mt-1 text-sm text-slate-500">Customize allowed company email domain and office attendance timing.</p>

            <form method="POST" action="{{ route('approval.settings.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label for="company_email_domain" class="mb-1.5 block text-sm font-medium text-slate-600">Company Email Domain</label>
                    <input id="company_email_domain" name="company_email_domain" type="text" value="{{ old('company_email_domain', $settings->company_email_domain) }}" placeholder="tiptap.id" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 focus:border-[#0B4A85] focus:ring-[#0B4A85]" />
                    <p class="mt-1 text-xs text-slate-500">Employees must register with this domain, for example name@domain.com.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="jam_masuk_kantor" class="mb-1.5 block text-sm font-medium text-slate-600">Office Check-In Time</label>
                        <input id="jam_masuk_kantor" name="jam_masuk_kantor" type="time" value="{{ old('jam_masuk_kantor', \Illuminate\Support\Str::of((string) $settings->jam_masuk_kantor)->substr(0, 5)) }}" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 focus:border-[#0B4A85] focus:ring-[#0B4A85]" />
                    </div>

                    <div>
                        <label for="jam_mulai_pulang" class="mb-1.5 block text-sm font-medium text-slate-600">Office Check-Out Time</label>
                        <input id="jam_mulai_pulang" name="jam_mulai_pulang" type="time" value="{{ old('jam_mulai_pulang', \Illuminate\Support\Str::of((string) $settings->jam_mulai_pulang)->substr(0, 5)) }}" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 focus:border-[#0B4A85] focus:ring-[#0B4A85]" />
                    </div>
                </div>

                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-[#0B4A85] px-5 py-3 font-semibold text-white transition hover:bg-[#063157]">Save Company Settings</button>
            </form>
        </section>
    </div>
</x-app-layout>
