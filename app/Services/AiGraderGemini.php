<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiGraderGemini
{
    public function evaluate(string $sourceCode, string $assignmentKey): array
    {
        $urlBase = rtrim(config('services.gemini.endpoint'), '/'); // ex: https://generativelanguage.googleapis.com/v1beta
        $apiKey  = config('services.gemini.key');

        $prompt = <<<PROMPT
You are an automatic grader for a web security lab.
Assignment key: "{$assignmentKey}" (SQLi, XSS, IDOR, LFI, or Open Redirect).
Evaluate the student's fix for secure coding best practices.
Return STRICT JSON with fields:
  - score: integer 0-100
  - explanation: short (<=60 words)
  - student_answer: verbatim code

Student answer:
----------------
{$sourceCode}
----------------
PROMPT;

        // ✅ daftar model yang dicoba
        $modelTry = [
            config('services.gemini.model', 'gemini-flash-latest'),
            'gemini-pro-latest',
            'gemini-2.5-flash',
        ];

        $response = null;
        foreach ($modelTry as $m) {
            $response = Http::timeout(30)->post(
                "{$urlBase}/models/{$m}:generateContent?key={$apiKey}",
                [
                    'contents' => [[
                        'role'  => 'user',
                        'parts' => [['text' => $prompt]],
                    ]],
                    'generationConfig' => [
                        'temperature' => 0.2,
                        'maxOutputTokens' => 512,
                        'response_mime_type' => 'application/json',
                    ],
                ]
            );

            if ($response->successful()) {
                break; // keluar loop kalau berhasil
            }

            // lanjut ke model berikut kalau error 404
            if ($response->status() === 404) {
                continue;
            }
        }

        if (!$response || $response->failed()) {
            throw new \RuntimeException('Semua percobaan model gagal: ' . ($response?->body() ?? 'no response'));
        }

        // ✅ parsing hasil
        $json = $response->json();
        $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $clean = trim(Str::of($text)->remove(['```json', '```']));

        $data = json_decode($clean, true);
        if (!is_array($data) || !isset($data['score'])) {
            throw new \RuntimeException('Invalid JSON dari Gemini: ' . $text);
        }

        return [
            'score'   => (int) $data['score'],
            'explain' => $data['explanation'] ?? '',
            'answer'  => $data['student_answer'] ?? $sourceCode,
        ];
    }
}
