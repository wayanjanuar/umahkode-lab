<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">Ringkasan Submission Siswa</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-4 overflow-x-auto">

            {{-- Filter sederhana --}}
            <form method="GET" class="mb-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                <input type="number" name="user_id" value="{{ request('user_id') }}" placeholder="Filter user_id"
                       class="rounded border-gray-300">
                <input type="number" name="assignment_id" value="{{ request('assignment_id') }}" placeholder="Filter assignment_id"
                       class="rounded border-gray-300">
                <button class="rounded bg-blue-600 text-white px-3 py-2 hover:bg-blue-700">Filter</button>
            </form>

            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2 pr-4">Siswa</th>
                        <th class="py-2 pr-4">Assignment</th>
                        <th class="py-2 pr-4">Total Submit</th>
                        <th class="py-2 pr-4">Terakhir</th>
                        <th class="py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr class="border-b">
                            <td class="py-2 pr-4">
                                {{ optional($r->user)->name ?? '-' }}<br>
                                <span class="text-gray-500 text-xs">{{ optional($r->user)->email }}</span>
                            </td>
                            <td class="py-2 pr-4">
                                <div class="font-medium">{{ $r->assignment->title ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $r->assignment->key ?? '' }}</div>
                            </td>
                            <td class="py-2 pr-4 font-semibold">{{ $r->total }}</td>
                            <td class="py-2 pr-4 text-gray-600">{{ \Carbon\Carbon::parse($r->last_at)->format('Y-m-d H:i') }}</td>
                            <td class="py-2">
                                <a class="inline-flex items-center rounded border px-3 py-1.5 hover:bg-gray-50"
                                   href="{{ route('admin.submissions.history', [$r->assignment_id, $r->user_id]) }}">
                                    Lihat Riwayat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-500">Belum ada submission.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $rows->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
