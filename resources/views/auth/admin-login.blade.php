<x-guest-layout variant="admin">
    <div class="flex flex-grow items-center justify-center px-4 sm:px-6 lg:justify-end lg:px-10">
        <div
            class="w-full max-w-md"
            x-data="{
                showPassword: false,
                email: @js(old('email', '')),
                password: '',
                touchedEmail: false,
                touchedPassword: false,
                hasPasswordError: @js($errors->has('password')),
                isSubmitting: false
            }"
        >
            <div class="rounded-2xl border border-[#0B4A85]/25 border-t-4 border-t-[#0B4A85] bg-white p-6 shadow-2xl shadow-black/25 sm:p-8">
                <div class="mb-6 text-center">
                    <div class="mx-auto inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#0B4A85] text-white">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path d="M12 3L5 6V11C5 16 8.4 20.5 12 21C15.6 20.5 19 16 19 11V6L12 3Z" stroke-width="1.8" stroke-linejoin="round" />
                            <path d="M9.5 11.8L11.2 13.5L14.8 9.9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Restricted Area</p>
                    <h1 class="mt-2 text-2xl font-extrabold text-[#0B4A85]">Administrative Portal</h1>
                    <p class="mt-1 text-sm text-slate-500">Authorized personnel only.</p>
                </div>

                <x-auth-session-status class="mb-5 rounded-lg border border-[#0B4A85]/25 bg-[#E7EFF6] px-4 py-3 text-sm text-[#0B4A85]" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5" @submit="isSubmitting = true">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#0B4A85]/85">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M2.94 5.5A2 2 0 014.75 4.5h10.5a2 2 0 011.81 1L10 9.97 2.94 5.5z" />
                                    <path d="M2.5 7.2V14a2 2 0 002 2h11a2 2 0 002-2V7.2l-7.06 4.04a1 1 0 01-.98 0L2.5 7.2z" />
                                </svg>
                            </span>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                :value="email"
                                x-model="email"
                                @blur="touchedEmail = true"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="admin@company.com"
                                class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-2.5 pl-10 text-slate-900 outline-none transition-all focus:border-[#0B4A85] focus:bg-white focus:ring-2 focus:ring-[#0B4A85]"
                            />
                        </div>

                        <p
                            x-cloak
                            x-show="touchedEmail && email.length > 0 && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)"
                            class="mt-1.5 text-xs text-rose-600"
                        >
                            Enter a valid email address.
                        </p>

                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs !text-rose-600" />
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#0B4A85]/85">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M6 8V6a4 4 0 118 0v2h.5A1.5 1.5 0 0116 9.5v7a1.5 1.5 0 01-1.5 1.5h-9A1.5 1.5 0 014 16.5v-7A1.5 1.5 0 015.5 8H6zm2 0h4V6a2 2 0 10-4 0v2z" clip-rule="evenodd" />
                                </svg>
                            </span>

                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                x-model="password"
                                @blur="touchedPassword = true"
                                required
                                autocomplete="current-password"
                                placeholder="Enter admin password"
                                class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-2.5 pl-10 pr-10 text-slate-900 outline-none transition-all focus:border-[#0B4A85] focus:bg-white focus:ring-2 focus:ring-[#0B4A85]"
                                :class="(touchedPassword && password.length === 0) || hasPasswordError ? 'border-rose-600 focus:border-rose-600 focus:ring-rose-600' : ''"
                            />

                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-[#0B4A85]/75 hover:text-[#063157]">
                                <svg x-show="!showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M1.5 12s3.75-6 10.5-6 10.5 6 10.5 6-3.75 6-10.5 6S1.5 12 1.5 12z" />
                                    <circle cx="12" cy="12" r="3" stroke-width="1.8" />
                                </svg>
                                <svg x-cloak x-show="showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.6 10.6a3 3 0 004.2 4.2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 5.09A11.07 11.07 0 0112 4.9c6.75 0 10.5 6 10.5 6a18.29 18.29 0 01-3.11 3.96M6.22 6.22A18.48 18.48 0 001.5 12s3.75 6 10.5 6a11.1 11.1 0 005.05-1.17" />
                                </svg>
                            </button>
                        </div>

                        <p x-cloak x-show="touchedPassword && password.length === 0" class="mt-1.5 text-xs text-rose-600">
                            Password is required.
                        </p>

                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs !text-rose-600" />
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label for="remember_me" class="inline-flex items-center gap-2 text-slate-600">
                            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-[#0B4A85] focus:ring-[#0B4A85]">
                            <span>{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="font-medium text-[#0B4A85] hover:text-[#063157] hover:underline">
                                {{ __('Forgot Password?') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="mt-2 w-full rounded-lg bg-navy-primary py-3 font-semibold text-white transition duration-200 hover:bg-navy-dark" :disabled="isSubmitting">
                        <span x-show="!isSubmitting">{{ __('LOGIN') }}</span>
                        <span x-cloak x-show="isSubmitting" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.35" stroke-width="4" />
                                <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                            </svg>
                            {{ __('Signing In') }}
                        </span>
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-600">
                    Need the standard employee sign in?
                    <a href="{{ route('login') }}" class="font-medium text-[#0B4A85] hover:text-[#063157] hover:underline">Employee Login</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
