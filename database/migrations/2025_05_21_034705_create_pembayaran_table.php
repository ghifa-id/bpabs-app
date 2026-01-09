<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            
            // ==========================================
            // FOREIGN KEYS & RELATIONSHIPS
            // ==========================================
            $table->unsignedBigInteger('tagihan_id');
            $table->unsignedBigInteger('verified_by')->nullable();
            
            // ==========================================
            // IDENTIFIERS & REFERENCE NUMBERS
            // ==========================================
            $table->string('nomor_pembayaran')->unique();
            $table->string('order_id')->nullable()->unique()->index();
            $table->string('transaction_id')->nullable()->index();
            
            // ==========================================
            // FINANCIAL INFORMATION
            // ==========================================
            $table->decimal('jumlah_bayar', 15, 2);
            $table->decimal('gross_amount', 15, 2)->nullable();
            
            // ==========================================
            // TIMING INFORMATION
            // ==========================================
            $table->datetime('tanggal_pembayaran');
            $table->datetime('transaction_time')->nullable();
            $table->datetime('processed_at')->nullable();
            $table->datetime('expired_at')->nullable();
            $table->datetime('verified_at')->nullable();
            
            // ==========================================
            // STATUS & WORKFLOW
            // ==========================================
            $table->string('status', 50)->default('pending')
                ->comment('Status: pending, paid, failed, expired, cancelled');
            $table->boolean('is_verified')->default(false);
            
            // ==========================================
            // PAYMENT METHOD INFORMATION
            // ==========================================
            $table->string('metode_pembayaran', 100)->nullable()
                ->comment('Method: manual, online_payment, midtrans, bank_transfer, etc');
            $table->string('payment_type', 100)->nullable()
                ->comment('Midtrans payment type: bank_transfer, credit_card, ewallet, etc');
            
            // ==========================================
            // MIDTRANS INTEGRATION DATA
            // ==========================================
            $table->text('snap_token')->nullable()
                ->comment('Midtrans Snap token for payment popup');
            $table->string('fraud_status', 50)->nullable()
                ->comment('Midtrans fraud detection status');
            $table->json('midtrans_response')->nullable()
                ->comment('Complete response from Midtrans webhook');
            
            // ==========================================
            // BANK & PAYMENT CHANNEL INFO
            // ==========================================
            $table->string('bank', 100)->nullable()
                ->comment('Bank name for VA or bank transfer');
            $table->string('va_number', 100)->nullable()
                ->comment('Virtual Account number');
            $table->string('bill_key', 100)->nullable()
                ->comment('Bill key for convenience store payment');
            $table->string('biller_code', 100)->nullable()
                ->comment('Biller code for convenience store payment');
            
            // ==========================================
            // PROCESSING & AUDIT TRAIL
            // ==========================================
            $table->string('processed_by', 100)->nullable()
                ->comment('Who/what processed this payment: user_id, admin_id, system, webhook, etc');
            
            // ==========================================
            // NOTES & DESCRIPTIONS
            // ==========================================
            $table->text('keterangan')->nullable()
                ->comment('General notes about payment');
            $table->text('catatan_admin')->nullable()
                ->comment('Admin notes for internal use');
            $table->text('verification_note')->nullable()
                ->comment('Notes added during verification process');
            
            // ==========================================
            // ADDITIONAL URLS & REFERENCES
            // ==========================================
            $table->string('finish_redirect_url')->nullable()
                ->comment('URL to redirect after payment completion');
            $table->string('pdf_url')->nullable()
                ->comment('URL to payment receipt PDF');
            
            // ==========================================
            // TIMESTAMPS
            // ==========================================
            $table->timestamps();
            
            // ==========================================
            // FOREIGN KEY CONSTRAINTS
            // ==========================================
            $table->foreign('tagihan_id')
                ->references('id')
                ->on('tagihan')
                ->onDelete('cascade')
                ->name('fk_pembayaran_tagihan');
                
            $table->foreign('verified_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->name('fk_pembayaran_verified_by');
            
            // ==========================================
            // INDEXES FOR PERFORMANCE
            // ==========================================
            
            // Composite indexes for common queries
            $table->index(['tagihan_id', 'status'], 'idx_pembayaran_tagihan_status');
            $table->index(['status', 'is_verified'], 'idx_pembayaran_status_verified');
            $table->index(['metode_pembayaran', 'status'], 'idx_pembayaran_method_status');
            
            // Date-based indexes for reporting
            $table->index('tanggal_pembayaran', 'idx_pembayaran_tanggal');
            $table->index('transaction_time', 'idx_pembayaran_transaction_time');
            $table->index('processed_at', 'idx_pembayaran_processed_at');
            
            // Payment gateway specific indexes
            $table->index(['transaction_id', 'order_id'], 'idx_pembayaran_midtrans_ids');
            $table->index('fraud_status', 'idx_pembayaran_fraud_status');
            $table->index('bank', 'idx_pembayaran_bank');
            $table->index('va_number', 'idx_pembayaran_va_number');
            
            // Amount-based indexes for financial queries
            $table->index('jumlah_bayar', 'idx_pembayaran_jumlah');
            $table->index('gross_amount', 'idx_pembayaran_gross_amount');
            
            // Status workflow indexes
            $table->index(['status', 'tanggal_pembayaran'], 'idx_pembayaran_status_date');
            $table->index(['is_verified', 'verified_at'], 'idx_pembayaran_verification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};