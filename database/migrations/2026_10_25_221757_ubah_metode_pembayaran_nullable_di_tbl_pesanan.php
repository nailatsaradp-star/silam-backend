<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_pesanan', function (Blueprint $table) {
            $table->enum('metode_pembayaran', ['Tunai', 'QRIS', 'Transfer'])->nullable()->change();        });
    }

    public function down(): void
    {
        // Isi dulu baris yang kosong, kalau tidak MySQL menolak mengembalikan NOT NULL
        DB::table('tbl_pesanan')->whereNull('metode_pembayaran')->update(['metode_pembayaran' => 'Tunai']);

        Schema::table('tbl_pesanan', function (Blueprint $table) {
            $table->string('metode_pembayaran')->nullable(false)->change();
        });
    }
};
