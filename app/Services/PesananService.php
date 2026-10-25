<?php

namespace App\Services;

use App\Models\LayananModel;
use App\Models\PesananModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PesananService
{
    public function buat(array $data, User $kasir): PesananModel
    {
        return DB::transaction(function () use ($data, $kasir) {
            $layanan = LayananModel::whereIn('id_layanan', collect($data['items'])->pluck('id_layanan'))
                ->get()->keyBy('id_layanan');

            $total = 0; $rows = []; $maxJam = 0;
            foreach ($data['items'] as $it) {
                $l = $layanan[$it['id_layanan']];
                $sub = round($l->harga_satuan * $it['jumlah'], 2);
                $total += $sub;
                $maxJam = max($maxJam, $l->estimasi_selesai);
                $rows[] = ['id_layanan' => $l->id_layanan, 'jumlah_berat' => $it['jumlah'], 'subtotal' => $sub];
            }

            $now = now();
            $prefix = 'SILAM-' . $now->format('Ymd');
            $urut = PesananModel::where('nomor_pesanan', 'like', $prefix . '-%')->lockForUpdate()->count() + 1;

            $p = PesananModel::create([
                'nomor_pesanan'            => $prefix . '-' . str_pad($urut, 4, '0', STR_PAD_LEFT),
                'id_pelanggan'             => $data['id_pelanggan'],
                'id_user'                  => $kasir->id_user,
                'total_tagihan'            => $total,
                'status_pembayaran'        => 'belum_lunas',
                'tanggal_pesanan_dibuat'   => $now,
                'tanggal_estimasi_selesai' => $now->copy()->addHours($maxJam),
                'status_kerja'             => 'antrian',
            ]);
            $p->detail()->createMany($rows);
            $p->riwayat()->create([
                'id_user' => $kasir->id_user, 'status_detail' => 'Pesanan diterima',
                'keterangan' => 'Pesanan dibuat oleh kasir.', 'waktu_update' => $now,
            ]);

            return $p;
        });
    }

    public function ubahStatus(PesananModel $pesanan, array $data, User $petugas): void
    {
        if ($data['status_kerja'] === 'selesai' && $pesanan->status_pembayaran !== 'lunas') {
            throw ValidationException::withMessages([
                'status_kerja' => 'Pesanan belum lunas, tidak bisa ditandai selesai.',
            ]);
        }

        DB::transaction(function () use ($pesanan, $data, $petugas) {
            $pesanan->update(['status_kerja' => $data['status_kerja']]);
            $pesanan->riwayat()->create([
                'id_user'       => $petugas->id_user,
                'status_detail' => $data['status_detail'] ?: PesananModel::STATUS_KERJA[$data['status_kerja']],
                'keterangan'    => $data['keterangan'] ?? null,
                'waktu_update'  => now(),
            ]);
        });
    }

    public function tandaiLunas(PesananModel $pesanan, User $petugas): void
    {
        if ($pesanan->status_pembayaran === 'lunas') {
            throw ValidationException::withMessages(['status_pembayaran' => 'Pesanan sudah lunas.']);
        }

        DB::transaction(function () use ($pesanan, $petugas) {
            $pesanan->update(['status_pembayaran' => 'lunas']);
            $pesanan->riwayat()->create([
                'id_user' => $petugas->id_user, 'status_detail' => 'Pembayaran lunas',
                'keterangan' => null, 'waktu_update' => now(),
            ]);
        });
    }
}
