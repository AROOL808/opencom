<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderBlock extends Model
{
    protected $table = 'order_blocks';
    protected $primaryKey = 'block_id';
    
    protected $fillable = [
        'block_hash',
        'previous_hash',
        'order_ids',
    ];

    protected $casts = [
        'order_ids' => 'array', // Otomatis konversi JSON ke Array
    ];
}