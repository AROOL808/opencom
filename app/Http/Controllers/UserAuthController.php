<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Laravel\Socialite\Facades\Socialite;

class UserAuthController extends Controller
{
    //  Tampilkan Halaman Registrasi Awal (Tombol Google)
    public function showRegister()
    {
        return view('auth.register');
    }

    //  Redirect ke Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    //  Tangkap Data dari Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Cek apakah email sudah terdaftar sebelumnya (Otomatis Login jika sudah)
            $existingUser = Pengguna::where('email', $googleUser->getEmail())->first();
            
            if ($existingUser) {
                session(['user_logged_in' => true, 'username' => $existingUser->username]);
                return redirect()->route('home'); 
            }

            // Jika belum terdaftar, simpan data dasar ke session dan lempar ke form lengkapi profil
            session([
                'register_email' => $googleUser->getEmail(),
                'register_name' => $googleUser->getName(),
            ]);

            return redirect()->route('register.complete');

        } catch (\Exception $e) {
            return redirect()->route('register')->withErrors(['error' => 'Gagal terhubung dengan Google.']);
        }
    }

    //  Tampilkan Form Lengkapi Profil
    public function showCompleteRegister()
    {
        if (!session('register_email')) {
            return redirect()->route('register');
        }
        return view('auth.complete-register');
    }

    //  Simpan Data Registrasi Akhir
    public function storeCompleteRegister(Request $request)
    {
        $request->validate([
            // Pengecekan username unik langsung ke tabel pengguna
            'username' => 'required|string|max:50|unique:pengguna,username|alpha_dash', 
            'nama_user' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string',
        ], [
            'username.unique' => 'Username ini sudah digunakan, silakan pilih yang lain.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip (-), dan garis bawah (_).'
        ]);

        $pengguna = Pengguna::create([
            'username' => $request->username,
            'nama_user' => $request->nama_user,
            'email' => session('register_email'), // Otomatis dari session Google
            'no_telepon' => $request->no_telepon,
            'alamat' => $request->alamat,
        ]);

        // Bersihkan session registrasi & set session login aktif
        session()->forget(['register_email', 'register_name']);
        session(['user_logged_in' => true, 'username' => $pengguna->username]);

        return redirect()->route('home')->with('success', 'Registrasi berhasil!');
    }
}