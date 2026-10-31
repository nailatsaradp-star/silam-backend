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
            $table->decimal('uang_diterima', 12, 2)->default(0)->after('metode_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_pesanan', function (Blueprint $table) {
            $table->dropColumn(['uang_diterima']);
        });
    }
};
