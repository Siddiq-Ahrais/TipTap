<x-guest-layout>
    <div class="flex-grow flex items-center justify-center">
        <div
            class="w-full max-w-md mx-auto"
            x-data="{
                showPassword: false,
                showConfirmation: false,
                name: @js(old('name', '')),
                username: @js(old('username', old('email') ? explode('@', old('email'))[0] : '')),
                password: '',
                confirmation: '',
                touchedUsername: false,
                touchedPassword: false,
                touchedConfirmation: false,
                hasPasswordError: @js($errors->has('password')),
                hasPasswordConfirmationError: @js($errors->has('password_confirmation')),
                isSubmitting: false
            }"
        >
            <div class="rounded-xl border border-[#0B4A85]/15 border-t-4 border-t-[#0B4A85] bg-white p-6 shadow-xl shadow-slate-200/50 sm:p-8">
                <h1 class="text-2xl font-bold text-center text-dark-slate">Create Account</h1>
                <p class="text-sm text-gray-500 text-center mb-6">Set up your TipTap account in a few steps.</p>

                <form method="POST" action="{{ route('register') }}" class="space-y-5" @submit="isSubmitting = true">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-[#0B4A85]/80 pointer-events-none">
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
                                class="w-full pl-10 px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#0B4A85] focus:border-[#0B4A85] focus:bg-white outline-none transition-all"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs !text-rose-600" />
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <div class="flex">
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-3 flex items-center text-[#0B4A85]/80 pointer-events-none">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M2.94 5.5A2 2 0 014.75 4.5h10.5a2 2 0 011.81 1L10 9.97 2.94 5.5z" />
                                        <path d="M2.5 7.2V14a2 2 0 002 2h11a2 2 0 002-2V7.2l-7.06 4.04a1 1 0 01-.98 0L2.5 7.2z" />
                                    </svg>
                                </span>
                                <input
                                    id="username"
                                    type="text"
                                    name="username"
                                    x-model="username"
                                    @input="username = username.replace(/@/g, '')"
                                    @blur="touchedUsername = true"
                                    required
                                    autocomplete="username"
                                    placeholder="username"
                                    class="w-full pl-10 px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-l-lg rounded-r-none text-gray-900 focus:ring-2 focus:ring-[#0B4A85] focus:border-[#0B4A85] focus:bg-white outline-none transition-all"
                                />
                            </div>
                            <span class="inline-flex items-center bg-gray-100 border border-l-0 border-gray-300 text-gray-500 text-sm font-medium rounded-r-lg px-3 select-none">
                                &#64;{{ $companyDomain }}
                            </span>
                        </div>

                        <!-- Hidden field sends the full concatenated email -->
                        <input type="hidden" name="email" :value="username + '@' + '{{ $companyDomain }}'">

                        <p
                            x-cloak
                            x-show="touchedUsername && username.length > 0 && !/^[a-zA-Z0-9._-]+$/.test(username)"
                            class="mt-1.5 text-xs text-rose-600"
                        >
                            Username may only contain letters, numbers, dots, hyphens, and underscores.
                        </p>

                        <x-input-error :messages="$errors->get('username')" class="mt-1.5 text-xs !text-rose-600" />
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
                                autocomplete="new-password"
                                placeholder="At least 8 characters"
                                class="w-full pl-10 pr-10 px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#0B4A85] focus:border-[#0B4A85] focus:bg-white outline-none transition-all"
                                :class="(touchedPassword && password.length > 0 && password.length < 8) || hasPasswordError ? 'border-rose-600 focus:ring-rose-600 focus:border-rose-600' : ''"
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

                        <p x-cloak x-show="touchedPassword && password.length > 0 && password.length < 8" class="mt-1.5 text-xs text-rose-600">
                            Password should be at least 8 characters.
                        </p>

                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs !text-rose-600" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-[#0B4A85]/80 pointer-events-none">
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
                                class="w-full pl-10 pr-10 px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 focus:ring-2 focus:ring-[#0B4A85] focus:border-[#0B4A85] focus:bg-white outline-none transition-all"
                                :class="(touchedConfirmation && confirmation.length > 0 && confirmation !== password) || hasPasswordConfirmationError ? 'border-rose-600 focus:ring-rose-600 focus:border-rose-600' : ''"
                            />
                            <button type="button" @click="showConfirmation = !showConfirmation" class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-dark-slate/60 hover:text-dark-slate/85">
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

                        <p x-cloak x-show="touchedConfirmation && confirmation.length > 0 && confirmation !== password" class="mt-1.5 text-xs text-rose-600">
                            Confirmation does not match password.
                        </p>

                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs !text-rose-600" />
                    </div>

                    <button type="submit" class="w-full bg-navy-primary text-white font-semibold py-3 rounded-lg hover:bg-[#063157] transition duration-200 mt-2" :disabled="isSubmitting">
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
                    <a href="{{ route('login') }}" class="text-navy-primary font-medium hover:underline hover:text-[#063157]">{{ __('Log in') }}</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
