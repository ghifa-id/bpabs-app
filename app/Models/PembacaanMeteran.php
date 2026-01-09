<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PembacaanMeteran extends Model
{
    protected $table = 'pembacaan_meteran';
    
    protected $fillable = [
        'id_meteran',
        'petugas_id',
        'tanggal_meteran',
        'bulan',
        'tahun',
        'meter_awal',
        'meter_akhir',
        'status',
        'catatan',
        'foto_meteran'
    ];

    protected $casts = [
        'tanggal_meteran' => 'datetime'
    ];

    /**
     * Relasi ke tabel meteran
     */
    public function meteran(): BelongsTo
    {
        return $this->belongsTo(Meteran::class, 'id_meteran');
    }

    /**
     * Relasi ke tabel tagihan
     */
    public function tagihan(): HasOne
    {
        return $this->hasOne(Tagihan::class, 'pembacaan_id');
    }

    /**
     * Relasi ke petugas yang melakukan pembacaan
     * petugas_id merujuk ke users.id dengan role = 'petugas'
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id')->where('role', 'petugas');
    }

    /**
     * Accessor untuk menghitung pemakaian air
     */
    public function getPemakaianAttribute()
    {
        return $this->meter_akhir - $this->meter_awal;
    }

    /**
     * Scope untuk pembacaan hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_meteran', today());
    }

    /**
     * Scope untuk pembacaan bulan ini
     */
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_meteran', now()->month)
                    ->whereYear('tanggal_meteran', now()->year);
    }

    /**
     * Scope untuk pembacaan berdasarkan petugas
     */
    public function scopeByPetugas($query, $petugasId)
    {
        return $query->where('petugas_id', $petugasId);
    }

    /**
     * Scope untuk pembacaan berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}