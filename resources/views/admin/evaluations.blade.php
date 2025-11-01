<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl leading-tight">Evaluations (Admin)</h2>
                <p class="text-sm text-gray-500">
                    Ringkasan hasil penilaian otomatis per peserta. Gunakan pencarian di bawah untuk memfilter.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.submissions.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border px-3 py-1.5 text-sm hover:bg-gray-50">
                    📄 Ringkasan Submission
                </a>
                <a href="{{ url()->current() }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 text-white px-3 py-1.5 text-sm hover:bg-blue-700">
                    🔄 Refresh
                </a>
            </div>
        </div>
    </x-slot>

    @php
        // nilai default q untuk Alpine dari query ?q=
        $q = request('q', '');
    @endphp

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ q: @js($q) }">
        <div class="bg-white shadow sm:rounded-xl p-4">

            {{-- Search (server-side) --}}
            <form method="GET" class="mb-4" @submit.prevent="$el.submit()">
                <div class="relative">
                    <input x-model="q" @change="$event.target.form.submit()" name="q" type="search"
                        placeholder="Cari peserta atau soal… (nama, email, judul, key)"
                        class="w-full rounded-lg border border-gray-300 ps-10 pe-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <svg class="absolute left-3 top-2.5 size-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                    </svg>
                </div>
            </form>

            @php
                $badge = function ($key) {
                    $map = [
                        'sqli' => ['SQLi', 'bg-red-100 text-red-700 ring-1 ring-red-200'],
                        'xss' => ['XSS', 'bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200'],
                        'openredirect' => ['Open Redirect', 'bg-purple-100 text-purple-700 ring-1 ring-purple-200'],
                        'idor' => ['IDOR', 'bg-blue-100 text-blue-700 ring-1 ring-blue-200'],
                        'lfi' => ['LFI', 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200'],
                    ];
                    foreach ($map as $k => $v) {
                        if (str_contains(strtolower($key ?? ''), $k)) {
                            return $v;
                        }
                    }
                    return ['Other', 'bg-gray-100 text-gray-700 ring-1 ring-gray-200'];
                };
                $pill = function ($s) {
                    if ($s === null) {
                        return 'bg-gray-100 text-gray-700';
                    }
                    if ($s >= 85) {
                        return 'bg-emerald-100 text-emerald-700';
                    }
                    if ($s >= 70) {
                        return 'bg-amber-100 text-amber-800';
                    }
                    return 'bg-rose-100 text-rose-700';
                };
            @endphp

            @if ($users->isEmpty())
                <div class="py-16 text-center text-gray-500">
                    <div class="mx-auto mb-3 size-12 rounded-full bg-gray-100 flex items-center justify-center">🗂️
                    </div>
                    Belum ada evaluasi.
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($users as $u)
                        @php
                            // statistik dari select subquery di controller
                            $avg = is_null($u->avg_score) ? '—' : number_format($u->avg_score, 1);
                            $max = is_null($u->max_score) ? '—' : number_format($u->max_score, 0);
                            $min = is_null($u->min_score) ? '—' : number_format($u->min_score, 0);

                            // evaluasi milik user (sudah diprefetch dalam $evalsByUser)
                            /** @var \Illuminate\Support\Collection $items */
                            $items = $evalsByUser[$u->id] ?? collect();

                            // untuk filter client-side tambahan (opsional)
                            $titles = $items
                                ->map(
                                    fn($e) => ($e->submission->assignment->title ?? '') .
                                        ' ' .
                                        ($e->submission->assignment->key ?? ''),
                                )
                                ->implode(' ');
                            $groupHay = strtolower(($u->name ?? '') . ' ' . ($u->email ?? '') . ' ' . $titles);
                        @endphp

                        <div x-data="{ open: false }" x-show="'{{ $groupHay }}'.includes(q.toLowerCase())"
                            x-transition class="border rounded-xl overflow-hidden">
                            {{-- Header per user --}}
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50">
                                <button type="button" class="text-left" @click="open = !open">
                                    <div class="font-semibold text-gray-800">
                                        {{ $u->name ?? '—' }}
                                        <span class="text-xs text-gray-500">({{ $u->email ?? '—' }})</span>
                                    </div>
                                    <div class="mt-0.5 text-xs text-gray-600">
                                        {{ $u->evals_count }} evaluasi · Rata-rata:
                                        <span class="font-medium">{{ $avg }}</span> ·
                                        Tertinggi: <span class="font-medium">{{ $max }}</span> ·
                                        Terendah: <span class="font-medium">{{ $min }}</span>
                                    </div>
                                </button>

                                <div class="flex items-center gap-2">
                                    {{-- Pindahkan tombol PDF ke tiap user (bukan di header global) --}}
                                    <a href="{{ route('admin.users.summary.pdf', $u->id) }}"
                                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 text-white px-3 py-1.5 text-sm hover:bg-red-700">
                                        🧾 Download PDF
                                    </a>


                                    <svg x-bind:class="open ? 'rotate-180' : ''" class="size-4 text-gray-600 transition"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>

                            {{-- List evaluasi milik user --}}
                            <div x-show="open" x-collapse>
                                @if ($items->isEmpty())
                                    <div class="px-4 py-6 text-sm text-gray-500">
                                        Belum ada evaluasi untuk user ini (terfilter oleh pencarian).
                                    </div>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead class="bg-white text-gray-600 border-t">
                                                <tr>
                                                    <th class="px-4 py-3 text-left">Waktu</th>
                                                    <th class="px-4 py-3 text-left">Assignment</th>
                                                    <th class="px-4 py-3 text-left">Score</th>
                                                    <th class="px-4 py-3 text-left">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y">
                                                @foreach ($items->sortByDesc('created_at') as $e)
                                                    @php
                                                        $ak = $e->submission->assignment->key ?? '';
                                                        $at = $e->submission->assignment->title ?? '';
                                                        [$label, $cls] = $badge($ak);
                                                    @endphp
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-4 py-3 text-gray-600">
                                                            {{ $e->created_at->format('Y-m-d H:i') }}</td>
                                                        <td class="px-4 py-3">
                                                            <div class="font-medium">{{ $at ?: '—' }}</div>
                                                            <div class="flex items-center gap-2 mt-1">
                                                                <span
                                                                    class="text-xs text-gray-400">{{ $ak }}</span>
                                                                <span
                                                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] {{ $cls }}">{{ $label }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <span
                                                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $pill($e->score) }}">
                                                                {{ $e->score ?? '—' }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <div class="flex items-center gap-2">
                                                                <a href="{{ route('admin.evaluations.show', $e->id) }}"
                                                                    class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 hover:bg-gray-50">
                                                                    🔍 Detail
                                                                </a>
                                                                <a href="{{ route('admin.submissions.history', ['assignment' => $e->submission->assignment_id, 'user' => $u->id]) }}"
                                                                    class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 hover:bg-gray-50">
                                                                    📜 Riwayat (soal ini)
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
