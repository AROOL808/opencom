<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderBlock;
use App\Models\Pesanan;
use App\Services\BlockchainService;

class AdminBlockchainController extends Controller
{
    public function index(BlockchainService $blockchainService)
    {
        // Jalankan sistem verifikasi otomatis
        $verification = $blockchainService->verifyChain();
        
        // Ambil data Block dari yang terbaru
        $blocks = OrderBlock::orderBy('block_id', 'desc')->paginate(10);
        
        // Hitung Pendapatan (Hanya pesanan selesai/dibayar, ABAIKAN yang dibatalkan/revoked)
        $totalRevenue = Pesanan::whereIn('status_pesanan', ['diproses', 'dikirim', 'selesai'])
            ->sum('total_harga');
            
        // Hitung total nilai pesanan yang di-revoke
        $revokedAmount = Pesanan::where('status_pesanan', 'dibatalkan')->sum('total_harga');

        return view('admin.dashboard.blockchain', compact('blocks', 'verification', 'totalRevenue', 'revokedAmount'));
    }

    // Fungsi Revoke (Membatalkan pesanan tanpa merusak Blockchain)
    public function revokeOrder($order_id)
    {
        $pesanan = Pesanan::findOrFail($order_id);
        
        // Perbarui status menjadi dibatalkan. 
        // Hash asli tidak berubah, sehingga block tidak rusak.
        $pesanan->update(['status_pesanan' => 'dibatalkan']);

        return back()->with('success', 'Pesanan berhasil di-revoke. Total Pendapatan telah disesuaikan.');
    }
}