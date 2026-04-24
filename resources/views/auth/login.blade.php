<x-guest-layout>
    <div class="flex-grow flex items-center justify-center">
        <div
            class="w-full max-w-md mx-auto"
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
            <div class="rounded-xl border border-[#0B4A85]/15 border-t-4 border-t-[#0B4A85] bg-white p-6 shadow-xl shadow-slate-200/50 sm:p-8">
                <x-auth-session-status class="mb-5 rounded-lg border border-[#0B4A85]/25 bg-[#E7EFF6] px-4 py-3 text-sm text-[#0B4A85]" :status="session('status')" />

                <h1 class="text-2xl font-bold text-center text-dark-slate">Employee Login</h1>
                <p class="text-sm text-gray-500 text-center mb-6">Welcome back. Please sign in to continue.</p>

                <form method="POST" action="{{ route('login') }}" class="space-y-5" @submit="isSubmitting = true">
                    @csrf
                    <input type="hidden" name="login_type" value="employee">

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-[#0B4A85]/80 pointer-events-none">
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
                                placeholder="you@company.com"
                                class="w-full pl-10 px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#0B4A85] focus:border-[#0B4A85] focus:bg-white outline-none transition-all"
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
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-[#0B4A85]/80 pointer-events-none">
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
                                placeholder="Enter your password"
                                class="w-full pl-10 pr-10 px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#0B4A85] focus:border-[#0B4A85] focus:bg-white outline-none transition-all"
                                :class="(touchedPassword && password.length === 0) || hasPasswordError ? 'border-rose-600 focus:ring-rose-600 focus:border-rose-600' : ''"
                            />

                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-dark-slate/60 hover:text-dark-slate/85">
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

                    <div class="flex items-center justify-end text-sm mb-6">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-navy-primary font-medium hover:underline hover:text-[#063157]">
                                {{ __('Forgot Password?') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="w-full bg-navy-primary text-white font-semibold py-3 rounded-lg hover:bg-[#063157] transition duration-200 mt-2" :disabled="isSubmitting">
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

                @if (Route::has('register'))
                    <p class="mt-6 text-center text-sm text-gray-600">
                        New here?
                        <a href="{{ route('register') }}" class="text-navy-primary font-medium hover:underline hover:text-[#063157]">Create an account</a>
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>
