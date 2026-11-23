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

            $total = 0;
            $rows = [];
            $maxJam = 0;

            foreach ($data['items'] as $it) {
                $l = $layanan[$it['id_layanan']];
                $sub = round($l->harga_satuan * $it['jumlah'], 2);
                $total += $sub;
                $maxJam = max($maxJam, $l->estimasi_selesai);
                $rows[] = [
                    'id_layanan'   => $l->id_layanan,
                    'jumlah_berat' => $it['jumlah'],
                    'subtotal'     => $sub,
                ];
            }

            $now = now();
            $prefix = 'SLM-' . $now->format('ymd');
            $urut = PesananModel::where('nomor_pesanan', 'like', $prefix . '-%')->lockForUpdate()->count() + 1;

            $statusBayar = $data['status_pembayaran'] ?? 'belum_lunas';
            $langsungLunas = $statusBayar === 'lunas';

            $p = PesananModel::create([
                'nomor_pesanan'            => $prefix . '-' . str_pad($urut, 3, '0', STR_PAD_LEFT),
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

        // Muat ulang relasi pelanggan langsung dari database
        $pesanan = $pesanan->fresh(['pelanggan']);

        // Kirim notifikasi WA pesanan baru ke nomor pelanggan
        $this->kirimNotifikasiWA($pesanan);

        return $pesanan;
    }

    private function kirimNotifikasiWA(PesananModel $pesanan): void
    {
        try {
            $pelanggan = $pesanan->pelanggan()->first();
            if (!$pelanggan || empty($pelanggan->nomor_telepon)) {
                Log::warning('WA Fonnte Batal: Nomor HP pelanggan tidak ditemukan untuk #' . $pesanan->nomor_pesanan);
                return;
            }

            $nomorHP = $this->formatNomorWA($pelanggan->nomor_telepon);
            if (empty($nomorHP)) {
                Log::warning('WA Fonnte Batal: Format nomor HP pelanggan tidak valid (' . $pelanggan->nomor_telepon . ')');
                return;
            }

            $kodeTracking = $pesanan->nomor_pesanan;
            $linkTracking = url('/status?q=' . $kodeTracking);
            $statusBayar = $pesanan->status_pembayaran === 'lunas'
                ? 'LUNAS'
                : 'BELUM LUNAS (Bayar saat ambil)';

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

            $this->kirimPesanFonnte($nomorHP, $pesan, 'Pesanan Baru #' . $kodeTracking . ' - ' . $pelanggan->nama_pelanggan);

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

        // Saat petugas mengubah status menjadi 'siap_ambil', kirim WA otomatis ke nomor HP pelanggan
        if ($data['status_kerja'] === 'siap_ambil') {
            $pesananSegar = $pesanan->fresh(['pelanggan']);
            if ($pesananSegar) {
                $this->kirimNotifikasiSiapAmbil($pesananSegar);
            }
        }
    }

    public function kirimNotifikasiSiapAmbil(PesananModel $pesanan): void
    {
        try {
            // Query langsung relasi pelanggan (id_pelanggan), bukan tabel users/petugas
            $pelanggan = $pesanan->pelanggan()->first();
            if (!$pelanggan || empty($pelanggan->nomor_telepon)) {
                Log::warning('WA Siap Ambil Batal: Nomor HP pelanggan tidak ditemukan untuk pesanan #' . $pesanan->nomor_pesanan);
                return;
            }

            $nomorHP = $this->formatNomorWA($pelanggan->nomor_telepon);
            if (empty($nomorHP)) {
                Log::warning('WA Siap Ambil Batal: Format nomor HP pelanggan tidak valid (' . $pelanggan->nomor_telepon . ')');
                return;
            }

            $kodeTracking = $pesanan->nomor_pesanan;
            $linkTracking = url('/status?q=' . $kodeTracking);

            $isLunas = $pesanan->status_pembayaran === 'lunas';
            $catatanBayar = $isLunas
                ? "• Status Pembayaran: *LUNAS (Sudah Dibayar)*"
                : "• Status Pembayaran: *BELUM LUNAS*\n• Sisa Tagihan yang harus dibayar saat ambil: *Rp " . number_format($pesanan->total_tagihan, 0, ',', '.') . "*";

            $pesan = "Halo Kak *{$pelanggan->nama_pelanggan}*,\n\n"
                . "Kabar gembira! Cucian pakaian Anda di *SILAM Laundry* dengan nomor pesanan *{$kodeTracking}* sudah *SELESAI* dan *SIAP DIAMBIL*! 🧺✨\n\n"
                . "*Rincian Pesanan:*\n"
                . "• Nomor Pesanan: *{$kodeTracking}*\n"
                . "• Total Tagihan: Rp " . number_format($pesanan->total_tagihan, 0, ',', '.') . "\n"
                . "{$catatanBayar}\n\n"
                . "Silakan datang ke outlet SILAM Laundry untuk mengambil pakaian Anda pada jam operasional kami. Mohon tunjukkan nomor pesanan ini kepada kasir/petugas saat pengambilan.\n\n"
                . "Cek nota dan status tracking Anda melalui link berikut:\n"
                . "{$linkTracking}\n\n"
                . "Terima kasih banyak telah mempercayakan pakaian Anda kepada *SILAM Laundry Mandiri*! 🙏\n\n"
                . "Salam hangat,\n"
                . "*SILAM Laundry Mandiri*";

            $this->kirimPesanFonnte($nomorHP, $pesan, 'Siap Ambil #' . $kodeTracking . ' - ' . $pelanggan->nama_pelanggan);

        } catch (\Exception $e) {
            Log::error('Gagal mengirim WhatsApp Fonnte Siap Ambil: ' . $e->getMessage());
        }
    }

    public function tandaiLunas(PesananModel $pesanan, User $petugas): void
    {
        if ($pesanan->status_pembayaran === 'lunas') {
            throw ValidationException::withMessages(['status_pembayaran' => 'Pesanan sudah lunas.']);
        }

        DB::transaction(function () use ($pesanan, $petugas) {
            $pesanan->update(['status_pembayaran' => 'lunas']);
            $pesanan->riwayat()->create([
                'id_user'       => $petugas->id_user,
                'status_detail' => 'Pembayaran lunas',
                'keterangan'    => null,
                'waktu_update'  => now(),
            ]);
        });
    }

    /**
     * Normalisasi nomor HP pelanggan ke format internasional 628xxxxxxxxxx
     */
    private function formatNomorWA(string $nomor): string
    {
        $bersih = preg_replace('/[^0-9]/', '', $nomor);
        if (empty($bersih)) {
            return '';
        }

        if (str_starts_with($bersih, '0')) {
            return '62' . substr($bersih, 1);
        }

        if (str_starts_with($bersih, '8')) {
            return '62' . $bersih;
        }

        return $bersih;
    }

    /**
     * Kirim pesan WhatsApp via Fonnte ke nomor tujuan pelanggan
     */
    private function kirimPesanFonnte(string $nomorTujuan, string $pesan, string $konteksLog): void
    {
        $tokenFonnte = env('FONNTE_TOKEN', '4oXVTj5YTJXWZBHGYYns');

        Log::info("Mengirim WA Fonnte [{$konteksLog}] ke nomor pelanggan: {$nomorTujuan}");

        $response = Http::withoutVerifying()
            ->timeout(15)
            ->withHeaders([
                'Authorization' => $tokenFonnte,
            ])->post('https://api.fonnte.com/send', [
                'target'  => $nomorTujuan,
                'message' => $pesan,
            ]);

        Log::info("Respon Fonnte WA [{$konteksLog}] -> {$nomorTujuan}: " . $response->body());
    }
}
