<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'category_id';
    public $timestamps = true;

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        
    ];
    public function category()
    {
        return $this->belongsTo(Kategori::class, 'category_id', 'category_id');
    }
    public function produk()
    { 
        return $this->hasMany(Produk::class, 'category_id', 'category_id'); 
    }
}
