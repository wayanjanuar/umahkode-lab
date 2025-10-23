<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Models\Assignment;
use App\Models\Submission;
use App\Jobs\EvaluateSubmission;

class SubmissionController extends Controller
{
    /**
     * (Opsional) Kalau kamu masih butuh halaman list khusus.
     * Saat ini dashboard siswa sudah menampilkan assignments,
     * jadi method ini bisa diarahkan ke view dashboard yg sama.
     */
    public function list()
    {
        $assignments = Assignment::all();
        // Jika dashboard siswa sudah memuat tabel assignments, pakai view itu:
        return view('student.assignments', compact('assignments'));
        // atau:
        // return redirect()->route('dashboard');
    }

    /**
     * Download template soal (jika disediakan path pada assignment).
     */
    public function downloadTemplate(string $key)
    {
        $a = Assignment::where('key', $key)->firstOrFail();

        if (empty($a->template_path)) {
            return back()->with('message', 'Template tidak tersedia untuk soal ini.');
        }

        $absPath = resource_path($a->template_path);
        if (!file_exists($absPath)) {
            return back()->with('message', 'File template tidak ditemukan di server.');
        }

        // Biar nama file rapi saat diunduh
        $downloadName = basename($absPath);
        return Response::download($absPath, $downloadName);
    }

    /**
     * Halaman form submit perbaikan kode.
     * Catatan: kamu minta "tanpa placeholder", jadi template tidak dipaksakan dimunculkan.
     */
    public function showSubmitForm(string $key)
    {
        $assignment = Assignment::where('key', $key)->firstOrFail();

        // Ambil hasil evaluasi terakhir user untuk soal ini (jika ada)
        $evaluation = \App\Models\Evaluation::whereHas('submission', function ($q) use ($assignment) {
            $q->where('assignment_id', $assignment->id)
                ->where('user_id', auth()->id());
        })->latest()->first();

        return view('student.submit', compact('assignment', 'evaluation'));
    }

    /**
     * Terima submission siswa & jalankan penilaian.
     * Kita pakai dispatchSync supaya hasil langsung diproses.
     */
    public function store(Request $request, string $key)
    {
        $assignment = Assignment::where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'source_code' => ['required', 'string'],
            'language' => ['nullable', 'string', 'max:20'],
        ]);

        $submission = Submission::create([
            'user_id' => Auth::id(),
            'assignment_id' => $assignment->id,
            'language' => $validated['language'] ?? 'php',
            'source_code' => $validated['source_code'],
            'status' => 'queued',
        ]);

        // Jalankan penilaian langsung (sinkron) agar terasa real-time
        // Kalau nanti ingin kembali ke queue: ganti ke EvaluateSubmission::dispatch($submission->id);
        EvaluateSubmission::dispatchSync($submission->id);

        // Redirect ke dashboard (karena /assignments sudah kamu hilangkan)
        return redirect()
            ->route('dashboard')
            ->with('message', 'Submission diterima. Sistem sudah menjalankan penilaian otomatis.');
    }
}
