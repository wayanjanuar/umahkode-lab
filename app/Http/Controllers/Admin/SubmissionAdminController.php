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
    public function index(Request $request)
    {
        $query = Submission::query()
            ->selectRaw('user_id, assignment_id, COUNT(*) as total, MAX(created_at) as last_at')
            ->with(['user:id,name,email', 'assignment:id,key,title'])
            ->groupBy('user_id', 'assignment_id')
            ->orderByDesc('last_at');

        if ($request->filled('user_id'))       $query->where('user_id', $request->user_id);
        if ($request->filled('assignment_id')) $query->where('assignment_id', $request->assignment_id);

        $rows = $query->paginate(25);
        return view('admin.submissions_index', compact('rows'));
    }

    // Riwayat lengkap 1 siswa untuk 1 assignment
    public function history($assignmentId, $userId)
    {
        $assignment = Assignment::findOrFail($assignmentId);
        $user       = User::findOrFail($userId);

        $subs = Submission::where('assignment_id', $assignmentId)
            ->where('user_id', $userId)
            ->with('evaluation')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.submissions_history', compact('assignment','user','subs'));
    }
}
