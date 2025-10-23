<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Evaluation;
use App\Models\Assignment;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $metrics = [
            'total_submissions' => Submission::count(),
            'pending_queue'     => Submission::whereIn('status', ['queued','running'])->count(),
            'evaluations_total' => Evaluation::count(),
            'evaluations_today' => Evaluation::whereDate('created_at', $today)->count(),
            'avg_score'         => round((float) Evaluation::avg('score'), 1),
        ];

        $latest = Evaluation::with(['submission.user','submission.assignment'])
            ->latest()->take(5)->get();

        $perAssignment = Assignment::withCount('submissions')
            ->orderByDesc('submissions_count')->take(5)->get();

        return view('admin.dashboard', compact('metrics','latest','perAssignment'));
    }
}
