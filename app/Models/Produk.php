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
        /* 
         * belongsTo(ModelTujuan::class, 'foreign_key_di_tabel_produk', 'primary_key_di_tabel_kategori')
         * 
         * Catatan: Jika Model kategori Anda bernama "Kategori" (bukan Category), 
         * ubah Category::class menjadi Kategori::class di bawah ini.
         */
        return $this->belongsTo(Kategori::class, 'category_id', 'category_id');
    }
}
