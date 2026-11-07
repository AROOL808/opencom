<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\OrderBlock;
use Carbon\Carbon;

class BlockchainService
{
    /**
     * 1. Buat Hash Tunggal untuk Pesanan
     */
    public function hashOrder(Pesanan $order)
    {
        // Standarisasi: Pastikan total_harga jadi integer string dan tanggal format baku
        $totalHargaStr = (string) (int) $order->total_harga;
        
        $tanggalStr = $order->tanggal_pesanan instanceof Carbon
            ? $order->tanggal_pesanan->format('Y-m-d H:i:s')
            : date('Y-m-d H:i:s', strtotime($order->tanggal_pesanan));

        $dataToHash = $order->order_id . $order->user_id . $totalHargaStr . $tanggalStr;
        
        $hash = hash('sha256', $dataToHash);
        
        $order->update(['order_hash' => $hash]);
        
        $this->createBlockIfNeeded();
    }

    /**
     * 2. Buat Block Baru Setiap 5 Pesanan
     */
    public function createBlockIfNeeded()
    {
        // Pastikan kita urutkan secara menaik (ascending) agar urutan hash selalu konsisten
        $unbatchedOrders = Pesanan::where('is_batched', false)->orderBy('order_id', 'asc')->get();

        if ($unbatchedOrders->count() >= 5) {
            $batch = $unbatchedOrders->take(5);
            $orderIds = [];
            $orderHashesString = '';

            // Gunakan foreach biasa alih-alih pluck untuk memastikan urutan hash tergabung dengan sempurna
            foreach ($batch as $order) {
                $orderIds[] = $order->order_id;
                $orderHashesString .= $order->order_hash;
            }

            // Dapatkan hash dari block terakhir
            $lastBlock = OrderBlock::orderBy('block_id', 'desc')->first();
            $previousHash = $lastBlock ? $lastBlock->block_hash : '00000000000000000000000000000000';

            // Hitung Block Hash baru
            $blockData = $previousHash . $orderHashesString;
            $blockHash = hash('sha256', $blockData);

            // Simpan Block ke Database
            OrderBlock::create([
                'block_hash' => $blockHash,
                'previous_hash' => $previousHash,
                'order_ids' => $orderIds,
            ]);

            // Tandai pesanan sudah masuk block
            Pesanan::whereIn('order_id', $orderIds)->update(['is_batched' => true]);
        }
    }

    /**
     * 3. Sistem Verifikasi Integritas Chain
     */
    public function verifyChain()
    {
        $blocks = OrderBlock::orderBy('block_id', 'asc')->get();
        $isValid = true;
        $corruptedBlocks = [];

        foreach ($blocks as $i => $block) {
            
            // A. Cek Integritas Rantai (Previous Hash)
            if ($i > 0) {
                if ($block->previous_hash !== $blocks[$i - 1]->block_hash) {
                    $isValid = false;
                    $corruptedBlocks[] = $block->block_id;
                    continue; // Jika rantai putus, tidak perlu cek isinya
                }
            } else {
                 // Pastikan block pertama (index 0) selalu memiliki previous_hash genesis
                 if ($block->previous_hash !== '00000000000000000000000000000000') {
                    $isValid = false;
                    $corruptedBlocks[] = $block->block_id;
                 }
            }

            // B. Hitung Ulang Hash Block
            // PENTING: Ambil pesanan dan WAJIB urutkan sesuai ID yang ada di dalam array $block->order_ids 
            // agar urutan penggabungan string tidak kacau saat PostgreSQL mengembalikan data secara asinkron.
            $orders = Pesanan::whereIn('order_id', $block->order_ids)
                             ->orderBy('order_id', 'asc') // Urutan WAJIB SAMA dengan di createBlockIfNeeded
                             ->get();
                             
            $recalculatedOrderHashes = '';
            
            foreach ($orders as $order) {
                // Standarisasi: Sama persis dengan hashOrder()
                $totalHargaStr = (string) (int) $order->total_harga;
                
                $tanggalStr = $order->tanggal_pesanan instanceof Carbon
                    ? $order->tanggal_pesanan->format('Y-m-d H:i:s')
                    : date('Y-m-d H:i:s', strtotime($order->tanggal_pesanan));

                $dataToHash = $order->order_id . $order->user_id . $totalHargaStr . $tanggalStr;
                $recalculatedOrderHashes .= hash('sha256', $dataToHash);
            }

            $recalculatedBlockHash = hash('sha256', $block->previous_hash . $recalculatedOrderHashes);

            // Jika hash beda, berarti data dimanipulasi
            if ($recalculatedBlockHash !== $block->block_hash) {
                $isValid = false;
                if (!in_array($block->block_id, $corruptedBlocks)) {
                     $corruptedBlocks[] = $block->block_id;
                }
            }
        }

        return [
            'is_valid' => $isValid,
            'corrupted_blocks' => $corruptedBlocks
        ];
    }
}