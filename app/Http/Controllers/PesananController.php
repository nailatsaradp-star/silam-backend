<?php

namespace App\Http\Controllers;

use App\Models\LayananModel;
use App\Models\PelangganModel;
use App\Models\PesananModel;
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
            'metode_pembayaran'  => 'required|string',
        ]);

        return DB::transaction(function () use ($data) {
            // 1. Hitung total tagihan dari item cucian
            $totalTagihan = 0;
            foreach ($data['items'] as $item) {
                $layanan = LayananModel::findOrFail($item['id_layanan']);
                $totalTagihan += $layanan->harga_satuan * $item['jumlah'];
            }

            // 2. Generate nomor pesanan otomatis
            $nomorPesanan = 'SLM-' . date('ymd') . '-' . rand(100, 999);

            // 3. Simpan header pesanan
            $pesanan = PesananModel::create([
                'nomor_pesanan'            => $nomorPesanan,
                'id_pelanggan'             => $data['id_pelanggan'],
                'id_user'                  => auth()->id(),
                'total_tagihan'            => $totalTagihan,
                'status_pembayaran'        => $data['status_pembayaran'],
                'metode_pembayaran'        => $data['metode_pembayaran'],
                'status_kerja'             => 'antrian',
                'tanggal_pesanan_dibuat'   => now(),
                'tanggal_estimasi_selesai' => now()->addDays(2),
            ]);

            // 4. Simpan detail item cucian
            foreach ($data['items'] as $item) {
                $layanan = LayananModel::findOrFail($item['id_layanan']);
                $pesanan->detail()->create([
                    'id_layanan'   => $item['id_layanan'],
                    'jumlah_berat' => $item['jumlah'],
                    'subtotal'     => $layanan->harga_satuan * $item['jumlah'],
                ]);
            }

            // 5. Simpan riwayat tracking awal
            $pesanan->riwayat()->create([
                'id_user'       => auth()->id(),
                'status_detail' => 'Pesanan Diterima di Outlet',
                'keterangan'    => 'Pesanan baru berhasil dibuat oleh kasir.',
                'waktu_update'  => now(),
            ]);

            return redirect()->route('pesanan.show', $pesanan->id_pesanan)
                ->with('success', 'Transaksi pesanan baru berhasil dibuat!');
        });
    }

    public function show($id)
    {
        // Menghapus 'riwayat.petugas' untuk mencegah RelationNotFoundException
        $pesanan = PesananModel::with(['pelanggan', 'kasir', 'detail.layanan', 'riwayat'])
            ->findOrFail($id);

        return view('hak_akses.kasir.pesanan.show', compact('pesanan'));
    }

    public function bayar($id)
    {
        $pesanan = PesananModel::findOrFail($id);
        $pesanan->update(['status_pembayaran' => 'lunas']);

        return redirect()->back()->with('success', 'Status pembayaran berhasil diubah menjadi LUNAS.');
    }

    public function updateStatus(Request $request, $id)
    {
        $pesanan = PesananModel::findOrFail($id);

        $data = $request->validate([
            'status_kerja'  => 'required|in:antrian,proses,siap_ambil,selesai',
            'status_detail' => 'nullable|string|max:255',
            'keterangan'    => 'nullable|string',
        ]);

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
}
