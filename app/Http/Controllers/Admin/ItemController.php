<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ItemController extends Controller
{
    // Menampilkan halaman CRUD
    public function index()
    {
        // Ambil data produk (beserta relasi kategorinya) dan data kategori
        $products = Produk::with('category')->latest()->get();
        $categories = Kategori::latest()->get();

        return view('admin.dashboard.items', compact('products', 'categories'));
    }

    // ==========================================
    // LOGIKA KATEGORI
    // ==========================================
    public function storeCategory(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Kategori::create($request->all());
        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function destroyCategory($id)
    {
        Kategori::findOrFail($id)->delete();
        return back()->with('success', 'Kategori berhasil dihapus!');
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $category = Kategori::findOrFail($id);
        $category->update($request->all());

        return back()->with('success', 'Kategori berhasil diperbarui!');
    }


    // ==========================================
    // LOGIKA PRODUK & PEMROSESAN GAMBAR
    // ==========================================
    public function storeProduct(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
            'category_id' => 'required|exists:kategori,category_id',
            'deskripsi' => 'nullable|string',
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // Maks 5MB
        ]);

        $productData = $request->except('foto');

        // Proses Gambar
        if ($request->hasFile('foto')) {
            // Pastikan folder ada
            if (!Storage::disk('public')->exists('products')) {
                Storage::disk('public')->makeDirectory('products');
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('foto'));

            // Logika Crop: max 1000x1000, atau ratio 1:1 jika lebih kecil
            $minSide = min($image->width(), $image->height());
            $cropSize = min($minSide, 1000); // Batasi maksimal 1000px
            
            // Crop dari tengah ke rasio 1:1
            $image->cover($cropSize, $cropSize);

            // Simpan ke WebP Kualitas 100%
            $filename = time() . '_' . uniqid() . '.webp';
            $image->toWebp(100)->save(storage_path('app/public/products/' . $filename));

            $productData['alamat_foto'] = $filename;
        }

        Produk::create($productData);
        return back()->with('success', 'Produk berhasil ditambahkan!');
    }

    public function destroyProduct($id)
    {
        $product = Produk::findOrFail($id);

        // Hapus file foto secara fisik dari storage jika ada
        if ($product->alamat_foto && Storage::disk('public')->exists('products/' . $product->alamat_foto)) {
            Storage::disk('public')->delete('products/' . $product->alamat_foto);
        }

        // Hapus data dari database
        $product->delete();
        return back()->with('success', 'Produk dan fotonya berhasil dihapus!');
    }


    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
            'category_id' => 'required|exists:kategori,category_id',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', 
        ]);

        $product = Produk::findOrFail($id);
        $productData = $request->except('foto');

        // Jika user mengupload foto baru saat edit
        if ($request->hasFile('foto')) {
            // 1. Hapus foto lama secara fisik
            if ($product->alamat_foto && Storage::disk('public')->exists('products/' . $product->alamat_foto)) {
                Storage::disk('public')->delete('products/' . $product->alamat_foto);
            }

            // 2. Inisialisasi dan crop foto baru (v3)
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('foto'));

            $minSide = min($image->width(), $image->height());
            $cropSize = min($minSide, 1000);
            
            $image->cover($cropSize, $cropSize);

            // 3. Simpan nama file baru ke lokal
            $filename = time() . '_' . uniqid() . '.webp';
            $image->toWebp(100)->save(storage_path('app/public/products/' . $filename));

            // Perbarui data nama file di array sebelum disimpan ke database
            $productData['alamat_foto'] = $filename;
        }

        $product->update($productData);
        return back()->with('success', 'Produk berhasil diperbarui!');
    }
}