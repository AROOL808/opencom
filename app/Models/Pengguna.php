<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pengguna extends Model
{
    protected $table = 'pengguna';
    protected $primaryKey = 'user_id';
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'username',
        'nama_user',
        'email',
        'no_telepon',
        'alamat'
    ];
    //uuid generator
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->user_id)) {
                $model->user_id = (string) Str::uuid(); // Mengembalikan string UUID
            }
        });
    }

    public function pesanan()
    { 
        return $this->hasMany(Pesanan::class, 'user_id', 'user_id'); 
    }

    public function item_pesanan()
    { 
        return $this->hasManyThrough(
            ItemPesanan::class, 
            Pesanan::class, 
            'user_id', // Foreign key di tabel pesanan
            'order_id', // Foreign key di tabel item_pesanan
            'user_id', // Local key di tabel pengguna
            'order_id' // Local key di tabel pesanan
        ); 
    }
}