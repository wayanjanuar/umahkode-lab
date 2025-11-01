<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Assignment;

class AssignmentRubricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultWeights = [
            'pemahaman' => 15,
            'metodologi'=> 20,
            'ketepatan' => 30,
            'analisis'  => 30,
            'waktu'     => 5,
        ];

        $set = [
            'xss-basic' => [
                'title' => 'XSS Basic',
                'description' => 'Perbaiki output agar tidak mengeksekusi script injection.',
                'rubric' => [
                    'weights' => $defaultWeights,
                    'criteria'=> [
                        'pemahaman'=>'Memahami risiko XSS & tujuan output-escaping.',
                        'metodologi'=>'Gunakan htmlspecialchars/templating. Hindari echo raw input.',
                        'ketepatan' =>'Payload tidak tereksekusi; karakter berbahaya dinetralkan.',
                        'analisis'  =>'Jelaskan kenapa aman & contoh serangan yang diblok.',
                        'waktu'     =>'Rapi & tepat.',
                    ],
                ],
                'expected_patterns'  => ['contains' => ['htmlspecialchars', 'ENT_QUOTES']],
                'forbidden_patterns' => ['regex'    => ['/echo\s*\$_(GET|POST)\[[^\]]+\]\s*;?/i']],
                'test_cases' => [
                    ['input'=>'<script>alert(1)</script>', 'expect'=>'Tag tidak eksekusi, tampil sebagai teks'],
                    ['input'=>'" onmouseover="alert(1)',  'expect'=>'Event tidak aktif'],
                ],
                'rubric_version' => 'xss-v1',
            ],

            'sqli-login' => [
                'title' => 'SQLi Login',
                'description' => 'Amankan query login dari SQL Injection.',
                'rubric' => [
                    'weights' => $defaultWeights,
                    'criteria'=> [
                        'pemahaman'=>'Memahami risiko SQLi & parameterization.',
                        'metodologi'=>'Prepared statements (PDO/bind), TIDAK concat string.',
                        'ketepatan' =>'Payload umum gagal eksploit; hanya kredensial valid yang lolos.',
                        'analisis'  =>'Alasan mitigasi; contoh payload yang gagal.',
                        'waktu'     =>'Rapi & tepat.',
                    ],
                ],
                'expected_patterns'  => ['contains' => ['prepare', 'bindParam', 'bindValue', 'PDO']],
                'forbidden_patterns' => ['regex'    => ['/SELECT.+FROM.+WHERE.+\.\s*\./is','/["\']\s*\.\s*\$[a-z_]+/i']],
                'test_cases' => [
                    ['input'=>"' OR '1'='1", 'expect'=>'Login gagal'],
                    ['input'=>'admin / wrongpass','expect'=>'Login gagal'],
                ],
                'rubric_version' => 'sqli-v1',
            ],

            // New: Open Redirect
            'openredirect' => [
                'title' => 'Open Redirect',
                'description' => 'Amankan parameter redirect sehingga tidak bisa diarahkan ke domain asing.',
                'rubric' => [
                    'weights' => $defaultWeights,
                    'criteria'=> [
                        'pemahaman'=>'Mengerti risiko open redirect & efeknya terhadap phishing.',
                        'metodologi'=>'Validate/whitelist target, hindari langsung meng-echo param redirect.',
                        'ketepatan' =>'Redirect ke domain non-whitelist diblok/ditolak.',
                        'analisis'  =>'Jelaskan whitelist/validasi dan contoh exploit yang gagal.',
                        'waktu'     =>'Implementasi rapi & cepat.',
                    ],
                ],
                'expected_patterns'  => ['contains' => ['parse_url', 'host', 'in_array', 'allowed', 'redirect']],
                'forbidden_patterns' => ['regex'    => ['/header\s*\(\s*[\'"]location:/i','/redirect\([\'"]http/i']],
                'test_cases' => [
                    ['input'=>'https://evil.example.com', 'expect'=>'Tidak redirect ke domain jahat; tampil pesan/redirect ke safe page'],
                    ['input'=>'/internal/dashboard', 'expect'=>'Redirect ke route internal berhasil'],
                ],
                'rubric_version' => 'openredirect-v1',
            ],

            // New: IDOR (Insecure Direct Object Reference)
            'idor' => [
                'title' => 'IDOR (Access Control)',
                'description' => 'Pastikan user tidak dapat mengakses resource orang lain melalui manipulasi id.',
                'rubric' => [
                    'weights' => $defaultWeights,
                    'criteria'=> [
                        'pemahaman'=>'Mengerti kontrol akses dan konsep owner-based authorization.',
                        'metodologi'=>'Periksa ownership / policy; jangan hanya bergantung pada id di URL.',
                        'ketepatan' =>'Permintaan resource milik user lain ditolak (403/404).',
                        'analisis'  =>'Jelaskan bagaimana exploit bekerja & mitigasi yang dipasang.',
                        'waktu'     =>'Solusi diterapkan rapi.',
                    ],
                ],
                'expected_patterns'  => ['contains' => ['authorize', 'policy', 'gate', 'user_id', 'owner']],
                'forbidden_patterns' => ['regex'    => ['/where\s*\(\s*[\'"]user_id[\'"]\s*=\s*\$user->id/i','/SELECT.+FROM.+WHERE.+id\s*=\s*\$id\s*;/i']],
                'test_cases' => [
                    ['input'=>'akses resource id=5 (user bukan owner)', 'expect'=>'Akses ditolak'],
                    ['input'=>'akses resource id=your-own', 'expect'=>'Akses diperbolehkan'],
                ],
                'rubric_version' => 'idor-v1',
            ],

            // New: LFI (Local File Inclusion)
            'lfi' => [
                'title' => 'LFI (Local File Inclusion)',
                'description' => 'Cegah pengguna menyertakan file sistem lokal melalui parameter user-controlled.',
                'rubric' => [
                    'weights' => $defaultWeights,
                    'criteria'=> [
                        'pemahaman'=>'Mengerti ancaman LFI/RFI dan dampaknya.',
                        'metodologi'=>'Gunakan whitelist, canonicalize path, hindari include( $_GET[...] ).',
                        'ketepatan' =>'Payload tidak bisa menyertakan file arbitrer (.php, /etc/passwd) yang sensitif.',
                        'analisis'  =>'Jelaskan mitigasi & contoh payload yang gagal.',
                        'waktu'     =>'Solusi diterapkan dengan rapi.',
                    ],
                ],
                'expected_patterns'  => ['contains' => ['realpath', 'basename', 'in_array', 'allowed_files']],
                'forbidden_patterns' => ['regex'    => ['/include\s*\(\s*\$_(GET|POST)\[[^\]]+\]\s*\)/i','/require_once\s*\(\s*\$_(GET|POST)\[[^\]]+\]\s*\)/i']],
                'test_cases' => [
                    ['input'=>'../../../../etc/passwd', 'expect'=>'Tidak boleh membaca file sistem; ditolak atau sanitize'],
                    ['input'=>'templates/default.php', 'expect'=>'Hanya file dari whitelist yang boleh include'],
                ],
                'rubric_version' => 'lfi-v1',
            ],
        ];

        foreach ($set as $key => $cfg) {
            $a = Assignment::firstOrCreate(
                ['key' => $key],
                [
                    'title' => $cfg['title'],
                    'description' => $cfg['description'],
                    // pastikan kolom template_path nullable; kalau ada file template, isi path di sini
                    'template_path' => $cfg['template_path'] ?? null,
                ]
            );

            // simpan JSON/array ke kolom terkait (pastikan kolom migration support JSON or text)
            $a->rubric             = $cfg['rubric'] ?? null;
            $a->expected_patterns  = $cfg['expected_patterns'] ?? null;
            $a->forbidden_patterns = $cfg['forbidden_patterns'] ?? null;
            $a->test_cases         = $cfg['test_cases'] ?? null;
            $a->rubric_version     = $cfg['rubric_version'] ?? null;
            $a->save();
        }
    }
}
