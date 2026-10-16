<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Pesanan::with(['pengguna', 'item_pesanan.produk'])->latest()->get();
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
}