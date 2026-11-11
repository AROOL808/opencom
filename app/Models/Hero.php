<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    protected $table = 'hero';
    protected $primaryKey = 'hero_item_id';

    protected $fillable = [
        'title',
        'description',
        'redirect_link',
        'cta',
        'urutan',
        'alamat_foto',

    ];
}
