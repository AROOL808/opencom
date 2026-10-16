<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $table = 'keranjang';
    protected $primaryKey = 'cart_id';

     protected $fillable = [
        'user_id',
    ];

        public function user()
    {
        return $this->belongsTo(Pengguna::class, 'user_id', 'user_id');
    }
}
