<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureStudent
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Jika pakai kolom 'role'
        if (isset($user->role) && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Jika pakai spatie/roles:
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        // Lolos: ini siswa
        return $next($request);
    }
}
