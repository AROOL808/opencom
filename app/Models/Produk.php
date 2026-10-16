<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    public $timestamps = true;
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'alamat_foto',
        'nama_produk',
        'harga',
        'stok',
        'deskripsi',
        'category_id'
        
    ];
    public function category()
    {
        return $this->belongsTo(Kategori::class, 'category_id', 'category_id');
    }
}
