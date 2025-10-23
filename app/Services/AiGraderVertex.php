<?php

namespace App\Services;

use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class AiGraderVertex
{
    public function evaluate(string $sourceCode, string $assignmentKey): array
    {
        $project  = config('services.gcp.project_id', env('GCP_PROJECT_ID'));
        $location = config('services.gcp.location',   env('GCP_LOCATION', 'us-central1'));
        $modelId  = env('VERTEX_MODEL', 'gemini-1.5-pro');

        // 1) Dapatkan access token via ADC (Service Account)
        $scopes = ['https://www.googleapis.com/auth/cloud-platform'];
        $creds  = ApplicationDefaultCredentials::getCredentials($scopes);
        $token  = $creds->fetchAuthToken()['access_token'] ?? null;
        if (!$token) {
            throw new \RuntimeException('Gagal mendapatkan access token Vertex AI.');
        }

        // 2) Siapkan endpoint + model path (REST)
        $base   = "https://{$location}-aiplatform.googleapis.com";
        $model  = "projects/{$project}/locations/{$location}/publishers/google/models/{$modelId}";
        $url    = "{$base}/v1/{$model}:generateContent"; // REST method generateContent

        // 3) Prompt & payload (minta JSON agar mudah parse)
        $prompt = <<<PROMPT
You are an automatic grader for a security lab.
Assignment key: "{$assignmentKey}" (SQLi, XSS, IDOR, LFI, or Open Redirect).
Evaluate the student's fix for secure coding best practices.
Return STRICT JSON with fields:
  - score: integer 0-100
  - explanation: short (<= 60 words)
  - student_answer: verbatim code

Student answer:
----------------
{$sourceCode}
----------------
PROMPT;

        $payload = [
            'contents' => [[
                'role'  => 'user',
                'parts' => [['text' => $prompt]],
            ]],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 512,
                'response_mime_type' => 'application/json',
            ],
        ];

        // 4) Panggil API
        $http = new Client(['timeout' => 30]);
        $res  = $http->post($url, [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
            ],
            'json' => $payload,
        ]);

        $body = json_decode((string) $res->getBody(), true);
        $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // Bersihkan code fence ```json ... ```
        $clean = trim($text);
        if (Str::startsWith($clean, '```')) {
            $clean = preg_replace('/^```json|^```/i', '', $clean);
            $clean = preg_replace('/```$/', '', $clean);
            $clean = trim($clean);
        }

        $data = json_decode($clean, true);
        if (!is_array($data) || !isset($data['score'])) {
            throw new \RuntimeException('Vertex AI mengembalikan format tak valid: ' . $text);
        }

        // Normalisasi
        $score   = max(0, min(100, (int) $data['score']));
        $explain = (string) ($data['explanation'] ?? '');
        $answer  = (string) ($data['student_answer'] ?? $sourceCode);

        return compact('score','explain','answer');
    }
}
