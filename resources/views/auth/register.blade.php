<x-guest-layout>
    <div class="flex-grow flex items-center justify-center">
        <div
            class="w-full max-w-md mx-auto"
            x-data="{
                showPassword: false,
                showConfirmation: false,
                name: @js(old('name', '')),
                email: @js(old('email', '')),
                password: '',
                confirmation: '',
                touchedEmail: false,
                touchedPassword: false,
                touchedConfirmation: false,
                isSubmitting: false
            }"
        >
            <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg border border-gray-100">
                <h1 class="text-2xl font-bold text-center text-gray-900">Create Account</h1>
                <p class="text-sm text-gray-500 text-center mb-6">Set up your TipTap account in a few steps.</p>

                <form method="POST" action="{{ route('register') }}" class="space-y-5" @submit="isSubmitting = true">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10 2a4 4 0 100 8 4 4 0 000-8zM3 15.5A3.5 3.5 0 016.5 12h7a3.5 3.5 0 013.5 3.5V18H3v-2.5z" />
                                </svg>
                            </span>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                x-model="name"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Your full name"
                                class="w-full pl-10 px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white outline-none transition-all"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
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
                                autocomplete="username"
                                placeholder="you@company.com"
                                class="w-full pl-10 px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white outline-none transition-all"
                            />
                        </div>

                        <p
                            x-cloak
                            x-show="touchedEmail && email.length > 0 && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)"
                            class="mt-1.5 text-xs text-red-500"
                        >
                            Enter a valid email address.
                        </p>

                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
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
                                autocomplete="new-password"
                                placeholder="At least 8 characters"
                                class="w-full pl-10 pr-10 px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white outline-none transition-all"
                            />
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600">
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

                        <p x-cloak x-show="touchedPassword && password.length > 0 && password.length < 8" class="mt-1.5 text-xs text-red-500">
                            Password should be at least 8 characters.
                        </p>

                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M6 8V6a4 4 0 118 0v2h.5A1.5 1.5 0 0116 9.5v7a1.5 1.5 0 01-1.5 1.5h-9A1.5 1.5 0 014 16.5v-7A1.5 1.5 0 015.5 8H6zm2 0h4V6a2 2 0 10-4 0v2z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <input
                                id="password_confirmation"
                                :type="showConfirmation ? 'text' : 'password'"
                                name="password_confirmation"
                                x-model="confirmation"
                                @blur="touchedConfirmation = true"
                                required
                                autocomplete="new-password"
                                placeholder="Repeat your password"
                                class="w-full pl-10 pr-10 px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white outline-none transition-all"
                            />
                            <button type="button" @click="showConfirmation = !showConfirmation" class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600">
                                <svg x-show="!showConfirmation" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M1.5 12s3.75-6 10.5-6 10.5 6 10.5 6-3.75 6-10.5 6S1.5 12 1.5 12z" />
                                    <circle cx="12" cy="12" r="3" stroke-width="1.8" />
                                </svg>
                                <svg x-cloak x-show="showConfirmation" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.6 10.6a3 3 0 004.2 4.2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 5.09A11.07 11.07 0 0112 4.9c6.75 0 10.5 6 10.5 6a18.29 18.29 0 01-3.11 3.96M6.22 6.22A18.48 18.48 0 001.5 12s3.75 6 10.5 6a11.1 11.1 0 005.05-1.17" />
                                </svg>
                            </button>
                        </div>

                        <p x-cloak x-show="touchedConfirmation && confirmation.length > 0 && confirmation !== password" class="mt-1.5 text-xs text-red-500">
                            Confirmation does not match password.
                        </p>

                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs" />
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition duration-200 mt-2" :disabled="isSubmitting">
                        <span x-show="!isSubmitting">{{ __('REGISTER') }}</span>
                        <span x-cloak x-show="isSubmitting" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.35" stroke-width="4" />
                                <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                            </svg>
                            {{ __('Creating') }}
                        </span>
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-600">
                    {{ __('Already registered?') }}
                    <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:underline hover:text-blue-800">{{ __('Log in') }}</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
