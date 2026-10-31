<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Tambahkan ini untuk fungsi random string
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
        // $request->layanan_ongkir nilainya misal: "REG|45000"
        $request->validate(['layanan_ongkir' => 'required']);
        
        $ongkirPilihan = explode('|', $request->layanan_ongkir);
        $namaLayanan = $ongkirPilihan[0];
        $hargaOngkir = (int) $ongkirPilihan[1];

        $pengguna = Pengguna::where('username', session('username'))->firstOrFail();
        $keranjang = Keranjang::where('user_id', $pengguna->user_id)->first();
        $cartItems = ItemKeranjang::with('product')->where('cart_id', $keranjang->cart_id)->get();

        $subtotal = 0;
        $formattedItems = [];

        foreach ($cartItems as $item) {
            $subtotal += ($item->product->harga * $item->jumlah);
            
            // Format item belanja sesuai dokumentasi Komerce
            $formattedItems[] = [
                'name' => substr($item->product->nama_produk, 0, 50),
                'quantity' => (int) $item->jumlah,
                'price' => (int) $item->product->harga
            ];
        }
        
        // Masukkan ongkir sebagai bagian dari item Komerce agar Amount balance
        $formattedItems[] = [
            'name' => 'Ongkos Kirim (' . $namaLayanan . ')',
            'quantity' => 1,
            'price' => $hargaOngkir
        ];

        $grandTotal = $subtotal + $hargaOngkir;

        // Generate ID Invoice Unik (Misal: INV-20261025-XYZ12)
        $orderAnotherId = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        // 1. Simpan Pesanan ke Database
        $pesanan = Pesanan::create([
            'user_id' => $pengguna->user_id,
            'tanggal_pesanan' => now(),
            'total_harga' => $grandTotal,
            'status_pesanan' => 'menunggu_pembayaran',
            'order_another_id' => $orderAnotherId, // Simpan ID Unik
        ]);

        foreach ($cartItems as $item) {
            ItemPesanan::create([
                'order_id' => $pesanan->order_id,
                'product_id' => $item->product_id,
                'jumlah' => $item->jumlah,
                'harga_satuan' => $item->product->harga,
            ]);
        }

        // 2. Kosongkan Keranjang
        ItemKeranjang::where('cart_id', $keranjang->cart_id)->delete();

        // 3. Panggil API Payment Komerce
        $customerData = [
            'name' => $pengguna->nama_user,
            'email' => $pengguna->email,
            'phone' => $pengguna->no_telepon ?? '080000000000'
        ];

        $paymentType = 'bank_transfer';
        $channelCode = 'BCA';

        $paymentResponse = $this->rajaOngkir->createPayment(
            $orderAnotherId, 
            $grandTotal, 
            $customerData, 
            $formattedItems,
            $paymentType,
            $channelCode
        );

        // 4. Handle Respons Komerce
        // Berdasarkan standar Komerce Payment API, jika sukses mereka biasanya mengembalikan checkout_url
        if (isset($paymentResponse['data']['payment_url']) || isset($paymentResponse['data']['checkout_url'])) {
            $redirectUrl = $paymentResponse['data']['payment_url'] ?? $paymentResponse['data']['checkout_url'];
            return redirect($redirectUrl);
        }
        
        // Jika Komerce hanya mengembalikan nomor Virtual Account mentah (tanpa UI link)
        if (isset($paymentResponse['data']['virtual_account_number'])) {
            return redirect()->route('orders.index')->with('success', 'Silakan transfer ke VA BCA: ' . $paymentResponse['data']['virtual_account_number']);
        }

        // Jika terjadi error dari Komerce
        // return redirect()->route('orders.index')->with('error', 'Gagal memuat Payment Gateway: ' . json_encode($paymentResponse));
        dd('ERROR DARI KOMERCE CHECKOUT:', $paymentResponse);
    }
    public function payNow($order_id)
    {
        // 1. Cari pesanan beserta item dan produknya
        $pesanan = Pesanan::with('item_pesanan.produk')->where('order_id', $order_id)->firstOrFail();
        
        // 2. Pastikan status masih menunggu pembayaran
        if ($pesanan->status_pesanan !== 'menunggu_pembayaran') {
            return redirect()->route('orders.index')->with('error', 'Pesanan ini sudah diproses atau dibayar.');
        }

        $pengguna = Pengguna::where('user_id', $pesanan->user_id)->first();

        // 3. Susun ulang daftar belanjaan untuk API Komerce
        $formattedItems = [];
        $subtotalBarang = 0;
        
        foreach ($pesanan->item_pesanan as $item) {
            $subtotalBarang += $item->subtotal;
            $formattedItems[] = [
                'name' => substr($item->produk->nama_produk ?? 'Produk', 0, 50),
                'quantity' => (int) $item->jumlah,
                'price' => (int) $item->harga_satuan
            ];
        }

        // Hitung nilai ongkir (Total Harga - Subtotal Barang)
        $hargaOngkir = $pesanan->total_harga - $subtotalBarang;
        if ($hargaOngkir > 0) {
            $formattedItems[] = [
                'name' => 'Ongkos Kirim',
                'quantity' => 1,
                'price' => (int) $hargaOngkir
            ];
        }

        $customerData = [
            'name' => $pengguna->nama_user,
            'email' => $pengguna->email,
            'phone' => $pengguna->no_telepon ?? '080000000000'
        ];

        // 4. Perbarui ID Invoice (order_another_id) agar Komerce tidak menolak request karena ID ganda
        $newOrderAnotherId = 'INV-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(5));
        $pesanan->update(['order_another_id' => $newOrderAnotherId]);

        // 5. Tembak API Komerce
        $paymentResponse = $this->rajaOngkir->createPayment(
            $newOrderAnotherId, 
            $pesanan->total_harga, 
            $customerData, 
            $formattedItems,
            'bank_transfer',  // Metode
            'BCA'  // Bank
        );

        // 6. Redirect ke halaman Komerce
        if (isset($paymentResponse['data']['payment_url']) || isset($paymentResponse['data']['checkout_url'])) {
            $redirectUrl = $paymentResponse['data']['payment_url'] ?? $paymentResponse['data']['checkout_url'];
            return redirect($redirectUrl);
        }

        if (isset($paymentResponse['data']['virtual_account_number'])) {
            return redirect()->route('orders.index')->with('success', 'Silakan transfer ke VA BCA: ' . $paymentResponse['data']['virtual_account_number']);
        }

        // return redirect()->route('orders.index')->with('error', 'Gagal memuat Payment Gateway: ' . json_encode($paymentResponse));
        dd('ERROR DARI KOMERCE CHECKOUT:', $paymentResponse);
        
    }
    public function paymentCallback(Request $request)
    {
        // 1. Ambil seluruh data JSON yang dikirim oleh Komerce
        $payload = $request->all();

        // 2. Komerce biasanya mengirim 'order_id' (yang mana adalah order_another_id kita) 
        // dan status pembayaran seperti 'PAID', 'SETTLED', atau 'SUCCESS'
        // (Catatan: Sesuaikan nilai 'PAID' ini dengan dokumentasi webhook Komerce Anda)
        
        if (isset($payload['order_id']) && isset($payload['status'])) {
            
            // Cek apakah statusnya sukses dibayar
            if (strtoupper($payload['status']) === 'PAID' || strtoupper($payload['status']) === 'SUCCESS') {
                
                // Cari pesanan berdasarkan order_another_id
                $pesanan = Pesanan::where('order_another_id', $payload['order_id'])->first();

                if ($pesanan) {
                    // Ubah status pesanan menjadi diproses
                    $pesanan->update([
                        'status_pesanan' => 'diproses'
                    ]);

                    // Beri tahu server Komerce bahwa kita sudah menerima datanya (HTTP 200 OK)
                    return response()->json(['status' => 'success', 'message' => 'Status pesanan berhasil diperbarui'], 200);
                }

                return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan'], 404);
            }
        }
        return response()->json(['status' => 'ignored', 'message' => 'Status bukan sukses'], 200);
    }
}