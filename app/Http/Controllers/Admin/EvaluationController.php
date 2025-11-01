<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        // 1) PAGINASI PER USER yang punya evaluations (pakai JOIN + GROUP BY aman utk ONLY_FULL_GROUP_BY)
        $users = User::query()
            ->select([
                'users.id',
                'users.name',
                'users.email',
            ])
            ->join('submissions', 'submissions.user_id', '=', 'users.id')
            ->join('evaluations', 'evaluations.submission_id', '=', 'submissions.id')
            ->when($q, function ($w) use ($q) {
                $w->where(function ($s) use ($q) {
                    $s->where('users.name', 'like', "%{$q}%")
                        ->orWhere('users.email', 'like', "%{$q}%");
                });
            })
            // agregat per user
            ->selectRaw('COUNT(evaluations.id)   AS evals_count')
            ->selectRaw('AVG(evaluations.score)  AS avg_score')
            ->selectRaw('MAX(evaluations.score)  AS max_score')
            ->selectRaw('MIN(evaluations.score)  AS min_score')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('users.name')
            ->paginate(25)
            ->withQueryString();

        // 2) Evaluations utk user di halaman ini (prefetch & group by user_id)
        $userIds = $users->pluck('id');

        $evalsByUser = Evaluation::with(['submission.assignment', 'submission.user'])
            ->whereHas('submission', fn($s) => $s->whereIn('user_id', $userIds))
            // ikutkan filter judul/key kalau ada q
            ->when($q, function ($qe) use ($q) {
                $qe->whereHas('submission.assignment', function ($qa) use ($q) {
                    $qa->where('title', 'like', "%{$q}%")
                        ->orWhere('key', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->get()
            ->groupBy(fn($e) => optional($e->submission)->user_id);

        return view('admin.evaluations', [
            'users' => $users,       // paginator user (tanpa duplikasi lintas halaman)
            'evalsByUser' => $evalsByUser, // evaluations per user_id
            'q' => $q,
        ]);
    }

    public function show($id)
    {
        $eval = Evaluation::with('submission.user', 'submission.assignment')->findOrFail($id);
        return view('admin.show_eval', compact('eval'));
    }

    public function downloadPdf(User $user)
    {
        $evals = Evaluation::with('submission.assignment')
            ->whereHas('submission', fn($q) => $q->where('user_id', $user->id))
            ->get();

        $weights = [
            'pemahaman' => 15,
            'metodologi' => 20,
            'ketepatan' => 30,
            'analisis' => 30,
            'waktu' => 5,
        ];

        // Hitung rata-rata per komponen
        $avg = [];
        foreach ($weights as $k => $w) {
            $vals = $evals->pluck("components.$k")->filter(fn($v) => is_numeric($v));
            $avg[$k] = $vals->count() ? round($vals->avg(), 2) : 0;
        }

        // Hitung nilai akhir
        $final = 0;
        foreach ($weights as $k => $w) {
            $final += $avg[$k] * ($w / 100);
        }

        // Konversi ke skala 1–5
        $likert = match (true) {
            $final > 80 => 5,
            $final > 60 => 4,
            $final > 40 => 3,
            $final > 20 => 2,
            default => 1,
        };

        // Data untuk view PDF
        $data = [
            'user' => $user,
            'evals' => $evals,
            'avg' => $avg,
            'weights' => $weights,
            'finalPercent' => $final,
            'finalLikert' => $likert,
            'date' => now()->format('d M Y H:i'),
        ];

        $pdf = Pdf::loadView('admin.pdf_summary', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('Rekap-Nilai-' . $user->name . '.pdf');
    }
}
