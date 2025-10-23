<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $users = User::query()
            ->when($q, function($qr) use ($q) {
                $qr->where(function($w) use ($q){
                    $w->where('name','like',"%$q%")
                      ->orWhere('email','like',"%$q%");
                });
            })
            ->where('role', 'student')
            ->orderBy('created_at','desc')
            ->paginate(20);

        return view('admin.students_index', compact('users','q'));
    }

    public function create()
    {
        return view('admin.students_create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','max:120', Rule::unique('users','email')],
            'password' => ['nullable','string','min:8'], // boleh kosong -> generate otomatis
        ]);

        $plain = $data['password'] ?? Str::password(10); // Laravel 10+ helper
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($plain),
            'role'     => 'student',
        ]);

        // Simpan password sementara ke session flash agar admin bisa kasih ke siswa
        return redirect()
            ->route('admin.students.index')
            ->with('message', "Akun siswa berhasil dibuat: {$user->email} / password: {$plain}");
    }

    // Opsional: hapus akun siswa
    public function destroy(User $user)
    {
        if ($user->role !== 'student') {
            return back()->with('error','Hanya akun siswa yang bisa dihapus.');
        }
        $user->delete();
        return back()->with('message','Akun siswa dihapus.');
    }

    // Opsional: reset password acak
    public function resetPassword(User $user)
    {
        if ($user->role !== 'student') {
            return back()->with('error','Hanya akun siswa yang bisa direset.');
        }
        $plain = Str::password(10);
        $user->password = Hash::make($plain);
        $user->save();

        return back()->with('message',"Password baru untuk {$user->email}: {$plain}");
    }
}
