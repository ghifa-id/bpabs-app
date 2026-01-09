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
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('meteran_id');
            $table->unsignedBigInteger('pembacaan_id')->nullable();
            $table->unsignedBigInteger('pembayaran_id')->nullable(); // Tanpa constraint dulu
            
            // Informasi Tagihan
            $table->string('nomor_tagihan')->unique();
            $table->datetime('tanggal_tagihan');
            $table->date('tanggal_jatuh_tempo');
            $table->string('bulan');
            $table->integer('tahun');
            
            // Data Pemakaian
            $table->decimal('meter_awal', 15, 3)->default(0);
            $table->decimal('meter_akhir', 15, 3)->default(0);
            $table->decimal('jumlah_pemakaian', 15, 3)->default(0);
            
            // Data Tarif dan Biaya
            $table->decimal('tarif_per_kubik', 15, 2)->default(0);
            $table->decimal('biaya_pemakaian', 15, 2)->default(0);
            $table->decimal('biaya_admin', 15, 2)->default(0);
            $table->decimal('biaya_beban', 15, 2)->default(0);
            $table->decimal('denda', 15, 2)->default(0);
            $table->decimal('total_tagihan', 15, 2);
            
            // Status Pembayaran
            $table->string('status', 50)->default('belum_bayar')
                ->comment('Status: belum_bayar, sudah_bayar, terlambat, menunggu_konfirmasi');
            
            // Informasi Tambahan
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Foreign Key Constraints (hanya untuk tabel yang sudah ada)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('meteran_id')->references('id')->on('meteran')->onDelete('cascade');
            $table->foreign('pembacaan_id')->references('id')->on('pembacaan_meteran')->onDelete('set null');
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['meteran_id', 'tahun', 'bulan']);
            $table->index('tanggal_jatuh_tempo');
            $table->index('status');
            $table->index('pembayaran_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};