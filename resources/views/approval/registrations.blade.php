<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-navy-primary/70">Approval Center</p>
                <h2 class="mt-1 font-display text-2xl font-semibold text-navy-primary sm:text-3xl">
                    Pending Registrations
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
        @php
            $monthOptions = [
                ['value' => '01', 'label' => 'Jan'],
                ['value' => '02', 'label' => 'Feb'],
                ['value' => '03', 'label' => 'Mar'],
                ['value' => '04', 'label' => 'Apr'],
                ['value' => '05', 'label' => 'May'],
                ['value' => '06', 'label' => 'Jun'],
                ['value' => '07', 'label' => 'Jul'],
                ['value' => '08', 'label' => 'Aug'],
                ['value' => '09', 'label' => 'Sep'],
                ['value' => '10', 'label' => 'Oct'],
                ['value' => '11', 'label' => 'Nov'],
                ['value' => '12', 'label' => 'Dec'],
            ];
            $currentYear = (int) now()->format('Y');
            $yearOptions = [$currentYear - 1, $currentYear, $currentYear + 1];
        @endphp

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

        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="w-full lg:max-w-sm">
                    <label for="search_users" class="sr-only">Search users</label>
                    <input id="search_users" type="text" placeholder="Search employee name or email..." class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-slate-700 focus:border-[#0B4A85] focus:outline-none focus:ring-2 focus:ring-[#0B4A85]/20" />
                </div>

                <div class="flex gap-3 items-center lg:justify-end">
                    <select class="bg-gray-50 border border-gray-200 text-sm rounded-md px-3 py-2 text-slate-700 focus:border-[#0B4A85] focus:outline-none focus:ring-2 focus:ring-[#0B4A85]/20">
                        @foreach ($monthOptions as $month)
                            <option value="{{ $month['value'] }}" @selected($month['value'] === now()->format('m'))>{{ $month['label'] }}</option>
                        @endforeach
                    </select>

                    <select class="bg-gray-50 border border-gray-200 text-sm rounded-md px-3 py-2 text-slate-700 focus:border-[#0B4A85] focus:outline-none focus:ring-2 focus:ring-[#0B4A85]/20">
                        @foreach ($yearOptions as $year)
                            <option value="{{ $year }}" @selected($year === $currentYear)>{{ $year }}</option>
                        @endforeach
                    </select>

                    <button type="button" class="bg-[#0B4A85] hover:bg-blue-900 text-white font-medium rounded-md text-sm px-4 py-2 flex items-center gap-2 transition">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M3 14.5A1.5 1.5 0 014.5 13h1a1 1 0 000-2h-1A3.5 3.5 0 001 14.5v1A3.5 3.5 0 004.5 19h11a3.5 3.5 0 003.5-3.5v-1a3.5 3.5 0 00-3.5-3.5h-1a1 1 0 000 2h1a1.5 1.5 0 011.5 1.5v1a1.5 1.5 0 01-1.5 1.5h-11A1.5 1.5 0 013 15.5v-1z" />
                            <path d="M9 1.5a1 1 0 112 0V10l2.3-2.3a1 1 0 111.4 1.4l-4 4a1 1 0 01-1.4 0l-4-4a1 1 0 011.4-1.4L9 10V1.5z" />
                        </svg>
                        Export CSV
                    </button>
                </div>
            </div>
        </section>

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
    </div>
</x-app-layout>
