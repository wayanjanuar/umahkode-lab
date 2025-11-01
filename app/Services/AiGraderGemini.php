<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiGraderGemini
{
    public function evaluate(string $source, string $assignmentKey): array
    {
        $endpoint = rtrim(config('services.gemini.endpoint'), '/'); // v1
        $apiKey   = config('services.gemini.key');

        if (!$apiKey) {
            throw new \RuntimeException('GEMINI_API_KEY kosong.');
        }

        // Urutan model: yang **pasti ada** di GLM v1 dgn API key
        $modelsTry = array_values(array_unique([
            config('services.gemini.model', 'gemini-2.0-flash'),
            'gemini-2.0-flash',
            'gemini-pro-latest',   // kalau akunmu punya
            'gemini-flash-latest', // kalau akunmu punya
        ]));

        // Prompt ringkas sesuai soal
        $topic = $this->topicFromKey($assignmentKey);
        $prompt = <<<TXT
Anda adalah penilai keamanan aplikasi web. Tugas: nilai jawaban siswa untuk soal {$topic}.
Kembalikan JSON dgn format:
{
  "components": {
    "pemahaman": 0-100,
    "metodologi": 0-100,
    "ketepatan": 0-100,
    "analisis": 0-100,
    "waktu": 0-100
  },
  "score": 0-100,
  "explain": "penjelasan singkat dalam Bahasa Indonesia"
}

Konteks:
- Kode/jawaban siswa:
----------------
{$source}
----------------

Kriteria ringkas:
- Pemahaman: mengerti risiko & tujuan perbaikan.
- Metodologi: langkah teknis tepat & sistematis.
- Ketepatan: hasil benar, aman/efektif.
- Analisis & Laporan: alasan, trade-off, kejelasan.
- Waktu: solusi ringkas/efisien (estimasi).
Pastikan semua nilai 0..100. Jangan tambah field lain.
TXT;

        $payload = [
            'contents' => [[
                'role'  => 'user',
                'parts' => [['text' => $prompt]],
            ]],
            'generationConfig' => [
                'temperature' => 0.2,
                'topP'        => 0.9,
            ],
        ];

        $lastErr = null;
        foreach ($modelsTry as $model) {
            try {
                $url = "{$endpoint}/models/{$model}:generateContent?key={$apiKey}";
                $res = Http::timeout(20)->post($url, $payload);

                if ($res->status() === 404) {
                    // model tidak tersedia di akun/regional ini -> lanjut coba model lain
                    \Log::warning("Gemini model {$model} failed", ['e' => "HTTP 404: "]);
                    continue;
                }

                if ($res->failed()) {
                    $lastErr = $res->body();
                    \Log::warning("Gemini model {$model} failed", ['e' => $lastErr]);
                    continue;
                }

                $data = $res->json();

                $text = data_get($data, 'candidates.0.content.parts.0.text')
                     ?? data_get($data, 'candidates.0.content.parts.0.inline_data.data');

                if (!$text) {
                    throw new \RuntimeException('Tidak ada teks dari model.');
                }

                // ambil JSON dari response (kadang model menambahkan penjelasan)
                $json = $this->extractJson($text);
                if (!$json) {
                    throw new \RuntimeException('Gagal parse JSON hasil AI.');
                }

                // Normalisasi output
                $components = array_map('floatval', array_merge([
                    'pemahaman' => 0, 'metodologi' => 0, 'ketepatan' => 0, 'analisis' => 0, 'waktu' => 0
                ], (array) data_get($json, 'components', [])));

                $score   = (int) round((float) ($json['score'] ?? $this->weighted($components)));
                $explain = (string) ($json['explain'] ?? '—');

                return [
                    'components' => $components,
                    'score'      => max(0, min(100, $score)),
                    'explain'    => $explain,
                ];
            } catch (\Throwable $e) {
                $lastErr = $e->getMessage();
                \Log::warning("Gemini model {$model} failed", ['e' => $lastErr]);
            }
        }

        \Log::error('Gemini evaluation failed; using fallback', ['error' => $lastErr]);

        // fallback default 50
        return [
            'components' => [
                'pemahaman' => 50, 'metodologi' => 50, 'ketepatan' => 50, 'analisis' => 50, 'waktu' => 50
            ],
            'score'   => 50,
            'explain' => 'Penilaian otomatis gagal, menggunakan nilai default 50 pada semua aspek.',
        ];
    }

    private function topicFromKey(string $key): string
    {
        $k = Str::lower($key);
        return match (true) {
            str_contains($k, 'sqli') => 'SQL Injection (SQLi)',
            str_contains($k, 'xss')  => 'Cross-Site Scripting (XSS)',
            str_contains($k, 'idor') => 'IDOR (Insecure Direct Object Reference)',
            str_contains($k, 'lfi')  => 'Local File Inclusion (LFI)',
            str_contains($k, 'open') && str_contains($k, 'redirect') => 'Open Redirect',
            default                   => 'Keamanan Aplikasi Web',
        };
    }

    private function weighted(array $c): float
    {
        // bobot sesuai kesepakatan
        $w = ['pemahaman'=>15,'metodologi'=>20,'ketepatan'=>30,'analisis'=>30,'waktu'=>5];
        $sum = 0;
        foreach ($w as $k => $b) {
            $sum += (($c[$k] ?? 0) * $b / 100);
        }
        return round($sum, 2);
    }

    private function extractJson(string $text): ?array
    {
        // Coba deteksi blok JSON
        if (preg_match('/\{.*\}/s', $text, $m)) {
            $j = json_decode($m[0], true);
            if (json_last_error() === JSON_ERROR_NONE) return $j;
        }
        return null;
    }
}
