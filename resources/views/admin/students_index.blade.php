<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden rounded-2xl">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 opacity-90"></div>
            <div class="relative flex items-center justify-between p-5 text-white">
                <div>
                    <h2 class="font-semibold text-xl">Daftar Akun Casis</h2>
                    <p class="text-sm text-white/90">Kelola akun Casis (buat, reset password, hapus).</p>
                </div>
                <a href="{{ route('admin.students.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-white/15 px-3 py-1.5 text-sm ring-1 ring-white/30 hover:bg-white/25">
                    ➕ Tambah Casis
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('message'))
            <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 p-3 text-emerald-800">
                {{ session('message') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-md border border-rose-200 bg-rose-50 p-3 text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl shadow border border-indigo-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-violet-50 border-b border-indigo-100 px-4 sm:px-6 py-3">
                <form method="GET" class="flex gap-2">
                    <input type="search" name="q" value="{{ $q }}"
                           class="flex-1 rounded-xl border border-indigo-200 bg-white/80 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="Cari nama atau email Casis…">
                    <button class="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-4 py-2 text-sm font-medium hover:from-indigo-700 hover:to-violet-700">Cari</button>
                    <a href="{{ route('admin.students.index') }}" class="rounded-xl border border-indigo-200 px-3 py-2 text-sm text-indigo-700 hover:bg-white">Reset</a>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-indigo-50 text-indigo-900/90">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Dibuat</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($users as $u)
                            <tr class="hover:bg-indigo-50/40">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $u->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $u->email }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $u->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.students.reset', $u->id) }}">
                                            @csrf
                                            <button class="inline-flex items-center rounded-lg border border-violet-200 px-3 py-1.5 text-violet-700 hover:bg-white shadow-sm"
                                                    onclick="return confirm('Reset password untuk {{ $u->email }} ?')">
                                                Reset Password
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.students.destroy', $u->id) }}">
                                            @csrf @method('DELETE')
                                            <button class="inline-flex items-center rounded-lg border border-rose-200 px-3 py-1.5 text-rose-700 hover:bg-white shadow-sm"
                                                    onclick="return confirm('Hapus akun {{ $u->email }} ?')">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10">
                                    <div class="text-center text-gray-500">Belum ada akun Casis.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white px-4 sm:px-6 py-3 border-t border-indigo-100">
                {{ $users->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
