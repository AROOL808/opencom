<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'order_id';
    
    protected $fillable = [
        'user_id',
        'tanggal_pesanan',
        'total_harga',
        'status_pesanan', //menunggu_pembayaran, diproses, dikirim, selesai, dibatalkan
        'order_another_id',
        'nomor_resi',
    ];

    public function item_pesanan()
    { 
        return $this->hasMany(ItemPesanan::class, 'order_id', 'order_id'); 
    }
    
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'user_id', 'user_id');
    }
}

