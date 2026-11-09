<?php

namespace App\Services;

use App\Models\LayananModel;
use App\Models\PesananModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PesananService
{
    public function buat(array $data, User $kasir): PesananModel
    {
        $pesanan = DB::transaction(function () use ($data, $kasir) {
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
            $prefix = 'SLM-' . $now->format('Ymd');
            $urut = PesananModel::where('nomor_pesanan', 'like', $prefix . '-%')->lockForUpdate()->count() + 1;

            $statusBayar = $data['status_pembayaran'] ?? 'belum_lunas';
            $langsungLunas = $statusBayar === 'lunas';

            $p = PesananModel::create([
                'nomor_pesanan'            => $prefix . '-' . str_pad($urut, 4, '0', STR_PAD_LEFT),
                'id_pelanggan'             => $data['id_pelanggan'],
                'id_user'                  => $kasir->id_user,
                'total_tagihan'            => $total,
                'status_pembayaran'        => $statusBayar,
                'metode_pembayaran'        => $langsungLunas ? ($data['metode_pembayaran'] ?? null) : null,
                'uang_diterima'            => $langsungLunas ? ($data['uang_diterima'] ?? $total) : 0,
                'tanggal_pesanan_dibuat'   => $now,
                'tanggal_estimasi_selesai' => $now->copy()->addHours($maxJam),
                'status_kerja'             => 'antrian',
            ]);

            $p->detail()->createMany($rows);
            $p->riwayat()->create([
                'id_user'       => $kasir->id_user,
                'status_detail' => 'Pesanan Diterima di Outlet',
                'keterangan'    => 'Pesanan baru dibuat oleh kasir.',
                'waktu_update'  => $now,
            ]);

            return $p;
        });

        // Load relasi pelanggan untuk mengambil nomor HP
        $pesanan->load('pelanggan');

        // Kirim notifikasi WA Fonnte
        $this->kirimNotifikasiWA($pesanan);

        return $pesanan;
    }

    private function kirimNotifikasiWA(PesananModel $pesanan): void
    {
        try {
            $pelanggan = $pesanan->pelanggan;
            if (!$pelanggan || !$pelanggan->nomor_telepon) {
                Log::warning('WA Fonnte Batal: Nomor HP pelanggan tidak ditemukan.');
                return;
            }

            // Bersihkan format nomor HP (hilangkan spasi, strip, dan titik)
            $nomorHP = preg_replace('/[^0-9]/', '', $pelanggan->nomor_telepon);
            $kodeTracking = $pesanan->nomor_pesanan;
            $linkTracking = url('/status?q=' . $kodeTracking);
            $statusBayar = $pesanan->status_pembayaran === 'lunas' ? 'LUNAS' : 'BELUM LUNAS (Bayar saat ambil)';

            $statusBayar = $pesanan->status_pembayaran === 'lunas' ? 'LUNAS' : 'BELUM LUNAS';

            $pesan = "Yth. Bapak/Ibu *{$pelanggan->nama_pelanggan}*,\n\n"
                . "Terima kasih telah mempercayakan layanan cuci pakaian Anda kepada *SILAM Laundry*.\n\n"
                . "Berikut adalah rincian pesanan Anda:\n"
                . "• Nomor Pesanan: *{$kodeTracking}*\n"
                . "• Total Tagihan: Rp " . number_format($pesanan->total_tagihan, 0, ',', '.') . "\n"
                . "• Status Pembayaran: *{$statusBayar}*\n\n"
                . "Anda dapat memantau status pengerjaan pakaian Anda secara berkala melalui tautan berikut:\n"
                . "{$linkTracking}\n\n"
                . "Mohon simpan nomor pesanan *{$kodeTracking}* ini untuk keperluan pengambilan atau pengecekan status pesanan.\n\n"
                . "Hormat kami,\n"
                . "*SILAM Laundry Mandiri*";

            $tokenFonnte = '4oXVTj5YTJXWZBHGYYns';

            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withHeaders([
                    'Authorization' => $tokenFonnte,
                ])->post('https://api.fonnte.com/send', [
                    'target'      => $nomorHP,
                    'message'     => $pesan,
                    'countryCode' => '62',
                ]);

            Log::info('Respon Fonnte WA untuk #' . $kodeTracking . ': ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Gagal mengirim WhatsApp Fonnte: ' . $e->getMessage());
        }
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
