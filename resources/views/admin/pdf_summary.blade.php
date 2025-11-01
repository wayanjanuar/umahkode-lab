<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai – {{ $user->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1,h2,h3 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: center; }
        th { background-color: #f3f4f6; }
        .section { margin-bottom: 20px; }
        .summary-box { border: 1px solid #ccc; padding: 10px; margin-top: 10px; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <h2>REKAP NILAI PRAKTIK</h2>
    <p><b>Nama:</b> {{ $user->name }}<br>
       <b>Email:</b> {{ $user->email }}<br>
       <b>Tanggal Cetak:</b> {{ $date }}</p>

    <div class="section">
        <h3>1. Nilai Akhir</h3>
        <div class="summary-box">
            <p><b>Total Nilai:</b> {{ number_format($finalPercent, 2) }} / 100</p>
            <p><b>Skala 1–5:</b> {{ $finalLikert }}</p>
        </div>
    </div>

    <div class="section">
        <h3>2. Rincian Komponen Penilaian</h3>
        <table>
            <thead>
                <tr>
                    <th>Komponen</th>
                    <th>Bobot (%)</th>
                    <th>Rata-rata (0–100)</th>
                    <th>Kontribusi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($weights as $key => $w)
                    @php
                        $p = $avg[$key] ?? 0;
                        $contrib = round($p * ($w / 100), 2);
                        $label = match($key) {
                            'pemahaman' => 'Pemahaman Kasus',
                            'metodologi' => 'Langkah Teknis/Metodologi',
                            'ketepatan' => 'Ketepatan Hasil',
                            'analisis' => 'Analisis & Laporan',
                            'waktu' => 'Manajemen Waktu',
                            default => ucfirst($key)
                        };
                    @endphp
                    <tr>
                        <td class="text-left">{{ $label }}</td>
                        <td>{{ $w }}</td>
                        <td>{{ $p }}</td>
                        <td>{{ $contrib }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>3. Nilai per Soal</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Soal</th>
                    <th>Pem</th>
                    <th>Met</th>
                    <th>Tep</th>
                    <th>Analisis</th>
                    <th>Waktu</th>
                    <th>Score (100)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($evals as $i => $e)
                    @php $c = is_array($e->components) ? $e->components : json_decode($e->components ?? '[]', true); @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="text-left">{{ $e->submission->assignment->title ?? '-' }}</td>
                        <td>{{ $c['pemahaman'] ?? 0 }}</td>
                        <td>{{ $c['metodologi'] ?? 0 }}</td>
                        <td>{{ $c['ketepatan'] ?? 0 }}</td>
                        <td>{{ $c['analisis'] ?? 0 }}</td>
                        <td>{{ $c['waktu'] ?? 0 }}</td>
                        <td>{{ $e->score ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>4. Kesimpulan</h3>
        <p>Nilai akhir berdasarkan bobot rubrik resmi:
            Pemahaman Kasus (15%), Langkah Teknis/Metodologi (20%),
            Ketepatan Hasil (30%), Analisis & Laporan (30%), dan Manajemen Waktu (5%).</p>
        <p><b>Skala Konversi:</b>  
           81–100 = 5, 61–80 = 4, 41–60 = 3, 21–40 = 2, 0–20 = 1</p>
    </div>

    <div class="center" style="margin-top: 40px;">
        <p>.........................................................<br>
        <b>Panitia Penilai Praktik Siber</b></p>
    </div>
</body>
</html>
