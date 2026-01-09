<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'nik',
        'alamat',
        'no_hp',
        'role',
        'email',
        'username',
        'password',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function logActivities()
    {
        return $this->hasMany(LogActivity::class);
    }

    /**
     * Relasi dengan model Meteran
     * User bisa memiliki banyak meteran (untuk pelanggan)
     */
    public function meteran(): HasMany
    {
        return $this->hasMany(Meteran::class);
    }

    /**
     * Relasi dengan model Meteran (alias untuk konsistensi)
     */
    public function meterans(): HasMany
    {
        return $this->hasMany(Meteran::class);
    }

    /**
     * Relasi dengan model PembacaanMeteran
     * User sebagai petugas bisa melakukan banyak pembacaan meteran
     */
    public function pembacaanMeteran(): HasMany
    {
        return $this->hasMany(PembacaanMeteran::class, 'petugas_id');
    }

    /**
     * Relasi dengan model Tagihan
     */
    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    // Role checker methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPelanggan()
    {
        return $this->role === 'pelanggan';
    }

    public function isSuperuser()
    {
        return $this->role === 'superuser';
    }

    public function isPetugas()
    {
        return $this->role === 'petugas';
    }

    /**
     * Scope untuk mendapatkan user berdasarkan role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope untuk mendapatkan user aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Accessor untuk mendapatkan nama lengkap dengan role
     */
    public function getNameWithRoleAttribute()
    {
        return $this->name . ' (' . ucfirst($this->role) . ')';
    }
}