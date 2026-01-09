<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Events\PembayaranStatusUpdated;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    
    // Konstanta status pembayaran
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid'; 
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    // Konstanta metode pembayaran
    const METHOD_MANUAL = 'manual';
    const METHOD_MIDTRANS = 'midtrans';
    const METHOD_ONLINE_PAYMENT = 'online_payment'; // TAMBAHAN
    const METHOD_TRANSFER = 'transfer';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_VA = 'virtual_account';
    const METHOD_VIRTUAL_ACCOUNT = 'virtual_account';
    const METHOD_CREDIT_CARD = 'credit_card';
    const METHOD_EWALLET = 'ewallet';
    const METHOD_E_WALLET = 'e_wallet';
    const METHOD_CONVENIENCE_STORE = 'convenience_store';
    const METHOD_CASH = 'cash';

    // Konstanta status Midtrans
    const MIDTRANS_CAPTURE = 'capture';
    const MIDTRANS_SETTLEMENT = 'settlement';
    const MIDTRANS_PENDING = 'pending';
    const MIDTRANS_DENY = 'deny';
    const MIDTRANS_CANCEL = 'cancel';
    const MIDTRANS_EXPIRE = 'expire';
    const MIDTRANS_FAILURE = 'failure';

    protected $fillable = [
        'tagihan_id',
        'nomor_pembayaran',
        'jumlah_bayar',
        'gross_amount',
        'tanggal_pembayaran',
        'transaction_time',
        'status',
        'metode_pembayaran',
        'payment_type',
        'transaction_id',
        'order_id',
        'fraud_status',
        'midtrans_response',
        'bank',
        'va_number',
        'bill_key',
        'biller_code',
        'processed_at',
        'processed_by',
        'expired_at',
        'keterangan',
        'catatan_admin',
        'is_verified',
        'verified_at',
        'verified_by',
        'verification_note',
        'snap_token',
        'finish_redirect_url',
        'pdf_url'
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'datetime',
        'transaction_time' => 'datetime',
        'processed_at' => 'datetime',
        'expired_at' => 'datetime',
        'verified_at' => 'datetime',
        'midtrans_response' => 'array',
        'jumlah_bayar' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'is_verified' => 'boolean'
    ];

    protected $appends = [
        'status_label',
        'status_color',
        'formatted_amount',
        'metode_label',
        'is_expired'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_pembayaran)) {
                $model->nomor_pembayaran = $model->generateNomorPembayaran();
            }
            
            // Set gross_amount default sama dengan jumlah_bayar
            if (empty($model->gross_amount) && !empty($model->jumlah_bayar)) {
                $model->gross_amount = $model->jumlah_bayar;
            }
        });
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Relasi ke Tagihan
     */
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }

    /**
     * Relasi ke User (yang memverifikasi)
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Relasi ke User melalui tagihan (pelanggan)
     */
    public function pelanggan()
    {
        return $this->hasOneThrough(
            User::class,
            Tagihan::class,
            'id',
            'id', 
            'tagihan_id',
            'user_id'
        );
    }

    // =============================================
    // SCOPES (tetap sama seperti sebelumnya)
    // =============================================

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_pembayaran', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal_pembayaran', now()->month)
                    ->whereYear('tanggal_pembayaran', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('tanggal_pembayaran', now()->year);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('metode_pembayaran', $method);
    }

    public function scopeByBank($query, $bank)
    {
        return $query->where('bank', $bank);
    }

    public function scopeMidtrans($query)
    {
        return $query->whereIn('metode_pembayaran', [self::METHOD_MIDTRANS, self::METHOD_ONLINE_PAYMENT]);
    }

    // =============================================
    // ACCESSORS
    // =============================================

    /**
     * Label status dalam bahasa Indonesia
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PAID => 'Lunas',
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_FAILED => 'Gagal',
            self::STATUS_CANCELLED => 'Dibatalkan',
            self::STATUS_EXPIRED => 'Kedaluwarsa',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Warna untuk status badge
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PAID => 'success',
            self::STATUS_PENDING => 'warning',
            self::STATUS_FAILED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            self::STATUS_EXPIRED => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Label metode pembayaran
     */
    public function getMetodeLabelAttribute()
    {
        return match($this->metode_pembayaran) {
            self::METHOD_MANUAL => 'Manual',
            self::METHOD_MIDTRANS => 'Midtrans',
            self::METHOD_ONLINE_PAYMENT => 'Pembayaran Online',
            self::METHOD_TRANSFER => 'Transfer Bank',
            self::METHOD_BANK_TRANSFER => 'Transfer Bank',
            self::METHOD_VA => 'Virtual Account',
            self::METHOD_VIRTUAL_ACCOUNT => 'Virtual Account',
            self::METHOD_CREDIT_CARD => 'Kartu Kredit',
            self::METHOD_EWALLET => 'E-Wallet',
            self::METHOD_E_WALLET => 'E-Wallet',
            self::METHOD_CONVENIENCE_STORE => 'Convenience Store',
            self::METHOD_CASH => 'Tunai',
            default => ucfirst(str_replace('_', ' ', $this->metode_pembayaran ?? 'Unknown'))
        };
    }

    /**
     * Format amount dalam rupiah
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_bayar, 0, ',', '.');
    }

    /**
     * Cek apakah pembayaran sudah expired
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->expired_at) {
            return false;
        }
        
        return Carbon::parse($this->expired_at)->isPast();
    }

    // =============================================
    // METHODS
    // =============================================

    /**
     * Generate nomor pembayaran otomatis
     */
    public function generateNomorPembayaran()
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');
        $lastPayment = static::whereDate('created_at', today())
            ->where('nomor_pembayaran', 'like', $prefix . $date . '%')
            ->orderBy('nomor_pembayaran', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = intval(substr($lastPayment->nomor_pembayaran, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Mark pembayaran sebagai paid
     */
    public function markAsPaid($processedBy = null)
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'processed_by' => $processedBy ?: 'system',
            'processed_at' => now()
        ]);

        // Update tagihan status
        if ($this->tagihan) {
            $this->tagihan->status = Tagihan::STATUS_SUDAH_BAYAR;
            $this->tagihan->save();
        }

        return $this;
    }

    /**
     * Mark pembayaran sebagai failed
     */
    public function markAsFailed($reason = null, $processedBy = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'keterangan' => $reason,
            'processed_by' => $processedBy ?: 'system',
            'processed_at' => now()
        ]);

        // Update tagihan status
        if ($this->tagihan) {
            $this->tagihan->updateStatus();
        }

        return $this;
    }

    /**
     * Cancel pembayaran
     */
    public function cancel($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'keterangan' => $reason,
            'processed_at' => now()
        ]);

        // Update tagihan status
        if ($this->tagihan) {
            $this->tagihan->updateStatus();
        }

        return $this;
    }

    /**
     * Verify pembayaran
     */
    public function verify($verifiedBy, $note = null)
    {
        $this->update([
            'is_verified' => true,
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
            'verification_note' => $note
        ]);

        return $this;
    }

    /**
     * Update pembayaran dari response Midtrans
     */
    public function updateFromMidtrans($data)
    {
        $status = $this->mapMidtransStatus($data['transaction_status']);
        
        $updateData = [
            'fraud_status' => $data['fraud_status'] ?? null,
            'transaction_time' => isset($data['transaction_time']) ? 
                Carbon::createFromFormat('Y-m-d H:i:s', $data['transaction_time']) : null,
            'status' => $status,
            'midtrans_response' => $data,
            'processed_by' => 'midtrans_webhook',
            'processed_at' => now()
        ];

        // Add payment method specific data
        if (isset($data['payment_type'])) {
            $updateData['payment_type'] = $data['payment_type'];
        }

        if (isset($data['va_numbers']) && !empty($data['va_numbers'])) {
            $updateData['va_number'] = $data['va_numbers'][0]['va_number'];
            $updateData['bank'] = $data['va_numbers'][0]['bank'];
        }

        if (isset($data['pdf_url'])) {
            $updateData['pdf_url'] = $data['pdf_url'];
        }

        $this->update($updateData);

        // Auto verify if paid
        if ($status === self::STATUS_PAID && !$this->is_verified) {
            $this->verify('system', 'Auto verified from Midtrans');
        }

        // Update tagihan status
        if ($this->tagihan) {
            $this->tagihan->updateStatus();
        }

        return $this;
    }

    /**
     * Map status Midtrans ke status internal
     */
    private function mapMidtransStatus($midtransStatus)
    {
        $mapping = [
            self::MIDTRANS_CAPTURE => self::STATUS_PAID,
            self::MIDTRANS_SETTLEMENT => self::STATUS_PAID,
            self::MIDTRANS_PENDING => self::STATUS_PENDING,
            self::MIDTRANS_DENY => self::STATUS_FAILED,
            self::MIDTRANS_CANCEL => self::STATUS_CANCELLED,
            self::MIDTRANS_EXPIRE => self::STATUS_EXPIRED,
            self::MIDTRANS_FAILURE => self::STATUS_FAILED
        ];

        return $mapping[$midtransStatus] ?? self::STATUS_PENDING;
    }

    /**
     * Cek apakah pembayaran bisa diedit
     */
    public function canBeEdited()
    {
        return !($this->status === self::STATUS_PAID && $this->is_verified);
    }

    /**
     * Cek apakah pembayaran bisa dihapus
     */
    public function canBeDeleted()
    {
        return !($this->status === self::STATUS_PAID && $this->is_verified);
    }

    /**
     * Cek status pembayaran
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isVerified()
    {
        return $this->is_verified;
    }

    public function isMidtransPayment()
    {
        return in_array($this->metode_pembayaran, [self::METHOD_MIDTRANS, self::METHOD_ONLINE_PAYMENT]);
    }
    protected static function booted()
    {
        static::updated(function ($pembayaran) {
            // Cek jika status berubah
            if ($pembayaran->isDirty('status')) {
                $oldStatus = $pembayaran->getOriginal('status');
                $newStatus = $pembayaran->status;
                
                // Trigger event
                event(new PembayaranStatusUpdated(
                    $pembayaran, 
                    $oldStatus, 
                    $newStatus
                ));
            }
            
            // Cek jika verifikasi status berubah
            if ($pembayaran->isDirty('is_verified') && $pembayaran->is_verified) {
                event(new PembayaranStatusUpdated(
                    $pembayaran,
                    $pembayaran->getOriginal('status'),
                    $pembayaran->status
                ));
            }
        });
    }
    }