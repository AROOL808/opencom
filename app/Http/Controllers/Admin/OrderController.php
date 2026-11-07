<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Pesanan::with(['pengguna', 'item_pesanan.produk'])->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.dashboard.orders', compact('orders'));
    }

    public function updateResi(Request $request, $order_id)
    {
        $request->validate([
            'nomor_resi' => 'required|string|max:100',
        ]);

        $order = Pesanan::findOrFail($order_id);
        $order->update([
            'nomor_resi' => $request->nomor_resi,
            'status_pesanan' => 'dikirim',
        ]);

        return back()->with('success', 'Nomor resi berhasil diperbarui dan status diubah menjadi dikirim.');
    }
    public function updateStatus(Request $request, $order_id)
    {
        $request->validate([
            'status_pesanan' => 'required|in:menunggu_pembayaran,diproses,dikirim,selesai,dibatalkan'
        ]);

        $pesanan = Pesanan::findOrFail($order_id);
        $statusLama = $pesanan->status_pesanan;
        $statusBaru = $request->status_pesanan;

        $pesanan->update([
            'status_pesanan' => $statusBaru
        ]);

        // Berikan pesan sukses yang spesifik jika pesanan dibatalkan (revoke otomatis)
        if ($statusBaru === 'dibatalkan' && $statusLama !== 'dibatalkan') {
            return back()->with('success', 'Pesanan berhasil dibatalkan (Revoke otomatis). Kalkulasi pendapatan pada Blockchain telah disesuaikan.');
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}