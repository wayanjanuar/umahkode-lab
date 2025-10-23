<?php

namespace App\Jobs;

use App\Models\Evaluation;
use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EvaluateSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;           
    public int $timeout = 60;        

    public function __construct(protected int $submissionId)
    {
        //
    }

    public function handle(): void
    {
        /** @var Submission $sub */
        $sub = Submission::with('assignment')->findOrFail($this->submissionId);
        $sub->update(['status' => 'running']);

        try {
            // Pakai AI Studio (API key) – service AiGraderGemini
            /** @var \App\Services\AiGraderGemini $grader */
            $grader  = app(\App\Services\AiGraderGemini::class);
            $result  = $grader->evaluate($sub->source_code ?? '', $sub->assignment->key);

            $score   = (int) ($result['score'] ?? 0);
            $explain = (string) ($result['explain'] ?? '');
            $answer  = (string) ($result['answer'] ?? ($sub->source_code ?? ''));

            Evaluation::create([
                'submission_id' => $sub->id,
                'score'         => $score,
                // feedback = penjelasan singkat dari Gemini (clean)
                'feedback'      => $explain,
                // meta ringan untuk audit
                'breakdown'     => [
                    'engine' => 'gemini',
                    'model'  => config('services.gemini.model'),
                ],
                // simpan jawaban siswa agar bisa ditampilkan rapi di UI
                'artifacts'     => [
                    'student_answer' => $answer,
                ],
            ]);

            $sub->update(['status' => 'evaluated']);
        } catch (\Throwable $e) {
            // Fallback agar sistem tetap menghasilkan evaluasi
            $src = trim($sub->source_code ?? '');

            Evaluation::create([
                'submission_id' => $sub->id,
                'score'         => 50,
                // Feedback singkat agar UI "Penjelasan Singkat" tetap bagus
                'feedback'      => 'Fallback lokal (API gagal).',
                'breakdown'     => [
                    'engine' => 'fallback',
                    'error'  => $e->getMessage(),
                ],
                'artifacts'     => [
                    'student_answer' => $src,
                ],
            ]);

            $sub->update(['status' => 'error']);
            \Log::error('AI grading failed', [
                'submission_id' => $sub->id,
                'exception'     => $e->getMessage(),
            ]);
        }
    }
}
