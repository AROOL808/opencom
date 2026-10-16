<?php

namespace App\Http\Controllers;
use App\Models\Konfigurasi;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function editToko()
    {
        // Ambil baris pertama dari tabel konfigurasi. Jika kosong, buat default baru.
        $konfigurasi = Konfigurasi::firstOrCreate(
            [], // Kondisi pencarian (kosongkan agar mencari apapun yang pertama)
            ['nama_toko' => 'OpenCom Store', 'alamat_toko' => ''] // Nilai default jika database masih kosong
        );

        return view('admin.dashboard.profile', compact('konfigurasi'));
    }

    public function updateToko(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'alamat_toko' => 'required|string',
        ]);

        $konfigurasi = Konfigurasi::first();
        
        $konfigurasi->update([
            'nama_toko' => $request->nama_toko,
            'alamat_toko' => $request->alamat_toko,
        ]);

        return back()->with('success', 'Profil toko berhasil diperbarui!');
    }
}
