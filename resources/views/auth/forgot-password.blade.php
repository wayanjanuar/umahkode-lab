<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-violet-50 flex items-center justify-center py-10 px-4">
        <div class="w-full max-w-md">
            {{-- Brand --}}
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center rounded-2xl bg-indigo-600/10 p-3 ring-1 ring-indigo-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-7 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 12a4 4 0 10-8 0m8 0v1a4 4 0 11-8 0v-1m-2 0a10 10 0 1120 0v1a10 10 0 11-20 0v-1z"/>
                    </svg>
                </div>
                <h1 class="mt-3 text-2xl font-bold text-slate-800">Lupa Password</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Masukkan email terdaftar. Kami akan mengirim tautan untuk mengatur ulang password.
                </p>
            </div>

            {{-- Card --}}
            <div class="bg-white shadow-xl ring-1 ring-black/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-5">
                    {{-- Session Status --}}
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input
                                id="email"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autofocus
                                class="block mt-1 w-full"
                                placeholder="nama@contoh.com"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Tips kecil --}}
                        <div class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                            Pastikan email aktif. Jika tidak menerima email, periksa folder <span class="font-semibold">Spam/Junk</span>.
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-between pt-2">
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Kembali ke Login
                            </a>

                            <x-primary-button class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 12H8m8 0l-3-3m3 3l-3 3"/>
                                </svg>
                                {{ __('Kirim Tautan Reset') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>

                {{-- Footer kecil --}}
                <div class="px-6 py-4 bg-slate-50 text-[11px] text-slate-500">
                    Butuh bantuan? Hubungi panitia untuk verifikasi akun.
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
