<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman login admin
    public function showLoginForm()
    {
        return view('admin.login');
    }

    // Memproses login khusus tabel admin
    public function login(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ], [
            'username.required' => 'Username admin wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // 2. Cari data berdasarkan username di tabel 'admin'
        $admin = Admin::where('username', $request->username)->first();


        // 3. Verifikasi apakah admin ada dan password-nya cocok (ter-hash)
        if ($admin && Hash::check($request->password, $admin->password)) {
            
            // Simpan status login ke dalam Session Laravel
            session([
                'admin_logged_in' => true,
                'admin_id' => $admin->id,
                'admin_username' => $admin->username,
            ]);

            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        // 4. Jika gagal
        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    // Memproses logout admin
    public function logout(Request $request)
    {
        // Hapus session admin
        $request->session()->forget(['admin_logged_in', 'admin_id', 'admin_username']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}