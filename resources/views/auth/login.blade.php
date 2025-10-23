<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center py-10 px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-6">
                <div class="flex justify-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo"
                         class="h-14 w-auto object-contain drop-shadow-sm">
                </div>
                <h1 class="mt-3 text-2xl font-bold text-slate-800">Masuk ke Security Lab</h1>
                <p class="text-sm text-slate-500">Latihan keamanan web — SQLi, XSS, IDOR, LFI, Open Redirect</p>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl ring-1 ring-black/5 overflow-hidden">
                <div class="p-6">
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <div class="flex items-center justify-between">
                                <x-input-label for="password" :value="__('Password')" />
                                @if (Route::has('password.request'))
                                    <a class="text-xs text-blue-600 hover:underline" href="{{ route('password.request') }}">
                                        Lupa password?
                                    </a>
                                @endif
                            </div>
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="block">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                            </label>
                        </div>

                        <div class="pt-2">
                            <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 text-white px-4 py-2.5 text-sm font-semibold shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition">
                                Masuk
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
