<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Pengguna;
use App\Models\Keranjang;
use App\Models\ItemKeranjang;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $heroSlides = [
            [
                'title' => 'Diskon Musim Panas 50%',
                'desc' => 'Koleksi pakaian pria terbaik untuk musim ini. Jangan sampai kehabisan!',
                'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'cta' => 'Belanja Sekarang'
            ],
            [
                'title' => 'Sneakers Rilis Terbaru',
                'desc' => 'Langkah lebih gaya dengan koleksi sneakers eksklusif minggu ini.',
                'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'cta' => 'Lihat Koleksi'
            ],
            [
                'title' => 'Aksesoris Premium',
                'desc' => 'Lengkapi gayamu dengan jam tangan minimalis dan elegan.',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'cta' => 'Eksplor Aksesoris'
            ]
        ];
        $categories = Kategori::all();
        
        // Mengambil 4 produk terbaru sebagai "Produk Terpopuler" simulasi
        $popularProducts = Produk::latest()->take(4)->get();
        
        // Mengelompokkan produk berdasarkan kategori
        $productsByCategory = Kategori::with('produk')->get(); 

        // Menghitung jumlah isi keranjang user yang sedang login
        $cartCount = 0;
        if (session('user_logged_in')) {
            $pengguna = Pengguna::where('username', session('username'))->first();
            if ($pengguna) {
                $keranjang = Keranjang::where('user_id', $pengguna->user_id)->first();
                if ($keranjang) {
                    $cartCount = ItemKeranjang::where('cart_id', $keranjang->cart_id)->count();
                }
            }
        }

        return view('welcome', compact('heroSlides','categories', 'popularProducts', 'productsByCategory', 'cartCount'));
    }

    //  FUNGSI MENAMBAH KE KERANJANG
   public function addToCart(Request $request, $product_id)
    {
        if (!session('user_logged_in')) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk berbelanja.');
        }

        // Validasi input jumlah dari modal box
        $request->validate([
            'jumlah' => 'required|integer|min:1'
        ]);

        $pengguna = Pengguna::where('username', session('username'))->firstOrFail();

        $keranjang = Keranjang::firstOrCreate([
            'user_id' => $pengguna->user_id
        ]);

        // Cek apakah produk ini sudah ada di keranjang milik user
        $item = ItemKeranjang::where('cart_id', $keranjang->cart_id)
                             ->where('product_id', $product_id)
                             ->first();

        if ($item) {
            // Jika produk sudah ada di keranjang, tambahkan jumlahnya saja
            $item->jumlah += $request->jumlah;
            $item->save();
        } else {
            // Jika belum ada, buat entri baru
            ItemKeranjang::create([
                'cart_id' => $keranjang->cart_id,
                'product_id' => $product_id,
                'jumlah' => $request->jumlah
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }
    
    public function showProduct($product_id)
    {
        // Ambil data produk berdasarkan product_id beserta relasi kategorinya
        // Pastikan nama relasi kategori di Model Produk Anda sesuai, misalnya 'category'
        $product = Produk::with('category')->where('product_id', $product_id)->firstOrFail();
        
        // Menghitung jumlah isi keranjang untuk badge navbar
        $cartCount = 0;
        if (session('user_logged_in')) {
            $pengguna = Pengguna::where('username', session('username'))->first();
            if ($pengguna) {
                $keranjang = Keranjang::where('user_id', $pengguna->user_id)->first();
                if ($keranjang) {
                    $cartCount = ItemKeranjang::where('cart_id', $keranjang->cart_id)->count();
                }
            }
        }

        return view('product-detail', compact('product', 'cartCount'));
    }
}
