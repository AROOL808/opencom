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
}