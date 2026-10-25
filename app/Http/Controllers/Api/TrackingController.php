<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PelangganModel;
use App\Models\PesananModel;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim((string) $request->query('q'));
        abort_if($q === '', 422, 'Parameter q wajib diisi.');

        $with = ['pelanggan', 'detail.layanan', 'riwayat'];
        $order = PesananModel::with($with)->where('nomor_pesanan', strtoupper($q))->first();

        if (! $order && preg_match('/^[0-9+\-\s]{8,20}$/', $q)) {
            $no = preg_replace('/[^0-9]/', '', $q);
            $no = str_starts_with($no, '62') ? '0' . substr($no, 2) : $no;

            $pelanggan = PelangganModel::where('nomor_telepon', $no)->first();
            if ($pelanggan) {
                $order = PesananModel::with($with)->where('id_pelanggan', $pelanggan->id_pelanggan)
                    ->orderByDesc('id_pesanan')->first();
            }
        }

        abort_if(! $order, 404, 'Pesanan tidak ditemukan.');

        // Data publik: nama disamarkan, tanpa alamat dan nomor telepon
        $bagian = preg_split('/\s+/', trim($order->pelanggan->nama_pelanggan));
        $depan = array_shift($bagian);
        $inisial = collect($bagian)->map(fn ($b) => mb_substr($b, 0, 1) . '.')->implode(' ');

        return response()->json([
            'nomor_pesanan'    => $order->nomor_pesanan,
            'pelanggan'        => trim($depan . ' ' . $inisial),
            'status_kerja'     => $order->status_kerja,
            'status_label'     => $order->label_kerja,
            'status_pembayaran' => $order->status_pembayaran,
            'total_tagihan'    => $order->total_tagihan,
            'tanggal_masuk'    => $order->tanggal_pesanan_dibuat,
            'estimasi_selesai' => $order->tanggal_estimasi_selesai,
            'layanan'          => $order->detail->map(fn ($d) => [
                'nama' => $d->layanan->nama_layanan, 'jumlah' => $d->jumlah_berat, 'satuan' => $d->layanan->satuan,
            ]),
            'riwayat'          => $order->riwayat->map(fn ($r) => [
                'status' => $r->status_detail, 'keterangan' => $r->keterangan, 'waktu' => $r->waktu_update,
            ]),
        ]);
    }
}
