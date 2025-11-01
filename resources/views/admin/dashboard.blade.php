<x-app-layout>
    {{-- HEADER GRADIENT --}}
    <x-slot name="header">
        <div class="relative overflow-hidden rounded-2xl">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 opacity-90">
            </div>
            <div class="relative flex items-center justify-between p-5 text-white">
                <div>
                    <h2 class="font-semibold text-xl leading-tight">Admin Dashboard</h2>
                    <p class="text-sm text-white/90">Ringkasan sistem penilaian & aktivitas submission.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.evaluations.index') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-white/15 px-3 py-1.5 text-sm ring-1 ring-white/30 hover:bg-white/25">
                        🔍 Evaluations
                    </a>
                    <a href="{{ route('admin.submissions.index') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-white/15 px-3 py-1.5 text-sm ring-1 ring-white/30 hover:bg-white/25">
                        📄 Submissions
                    </a>
                    <a href="{{ route('admin.students.index') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-white/15 px-3 py-1.5 text-sm ring-1 ring-white/30 hover:bg-white/25">
                        👥 Casis
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        {{-- METRICS COLORFUL --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="rounded-2xl p-4 text-white shadow bg-gradient-to-br from-sky-500 to-indigo-600">
                <div class="text-xs uppercase/relaxed opacity-90">Total Submissions</div>
                <div class="mt-1 text-3xl font-semibold">{{ $metrics['total_submissions'] }}</div>
            </div>
            <div class="rounded-2xl p-4 text-white shadow bg-gradient-to-br from-amber-500 to-orange-600">
                <div class="text-xs uppercase/relaxed opacity-90">In Queue</div>
                <div class="mt-1 text-3xl font-semibold">{{ $metrics['pending_queue'] }}</div>
            </div>
            <div class="rounded-2xl p-4 text-white shadow bg-gradient-to-br from-emerald-500 to-teal-600">
                <div class="text-xs uppercase/relaxed opacity-90">Evaluations</div>
                <div class="mt-1 text-3xl font-semibold">{{ $metrics['evaluations_total'] }}</div>
            </div>
            <div class="rounded-2xl p-4 text-white shadow bg-gradient-to-br from-fuchsia-500 to-purple-600">
                <div class="text-xs uppercase/relaxed opacity-90">Today</div>
                <div class="mt-1 text-3xl font-semibold">{{ $metrics['evaluations_today'] }}</div>
            </div>
            <div class="rounded-2xl p-4 text-white shadow bg-gradient-to-br from-rose-500 to-pink-600">
                <div class="text-xs uppercase/relaxed opacity-90">Average Score</div>
                <div class="mt-1 text-3xl font-semibold">{{ $metrics['avg_score'] ?? '—' }}</div>
            </div>
        </div>

        {{-- EVALUASI TERBARU --}}
        <div class="shadow rounded-2xl p-4 border border-indigo-100">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="font-semibold text-indigo-900">Evaluasi Terbaru</h3>
                <a href="{{ route('admin.evaluations.index') }}"
                    class="text-sm rounded-lg border border-indigo-200 px-3 py-1.5 text-indigo-700 hover:bg-indigo-50">Lihat
                    semua</a>
            </div>

            @php
                $scoreCls = fn($s) => is_null($s)
                    ? 'bg-gray-100 text-gray-700'
                    : ($s >= 85
                        ? 'bg-emerald-100 text-emerald-700'
                        : ($s >= 70
                            ? 'bg-amber-100 text-amber-800'
                            : 'bg-rose-100 text-rose-700'));
                $badge = function ($key) {
                    $key = strtolower($key);
                    return str_contains($key, 'sqli')
                        ? ['SQLi', 'bg-rose-100 text-rose-700 ring-1 ring-rose-200']
                        : (str_contains($key, 'xss')
                            ? ['XSS', 'bg-amber-100 text-amber-800 ring-1 ring-amber-200']
                            : (str_contains($key, 'openredirect')
                                ? ['Open Redirect', 'bg-purple-100 text-purple-700 ring-1 ring-purple-200']
                                : (str_contains($key, 'idor')
                                    ? ['IDOR', 'bg-sky-100 text-sky-700 ring-1 ring-sky-200']
                                    : (str_contains($key, 'lfi')
                                        ? ['LFI', 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200']
                                        : ['Other', 'bg-gray-100 text-gray-700 ring-1 ring-gray-200']))));
                };
            @endphp

            @if ($latest->isEmpty())
                <div class="py-10 text-center text-gray-500">Belum ada evaluasi.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-indigo-50 text-indigo-900/90">
                            <tr>
                                <th class="px-4 py-3 text-left">Waktu</th>
                                <th class="px-4 py-3 text-left">Assignment</th>
                                <th class="px-4 py-3 text-left">Casis</th>
                                <th class="px-4 py-3 text-left">Score</th>
                                <th class="px-4 py-3 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($latest as $e)
                                @php
                                    [$lab, $cls] = $badge($e->submission->assignment->key);
                                @endphp
                                <tr class="hover:bg-indigo-50/40">
                                    <td class="px-4 py-3 text-gray-600">{{ $e->created_at->format('Y-m-d H:i') }}</td>

                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $e->submission->assignment->title }}
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span
                                                class="text-xs text-gray-400">{{ $e->submission->assignment->key }}</span>
                                            <span
                                                class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] {{ $cls }}">{{ $lab }}</span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="text-gray-800">{{ optional($e->submission->user)->name ?? '-' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ optional($e->submission->user)->email ?? '-' }}</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $scoreCls($e->score) }}">
                                            {{ $e->score ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.evaluations.show', $e->id) }}"
                                                class="inline-flex items-center rounded-lg border border-indigo-200 px-3 py-1.5 text-indigo-700 hover:bg-white shadow-sm">
                                                Detail
                                            </a>
                                            <a href="{{ route('admin.submissions.history', [
                                                'assignment' => $e->submission->assignment_id,
                                                'user' => $e->submission->user_id,
                                            ]) }}"
                                                class="inline-flex items-center rounded-lg border border-indigo-200 px-3 py-1.5 text-indigo-700 hover:bg-white shadow-sm">
                                                Riwayat
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

        {{-- TOP ASSIGNMENTS --}}
        <div class="shadow rounded-2xl p-4 border border-violet-100">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="font-semibold text-violet-900">Assignment Terpopuler (berdasarkan jumlah submit)</h3>
                <a href="{{ route('admin.submissions.index') }}"
                    class="text-sm rounded-lg border border-violet-200 px-3 py-1.5 text-violet-700 hover:bg-violet-50">Kelola</a>
            </div>

            @if ($perAssignment->isEmpty())
                <div class="py-8 text-center text-gray-500">Belum ada data submission.</div>
            @else
                <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($perAssignment as $a)
                        <li
                            class="rounded-xl border border-violet-200 p-4 bg-gradient-to-br from-violet-50 to-indigo-50 hover:from-violet-100 hover:to-indigo-100 transition">
                            <div class="font-medium text-gray-900">{{ $a->title }}</div>
                            <div class="text-xs text-gray-500 mb-2">{{ $a->key }}</div>
                            <div class="text-sm">
                                <span
                                    class="rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs ring-1 ring-indigo-200">
                                    {{ $a->submissions_count }} submit
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>
