<?php
require 'vendor/autoload.php';

use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;

// --- Pastikan kredensial terbaca ---
$cred = getenv('GOOGLE_APPLICATION_CREDENTIALS');
if (!$cred) {
    // fallback: set otomatis berdasarkan lokasi file ini
    $cred = __DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'keys' . DIRECTORY_SEPARATOR . 'vertex-service-account.json';
    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $cred);
}

if (!file_exists($cred)) {
    die("Credential file not found at: {$cred}\n");
}

try {
    $scopes = ['https://www.googleapis.com/auth/cloud-platform'];
    $creds  = ApplicationDefaultCredentials::getCredentials($scopes);
    $token  = $creds->fetchAuthToken()['access_token'] ?? null;
    if (!$token) {
        throw new RuntimeException('Failed to fetch access token.');
    }

    $project  = 'seclab-project';   // ganti kalau project id beda
    $location = 'us-central1';      // pastikan region ini benar
    $model    = 'gemini-1.5-flash'; // bisa diganti ke gemini-1.5-pro

    $client = new Client(['timeout' => 30]);
    $url = "https://{$location}-aiplatform.googleapis.com/v1/projects/{$project}/locations/{$location}/publishers/google/models/{$model}:generateContent";

    $res = $client->post($url, [
        'headers' => [
            'Authorization' => "Bearer {$token}",
            'Content-Type'  => 'application/json'
        ],
        'json' => [
            'contents' => [[
                'role'  => 'user',
                'parts' => [[ 'text' => 'Tolong balas JSON: {"score":90,"explanation":"OK"}' ]]
            ]],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
                'temperature' => 0.2
            ],
        ],
    ]);

    echo (string) $res->getBody() . PHP_EOL;

} catch (Throwable $e) {
    // biar error-nya kebaca jelas
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
