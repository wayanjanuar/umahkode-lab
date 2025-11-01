<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Models\Evaluation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AiGraderGemini;

class EvaluateSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Berapa kali retry job ini (opsional) */
    public int $tries = 1;

    /** Timeout per eksekusi job (detik) */
    public int $timeout = 60;

    protected int $submissionId;

    public function __construct(int $submissionId)
    {
        $this->submissionId = $submissionId;
    }

    public function handle(): void
    {
        /** @var Submission $sub */
        $sub = Submission::with('assignment', 'user')->findOrFail($this->submissionId);
        $sub->update(['status' => 'running']);

        // Komponen yang diakui sistem
        $validKeys = ['pemahaman', 'metodologi', 'ketepatan', 'analisis', 'waktu'];

        // Normalisasi bentuk komponen → integer 0..100 per kunci valid
        $normalizeComponents = static function ($raw) use ($validKeys): array {
            // Jika string JSON, decode dulu
            if (is_string($raw)) {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $raw = $decoded;
                }
            }
            if (!is_array($raw)) {
                $raw = [];
            }

            $out = [];
            foreach ($validKeys as $k) {
                $v = $raw[$k] ?? null;

                // Dukung bentuk {percent:..} atau {likert:..}
                if (is_array($v)) {
                    if (array_key_exists('percent', $v)) {
                        $v = (float) $v['percent'];
                    } elseif (array_key_exists('likert', $v)) {
                        // 1..5 → 20..100 (dibulatkan)
                        $v = (float) $v['likert'] * 20;
                    } else {
                        $v = null;
                    }
                }

                if (!is_numeric($v)) {
                    $v = 0;
                }

                $v = max(0, min(100, (float) $v));
                $out[$k] = (int) round($v);
            }

            return $out;
        };

        try {
            /** @var AiGraderGemini $grader */
            $grader = app(AiGraderGemini::class);

            // Wajibnya: ['score'=>int, 'components'=>array|json, 'explain'=>string]
            $result = $grader->evaluate(
                (string) ($sub->source_code ?? ''),
                (string) ($sub->assignment->key ?? '')
            );

            $score      = (int) ($result['score'] ?? 0);
            $components = $normalizeComponents($result['components'] ?? []);
            $aiExplain  = trim((string) ($result['explain'] ?? ''));

            // Teks ringkas yang disimpan di kolom feedback (boleh dipakai untuk dump cepat)
            $feedbackText = "Nilai: {$score}/100\n\nPenjelasan singkat: " . ($aiExplain !== '' ? $aiExplain : '—');

            Evaluation::create([
                'submission_id' => $sub->id,
                'score'         => $score,
                'components'    => $components, // JSON 0..100 per komponen
                'feedback'      => $feedbackText,
                'artifacts'     => [
                    // Simpan analisa AI “bersih” agar di PDF/detail bisa diambil langsung.
                    'ai_explain'     => $aiExplain,
                    // Simpan kode jawaban untuk dilampirkan di PDF laporan.
                    'student_answer' => (string) ($sub->source_code ?? ''),
                ],
            ]);

            $sub->update(['status' => 'evaluated']);
        } catch (\Throwable $e) {
            // Log error biar gampang dilacak
            \Log::error('EvaluateSubmission: auto-grading failed', [
                'submission_id' => $sub->id,
                'message'       => $e->getMessage(),
            ]);

            // Fallback aman (semua komponen 50, skor 50)
            $fallbackComponents = [
                'pemahaman' => 50,
                'metodologi'=> 50,
                'ketepatan' => 50,
                'analisis'  => 50,
                'waktu'     => 50,
            ];
            $fallbackScore   = 50;
            $fallbackExplain = 'Penilaian otomatis gagal, menggunakan nilai default 50 pada semua aspek.';

            $feedbackText = "Nilai: {$fallbackScore}/100\n\nPenjelasan singkat: {$fallbackExplain}";

            Evaluation::create([
                'submission_id' => $sub->id,
                'score'         => $fallbackScore,
                'components'    => $fallbackComponents,
                'feedback'      => $feedbackText,
                'artifacts'     => [
                    'ai_explain'     => $fallbackExplain,
                    'student_answer' => (string) ($sub->source_code ?? ''),
                ],
            ]);

            $sub->update(['status' => 'error']);
        }
    }
}
