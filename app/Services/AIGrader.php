<?php
namespace App\Services;

class AIGrader
{
    public function grade(array $ctx){
        // Build prompt (for real API integration, send this to LLM and parse JSON).
        $prompt = $this->buildPrompt($ctx);

        // Demo heuristic — replace with real AI client call & robust JSON validation.
        $text = $ctx['artifacts']['stdout'] ?? '';
        $score = 80;
        $break = ['security'=>80,'functionality'=>80,'quality'=>80];
        $feedback = 'Heuristic grading based on static analysis. ' . $text;

        if (stripos($text, 'sql') !== false or stripos($text,'raw SQL')!==false) {
            $break['security'] -= 20; $score -= 15;
            $feedback .= ' Potential SQL injection risk remains.';
        }
        if (stripos($text, 'open redirect') !== false) {
            $break['security'] -= 10; $score -= 10;
            $feedback .= ' Open redirect check missing.';
        }
        if (stripos($text, 'file reads') !== false) {
            $break['security'] -= 10; $score -= 10;
            $feedback .= ' File path validation might be insufficient (LFI risk).';
        }

        $score = max(0, min(100, $score));
        return ['score'=>$score,'breakdown'=>$break,'feedback'=>$feedback];
    }

    protected function buildPrompt($ctx){
        $assignment = $ctx['assignment_key'];
        $desc = $ctx['assignment_description'];
        $code = $ctx['source_code'];
        $artifacts = $ctx['artifacts']['stdout'] ?? '';
        $prompt = <<<PROMPT
You are an autograder for secure web coding assignments.
Assignment: {$assignment}
Description: {$desc}
Code:
{$code}

Runtime / analysis artifacts:
{$artifacts}

Evaluate for: SQL Injection, XSS, IDOR, Open Redirect, LFI, Functionality.
Return JSON ONLY:
{ "score":0-100,
  "breakdown":{"security":0-100,"functionality":0-100,"quality":0-100},
  "feedback":"detailed human-readable feedback with specific lines and fixes"
}
PROMPT;
        return $prompt;
    }
}
