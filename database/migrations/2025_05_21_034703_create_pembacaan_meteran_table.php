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
        Schema::create('pembacaan_meteran', function (Blueprint $table) {
            $table->id()->primary();
            $table->unsignedBigInteger('id_meteran');
            $table->foreign('id_meteran')->references('id')->on('meteran');
            
            // Field petugas_id untuk tracking siapa yang melakukan pembacaan
            $table->unsignedBigInteger('petugas_id')->nullable()
                  ->comment('ID petugas dari tabel users dengan role petugas');
            $table->foreign('petugas_id')->references('id')->on('users')->onDelete('set null');
            
            $table->datetime('tanggal_meteran');
            $table->string('bulan');
            $table->integer('tahun');
            $table->decimal('meter_awal', 10, 2);
            $table->decimal('meter_akhir', 10, 2);
            
            // Update enum status dengan opsi yang lebih lengkap
            $table->enum('status', ['belum_dibaca', 'sudah_dibaca', 'selesai', 'pending', 'bermasalah'])
                  ->default('belum_dibaca');
            
            // Field tambahan untuk dokumentasi dan tracking
            $table->text('catatan')->nullable()
                  ->comment('Catatan tambahan dari petugas');
            $table->string('foto_meteran')->nullable()
                  ->comment('Path file foto meteran');
            
            // Koordinat GPS untuk tracking lokasi pembacaan
            $table->decimal('latitude', 10, 8)->nullable()
                  ->comment('Koordinat lintang lokasi pembacaan');
            $table->decimal('longitude', 11, 8)->nullable()
                  ->comment('Koordinat bujur lokasi pembacaan');
            
            $table->timestamps();
            
            // Indexes untuk optimasi query
            $table->index(['tanggal_meteran', 'petugas_id'], 'idx_tanggal_petugas');
            $table->index(['status'], 'idx_status');
            $table->index(['petugas_id'], 'idx_petugas');
            $table->index(['id_meteran'], 'idx_meteran');
            $table->index(['created_at'], 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembacaan_meteran');
    }
};