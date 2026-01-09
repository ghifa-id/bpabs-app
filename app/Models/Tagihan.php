<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';

    // Status constants
    const STATUS_BELUM_BAYAR = 'belum_bayar';
    const STATUS_SUDAH_BAYAR = 'sudah_bayar';
    const STATUS_TERLAMBAT = 'terlambat';
    const STATUS_MENUNGGU_KONFIRMASI = 'menunggu_konfirmasi';

    protected $fillable = [
        'user_id',
        'meteran_id',
        'pembacaan_id',
        'pembayaran_id',
        'nomor_tagihan',
        'tanggal_tagihan',
        'tanggal_jatuh_tempo',
        'bulan',
        'tahun',
        'meter_awal',
        'meter_akhir',
        'jumlah_pemakaian',
        'tarif_per_kubik',
        'biaya_pemakaian',
        'biaya_admin',
        'biaya_beban',
        'denda',
        'total_tagihan',
        'status',
        'keterangan',
        'is_active',
        'is_manual_status',
        'manual_updated_by',
        'manual_updated_at'
    ];

    protected $casts = [
        'tanggal_tagihan' => 'datetime',
        'tanggal_jatuh_tempo' => 'date',
        'meter_awal' => 'decimal:2',
        'meter_akhir' => 'decimal:2',
        'jumlah_pemakaian' => 'decimal:2',
        'tarif_per_kubik' => 'decimal:2',
        'biaya_pemakaian' => 'decimal:2',
        'biaya_admin' => 'decimal:2',
        'biaya_beban' => 'decimal:2',
        'denda' => 'decimal:2',
        'total_tagihan' => 'decimal:2',
        'is_active' => 'boolean',
        'is_manual_status' => 'boolean',
        'manual_updated_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meteran()
    {
        return $this->belongsTo(Meteran::class);
    }

    public function pembacaanMeteran()
    {
        return $this->belongsTo(PembacaanMeteran::class, 'pembacaan_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    public function manualUpdatedBy()
    {
        return $this->belongsTo(User::class, 'manual_updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBelumBayar($query)
    {
        return $query->where('status', self::STATUS_BELUM_BAYAR);
    }

    public function scopeSudahBayar($query)
    {
        return $query->where('status', self::STATUS_SUDAH_BAYAR);
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', self::STATUS_TERLAMBAT);
    }

    // ✅ FIXED: Method untuk update status manual (hanya oleh admin)
    public function updateStatusManually($status, $adminId, $createPaymentIfPaid = true)
    {
        $oldStatus = $this->status;
        
        return DB::transaction(function () use ($status, $adminId, $oldStatus, $createPaymentIfPaid) {
            // Update status tagihan
            $this->update([
                'status' => $status,
                'is_manual_status' => true,
                'manual_updated_by' => $adminId,
                'manual_updated_at' => now(),
            ]);

            // Jika diubah menjadi "sudah_bayar" dan belum ada pembayaran, buat otomatis
            if ($status === self::STATUS_SUDAH_BAYAR && $createPaymentIfPaid && !$this->pembayaran) {
                $this->createAdminPayment($adminId);
            }

            // Jika diubah dari "sudah_bayar" ke status lain, update pembayaran terkait
            if ($oldStatus === self::STATUS_SUDAH_BAYAR && $status !== self::STATUS_SUDAH_BAYAR) {
                if ($this->pembayaran) {
                    $this->pembayaran->update([
                        'status' => 'cancelled',
                        'catatan_admin' => "Status tagihan diubah manual oleh admin menjadi: {$status}"
                    ]);
                }
            }

            Log::info('Manual status update', [
                'tagihan_id' => $this->id,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'admin_id' => $adminId,
                'auto_payment_created' => $status === self::STATUS_SUDAH_BAYAR && !$this->pembayaran
            ]);

            return true;
        });
    }

    // ✅ FIXED: Method untuk membuat pembayaran otomatis ketika admin set manual "sudah_bayar"
    private function createAdminPayment($adminId)
    {
        try {
            $pembayaran = new Pembayaran();
            $pembayaran->tagihan_id = $this->id;
            $pembayaran->jumlah = $this->total_tagihan;
            $pembayaran->tanggal_pembayaran = now();
            $pembayaran->metode_pembayaran = 'admin_manual';
            $pembayaran->status = 'lunas';
            $pembayaran->keterangan = 'Pembayaran dibuat otomatis karena admin mengubah status tagihan menjadi sudah bayar';
            $pembayaran->processed_by = 'admin_manual_' . $adminId;
            $pembayaran->processed_at = now();
            $pembayaran->is_verified = true;
            $pembayaran->verified_by = $adminId;
            $pembayaran->verified_at = now();
            $pembayaran->save();

            Log::info('Auto-created admin payment', [
                'tagihan_id' => $this->id,
                'pembayaran_id' => $pembayaran->id,
                'admin_id' => $adminId,
                'amount' => $this->total_tagihan
            ]);

            return $pembayaran;
        } catch (\Exception $e) {
            Log::error('Failed to create admin payment', [
                'tagihan_id' => $this->id,
                'admin_id' => $adminId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    // ✅ FIXED: Method untuk cek apakah boleh di-auto sync
    public function shouldAutoSync()
    {
        // Jangan auto sync jika:
        // 1. Status diubah manual dalam 24 jam terakhir
        // 2. Tidak ada pembayaran terkait (untuk manual "sudah_bayar")
        
        if (!$this->is_manual_status) {
            return true; // Boleh auto sync jika bukan manual
        }

        // Jika manual update sudah lebih dari 24 jam, boleh auto sync lagi
        if ($this->manual_updated_at && $this->manual_updated_at->diffInHours(now()) > 24) {
            return true;
        }

        // Jika manual set "sudah_bayar" tapi ada pembayaran lunas, jangan sync
        if ($this->status === self::STATUS_SUDAH_BAYAR && $this->pembayaran && $this->pembayaran->status === 'lunas') {
            return false;
        }

        return false; // Default: jangan auto sync jika baru diubah manual
    }

    // ✅ FIXED: Method untuk reset manual status
    public function resetManualStatus()
    {
        return $this->update([
            'is_manual_status' => false,
            'manual_updated_by' => null,
            'manual_updated_at' => null,
        ]);
    }

    // Legacy method - keep for backward compatibility
    public function updateStatus()
    {
        if (!$this->shouldAutoSync()) {
            Log::info('Skipping auto status update for manual tagihan', [
                'tagihan_id' => $this->id,
                'is_manual_status' => $this->is_manual_status,
                'manual_updated_at' => $this->manual_updated_at
            ]);
            return false;
        }

        $today = Carbon::now();
        $oldStatus = $this->status;
        $newStatus = $oldStatus;

        // Check if there's a successful payment
        if ($this->pembayaran && $this->pembayaran->status === 'lunas' && $this->pembayaran->is_verified) {
            $newStatus = self::STATUS_SUDAH_BAYAR;
        } elseif ($this->tanggal_jatuh_tempo && Carbon::parse($this->tanggal_jatuh_tempo)->isPast()) {
            if ($oldStatus === self::STATUS_BELUM_BAYAR) {
                $newStatus = self::STATUS_TERLAMBAT;
            }
        }

        if ($oldStatus !== $newStatus) {
            $this->update(['status' => $newStatus]);
            
            Log::info('Auto status update', [
                'tagihan_id' => $this->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'has_payment' => $this->pembayaran ? true : false,
                'payment_status' => $this->pembayaran->status ?? null
            ]);

            return true;
        }

        return false;
    }

    // Accessor untuk pemakaian (untuk backward compatibility)
    public function getPemakaianAttribute()
    {
        return $this->jumlah_pemakaian;
    }

    // Accessor untuk tarif per m3 (untuk backward compatibility)  
    public function getTarifPerM3Attribute()
    {
        return $this->tarif_per_kubik;
    }

    // Method untuk format tanggal
    public function getFormattedTanggalTagihanAttribute()
    {
        return $this->tanggal_tagihan ? $this->tanggal_tagihan->format('d M Y') : '';
    }

    public function getFormattedTanggalJatuhTempoAttribute()
    {
        return $this->tanggal_jatuh_tempo ? Carbon::parse($this->tanggal_jatuh_tempo)->format('d M Y') : '';
    }

    // Method untuk cek status
    public function isOverdue()
    {
        if (!$this->tanggal_jatuh_tempo) {
            return false;
        }

        return Carbon::parse($this->tanggal_jatuh_tempo)->isPast() && 
               !in_array($this->status, [self::STATUS_SUDAH_BAYAR]);
    }

    public function isPaid()
    {
        return $this->status === self::STATUS_SUDAH_BAYAR;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_MENUNGGU_KONFIRMASI;
    }

    public function isUnpaid()
    {
        return in_array($this->status, [self::STATUS_BELUM_BAYAR, self::STATUS_TERLAMBAT]);
    }

    // Method untuk format currency
    public function getFormattedTotalTagihanAttribute()
    {
        return 'Rp ' . number_format($this->total_tagihan, 0, ',', '.');
    }

    public function getFormattedBiayaPemakaianAttribute()
    {
        return 'Rp ' . number_format($this->biaya_pemakaian, 0, ',', '.');
    }

    public function getFormattedBiayaAdminAttribute()
    {
        return 'Rp ' . number_format($this->biaya_admin, 0, ',', '.');
    }

    // Boot method untuk auto-generate nomor tagihan
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tagihan) {
            if (empty($tagihan->nomor_tagihan)) {
                $tagihan->nomor_tagihan = 'TGH-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
            
            if (is_null($tagihan->is_active)) {
                $tagihan->is_active = true;
            }
            
            if (is_null($tagihan->is_manual_status)) {
                $tagihan->is_manual_status = false;
            }
        });

        static::updating(function ($tagihan) {
            // Auto-calculate jumlah_pemakaian if meter values change
            if ($tagihan->isDirty(['meter_awal', 'meter_akhir'])) {
                $tagihan->jumlah_pemakaian = $tagihan->meter_akhir - $tagihan->meter_awal;
            }

            // Auto-calculate biaya_pemakaian if pemakaian or tarif changes
            if ($tagihan->isDirty(['jumlah_pemakaian', 'tarif_per_kubik'])) {
                $tagihan->biaya_pemakaian = $tagihan->jumlah_pemakaian * $tagihan->tarif_per_kubik;
            }

            // Auto-calculate total_tagihan if any biaya changes
            if ($tagihan->isDirty(['biaya_pemakaian', 'biaya_admin', 'biaya_beban', 'denda'])) {
                $tagihan->total_tagihan = $tagihan->biaya_pemakaian + $tagihan->biaya_admin + $tagihan->biaya_beban + $tagihan->denda;
            }
        });
    }
}