<?php
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Modul lab
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Admin\SubmissionAdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ScoreController;
use App\Http\Controllers\Admin\UserReportController;
use App\Services\AiGraderGemini;
use App\Http\Middleware\EnsureStudent;



Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard diarahkan ke daftar tugas (assignments list view)
Route::get('/dashboard', [SubmissionController::class, 'list'])
    ->middleware(['auth', 'verified', EnsureStudent::class])
    ->name('dashboard');

// =========================
// Rute PROFILE (Breeze)
// =========================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =========================
// Rute STUDENT (Lab Soal)
// (tanpa /assignments index; dashboard sudah jadi index)
// =========================
Route::middleware('auth')->group(function () {
    Route::get('/assignments/{key}/download', [SubmissionController::class, 'downloadTemplate'])->name('assignments.download');
    Route::get('/assignments/{key}/submit', [SubmissionController::class, 'showSubmitForm'])->name('assignments.submit.form');
    Route::post('/assignments/{key}/submit', [SubmissionController::class, 'store'])->name('assignments.submit');
});

Route::get('/assignments', function () {
    return redirect()->route('dashboard');
})->name('assignments');

// =========================
// Rute ADMIN
// =========================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Evaluations
        Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
        Route::get('/evaluations/{id}', [EvaluationController::class, 'show'])->name('evaluations.show');

        // Submissions summary & history
        Route::get('/submissions', [SubmissionAdminController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/history/{assignment}/{user}', [SubmissionAdminController::class, 'history'])->name('submissions.history');

        Route::get('/students', [UserManagementController::class, 'index'])->name('students.index');
        Route::get('/students/create', [UserManagementController::class, 'create'])->name('students.create');
        Route::post('/students', [UserManagementController::class, 'store'])->name('students.store');

        Route::delete('/students/{user}', [UserManagementController::class, 'destroy'])->name('students.destroy');
        Route::post('/students/{user}/reset', [UserManagementController::class, 'resetPassword'])->name('students.reset');

        Route::get('/penilaian', [ScoreController::class, 'create'])->name('scores.create');
        Route::post('/penilaian', [ScoreController::class, 'store'])->name('scores.store');
        Route::get('/penilaian/daftar', [ScoreController::class, 'index'])->name('scores.index');

        // Rekap Nilai Akhir (Admin)
        Route::get('/scores', [ScoreController::class, 'index'])
            ->name('scores.index');
        Route::post('/scores/generate', [ScoreController::class, 'generate'])
            ->name('scores.generate');

        Route::get('/users/{user}/summary.pdf', [UserReportController::class, 'summaryPdf'])
            ->name('users.summary.pdf');

    });

Route::get('/admin/test-ai', function () {
    $grader = app(\App\Services\AiGraderGemini::class);
    return response()->json(
        $grader->evaluate('<?php echo htmlspecialchars($_GET["x"] ?? ""); ?>', 'xss-basic')
    );
})->middleware(['auth', 'role:admin']);





// Auth routes (Breeze)
require __DIR__ . '/auth.php';
