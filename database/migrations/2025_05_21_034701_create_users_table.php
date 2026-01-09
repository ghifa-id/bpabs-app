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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
            $table->string('nik')->unique();
            $table->string('alamat')->nullable();
            $table->string('no_hp')->unique();
            $table->enum('role', ['admin', 'pelanggan', 'superuser', 'petugas'])->default('pelanggan');
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('username')->unique();
            $table->string('password')->nullable();
            $table->enum('status', ['set_password', 'active', 'inactive'])->default('set_password');
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
            
            // Indexes untuk optimasi query
            $table->index(['role', 'status']);
            $table->index(['status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};