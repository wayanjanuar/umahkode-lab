<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Evaluation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
    public function summaryPdf(Request $request, User $user)
    {
        // Ambil semua evaluation user
        $evals = Evaluation::with(['submission.assignment','submission.user'])
            ->whereHas('submission', fn($q) => $q->where('user_id', $user->id))
            ->orderByDesc('created_at')
            ->get();

        $avg = round((float) $evals->avg('score'), 2);
        $max = $evals->max('score');
        $min = $evals->min('score');

        $weights = [
            'pemahaman' => 15,
            'metodologi'=> 20,
            'ketepatan' => 30,
            'analisis'  => 30,
            'waktu'     => 5,
        ];

        $pdf = Pdf::loadView('admin.pdf.user_summary', compact('user','evals','avg','max','min','weights'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-nilai-'.$user->id.'.pdf');
    }
}
