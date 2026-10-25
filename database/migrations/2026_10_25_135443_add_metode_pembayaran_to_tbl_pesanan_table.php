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
        Schema::table('tbl_pesanan', function (Blueprint $table) {
            $table->enum('metode_pembayaran', ['Tunai', 'QRIS', 'Transfer'])->default('Tunai')->after('status_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_pesanan', function (Blueprint $table) {
            $table->dropColumn('metode_pembayaran');
        });
    }
};
