<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananModel extends Model
{
    protected $table = 'tbl_pesanan';
    protected $primaryKey = 'id_pesanan';

    public const STATUS_KERJA = [
        'antrian'    => 'Antrian',
        'proses'     => 'Sedang Diproses',
        'siap_ambil' => 'Siap Diambil',
        'selesai'    => 'Selesai',
    ];

    // Ditambahkan: 'metode_pembayaran'
    protected $fillable = [
        'nomor_pesanan', 'id_pelanggan', 'id_user', 'total_tagihan',
        'status_pembayaran', 'metode_pembayaran', 'uang_diterima', 'tanggal_pesanan_dibuat',
        'tanggal_estimasi_selesai', 'status_kerja',
    ];

    protected $casts = [
        'tanggal_pesanan_dibuat'   => 'datetime',
        'tanggal_estimasi_selesai' => 'datetime',
        'total_tagihan'            => 'decimal:2',
    ];

    public function pelanggan() { return $this->belongsTo(PelangganModel::class, 'id_pelanggan', 'id_pelanggan'); }
    public function kasir()     { return $this->belongsTo(User::class, 'id_user', 'id_user'); }
    public function detail()    { return $this->hasMany(DetailPesananModel::class, 'id_pesanan', 'id_pesanan'); }
    public function riwayat()   { return $this->hasMany(RiwayatStatusModel::class, 'id_pesanan', 'id_pesanan')->orderByDesc('waktu_update')->orderByDesc('id_riwayat'); }

    public function getLabelKerjaAttribute(): string { return self::STATUS_KERJA[$this->status_kerja] ?? $this->status_kerja; }

    public function getBadgeKerjaAttribute(): string
    {
        $badge = [
            'antrian'    => 'secondary',
            'proses'     => 'primary',
            'siap_ambil' => 'warning',
        ];

        return $badge[$this->status_kerja] ?? 'success';
    }
}
