<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Security Lab – Latihan Keamanan Web</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gradient-to-br from-slate-50 via-indigo-50 to-violet-100 min-h-screen flex flex-col">
    {{-- Navbar --}}
    <nav class="bg-white/70 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto object-contain">
                <span class="font-semibold text-slate-700 text-lg">Security Lab</span>
            </a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="text-sm font-medium text-slate-700 hover:text-indigo-600">Dashboard</a>
                @else
                    <a href="{{ route('login') }}"
                       class="rounded-lg border border-indigo-300 text-indigo-700 hover:bg-indigo-50 text-sm px-4 py-1.5 font-medium">Masuk</a>
                    <a href="{{ route('register') }}"
                       class="rounded-lg bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-sm px-4 py-1.5 font-medium shadow hover:from-indigo-700 hover:to-violet-700 transition">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="flex-1 flex flex-col items-center justify-center text-center px-6 py-16">
        <div class="max-w-3xl">
            <div class="flex justify-center mb-6">
                <div class="size-20 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 11c0-1.105-.895-2-2-2H7a2 2 0 00-2 2v7h10v-7zM7 11V9a5 5 0 0110 0v2m0 0h3a2 2 0 012 2v7H2v-7a2 2 0 012-2h3z"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-800 mb-4">
                Latih Skill <span class="text-indigo-600">Keamanan Web</span> Anda
            </h1>
            <p class="text-slate-600 text-base sm:text-lg leading-relaxed mb-8">
                Platform pelatihan interaktif untuk memahami dan memperbaiki kerentanan
                <strong>SQL Injection, XSS, IDOR, LFI, dan Open Redirect</strong>.
                Uji kemampuan Anda secara langsung dan dapatkan penilaian otomatis berbasis AI.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('register') }}"
                   class="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-6 py-3 font-medium shadow hover:from-indigo-700 hover:to-violet-700 transition">
                   Mulai Sekarang
                </a>
                <a href="{{ route('login') }}"
                   class="rounded-xl border border-indigo-300 text-indigo-700 hover:bg-indigo-50 px-6 py-3 font-medium transition">
                   Sudah Punya Akun
                </a>
            </div>
        </div>
    </section>

    {{-- Feature Section --}}
    <section class="bg-white border-t border-slate-200 py-16">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h2 class="text-2xl font-bold text-slate-800 mb-8">Apa yang Akan Kamu Pelajari?</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="p-6 rounded-xl bg-gradient-to-br from-red-50 to-rose-100 border border-red-200">
                    <h3 class="font-semibold text-red-700 mb-1">SQL Injection</h3>
                    <p class="text-sm text-red-600">Pelajari bagaimana query disalahgunakan dan cara mencegahnya.</p>
                </div>
                <div class="p-6 rounded-xl bg-gradient-to-br from-yellow-50 to-amber-100 border border-yellow-200">
                    <h3 class="font-semibold text-yellow-700 mb-1">Cross-Site Scripting (XSS)</h3>
                    <p class="text-sm text-yellow-700">Pahami penyisipan script berbahaya dan teknik sanitasi input.</p>
                </div>
                <div class="p-6 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-100 border border-blue-200">
                    <h3 class="font-semibold text-indigo-700 mb-1">IDOR</h3>
                    <p class="text-sm text-indigo-700">Temukan risiko akses data tanpa otorisasi dan pencegahannya.</p>
                </div>
                <div class="p-6 rounded-xl bg-gradient-to-br from-emerald-50 to-green-100 border border-emerald-200">
                    <h3 class="font-semibold text-emerald-700 mb-1">LFI / RFI</h3>
                    <p class="text-sm text-emerald-700">Eksplorasi Local File Inclusion dan mitigasinya di PHP.</p>
                </div>
                <div class="p-6 rounded-xl bg-gradient-to-br from-purple-50 to-violet-100 border border-purple-200">
                    <h3 class="font-semibold text-purple-700 mb-1">Open Redirect</h3>
                    <p class="text-sm text-purple-700">Pelajari validasi URL untuk mencegah redirect berbahaya.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="py-6 text-center text-sm text-slate-500 bg-white border-t border-slate-200">
        © {{ date('Y') }} <strong>Security Lab</strong> — Dibuat untuk edukasi keamanan web.
    </footer>
</body>
</html>
