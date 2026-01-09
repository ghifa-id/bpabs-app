<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meteran extends Model
{
    protected $table = 'meteran';
    
    protected $fillable = [
        'user_id',
        'nomor_meteran',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pembacaanMeteran(): HasMany
    {
        return $this->hasMany(PembacaanMeteran::class, 'id_meteran');
    }

    /**
     * Relasi dengan model Tagihan
     * Meteran bisa memiliki banyak tagihan
     */
    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    /**
     * Relasi dengan model Tagihan (alias untuk konsistensi)
     */
    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    /**
     * Scope untuk meteran aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk meteran nonaktif
     */
    public function scopeNonaktif($query)
    {
        return $query->where('status', 'nonaktif');
    }

    /**
     * Scope untuk meteran milik user tertentu
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}