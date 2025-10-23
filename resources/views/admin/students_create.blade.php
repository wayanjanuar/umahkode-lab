<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden rounded-2xl">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 opacity-90"></div>
            <div class="relative flex items-center justify-between p-5 text-white">
                <div>
                    <h2 class="font-semibold text-xl">Tambah Akun Siswa</h2>
                    <p class="text-sm text-white/90">Buat akun siswa baru dengan role otomatis <strong>student</strong>.</p>
                </div>
                <a href="{{ route('admin.students.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-white/15 px-3 py-1.5 text-sm ring-1 ring-white/30 hover:bg-white/25">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="rounded-2xl shadow border border-emerald-100 overflow-hidden">
            <form method="POST" action="{{ route('admin.students.store') }}" class="bg-white p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @error('name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @error('email') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Password (opsional)
                        <span class="text-gray-400 font-normal text-xs">kosongkan untuk generate otomatis</span>
                    </label>
                    <input type="text" name="password"
                           class="mt-1 w-full rounded-xl border border-emerald-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @error('password') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2 flex items-center gap-2">
                    <button class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-emerald-600 to-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:from-emerald-700 hover:to-green-700 focus:ring-2 focus:ring-emerald-500">
                        Simpan
                    </button>
                    <a href="{{ route('admin.students.index') }}"
                       class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 px-4 py-2 text-sm text-emerald-700 hover:bg-white shadow-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <p class="mt-4 text-xs text-gray-400">
            Catatan: Password otomatis akan ditampilkan sekali di notifikasi setelah pembuatan akun.
        </p>
    </div>
</x-app-layout>
