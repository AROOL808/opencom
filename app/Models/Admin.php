<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    // Menentukan nama tabel secara eksplisit
    protected $table = 'admin';

    // Karena tabel admin tidak punya kolom created_at & updated_at
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}