<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\ItemPesanan;
use App\Models\Keranjang;
use App\Models\ItemKeranjang;
use App\Models\Pengguna;
use App\Services\RajaOngkirService;

class OrderController extends Controller
{
    protected $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    public function index()
    {
        if (!session('user_logged_in')) {
            return redirect()->route('login');
        }

        $pengguna = \App\Models\Pengguna::where('username', session('username'))->firstOrFail();
        
        // Mengambil pesanan dan relasi item pesanan beserta produknya, diurutkan dari yang terbaru
        $orders = \App\Models\Pesanan::with('item_pesanan.produk')
                         ->where('user_id', $pengguna->user_id)
                         ->orderBy('tanggal_pesanan', 'desc')
                         ->get();

        return view('orders.index', compact('orders'));
    }

    // Lacak Resi via RajaOngkir
    public function track($order_id)
    {
        $order = Pesanan::findOrFail($order_id);
        
        if (!$order->nomor_resi) {
            return back()->with('error', 'Nomor resi belum tersedia.');
        }

        $trackingData = $this->rajaOngkir->trackWaybill($order->nomor_resi, 'jne');

        return view('orders.track', compact('order', 'trackingData'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate(['layanan_ongkir' => 'required']);
        
        // Pisahkan nama layanan dan harga (Value: "REG|15000")
        $ongkirPilihan = explode('|', $request->layanan_ongkir);
        $hargaOngkir = (int) $ongkirPilihan[1];

        $pengguna = Pengguna::where('username', session('username'))->firstOrFail();
        $keranjang = Keranjang::where('user_id', $pengguna->user_id)->first();
        $cartItems = ItemKeranjang::with('product')->where('cart_id', $keranjang->cart_id)->get();

        $subtotal = 0;
        foreach ($cartItems as $item) $subtotal += ($item->product->harga * $item->jumlah);
        
        $grandTotal = $subtotal + $hargaOngkir;

        // 1. Simpan Pesanan ke Database
        $pesanan = Pesanan::create([
            'user_id' => $pengguna->user_id,
            'tanggal_pesanan' => now(),
            'total_harga' => $grandTotal,
            'status_pesanan' => 'menunggu_pembayaran',
        ]);

        foreach ($cartItems as $item) {
            ItemPesanan::create([
                'order_id' => $pesanan->order_id,
                'product_id' => $item->product_id,
                'jumlah' => $item->jumlah,
                'harga_satuan' => $item->product->harga,
                'subtotal' => $item->product->harga * $item->jumlah,
            ]);
        }

        // 2. Kosongkan Keranjang
        ItemKeranjang::where('cart_id', $keranjang->cart_id)->delete();

        // 3. Panggil API Payment Gateway RajaOngkir
        $paymentResponse = $this->rajaOngkir->createPayment(
            $pesanan->order_id, 
            $grandTotal, 
            $pengguna->nama_user, 
            $pengguna->email
        );

        // 4. Redirect ke halaman Invoice/Payment dari RajaOngkir
        // (Pastikan field 'invoice_url' atau 'payment_url' sesuai dengan response API RajaOngkir Anda)
        if (isset($paymentResponse['invoice_url'])) {
            return redirect($paymentResponse['invoice_url']);
        } else {
            // Fallback jika API gagal
            return redirect()->route('orders.index')->with('error', 'Pesanan dibuat, tetapi gagal memuat Gateway Pembayaran.');
        }
    }
}