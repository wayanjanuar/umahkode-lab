<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Rekap Nilai Akhir Siswa</h2>
            <form method="POST" action="{{ route('admin.scores.generate') }}">
                @csrf
                <button
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 text-white px-3 py-1.5 text-sm hover:bg-indigo-700">
                    🔄 Generate Nilai
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('message'))
            <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 p-3 text-emerald-800">
                {{ session('message') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-xl overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Siswa</th>
                        <th class="px-4 py-3 text-left">Progress</th>
                        <th class="px-4 py-3 text-left">Rata-rata (%)</th>
                        <th class="px-4 py-3 text-left">Skala 1–5</th>
                        <th class="px-4 py-3 text-left">Terakhir Generate</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($rows as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $r->user->name ?? '—' }}</div>
                                <div class="text-xs text-gray-500">{{ $r->user->email ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm">
                                    {{ $r->completed_assignments }} / {{ $r->total_assignments }} soal
                                </span>
                                <div class="mt-1 h-2 w-48 bg-gray-100 rounded overflow-hidden">
                                    @php
                                        $pct = $r->total_assignments ? round(($r->completed_assignments/$r->total_assignments)*100) : 0;
                                    @endphp
                                    <div class="h-full bg-indigo-500" style="width: {{ $pct }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-semibold">
                                {{ $r->average_percent !== null ? number_format($r->average_percent,2) : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($r->scale_1_5)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        {{ $r->scale_1_5 }} / 5
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">Lengkap semua soal</div>
                                @else
                                    <span class="text-xs text-gray-500">Menunggu semua soal lengkap</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ optional($r->generated_at)->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                Belum ada rekap nilai. Klik <strong>Generate Nilai</strong> di kanan atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-4">
                {{ $rows->links() }}
            </div>
        </div>

        {{-- Catatan --}}
        <p class="text-xs text-gray-500 mt-3">
            * Skala 1–5 tanpa desimal hanya muncul jika siswa telah mengumpulkan semua soal. Jika belum, sistem hanya menampilkan rata-rata persentase.
        </p>
    </div>
</x-app-layout>
