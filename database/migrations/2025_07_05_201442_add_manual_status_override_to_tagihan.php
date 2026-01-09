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
        Schema::table('tagihan', function (Blueprint $table) {
            $table->boolean('is_manual_status')->default(false)->after('status');
            $table->unsignedBigInteger('manual_updated_by')->nullable()->after('is_manual_status');
            $table->timestamp('manual_updated_at')->nullable()->after('manual_updated_by');
            $table->foreign('manual_updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropForeign(['manual_updated_by']);
            $table->dropColumn(['is_manual_status', 'manual_updated_by', 'manual_updated_at']);
        });
    }
};
