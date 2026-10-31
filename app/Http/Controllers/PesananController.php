<?php

namespace App\Http\Controllers;

use App\Models\LayananModel;
use App\Models\PelangganModel;
use App\Models\PesananModel;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $items = PesananModel::with(['pelanggan', 'kasir'])
            ->when($request->q, function ($query, $q) {
                $query->where('nomor_pesanan', 'like', "%{$q}%")
                      ->orWhereHas('pelanggan', function ($p) use ($q) {
                          $p->where('nama_pelanggan', 'like', "%{$q}%");
                      });
            })
            ->when($request->status_kerja, function ($query, $sk) {
                $query->where('status_kerja', $sk);
            })
            ->when($request->status_pembayaran, function ($query, $sp) {
                $query->where('status_pembayaran', $sp);
            })
            ->orderByDesc('id_pesanan')
            ->paginate(10)
            ->withQueryString();

        return view('hak_akses.kasir.pesanan.index', compact('items'));
    }

    public function create(Request $request)
    {
        $pelanggan = PelangganModel::orderBy('nama_pelanggan', 'asc')->get();
        $layanan   = LayananModel::orderBy('nama_layanan', 'asc')->get();

        return view('hak_akses.kasir.pesanan.create', compact('pelanggan', 'layanan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_pelanggan'       => ['required', Rule::exists(PelangganModel::class, 'id_pelanggan')],
            'items'              => 'required|array|min:1',
            'items.*.id_layanan' => ['required', Rule::exists(LayananModel::class, 'id_layanan')],
            'items.*.jumlah'     => 'required|numeric|min:0.1',
            'status_pembayaran'  => 'required|in:lunas,belum_lunas',
            'metode_pembayaran'  => [
                'nullable', 'string', 'max:30',
                Rule::requiredIf(fn () => $request->status_pembayaran === 'lunas'),
            ],
            'uang_diterima'      => [
                'nullable', 'numeric', 'min:0',
                Rule::requiredIf(fn () => $request->status_pembayaran === 'lunas'
                    && strcasecmp((string) $request->metode_pembayaran, 'Tunai') === 0),
            ],
        ], [
            'metode_pembayaran.required' => 'Pilih metode pembayaran untuk pesanan yang langsung dibayar.',
            'uang_diterima.required'     => 'Masukkan jumlah uang yang diterima dari pelanggan.',
        ]);

        // 1. Hitung dulu total dan estimasi terlama (JAM). Harga selalu dari server.
        $layanan = LayananModel::whereIn('id_layanan', collect($data['items'])->pluck('id_layanan'))
            ->get()->keyBy('id_layanan');

        $totalTagihan = 0;
        $maxJam = 0;
        foreach ($data['items'] as $item) {
            $l = $layanan[$item['id_layanan']];
            $totalTagihan += $l->harga_satuan * $item['jumlah'];
            $maxJam = max($maxJam, (int) $l->estimasi_selesai);
        }
        $totalTagihan = round($totalTagihan, 2);

        // 2. Cek pembayaran sebelum apa pun disimpan
        $langsungLunas = $data['status_pembayaran'] === 'lunas';
        $tunai = $langsungLunas && strcasecmp($data['metode_pembayaran'] ?? '', 'Tunai') === 0;
        $kembalian = 0;

        if ($tunai) {
            $uang = (float) $data['uang_diterima'];

            if ($uang < $totalTagihan) {
                throw ValidationException::withMessages([
                    'uang_diterima' => 'Uang kurang Rp ' . number_format($totalTagihan - $uang, 0, ',', '.')
                        . ' (total tagihan Rp ' . number_format($totalTagihan, 0, ',', '.') . ').',
                ]);
            }
            $kembalian = $uang - $totalTagihan;
        }

        // 3. Simpan
        return DB::transaction(function () use ($data, $layanan, $totalTagihan, $maxJam, $langsungLunas, $kembalian) {
            $prefix = 'SLM-' . date('ymd');
            $urut = PesananModel::where('nomor_pesanan', 'like', $prefix . '-%')->lockForUpdate()->count() + 1;
            $nomorPesanan = $prefix . '-' . str_pad($urut, 3, '0', STR_PAD_LEFT);

            $sekarang = now();
            $pesanan = PesananModel::create([
                'nomor_pesanan'            => $nomorPesanan,
                'id_pelanggan'             => $data['id_pelanggan'],
                'id_user'                  => auth()->id(),
                'total_tagihan'            => $totalTagihan,
                'status_pembayaran'        => $data['status_pembayaran'],
                'metode_pembayaran'        => $langsungLunas ? $data['metode_pembayaran'] : null,
                'uang_diterima'            => $data['uang_diterima'] ?? 0,
                'status_kerja'             => 'antrian',
                'tanggal_pesanan_dibuat'   => $sekarang,
                'tanggal_estimasi_selesai' => $sekarang->copy()->addHours($maxJam),
            ]);

            foreach ($data['items'] as $item) {
                $l = $layanan[$item['id_layanan']];
                $pesanan->detail()->create([
                    'id_layanan'   => $item['id_layanan'],
                    'jumlah_berat' => $item['jumlah'],
                    'subtotal'     => $l->harga_satuan * $item['jumlah'],
                ]);
            }

            $pesanan->riwayat()->create([
                'id_user'       => auth()->id(),
                'status_detail' => 'Pesanan Diterima di Outlet',
                'keterangan'    => 'Pesanan baru berhasil dibuat oleh kasir.',
                'waktu_update'  => $sekarang,
            ]);

            return redirect()->route('pesanan.show', $pesanan->id_pesanan)
            ->with('success', 'Transaksi pesanan baru berhasil dibuat!');
        });
    }

    public function show($id)
    {
        $pesanan = PesananModel::with(['pelanggan', 'kasir', 'detail.layanan', 'riwayat'])
            ->findOrFail($id);

        return view('hak_akses.kasir.pesanan.show', compact('pesanan'));
    }

    public function bayar(Request $request, $id)
    {
        $pesanan = PesananModel::findOrFail($id);

        if ($pesanan->status_pembayaran === 'lunas') {
            return back()->with('error', 'Pesanan ini sudah lunas.');
        }

        $data = $request->validate([
            'metode_pembayaran' => 'required|string|max:30',
            'uang_diterima'     => [
                'nullable', 'numeric', 'min:0',
                Rule::requiredIf(fn () => strcasecmp((string) $request->metode_pembayaran, 'Tunai') === 0),
            ],
        ], [
            'uang_diterima.required' => 'Masukkan jumlah uang yang diterima dari pelanggan.',
        ]);

        $total = (float) $pesanan->total_tagihan;
        $kembalian = 0;

        if (strcasecmp($data['metode_pembayaran'], 'Tunai') === 0) {
            $uang = (float) $data['uang_diterima'];

            if ($uang < $total) {
                throw ValidationException::withMessages([
                    'uang_diterima' => 'Uang kurang Rp ' . number_format($total - $uang, 0, ',', '.') . '.',
                ]);
            }
            $kembalian = $uang - $total;
        }

        DB::transaction(function () use ($pesanan, $data) {
            $pesanan->update([
                'status_pembayaran' => 'lunas',
                'metode_pembayaran' => $data['metode_pembayaran'],
            ]);
            $pesanan->riwayat()->create([
                'id_user'       => auth()->id(),
                'status_detail' => 'Pembayaran lunas (' . $data['metode_pembayaran'] . ')',
                'keterangan'    => null,
                'waktu_update'  => now(),
            ]);
        });

        return back()->with('success', 'Status pembayaran berhasil diubah menjadi LUNAS.')
            ->with('kembalian', $kembalian);
    }

    public function updateStatus(Request $request, $id)
    {
        $pesanan = PesananModel::findOrFail($id);

        $data = $request->validate([
            'status_kerja'  => 'required|in:antrian,proses,siap_ambil,selesai',
            'status_detail' => 'nullable|string|max:255',
            'keterangan'    => 'nullable|string',
        ]);

        // Definisikan urutan tahapan status kerja
        $urutanStatus = [
            'antrian'    => 1,
            'proses'     => 2,
            'siap_ambil' => 3,
            'selesai'    => 4,
        ];

        $statusSekarang = $urutanStatus[$pesanan->status_kerja] ?? 1;
        $statusBaru = $urutanStatus[$data['status_kerja']] ?? 1;

        // Validasi: Status baru tidak boleh sama atau mundur dari status saat ini
        if ($statusBaru <= $statusSekarang) {
            return back()->with('error', 'Status tidak valid. Anda tidak dapat memilih status yang sama atau kembali ke status sebelumnya.');
        }

        // 1. Update status kerja utama pesanan
        $pesanan->update([
            'status_kerja' => $data['status_kerja'],
        ]);

        $labelStatus = [
            'antrian'    => 'Pesanan Masuk Antrian',
            'proses'     => 'Sedang Dicuci / Diproses',
            'siap_ambil' => 'Cucian Selesai & Siap Diambil',
            'selesai'    => 'Pesanan Telah Diserahkan ke Pelanggan',
        ];

        $namaUser = auth()->user()->nama_user ?? auth()->user()->name ?? 'Petugas';

        // 2. Insert record baru ke tabel riwayat status
        $pesanan->riwayat()->create([
            'id_user'       => auth()->id(),
            'status_detail' => $data['status_detail'] ?: ($labelStatus[$data['status_kerja']] ?? $data['status_kerja']),
            'keterangan'    => $data['keterangan'] ?? 'Diperbarui oleh ' . $namaUser,
            'waktu_update'  => now(),
        ]);

        return redirect()->back()->with('success', 'Status pengerjaan cucian berhasil diperbarui.');
    }

    public function print($id)
    {
        // Ambil data pesanan lengkap dengan relasinya
        $pesanan = PesananModel::with(['pelanggan', 'detail.layanan', 'kasir'])->findOrFail($id);

        // Kembalikan ke view khusus cetak nota
        return view('hak_akses.kasir.pesanan.print', compact('pesanan'));
    }
}
