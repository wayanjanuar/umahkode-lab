<?php

namespace App\Services;

class ScoreCalculator
{
    public function calculate(array $scores): float
    {
        $weights = config('scoring.weights');

        $total = (
            ($scores['pemahaman_kasus'] ?? 0) * $weights['pemahaman_kasus'] +
            ($scores['langkah_teknis'] ?? 0) * $weights['langkah_teknis'] +
            ($scores['ketepatan_hasil'] ?? 0) * $weights['ketepatan_hasil'] +
            ($scores['analisa_laporan'] ?? 0) * $weights['analisa_laporan'] +
            ($scores['manajemen_waktu'] ?? 0) * $weights['manajemen_waktu']
        ) * 100;

        return round($total, 2);
    }
}
