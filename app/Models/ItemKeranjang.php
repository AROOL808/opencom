<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemKeranjang extends Model
{
    protected $table = 'item_keranjang';
    protected $primaryKey = 'cart_item_id';

     protected $fillable = [
        'cart_id',
        'product_id',
        'jumlah',

    ];

    public function cart()
    {
        return $this->belongsTo(Keranjang::class, 'cart_id', 'cart_id');
    }
    public function product()
    {
        return $this->belongsTo(Produk::class, 'product_id', 'product_id');
    }
}
