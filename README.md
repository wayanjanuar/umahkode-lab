# Laravel Security Lab (5 Soal: SQLi, XSS, Open Redirect, IDOR, LFI)

Paket ini adalah **module siap tempel** untuk proyek Laravel 10/11 yang sudah ada.
Ia menyertakan:
- 5 soal keamanan (file template rentan) di `resources/assignments/*`
- Migrations, Models, Controllers, Jobs, Services
- Blade views (student & admin)
- Seeder untuk mengisi daftar soal
- Middleware role sederhana (`role:admin`)
- Runner script contoh (Docker) — nonaktif by default

> **Catatan:** Ini *bukan* full project Laravel. Gunakan pada proyek Laravel yang sudah terpasang.

## Cara Setup (Quickstart)

1) **Salin isi zip** ini ke root proyek Laravel-mu (merge folder).
2) Jalankan perintah berikut:
```bash
composer install
php artisan key:generate  # (jika belum)
php artisan migrate
php artisan db:seed --class=AssignmentSeeder
php artisan queue:work --sleep=3 --tries=3
```
3) Tambahkan **route** ke `routes/web.php` sudah termasuk di paket ini.
4) Tambahkan **middleware role** ke `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    // ...
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
```
5) Tambahkan kolom `role` untuk user (migration disediakan: `2025_01_01_000003_add_role_to_users.php`),
   lalu set role user admin ke `admin` (mis. via tinker atau SQL).

6) **Login sebagai admin**, akses dashboard:
```
/admin/evaluations
```
**Student pages**:
```
/assignments               # daftar soal + download template
/assignments/{key}/submit  # form submit
```

## Integrasi AI (Autograder)

- `app/Services/AIGrader.php` saat ini **stub** (heuristik). Ganti dengan panggilan API AI sungguhan.
- Validasi JSON output dari AI, simpan ke `evaluations`.
- Hasil evaluasi **hanya tampil di dashboard admin**.

## Sandbox Runner

- `app/Services/SandboxRunner.php` **default melakukan static analysis** agar aman.
- Untuk eksekusi sungguhan dalam container, aktifkan `runner/docker-runner.sh` dan *harden* environment:
  - No network (`--network none`), memory/CPU limits, PID limit, seccomp/apparmor, read-only rootfs, user non-privileged.

## Soal yang Disertakan

1. **SQLi — Login** (`resources/assignments/sqli/login_vuln.php`)
2. **XSS — Profile** (`resources/assignments/xss/profile_vuln.php`)
3. **Open Redirect** (`resources/assignments/openredirect/redirect_vuln.php`)
4. **IDOR — File View** (`resources/assignments/idor/file_view_vuln.php`)
5. **LFI — View Log** (`resources/assignments/lfi/view_log_vuln.php`)

> Tugas siswa: memperbaiki kerentanan tanpa mengubah fungsionalitas dasar.

## Catatan Keamanan & Etika

- Jangan jalankan kode siswa di host langsung. Gunakan sandbox/container terisolasi.
- Beri tahu di silabus: penilaian otomatis dilakukan dan siswa dapat ajukan banding.
- Sanitasi artifacts/log sebelum ditampilkan di admin.
- Terapkan rate-limit submission.
# deploy kick
