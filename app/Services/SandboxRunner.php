<?php
namespace App\Services;

class SandboxRunner
{
    public function run($submissionId, $assignmentKey, $sourceCode){
        $workdir = storage_path("app/sandbox/{$submissionId}");
        if(!is_dir($workdir)) mkdir($workdir,0755, true);
        $file = $workdir.'/app.php';
        file_put_contents($file, $sourceCode);

        // SAFE default: static analysis (do NOT execute untrusted code by default).
        $output = $this->staticAnalysis($sourceCode);

        return [
            'stdout' => $output,
            'stderr' => null,
            'runner_note' => 'STATIC_ANALYSIS_ONLY - See runner/docker-runner.sh to enable containerized execution.'
        ];
    }

    protected function staticAnalysis($code){
        $notes = [];
        $lc = strtolower($code);
        if (strpos($lc, 'select') !== false and (strpos($lc, "'") !== false or strpos($lc, '"') !== false)) {
            $notes[] = 'Found possible raw SQL queries with interpolated strings.';
        }
        if (strpos($lc, 'header(') !== false and strpos($lc, 'location:') !== false) {
            $notes[] = 'Found header Location usage -> possible open redirect.';
        }
        if (strpos($lc, 'file_get_contents') !== false or strpos($lc, 'readfile') !== false) {
            $notes[] = 'Found file reads (possible LFI/IDOR depending on checks).';
        }
        if (strpos($lc, '$_get') !== false and strpos($lc, 'include') !== false) {
            $notes[] = 'Includes using $_GET may indicate RFI/LFI risk.';
        }
        return implode("\n", $notes) ?: 'No obvious static issues detected.';
    }
}
