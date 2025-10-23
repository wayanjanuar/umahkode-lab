<x-app-layout>
    {{-- HEADER GRADIENT --}}
    <x-slot name="header">
        <div class="relative overflow-hidden rounded-2xl">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 opacity-90"></div>
            <div class="relative flex items-center justify-between p-5 text-white">
                <div>
                    <h2 class="font-semibold text-xl leading-tight">Riwayat Submission</h2>
                    <p class="text-sm text-white/90">
                        {{ $user->name }} ({{ $user->email }}) — {{ $assignment->title }}
                        <span class="text-xs text-white/80">[{{ $assignment->key }}]</span>
                    </p>
                </div>
                <a href="{{ route('admin.submissions.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-white/15 px-3 py-1.5 text-sm ring-1 ring-white/30 hover:bg-white/25">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="rounded-2xl shadow border border-indigo-100 overflow-hidden">
            {{-- TOOLBAR --}}
            <div class="flex items-center justify-between gap-3 bg-gradient-to-r from-indigo-50 to-violet-50 px-4 sm:px-6 py-3 border-b border-indigo-100">
                <div class="text-sm text-indigo-900/90">
                    Total riwayat: <span class="font-semibold">{{ $subs->count() }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.evaluations.index') }}"
                       class="inline-flex items-center rounded-lg border border-indigo-200 px-3 py-1.5 text-sm text-indigo-700 hover:bg-white shadow-sm">Evaluations</a>
                    <a href="{{ route('admin.submissions.index') }}"
                       class="inline-flex items-center rounded-lg border border-indigo-200 px-3 py-1.5 text-sm text-indigo-700 hover:bg-white shadow-sm">Ringkasan</a>
                </div>
            </div>

            @php
                $statusPill = function($st) {
                    return match($st) {
                        'queued'    => 'bg-gray-100 text-gray-700 ring-1 ring-gray-200',
                        'running'   => 'bg-amber-100 text-amber-800 ring-1 ring-amber-200',
                        'evaluated' => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200',
                        'error'     => 'bg-rose-100 text-rose-700 ring-1 ring-rose-200',
                        default     => 'bg-gray-100 text-gray-700 ring-1 ring-gray-200',
                    };
                };
                $scorePill = function($s){
                    if (is_null($s)) return 'bg-gray-100 text-gray-700';
                    return $s >= 85 ? 'bg-emerald-100 text-emerald-700'
                         : ($s >= 70 ? 'bg-amber-100 text-amber-800'
                                     : 'bg-rose-100 text-rose-700');
                };
            @endphp

            {{-- TABLE --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-indigo-50 text-indigo-900/90">
                        <tr>
                            <th class="px-4 py-3 text-left">Waktu</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Skor</th>
                            <th class="px-4 py-3 text-left">Feedback Singkat</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($subs as $s)
                            @php
                                $score = optional($s->evaluation)->score;
                                $fb    = optional($s->evaluation)->feedback;
                                $short = $fb ? \Illuminate\Support\Str::limit($fb, 120) : '-';
                            @endphp
                            <tr class="hover:bg-indigo-50/40">
                                <td class="px-4 py-3 text-gray-700">{{ $s->created_at->format('Y-m-d H:i') }}</td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusPill($s->status) }}">
                                        {{ ucfirst($s->status) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $scorePill($score) }}">
                                        {{ $score ?? '—' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-gray-800">
                                    {{ $short }}
                                </td>

                                <td class="px-4 py-3">
                                    @if($s->evaluation)
                                        <a href="{{ route('admin.evaluations.show', $s->evaluation->id) }}"
                                           class="inline-flex items-center rounded-lg border border-violet-200 px-3 py-1.5 text-violet-700 hover:bg-white shadow-sm">
                                            Detail Evaluasi
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">Belum dievaluasi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10">
                                    <div class="text-center text-gray-500">
                                        <div class="mx-auto mb-2 size-10 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center ring-1 ring-indigo-200">🗂️</div>
                                        Belum ada submission untuk kombinasi ini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- FOOTER NOTE --}}
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-4 sm:px-6 py-3 border-t border-emerald-100 text-xs text-emerald-900/90">
                Tip: Klik <span class="font-medium">Detail Evaluasi</span> untuk melihat skor lengkap & feedback AI.
            </div>
        </div>
    </div>
</x-app-layout>
