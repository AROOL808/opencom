<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keranjang;
use App\Models\ItemKeranjang;
use App\Models\Pengguna;
use App\Services\RajaOngkirService;

class CartController extends Controller
{
    protected $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    public function index()
    {
        if (!session('user_logged_in')) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $pengguna = Pengguna::where('username', session('username'))->firstOrFail();
        $keranjang = Keranjang::where('user_id', $pengguna->user_id)->first();

        $cartItems = [];
        $total = 0;

        if ($keranjang) {
            $cartItems = ItemKeranjang::with('product')->where('cart_id', $keranjang->cart_id)->get();
            
            $totalBerat = 0;
            foreach ($cartItems as $item) {
                $total += ($item->product->harga * $item->jumlah);
                $totalBerat += (1000 * $item->jumlah); // Asumsi berat 1 item = 1000 gram (1kg)
            }

            
        }

        return view('cart', compact('cartItems', 'total', 'pengguna'));
    }

    public function update(Request $request, $item_cart_id)
    {
        $item = ItemKeranjang::findOrFail($item_cart_id);

        if ($request->action == 'increment') {
            $item->jumlah += 1;
        } elseif ($request->action == 'decrement' && $item->jumlah > 1) {
            $item->jumlah -= 1;
        }

        $item->save();
        return back();
    }

    public function destroy($item_cart_id)
    {
        $item = ItemKeranjang::findOrFail($item_cart_id);
        $item->delete();

        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }
    public function searchLocation(Request $request)
    {
        $keyword = $request->query('keyword');
        if (strlen($keyword) < 3) return response()->json(['data' => []]);

        $rajaOngkir = new RajaOngkirService();
        $result = $rajaOngkir->searchLocation($keyword);

        return response()->json($result);
    }

    // Fungsi untuk AJAX Hitung Ongkir di Frontend
    public function calculateCost(Request $request)
    {
        $request->validate(['courier' => 'required']);

        // 1. Ambil data alamat dari Database
        $pengguna = Pengguna::where('username', session('username'))->first();
        $konfigurasi = \App\Models\Konfigurasi::first();

        if (!$pengguna || !$pengguna->alamat) {
            return response()->json(['error' => 'Alamat Anda belum diisi di profil.']);
        }
        if (!$konfigurasi || !$konfigurasi->alamat_toko) {
            return response()->json(['error' => 'Admin belum mengatur alamat toko.']);
        }

        // 2. Cari ID Origin (Toko) dan ID Destinasi (User)
        $rajaOngkir = new RajaOngkirService();
        
        $originId = $rajaOngkir->getLocationIdByAddress($konfigurasi->alamat_toko);
        $destId = $rajaOngkir->getLocationIdByAddress($pengguna->alamat);

        if (!$originId || !$destId) {
            return response()->json(['error' => 'Gagal melacak lokasi spesifik dari alamat yang tersimpan.']);
        }

        // 3. Hitung Berat Keranjang
        $keranjang = Keranjang::where('user_id', $pengguna->user_id)->first();
        $cartItems = ItemKeranjang::where('cart_id', $keranjang->cart_id)->get();
        
        $totalWeight = 0;
        foreach ($cartItems as $item) {
            $totalWeight += ($item->jumlah * 100); // Asumsi 1 barang = 100 gram
        }

        // 4. Hitung Ongkir Akhir
        $result = $rajaOngkir->calculateCost($originId, $destId, $totalWeight, $request->courier);

        return response()->json($result);
    }
}