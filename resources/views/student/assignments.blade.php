<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl leading-tight">Dashboard Peserta Rekrutmen PA PK Sus Siber 2025</h2>
                <p class="text-sm text-gray-500">Security Lab</p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-3 py-2 text-sm shadow hover:from-blue-700 hover:to-indigo-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />
                </svg>
                Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ q: '', tag: 'all' }">

        {{-- Flash message --}}
        @if (session('message'))
            <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 p-3 text-emerald-800">
                {{ session('message') }}
            </div>
        @endif

        {{-- ====== HEADER STATS ====== --}}
        @php
            $total = $assignments->count();
            $u = auth()->user();

            $mySubmitCount = $u?->submissions()->count() ?? 0;
            $myLastSubmitAt = $u?->submissions()->latest()->value('created_at');

            // Soal yang sudah pernah disubmit oleh user
            $doneSoal = $assignments->where('my_submit_count', '>', 0);
            $doneCount = $doneSoal->count();
            $donePct = $total ? round(($doneCount / $total) * 100) : 0;

            // Daftar judul soal yang sudah pernah disubmit
            $doneKeys = $doneSoal->pluck('title')->implode(', ');
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            {{-- Total Soal --}}
            <div class="rounded-2xl bg-gradient-to-br from-sky-500 to-blue-600 text-white shadow p-5">
                <div class="text-xs uppercase/relaxed opacity-90">Total Soal</div>
                <div class="mt-1 flex items-end gap-2">
                    <div class="text-3xl font-semibold">{{ $total }}</div>
                    <span class="text-xs opacity-90">SQLi • XSS • Open Redirect • IDOR • LFI</span>
                </div>
            </div>

            {{-- Total Submit Saya --}}
            <div class="rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow p-5">
                <div class="text-xs uppercase/relaxed opacity-90">Total Submit Saya</div>
                <div class="mt-1 text-3xl font-semibold">{{ $mySubmitCount }}</div>
                <div class="mt-1 text-xs opacity-90">
                    Terakhir:
                    <span class="font-medium">
                        {{ $myLastSubmitAt ? \Carbon\Carbon::parse($myLastSubmitAt)->format('d M Y H:i') : '—' }}
                    </span>
                </div>
            </div>

            {{-- Progress Selesai (per soal) --}}
            <div class="rounded-2xl bg-gradient-to-br from-fuchsia-500 to-purple-600 text-white shadow p-5">
                <div class="text-xs uppercase/relaxed opacity-90">Progress Selesai</div>
                <div class="mt-1 flex items-end gap-2">
                    <div class="text-3xl font-semibold">{{ $doneCount }}</div>
                    <span class="text-sm opacity-90">/ {{ $total }}</span>
                </div>
                <div class="mt-2 h-2 w-full rounded-full bg-white/30 overflow-hidden">
                    <div class="h-full bg-white/90" style="width: {{ $donePct }}%"></div>
                </div>
                <div class="mt-1 text-xs opacity-90">{{ $donePct }}%</div>
            </div>
        </div>

        {{-- Ringkasan singkat soal yang sudah disubmit --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 -mt-2 mb-4">
            <p class="text-sm text-gray-700">
                Kamu sudah mengirimkan jawaban untuk <strong>{{ $doneCount }}</strong> dari
                <strong>{{ $total }}</strong> soal.
            </p>
            @if ($doneCount > 0)
                <p class="text-xs text-gray-500 mt-1">
                    Soal yang sudah pernah disubmit:
                    <span class="font-mono text-gray-700">{{ $doneKeys }}</span>
                </p>
            @endif
        </div>


        {{-- Tools: Search + Filter --}}
        <div class="shadow rounded-2xl p-4 mb-4 border border-indigo-100 bg-gradient-to-r from-white to-indigo-50/40">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input x-model="q" type="search" placeholder="Cari soal (mis. sqli, xss, idor)…"
                        class="w-full rounded-xl border border-indigo-200 pe-10 ps-11 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white/80 backdrop-blur">
                    <svg class="absolute left-3 top-2.5 size-5 text-indigo-500/70" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                    </svg>
                </div>

                <div>
                    <select x-model="tag"
                        class="rounded-xl border border-indigo-200 py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white/80 backdrop-blur">
                        <option value="all">Semua kategori</option>
                        <option value="sqli">SQLi</option>
                        <option value="xss">XSS</option>
                        <option value="openredirect">Open Redirect</option>
                        <option value="idor">IDOR</option>
                        <option value="lfi">LFI</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="shadow rounded-2xl overflow-hidden border border-indigo-100">
            <table class="min-w-full text-sm">
                <thead class="bg-indigo-50 text-indigo-900/90">
                    <tr>
                        <th class="px-4 py-3 text-left">Soal</th>
                        <th class="px-4 py-3 text-left">Deskripsi</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @php
                        function badge($key)
                        {
                            $map = [
                                'sqli' => ['SQLi', 'bg-red-100 text-red-700 ring-1 ring-red-200'],
                                'xss' => ['XSS', 'bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200'],
                                'openredirect' => [
                                    'Open Redirect',
                                    'bg-purple-100 text-purple-700 ring-1 ring-purple-200',
                                ],
                                'idor' => ['IDOR', 'bg-blue-100 text-blue-700 ring-1 ring-blue-200'],
                                'lfi' => ['LFI', 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200'],
                            ];
                            foreach ($map as $k => $v) {
                                if (str_contains(strtolower($key), $k)) {
                                    return $v;
                                }
                            }
                            return ['Other', 'bg-gray-100 text-gray-700 ring-1 ring-gray-200'];
                        }
                    @endphp

                    @forelse($assignments as $a)
                        @php [$label,$cls] = badge($a->key); @endphp
                        <tr x-show="(q==='' || '{{ strtolower($a->title . ' ' . $a->description . ' ' . $a->key) }}'.includes(q.toLowerCase()))
                                    && (tag==='all' || '{{ str_contains(strtolower($a->key), 'sqli') ? 'sqli' : '' }}{{ str_contains(strtolower($a->key), 'xss') ? 'xss' : '' }}{{ str_contains(strtolower($a->key), 'openredirect') ? 'openredirect' : '' }}{{ str_contains(strtolower($a->key), 'idor') ? 'idor' : '' }}{{ str_contains(strtolower($a->key), 'lfi') ? 'lfi' : '' }}'.includes(tag))"
                            x-transition class="hover:bg-indigo-50/40">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $a->title }}</div>
                                <div class="text-xs text-gray-400">{{ $a->key }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $a->description }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $cls }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 flex-wrap">
                                    {{-- Chip status submit --}}
                                    @if (($a->my_submit_count ?? 0) > 0)
                                        <span
                                            class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 text-xs">
                                            ✓ Sudah submit ({{ $a->my_submit_count }})
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-50 text-gray-600 border border-gray-200 px-2 py-0.5 text-xs">
                                            Belum submit
                                        </span>
                                    @endif

                                    <a href="{{ route('assignments.download', $a->key) }}"
                                        class="inline-flex items-center gap-1 rounded-lg border border-indigo-200 px-3 py-1.5 hover:bg-white shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                                        </svg>
                                        Download
                                    </a>

                                    <a href="{{ route('assignments.submit.form', $a->key) }}"
                                        class="inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-3 py-1.5 hover:from-indigo-700 hover:to-violet-700 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Submit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10">
                                <div class="text-center text-gray-500">
                                    <div
                                        class="mx-auto mb-2 size-10 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center ring-1 ring-indigo-200">
                                        🔎</div>
                                    Tidak ada soal. Jalankan seeder:
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">php artisan db:seed
                                        --class=AssignmentSeeder</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer hint --}}
        <p class="text-xs text-gray-400 mt-4">
            © Satsiber TNI | Panitia Rekrutmen PA PK Sus SIber 2025
        </p>
    </div>
</x-app-layout>
