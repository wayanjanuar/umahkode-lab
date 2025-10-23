<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl leading-tight">Evaluations (Admin)</h2>
                <p class="text-sm text-gray-500">Ringkasan hasil penilaian otomatis. Klik <em>Detail</em> untuk melihat feedback AI.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.submissions.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border px-3 py-1.5 text-sm hover:bg-gray-50">
                    📄 Ringkasan Submission
                </a>
                <a href="{{ url()->current() }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 text-white px-3 py-1.5 text-sm hover:bg-blue-700">
                    🔄 Refresh
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-xl p-4">
            @php
                $badge = function($key){
                    $map = [
                        'sqli'         => ['SQLi',        'bg-red-100 text-red-700 ring-1 ring-red-200'],
                        'xss'          => ['XSS',         'bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200'],
                        'openredirect' => ['Open Redirect','bg-purple-100 text-purple-700 ring-1 ring-purple-200'],
                        'idor'         => ['IDOR',        'bg-blue-100 text-blue-700 ring-1 ring-blue-200'],
                        'lfi'          => ['LFI',         'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200'],
                    ];
                    foreach($map as $k => $v){ if(str_contains(strtolower($key), $k)) return $v; }
                    return ['Other','bg-gray-100 text-gray-700 ring-1 ring-gray-200'];
                };
                $scoreColor = function($s){
                    if ($s === null) return 'bg-gray-100 text-gray-700';
                    if ($s >= 85)    return 'bg-emerald-100 text-emerald-700';
                    if ($s >= 70)    return 'bg-amber-100 text-amber-800';
                    return 'bg-rose-100 text-rose-700';
                };
            @endphp

            @if ($evals->count() === 0)
                <div class="py-16 text-center text-gray-500">
                    <div class="mx-auto mb-3 size-12 rounded-full bg-gray-100 flex items-center justify-center">🗂️</div>
                    Belum ada evaluasi. Minta siswa melakukan submit terlebih dahulu.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Assignment</th>
                                <th class="px-4 py-3 text-left">Student</th>
                                <th class="px-4 py-3 text-left">Score</th>
                                <th class="px-4 py-3 text-left">Created</th>
                                <th class="px-4 py-3 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($evals as $e)
                                @php
                                    [$label, $cls] = $badge($e->submission->assignment->key);
                                    $pill = $scoreColor($e->score);
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-700">{{ $e->id }}</td>

                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $e->submission->assignment->title }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs text-gray-400">{{ $e->submission->assignment->key }}</span>
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] {{ $cls }}">
                                                {{ $label }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="text-gray-800">{{ optional($e->submission->user)->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ optional($e->submission->user)->email ?? '-' }}</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $pill }}">
                                            {{ $e->score ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $e->created_at->format('Y-m-d H:i') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.evaluations.show', $e->id) }}"
                                               class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 hover:bg-gray-50">
                                                🔍 Detail
                                            </a>
                                            <a href="{{ route('admin.submissions.history', [
                                                    'assignment' => $e->submission->assignment_id,
                                                    'user'       => $e->submission->user_id
                                                ]) }}"
                                               class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 hover:bg-gray-50">
                                                📜 Riwayat
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $evals->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
