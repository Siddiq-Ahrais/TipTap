<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-navy-primary/70">System Config</p>
                <h2 class="mt-1 font-display text-2xl font-semibold text-navy-primary sm:text-3xl">
                    Global Working Hours Settings
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

        <section class="bg-white rounded-xl shadow-sm p-8 border border-[#0B4A85]/15">
            <h3 class="font-display text-xl font-semibold text-[#0B4A85]">Company Settings</h3>
            <p class="mt-1 text-sm text-slate-500">Customize allowed company email domain and office attendance timing.</p>

            <form method="POST" action="{{ route('approval.settings.update') }}" class="mt-6 space-y-5">
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
    </div>
</x-app-layout>
