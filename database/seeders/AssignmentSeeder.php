<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assignment;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['key'=>'sqli-login','title'=>'SQL Injection — Login','description'=>'Perbaiki SQL injection pada login.php','template_path'=>'assignments/sqli/login_vuln.php'],
            ['key'=>'xss-profile','title'=>'XSS — Profile','description'=>'Perbaiki XSS pada profile.php','template_path'=>'assignments/xss/profile_vuln.php'],
            ['key'=>'openredirect','title'=>'Open Redirect','description'=>'Perbaiki open redirect pada redirect.php','template_path'=>'assignments/openredirect/redirect_vuln.php'],
            ['key'=>'idor-fileview','title'=>'IDOR — File View','description'=>'Perbaiki kontrol akses IDOR pada file view','template_path'=>'assignments/idor/file_view_vuln.php'],
            ['key'=>'lfi-log','title'=>'LFI — View Log','description'=>'Perbaiki LFI pada viewer log','template_path'=>'assignments/lfi/view_log_vuln.php'],
        ];
        foreach($items as $i) Assignment::create($i);
    }
}
