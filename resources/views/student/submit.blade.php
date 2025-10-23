<x-app-layout x-data="{ chars: 0 }">
    {{-- HEADER --}}
    <x-slot name="header">
        <div class="relative overflow-hidden rounded-2xl">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 opacity-90">
            </div>
            <div class="relative flex items-center justify-between p-5 text-white">
                <div class="space-y-0.5">
                    <h2 class="font-semibold text-xl leading-tight">
                        Submit: {{ $assignment->title }}
                    </h2>
                    <p class="text-sm/relaxed text-white/90">{{ $assignment->description }}</p>
                </div>

                <div class="flex items-center gap-2">
                    @php
                        $key = strtolower($assignment->key);
                        $cat = 'OTHER';
                        $pill = 'bg-white/15 text-white ring-1 ring-white/30';
                        if (str_contains($key, 'sqli')) {
                            $cat = 'SQLI';
                            $pill = 'bg-rose-100 text-rose-700 ring-1 ring-rose-200';
                        } elseif (str_contains($key, 'xss')) {
                            $cat = 'XSS';
                            $pill = 'bg-amber-100 text-amber-800 ring-1 ring-amber-200';
                        } elseif (str_contains($key, 'openredirect')) {
                            $cat = 'OPEN REDIRECT';
                            $pill = 'bg-purple-100 text-purple-700 ring-1 ring-purple-200';
                        } elseif (str_contains($key, 'idor')) {
                            $cat = 'IDOR';
                            $pill = 'bg-sky-100 text-sky-700 ring-1 ring-sky-200';
                        } elseif (str_contains($key, 'lfi')) {
                            $cat = 'LFI';
                            $pill = 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200';
                        }
                    @endphp

                    <span
                        class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium {{ $pill }}">
                        {{ $cat }}
                    </span>

                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-white/15 text-white px-3 py-1.5 text-sm font-medium ring-1 ring-white/30 hover:bg-white/25 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- CARD --}}
    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-2xl shadow border border-indigo-100 overflow-hidden">

            {{-- Top strip info --}}
            <div
                class="flex items-center justify-between bg-gradient-to-r from-indigo-50 to-violet-50 border-b border-indigo-100 px-6 py-3 text-sm">
                <div class="flex items-center gap-2 text-indigo-900/90">
                    <span
                        class="inline-flex items-center justify-center rounded-full bg-indigo-600/10 text-indigo-700 size-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
                        </svg>
                    </span>
                    <strong>Kode Soal:</strong>
                    <span
                        class="font-mono text-xs rounded border border-indigo-200 bg-white px-2 py-0.5">{{ $assignment->key }}</span>
                </div>

                <div class="flex items-center gap-2 text-indigo-900/90">
                    <label for="language" class="text-xs">Bahasa:</label>
                    <input id="language" name="language" value="php" form="submit-form"
                        class="w-24 rounded border border-indigo-200 bg-white/80 px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>

            {{-- FORM --}}
            <form id="submit-form" method="POST" action="{{ route('assignments.submit', $assignment->key) }}">
                @csrf

                {{-- Editor with glow --}}
                <div class="p-6">
                    <label class="mb-2 block font-medium text-gray-800">Source Code</label>

                    <div
                        class="rounded-xl bg-gradient-to-br from-indigo-200/60 via-violet-200/60 to-fuchsia-200/60 p-[2px]">
                        <div class="rounded-[10px] bg-white">
                            <textarea name="source_code" rows="22" x-on:input="chars = $event.target.value.length"
                                class="w-full resize-none rounded-[10px] border-0 p-4 font-mono text-[13px] leading-6 focus:outline-none focus:ring-0"></textarea>
                        </div>
                    </div>

                    @error('source_code')
                        <div class="mt-2 text-sm text-rose-600">{{ $message }}</div>
                    @enderror

                </div>

                {{-- Footer --}}
                <div
                    class="flex items-center justify-between border-t bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 text-xs text-emerald-900/90">
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center justify-center rounded-full bg-emerald-600/10 text-emerald-700 size-6">✅</span>
                        <span>
                            Setelah disubmit, sistem akan melakukan evaluasi otomatis.
                            <span class="italic">Hasil hanya dapat dilihat oleh Panitia.</span>
                        </span>
                    </div>

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-emerald-600 to-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:from-emerald-700 hover:to-green-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Submit untuk Dinilai
                    </button>
                </div>
            </form>

        </div>

    </div>
</x-app-layout>
