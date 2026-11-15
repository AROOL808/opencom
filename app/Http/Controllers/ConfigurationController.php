<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Konfigurasi;
use App\Models\Hero; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ConfigurationController extends Controller
{
    // 1. Tampilkan Halaman
    public function editToko()
    {
        $konfigurasi = Konfigurasi::first() ?? new Konfigurasi();
        $heroes = Hero::orderBy('urutan', 'asc')->get(); // Ambil data hero urut dari terkecil
        
        return view('admin.dashboard.profile', compact('konfigurasi', 'heroes'));
    }

    // 2. Simpan Hero Baru
    public function storeHero(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'cta'           => 'nullable|string|max:50',
            'urutan'        => 'required|integer',
            'redirect_link' => 'nullable|string',
            'product_id'    => 'nullable|integer',
            'foto_file'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Hapus 'required'
            'foto_url'      => 'nullable|url',
        ]);

        // Cek apakah user tidak mengupload file dan juga tidak mengisi URL
        if (!$request->hasFile('foto_file') && empty($request->foto_url)) {
            return back()->withErrors(['foto_file' => 'Anda harus mengupload gambar atau memasukkan URL gambar.'])->withInput();
        }

        // Ambil semua data request KECUALI foto_file dan foto_url (karena akan kita proses manual)
        $data = $request->except(['foto_file', 'foto_url']);

        // Logika Penyimpanan: Prioritaskan file upload, jika tidak ada gunakan URL
        if ($request->hasFile('foto_file')) {
            $file = $request->file('foto_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/heroes', $filename); 
            
            $data['alamat_foto'] = $filename; // Simpan nama file ke db
        } else {
            $data['alamat_foto'] = $request->foto_url; // Simpan URL ke db
        }

        Hero::create($data);

        return back()->with('success', 'Banner Hero berhasil ditambahkan!');
    }
    // 3. Hapus Hero
    public function destroyHero($id)
    {
        $hero = Hero::findOrFail($id);
        
        // Hapus file gambar dari storage jika ada
        if ($hero->alamat_foto) {
            Storage::delete('public/heroes/' . $hero->alamat_foto);
        }
        
        $hero->delete();

        return back()->with('success', 'Banner Hero berhasil dihapus!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cta' => 'nullable|string|max:100',
            'urutan' => 'required|integer',
            'redirect_link' => 'nullable|string|max:255',
            'foto_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'foto_url' => 'nullable|url'
        ]);

        $hero = Hero::findOrFail($id);

        // Cek jika ada unggahan gambar baru via file
        if ($request->hasFile('foto_file')) {
            // Hapus file lama jika file tersebut ada di storage (bukan URL)
            if (!Str::startsWith($hero->alamat_foto, ['http://', 'https://']) && Storage::disk('public')->exists('heroes/' . $hero->alamat_foto)) {
                Storage::disk('public')->delete('heroes/' . $hero->alamat_foto);
            }

            $file = $request->file('foto_file');
            $filename = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('heroes', $filename, 'public'); // Simpan di storage/app/public/heroes
            
            $alamatFoto = $filename;
        } 
        // Cek jika menggunakan URL baru
        elseif ($request->filled('foto_url')) {
            // Hapus file lama jika sebelumnya menggunakan upload lokal
            if (!Str::startsWith($hero->alamat_foto, ['http://', 'https://']) && Storage::disk('public')->exists('heroes/' . $hero->alamat_foto)) {
                Storage::disk('public')->delete('heroes/' . $hero->alamat_foto);
            }
            
            $alamatFoto = $request->foto_url;
        } 
        // Jika tidak ada perubahan gambar, gunakan gambar lama
        else {
            $alamatFoto = $hero->alamat_foto;
        }

        $hero->update([
            'title' => $request->title,
            'description' => $request->description,
            'cta' => $request->cta,
            'urutan' => $request->urutan,
            'redirect_link' => $request->redirect_link,
            'alamat_foto' => $alamatFoto,
        ]);

        return redirect()->back()->with('success', 'Hero banner berhasil diperbarui!');
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
