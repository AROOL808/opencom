<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keranjang;
use App\Models\ItemKeranjang;
use App\Models\Pengguna;

class CartController extends Controller
{
    // 1. Tampilkan Halaman Keranjang
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
            // Ambil item keranjang beserta relasi produknya
            $cartItems = ItemKeranjang::with('product')->where('cart_id', $keranjang->cart_id)->get();
            
            // Hitung grand total
            foreach ($cartItems as $item) {
                $total += ($item->product->harga * $item->jumlah);
            }
        }

        return view('cart', compact('cartItems', 'total'));
    }

    // 2. Update Jumlah Item (Increment / Decrement)
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

    // 3. Hapus Item dari Keranjang
    public function destroy($item_cart_id)
    {
        $item = ItemKeranjang::findOrFail($item_cart_id);
        $item->delete();

        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }
}