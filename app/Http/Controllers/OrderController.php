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
use Illuminate\Support\Facades\Http;

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
    public function track(Request $request, $order_id)
    {
        $order = Pesanan::with('pengguna')->findOrFail($order_id);
        
        if (!$order->nomor_resi) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Nomor resi belum tersedia.'], 400);
            }
            return back()->with('error', 'Nomor resi belum tersedia.');
        }

        // Ambil kurir dari kolom pesanan (fallback ke 'jne')
        $kurir = strtolower(trim($order->kurir ?? ($order->pengiriman->kurir ?? 'jne')));

        // Ambil 5 digit terakhir dari nomor ponsel pemesan
        $rawPhone = $order->pengguna->no_telepon ?? '';
        $cleanPhone = preg_replace('/\D/', '', $rawPhone);
        $lastPhoneNumber = substr($cleanPhone, -5);

        $params = [
            'awb'     => trim($order->nomor_resi),
            'courier' => $kurir,
        ];

        if (!empty($lastPhoneNumber)) {
            $params['last_phone_number'] = $lastPhoneNumber;
        }

        $url = 'https://rajaongkir.komerce.id/api/v1/track/waybill?' . http_build_query($params);

        // Endpoint track/waybill Komerce V1 menggunakan method POST dengan query parameter
        $response = Http::withHeaders([
            'key'    => env('RAJAONGKIR_API_KEY'),
            'Accept' => 'application/json',
        ])->post($url);

        // Fallback ke GET apabila server mengembalikan 405 Method Not Allowed
        if ($response->status() === 405) {
            $response = Http::withHeaders([
                'key'    => env('RAJAONGKIR_API_KEY'),
                'Accept' => 'application/json',
            ])->get($url);
        }

        $trackingData = $response->json();

        // Tangani jika API Komerce mengembalikan error (misal resi tidak valid / API key salah)
        if ($response->failed()) {
            $errorMessage = $trackingData['meta']['message'] ?? 'Gagal mengambil data pelacakan dari kurir.';

            if ($request->wantsJson()) {
                return response()->json([
                    'status'   => 'error',
                    'message'  => $errorMessage,
                    'tracking' => $trackingData,
                ], $response->status());
            }

            return back()->with('error', $errorMessage);
        }

        // Jika dipanggil via AJAX dari halaman orders.index
        if ($request->wantsJson()) {
            return response()->json([
                'status'   => 'success',
                'tracking' => $trackingData,
            ]);
        }

        return view('orders.track', compact('order', 'trackingData'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'layanan_ongkir' => 'required',
            'kurir' => 'nullable|string|max:50',
        ]);
        
        $ongkirPilihan = explode('|', $request->layanan_ongkir);

        // Mendukung format "jne|REG|45000" maupun "REG|45000" dengan input terpisah name="kurir"
        if (count($ongkirPilihan) === 3) {
            $kurir = strtolower(trim($ongkirPilihan[0]));
            $namaLayanan = $ongkirPilihan[1];
            $hargaOngkir = (int) $ongkirPilihan[2];
        } else {
            $kurir = strtolower(trim($request->input('kurir', 'jne')));
            $namaLayanan = $ongkirPilihan[0];
            $hargaOngkir = (int) ($ongkirPilihan[1] ?? 0);
        }

        $pengguna = Pengguna::where('username', session('username'))->firstOrFail();
        $keranjang = Keranjang::where('user_id', $pengguna->user_id)->first();
        $cartItems = ItemKeranjang::with('product')->where('cart_id', $keranjang->cart_id)->get();

        $subtotal = 0;
        $formattedItems = [];

        foreach ($cartItems as $item) {
            $subtotal += ($item->product->harga * $item->jumlah);
            
            $formattedItems[] = [
                'name' => substr($item->product->nama_produk, 0, 50),
                'quantity' => (int) $item->jumlah,
                'price' => (int) $item->product->harga
            ];
        }
        
        $formattedItems[] = [
            'name' => 'Ongkos Kirim (' . strtoupper($kurir) . ' - ' . $namaLayanan . ')',
            'quantity' => 1,
            'price' => $hargaOngkir
        ];

        $grandTotal = $subtotal + $hargaOngkir;
        $orderAnotherId = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        // 1. Simpan Pesanan beserta Kurir ke Database
        $pesanan = Pesanan::create([
            'user_id' => $pengguna->user_id,
            'tanggal_pesanan' => now(),
            'total_harga' => $grandTotal,
            'status_pesanan' => 'menunggu_pembayaran',
            'order_another_id' => $orderAnotherId,
            'kurir' => $kurir,
        ]);

        $blockchain = new \App\Services\BlockchainService();
        $blockchain->hashOrder($pesanan);
        
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
        if (isset($paymentResponse['data']['payment_url']) || isset($paymentResponse['data']['checkout_url'])) {
            $redirectUrl = $paymentResponse['data']['payment_url'] ?? $paymentResponse['data']['checkout_url'];
            return redirect($redirectUrl);
        }
        
        if (isset($paymentResponse['data']['virtual_account_number'])) {
            return redirect()->route('orders.index')->with('success', 'Silakan transfer ke VA BCA: ' . $paymentResponse['data']['virtual_account_number']);
        }

        dd('ERROR DARI KOMERCE CHECKOUT:', $grandTotal);
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
            env('PAYMENT_BANK') // Bank
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