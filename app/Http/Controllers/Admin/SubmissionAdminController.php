<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\User;
use App\Models\Assignment;
use Illuminate\Http\Request;

class SubmissionAdminController extends Controller
{
    // Ringkasan: total submit per (user, assignment)
    public function index(\Illuminate\Http\Request $request)
    {
        $student = trim((string) $request->query('student'));
        $assignmentKey = trim((string) $request->query('assignment'));

        // daftar pilihan untuk select
        $assignmentOptions = \App\Models\Assignment::query()
            ->orderBy('title')
            ->get(['id', 'title', 'key']);

        $rows = \App\Models\Submission::query()
            ->selectRaw('user_id, assignment_id, COUNT(*) as total, MAX(created_at) as last_at')
            ->when($student, function ($q) use ($student) {
                $q->whereHas('user', function ($uq) use ($student) {
                    $uq->where('name', 'like', "%{$student}%")
                        ->orWhere('email', 'like', "%{$student}%");
                });
            })
            ->when($assignmentKey, function ($q) use ($assignmentKey) {
                // filter exact by key dari select
                $q->whereHas('assignment', function ($aq) use ($assignmentKey) {
                    $aq->where('key', $assignmentKey);
                });
            })
            ->groupBy('user_id', 'assignment_id')
            ->latest('last_at')
            ->with(['user:id,name,email', 'assignment:id,title,key'])
            ->paginate(15);

        return view('admin.submissions_index', compact('rows', 'assignmentOptions'));
    }



    // Riwayat lengkap 1 siswa untuk 1 assignment
    public function history($assignmentId, $userId)
    {
        $assignment = Assignment::findOrFail($assignmentId);
        $user = User::findOrFail($userId);

        $subs = Submission::where('assignment_id', $assignmentId)
            ->where('user_id', $userId)
            ->with('evaluation')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.submissions_history', compact('assignment', 'user', 'subs'));
    }
}
