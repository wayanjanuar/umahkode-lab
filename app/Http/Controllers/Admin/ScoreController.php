<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Evaluation;
use App\Models\FinalScore;
use App\Models\Submission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    /**
     * List rekap nilai (sudah di-generate).
     */
    public function index(Request $request)
    {
        $rows = FinalScore::with('user')->orderByDesc('generated_at')->paginate(20);

        return view('admin.scores.index', [
            'rows' => $rows,
        ]);
    }

    /**
     * Generate / refresh semua nilai akhir siswa.
     * - Ambil latest evaluation per (user, assignment)
     * - Hitung nilai per-soal berbobot (0..100)
     * - Rata-rata → average_percent (0..100)
     * - Jika user sudah submit SEMUA soal (completed == total), konversi ke 1..5 bulat
     */
    public function generate(Request $request)
    {
        $weights = config('score.weights', []);
        $totalAssignments = Assignment::count();

        // Ambil semua user yang punya submission/evaluation
        $userIds = Submission::query()->distinct()->pluck('user_id')->filter()->values();

        foreach ($userIds as $uid) {
            // latest evaluation per assignment untuk user ini
            $latestEvals = Evaluation::query()
                ->whereHas('submission', fn($q) => $q->where('user_id', $uid))
                ->with(['submission.assignment'])
                ->get()
                ->groupBy(fn($e) => $e->submission->assignment_id)
                ->map(fn($grp) => $grp->sortByDesc('id')->first()); // ambil paling baru

            $details = [];
            $perAssignmentScores = []; // 0..100 tiap assignment

            foreach ($latestEvals as $assignmentId => $eval) {
                $components = $this->normalizeComponents($eval->components ?? []);
                $scorePercent = $this->calcWeightedPercent($components, $weights);

                $details[] = [
                    'assignment_id'   => $assignmentId,
                    'assignment_key'  => $eval->submission->assignment->key ?? null,
                    'assignment_title'=> $eval->submission->assignment->title ?? null,
                    'score_percent'   => round($scorePercent, 2),
                    'components'      => $components, // sudah dipaksa percent 0..100
                ];

                $perAssignmentScores[] = $scorePercent;
            }

            $completed = count($perAssignmentScores);
            $avg = $completed > 0 ? round(array_sum($perAssignmentScores) / $completed, 2) : null;

            // Konversi ke skala 1..5 bulat HANYA jika semua soal sudah dikumpulkan
            $scale = null;
            if ($avg !== null && $totalAssignments > 0 && $completed === $totalAssignments) {
                // 0..100 → 1..5 bulat (20% per step)
                $scale = (int) max(1, min(5, round($avg / 20)));
            }

            FinalScore::updateOrCreate(
                ['user_id' => $uid],
                [
                    'completed_assignments' => $completed,
                    'total_assignments'     => $totalAssignments,
                    'average_percent'       => $avg,
                    'scale_1_5'             => $scale,
                    'details'               => $details,
                    'generated_at'          => Carbon::now(),
                ]
            );
        }

        return redirect()->route('admin.scores.index')
            ->with('message', 'Rekap nilai berhasil digenerate/diupdate.');
    }

    /**
     * Normalisasi komponen → percent 0..100 per key.
     * Input bisa:
     * - angka 1..5 (likert) → x20
     * - angka 0..100 (langsung)
     * - array ['likert'=>x] atau ['percent'=>x]
     */
    private function normalizeComponents($raw): array
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $raw = $decoded;
            }
        }
        $raw = is_array($raw) ? $raw : [];

        $out = [];
        foreach (['pemahaman','metodologi','ketepatan','analisa','waktu'] as $k) {
            $v = $raw[$k] ?? null;
            $out[$k] = $this->toPercent($v);
        }
        return $out;
    }

    /**
     * Satu nilai → percent 0..100
     */
    private function toPercent($v): float
    {
        if ($v === null) return 0.0;

        if (is_array($v)) {
            if (isset($v['percent'])) {
                return (float) max(0, min(100, $v['percent']));
            }
            if (isset($v['likert'])) {
                $lik = (float) $v['likert'];
                return (float) max(0, min(100, $lik * 20)); // 1→20 ... 5→100
            }
        }

        if (is_numeric($v)) {
            $num = (float) $v;
            if ($num <= 5) {
                return (float) max(0, min(100, $num * 20));
            }
            return (float) max(0, min(100, $num));
        }

        return 0.0;
    }

    /**
     * Hitung nilai berbobot: Σ( percent * (bobot/100) )
     */
    private function calcWeightedPercent(array $componentsPercent, array $weights): float
    {
        $sum = 0.0;
        foreach ($componentsPercent as $k => $pct) {
            $w = (float) ($weights[$k] ?? 0);
            $sum += ((float) $pct) * ($w / 100.0);
        }
        return $sum; // 0..100
    }
}
