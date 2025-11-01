<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Evaluation;
use App\Jobs\EvaluateSubmission;

class SubmissionController extends Controller
{

    public function list()
    {
        $assignments = \App\Models\Assignment::query()
            ->withCount([
                'submissions as my_submit_count' => function ($q) {
                    $q->where('user_id', auth()->id());
                }
            ])
            ->get();

        return view('student.assignments', compact('assignments'));
    }

    public function downloadTemplate(string $key)
    {
        $a = Assignment::where('key', $key)->firstOrFail();

        if (!$a->template_path) {
            return back()->with('message', 'Template tidak tersedia untuk soal ini.');
        }

        $absPath = resource_path($a->template_path);
        if (!is_file($absPath)) {
            return back()->with('message', 'File template tidak ditemukan di server.');
        }

        return Response::download($absPath, basename($absPath));
    }

    public function showSubmitForm(string $key)
    {
        $assignment = Assignment::where('key', $key)->firstOrFail();

        $evaluation = Evaluation::whereHas('submission', function ($q) use ($assignment) {
            $q->where('assignment_id', $assignment->id)
                ->where('user_id', auth()->id());
        })
            ->latest('id')
            ->first();

        return view('student.submit', compact('assignment', 'evaluation'));
    }

    public function store(Request $request, string $key)
    {
        $assignment = Assignment::where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'source_code' => ['required', 'string', 'max:200000'], // guard simple
            'language' => ['nullable', 'string', 'max:20'],
        ]);

        $submission = Submission::create([
            'user_id' => Auth::id(),
            'assignment_id' => $assignment->id,
            'language' => $validated['language'] ?? 'php',
            'source_code' => trim($validated['source_code']),
            'status' => 'queued',
        ]);

        // Penilaian sinkron agar langsung terlihat.
        // Pastikan .env -> QUEUE_CONNECTION=sync
        EvaluateSubmission::dispatchSync($submission->id);

        return redirect()
            ->route('dashboard')
            ->with('message', 'Submission diterima. Sistem sudah menjalankan penilaian otomatis.');
    }
}
