<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800">
            Hasil Evaluasi #{{ $eval->id }}
        </h2>
    </x-slot>

    @php
        $weights = [
            'pemahaman' => 15,
            'metodologi' => 20,
            'ketepatan' => 30,
            'analisis' => 30,
            'waktu' => 5,
        ];

        $components = $eval->components ?? [];
        $score = $eval->score ?? 0;
    @endphp

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-xl p-6 space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Assignment</p>
                    <p class="font-semibold">{{ $eval->submission->assignment->title }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Siswa</p>
                    <p class="font-semibold">
                        {{ optional($eval->submission->user)->name ?? '-' }}
                        <span class="text-xs text-gray-500">({{ optional($eval->submission->user)->email }})</span>
                    </p>
                </div>
            </div>

            <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 p-4 rounded-xl border border-emerald-200">
                <p class="text-sm font-semibold text-emerald-800">Nilai Akhir</p>
                <div class="text-3xl font-bold text-emerald-700">{{ $score }}/100</div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Rincian Penilaian Berbobot</h3>
                <div class="space-y-3">
                    @foreach ($weights as $key => $w)
                        @php
                            $val = $components[$key] ?? 0;
                            $contrib = round($val * ($w / 100), 2);
                        @endphp
                        <div class="p-3 rounded-lg border bg-white shadow-sm">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="font-medium capitalize">{{ ucfirst($key) }}</span>
                                    <span class="text-xs text-gray-500">({{ $w }}%)</span>
                                </div>
                                <div class="text-sm font-semibold text-gray-700">
                                    {{ $val }}/100
                                    <span class="text-xs text-gray-500">(+{{ $contrib }})</span>
                                </div>
                            </div>
                            <div class="mt-2 h-2 bg-gray-100 rounded overflow-hidden">
                                <div class="h-full bg-emerald-500" style="width: {{ $val }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-1">Feedback AI</h3>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-800 whitespace-pre-wrap">
                    {!! nl2br(e($eval->feedback ?? 'Belum ada feedback.')) !!}
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-1">Kode Jawaban Siswa</h3>
                <pre class="bg-slate-900 text-green-100 text-xs p-4 rounded-lg overflow-x-auto shadow-inner">{{ $eval->submission->source_code }}</pre>
            </div>
        </div>
    </div>
</x-app-layout>
