<?php

namespace App\Services;

use Carbon\Carbon;

class AutoGrader
{
    public function evaluate(string $source, string $assignmentKey, $submittedAt = null): array
    {
        $cfg     = config('score');
        $weights = $cfg['weights'];

        // ====== CONTOH HEURISTIK SEDERHANA (silakan sesuaikan per soal) ======
        // Kamu bisa membaca $assignmentKey dan menerapkan rule per jenis soal.
        $pemahamanLikert  = $this->gradePemahaman($source, $assignmentKey);      // 1–5
        $metodologiLikert = $this->gradeMetodologi($source, $assignmentKey);     // 1–5
        $ketepatanLikert  = $this->gradeKetepatan($source, $assignmentKey);      // 1–5
        $analisisLikert   = $this->gradeAnalisis($source);                        // 1–5
        $waktuLikert      = $this->gradeWaktu($submittedAt);                      // 1–5

        $components = [
            'pemahaman'  => $pemahamanLikert,
            'metodologi' => $metodologiLikert,
            'ketepatan'  => $ketepatanLikert,
            'analisis'   => $analisisLikert,
            'waktu'      => $waktuLikert,
        ];

        // konversi Likert (1..5) → persen (0..100) lalu bobot
        $toPct = fn($l) => max(1, min(5, (int)$l)) * 25 - 25; // 1→0, 5→100
        $final = 0;
        foreach ($components as $k => $l) {
            $final += $toPct($l) * ($weights[$k] / 100);
        }
        $final = (int) round($final);

        // ringkasan singkat lokal (fallback jika AI tak aktif)
        $explain = "Skor berdasarkan rubric:\n".
                   "• Pemahaman {$components['pemahaman']}/5, ".
                   "• Metodologi {$components['metodologi']}/5, ".
                   "• Ketepatan {$components['ketepatan']}/5, ".
                   "• Analisis {$components['analisis']}/5, ".
                   "• Waktu {$components['waktu']}/5.";

        return [
            'score'      => $final,           // 0–100
            'components' => $components,      // simpan di DB
            'explain'    => $explain,         // ringkasan fallback
            'answer'     => $source,          // tampilkan sebagai “jawaban siswa”
        ];
    }

    private function gradePemahaman(string $src, string $key): int
    {
        // contoh rule ringan; sesuaikan
        return str_contains(strtolower($src), 'risk') || str_contains(strtolower($src), 'sanitize') ? 4 : 3;
    }
    private function gradeMetodologi(string $src, string $key): int
    {
        $good = ['prepared', 'parameter', 'filter_input', 'regex', 'validation'];
        $hits = collect($good)->filter(fn($g)=>str_contains(strtolower($src), $g))->count();
        return $hits >= 3 ? 4 : ($hits >= 1 ? 3 : 2);
    }
    private function gradeKetepatan(string $src, string $key): int
    {
        // misal: untuk XSS, penggunaan htmlspecialchars → indikasi benar
        return str_contains(strtolower($src), 'htmlspecialchars') ? 5 : 2;
    }
    private function gradeAnalisis(string $src): int
    {
        // deteksi adanya “hasil/temuan/kesimpulan/rekomendasi”
        $tokens = ['hasil','temuan','kesimpulan','rekomendasi','bukti'];
        $hits   = collect($tokens)->filter(fn($t)=>str_contains(strtolower($src), $t))->count();
        return $hits >= 3 ? 4 : ($hits >= 1 ? 3 : 2);
    }
    private function gradeWaktu($submittedAt): int
    {
        // contoh: selalu 4 (tepat/nyaris tepat). Kamu bisa ganti berdasar deadline.
        return 4;
    }
}
