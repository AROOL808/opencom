<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPesanan extends Model
{
    protected $table = 'item_pesanan';
    protected $primaryKey = 'order_item_id';
    public $timestamps = false;
    
    protected $fillable = [
        'order_id',
        'product_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'order_id', 'order_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'product_id', 'product_id');
    }
}


