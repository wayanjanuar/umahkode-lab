<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl leading-tight">Ringkasan Submission Siswa</h2>
                <p class="text-sm text-gray-500">Rekap per siswa & per soal. Gunakan filter di bawah.</p>
            </div>
            <a href="{{ url()->current() }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 text-white px-3 py-1.5 text-sm hover:bg-blue-700">
                🔄 Refresh
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm ring-1 ring-gray-100 sm:rounded-xl p-4">

            {{-- FILTER: 1 baris (Nama siswa + Nama soal (select) + tombol) --}}
            <form method="GET" class="mb-4 flex flex-col sm:flex-row gap-2 items-stretch sm:items-end">
                {{-- Nama siswa --}}
                <div class="sm:flex-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama Siswa</label>
                    <div class="relative">
                        <input type="text" name="student" value="{{ request('student') }}"
                               placeholder="mis. Budi, Siti, atau email"
                               class="w-full rounded-lg border-gray-300 ps-9 pe-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="absolute left-3 top-2.5 text-gray-400">👤</span>
                    </div>
                </div>

                {{-- Nama soal (select by key) --}}
                <div class="sm:w-80">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama Soal</label>
                    <select name="assignment" class="w-full rounded-lg border-gray-300 py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="this.form.submit()">
                        <option value="">— Semua soal —</option>
                        @foreach ($assignmentOptions as $opt)
                            <option value="{{ $opt->key }}" @selected(request('assignment')===$opt->key)>
                                {{ $opt->title }} ({{ $opt->key }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="sm:w-auto flex gap-2">
                    <button class="rounded-lg bg-blue-600 text-white px-4 py-2 font-medium hover:bg-blue-700">
                        🔍 Filter
                    </button>
                    @if(request('student') || request('assignment'))
                        <a href="{{ route('admin.submissions.index') }}"
                           class="rounded-lg border px-4 py-2 hover:bg-gray-50">✖ Reset</a>
                    @endif
                </div>
            </form>

            {{-- Chips filter aktif --}}
            @if(request('student') || request('assignment'))
                <div class="mb-4 flex flex-wrap gap-2 text-xs">
                    @if(request('student'))
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 text-blue-700 px-3 py-1 ring-1 ring-blue-200">
                            👤 Siswa: <strong>{{ request('student') }}</strong>
                        </span>
                    @endif
                    @if(request('assignment'))
                        <span class="inline-flex items-center gap-1 rounded-full bg-violet-50 text-violet-700 px-3 py-1 ring-1 ring-violet-200">
                            🧩 Soal: <strong>{{ request('assignment') }}</strong>
                        </span>
                    @endif
                </div>
            @endif

            {{-- Tabel --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left">Siswa</th>
                            <th class="px-4 py-3 text-left">Assignment</th>
                            <th class="px-4 py-3 text-left">Total Submit</th>
                            <th class="px-4 py-3 text-left">Terakhir</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($rows as $r)
                            @php
                                $studentName  = optional($r->user)->name ?? '-';
                                $studentEmail = optional($r->user)->email ?? '-';
                                $title = $r->assignment->title ?? '-';
                                $akey  = $r->assignment->key ?? '';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $studentName }}</div>
                                    <div class="text-xs text-gray-500">{{ $studentEmail }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $title }}</div>
                                    <div class="text-xs text-gray-500">{{ $akey }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 px-2.5 py-0.5 ring-1 ring-emerald-200">
                                        {{ $r->total }} kali
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ \Carbon\Carbon::parse($r->last_at)->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <a class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 hover:bg-gray-50"
                                       href="{{ route('admin.submissions.history', [$r->assignment_id, $r->user_id]) }}">
                                        📜 Lihat Riwayat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                    Belum ada submission yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $rows->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
