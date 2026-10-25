<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelangganModel extends Model
{
    protected $table = 'tbl_pelanggan';
    protected $primaryKey = 'id_pelanggan';

    protected $fillable = [
        'nama_pelanggan',
        'nomor_telepon',
        'alamat'
    ];

    /**
     * Relasi ke model Pesanan
     */
    public function pesanan()
    {
        return $this->hasMany(PesananModel::class, 'id_pelanggan', 'id_pelanggan');
    }
}
