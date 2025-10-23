<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-gray-800">
                Hasil Evaluasi #{{ $eval->id }}
            </h2>
            <a href="{{ route('admin.evaluations.index') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-red-600 text-white px-3 py-1.5 text-sm hover:bg-red-700 transition">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-2xl p-6 space-y-6 border border-gray-100">

            {{-- Info dasar --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Assignment</p>
                    <p class="font-semibold text-gray-800">
                        {{ $eval->submission->assignment->key }} — {{ $eval->submission->assignment->title }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Siswa</p>
                    <p class="font-semibold text-gray-800">
                        {{ optional($eval->submission->user)->name ?? '—' }}
                        <span class="text-xs text-gray-500">({{ optional($eval->submission->user)->email }})</span>
                    </p>
                </div>
            </div>

            {{-- Nilai --}}
            <div class="rounded-xl bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 p-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-emerald-800">Nilai Akhir</p>
                    <span class="text-2xl font-bold text-emerald-700">{{ $eval->score ?? '—' }}/100</span>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-1">Penjelasan Singkat</h3>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap">
                    {{ $eval->feedback }}
                </div>
            </div>

            @php
                $studentAnswer = data_get($eval->artifacts, 'student_answer') ?: $eval->submission->source_code;
            @endphp
            <div>
                <h3 class="font-semibold text-gray-800 mb-1">Kode Jawaban Siswa</h3>
                <pre class="bg-slate-900 text-green-100 text-xs p-4 rounded-lg overflow-x-auto shadow-inner">{{ $studentAnswer }}</pre>
            </div>


        </div>
    </div>
</x-app-layout>
