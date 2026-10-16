<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konfigurasi extends Model
{
    protected $table = 'konfigurasi'; // Sesuaikan jika nama tabel Anda berbeda
    protected $primaryKey = 'configuration_id';
    public $timestamps = false;
    protected $fillable = [
        'warna_navigator',
        'warna_font',
        'warna_item',
        'warna_background',
        'alamat_logo',
        'alamat_font',
        'besar_font',
        'updated_at',
        'alamat_toko',
        'nama_toko',
    ];
}
