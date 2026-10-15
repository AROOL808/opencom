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
}
