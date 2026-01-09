<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $table = 'tarif';
    
    protected $fillable = [
        'nama_tarif',
        'harga',
        'deskripsi',
        'status'
    ];

    protected $casts = [
        'harga' => 'decimal:2'
    ];
}