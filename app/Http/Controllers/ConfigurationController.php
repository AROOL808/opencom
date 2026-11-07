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
        // 1. Validasi semua input dari form, termasuk input warna dan font
        $request->validate([
            'nama_toko'        => 'required|string|max:255',
            'alamat_toko'      => 'required|string',
            'alamat_logo'      => 'nullable|url', // Memastikan input logo adalah URL yang valid
            'alamat_font'      => 'nullable|string|max:255',
            'warna_item'       => 'nullable|string|max:7', // Hex code warna biasanya 7 karakter (contoh: #FFFFFF)
            'warna_navigator'  => 'nullable|string|max:7',
            'warna_background' => 'nullable|string|max:7',
            'warna_font'       => 'nullable|string|max:7',
        ]);

        // 2. Ambil baris pertama dari tabel konfigurasi
        $konfigurasi = Konfigurasi::first();
        
        // (Opsional/Keamanan) Jika tabel konfigurasi kebetulan masih benar-benar kosong, buat data baru
        if (!$konfigurasi) {
            $konfigurasi = new Konfigurasi();
        }
        
        // 3. Update semua data berdasarkan input dari form
        $konfigurasi->update([
            'nama_toko'        => $request->nama_toko,
            'alamat_toko'      => $request->alamat_toko,
            'alamat_logo'      => $request->alamat_logo,
            'alamat_font'      => $request->alamat_font,
            'warna_item'       => $request->warna_item,
            'warna_navigator'  => $request->warna_navigator,
            'warna_background' => $request->warna_background,
            'warna_font'       => $request->warna_font,
            'updated_at'       => now(),
        ]);

        return back()->with('success', 'Profil dan tampilan toko berhasil diperbarui!');
    }
}
