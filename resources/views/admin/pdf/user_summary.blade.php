<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Nilai Peserta</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 6px; }
        h2 { font-size: 14px; margin: 14px 0 6px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 6px; vertical-align: top; }
        th { background: #f3f4f6; }
        .muted { color: #666; }
        .small { font-size: 11px; }
        .chip { display:inline-block; padding:2px 6px; border-radius: 10px; background:#eef2ff; }
        .box { border:1px solid #e5e7eb; padding:8px; border-radius:6px; }
        .mt-8 { margin-top: 8px; }
        .mb-8 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 4px; }
        .mb-12{ margin-bottom: 12px; }
        pre { background:#111827; color:#e5e7eb; padding:8px; border-radius:6px; overflow: hidden; white-space: pre-wrap; }
    </style>
</head>
<body>

    <h1>Laporan Nilai Peserta</h1>
    <div class="mb-12">
        <div><strong>Nama:</strong> {{ $user->name ?? '—' }}</div>
        <div><strong>Email:</strong> {{ $user->email ?? '—' }}</div>
        <div class="mt-8">
            <span class="chip"><strong>Rata-rata:</strong> {{ $avg ?: '—' }}</span>
            <span class="chip"><strong>Tertinggi:</strong> {{ $max ?: '—' }}</span>
            <span class="chip"><strong>Terendah:</strong> {{ $min ?: '—' }}</span>
        </div>
    </div>

    <h2>Daftar Evaluasi</h2>
    @if($evals->isEmpty())
        <div class="box">Belum ada evaluasi untuk peserta ini.</div>
    @else
        <table class="mb-12">
            <thead>
                <tr>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Soal</th>
                    <th style="width: 70px;">Skor</th>
                    <th>Komponen (0–100)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evals as $e)
                    @php
                        $as = $e->submission->assignment ?? null;
                        $c  = $e->components ?: [];
                        // pastikan integer 0..100
                        $c = is_array($c) ? $c : (json_decode($c, true) ?: []);
                        $norm = [
                            'pemahaman' => (int)($c['pemahaman'] ?? 0),
                            'metodologi'=> (int)($c['metodologi'] ?? 0),
                            'ketepatan' => (int)($c['ketepatan'] ?? 0),
                            'analisis'  => (int)($c['analisis'] ?? 0),
                            'waktu'     => (int)($c['waktu'] ?? 0),
                        ];
                    @endphp
                    <tr>
                        <td class="small">{{ $e->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <div><strong>{{ $as?->title ?? '—' }}</strong></div>
                            <div class="muted small">{{ $as?->key ?? '' }}</div>
                        </td>
                        <td style="text-align:center;"><strong>{{ $e->score ?? '—' }}</strong></td>
                        <td class="small">
                            <div><strong>Pemahaman ({{ $weights['pemahaman'] }}%)</strong>: {{ $norm['pemahaman'] }}</div>
                            <div><strong>Metodologi ({{ $weights['metodologi'] }}%)</strong>: {{ $norm['metodologi'] }}</div>
                            <div><strong>Ketepatan ({{ $weights['ketepatan'] }}%)</strong>: {{ $norm['ketepatan'] }}</div>
                            <div><strong>Analisis ({{ $weights['analisis'] }}%)</strong>: {{ $norm['analisis'] }}</div>
                            <div><strong>Waktu ({{ $weights['waktu'] }}%)</strong>: {{ $norm['waktu'] }}</div>
                        </td>
                    </tr>
                    {{-- Analisa AI + Jawaban Siswa --}}
                    @php
                        $aiExplain = data_get($e->artifacts, 'ai_explain');
                        $student   = data_get($e->artifacts, 'student_answer', $e->submission->source_code);
                        // batasi panjang jawaban supaya PDF tetap ringkas (opsional)
                        $student = is_string($student) ? (mb_strlen($student) > 2000 ? mb_substr($student,0,2000).' …' : $student) : '';
                    @endphp
                    <tr>
                        <td colspan="4">
                            <div class="mb-8">
                                <div class="mb-4"><strong>Penjelasan Singkat (AI):</strong></div>
                                <div class="box small">{{ $aiExplain ?: '—' }}</div>
                            </div>
                            <div>
                                <div class="mb-4"><strong>Jawaban / Kode Peserta:</strong></div>
                                <pre class="small">{{ $student }}</pre>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="muted small">© Satsiber TNI — Rekrutmen PA PK Sus Siber 2025</div>
</body>
</html>
