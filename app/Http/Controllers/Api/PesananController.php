<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LayananModel;
use App\Models\PelangganModel;
use App\Models\PesananModel;
use App\Services\PesananService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PesananController extends Controller
{
    public function __construct(private PesananService $service) {}

    public function index(Request $request)
    {
        $items = PesananModel::with('pelanggan:id_pelanggan,nama_pelanggan')
            ->when($request->status_kerja, fn ($q, $v) => $q->where('status_kerja', $v))
            ->when($request->status_pembayaran, fn ($q, $v) => $q->where('status_pembayaran', $v))
            ->when($request->q, fn ($q, $s) => $q->where('nomor_pesanan', 'like', "%$s%"))
            ->orderByDesc('id_pesanan')
            ->paginate(15);

        return response()->json($items); // otomatis berisi data + info halaman
    }

    public function show(PesananModel $pesanan)
    {
        return response()->json($pesanan->load([
            'pelanggan', 'kasir:id_user,nama_user', 'detail.layanan', 'riwayat.petugas:id_user,nama_user',
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_pelanggan'       => ['required', Rule::exists(PelangganModel::class, 'id_pelanggan')->where('status', 'aktif')],
            'items'              => 'required|array|min:1',
            'items.*.id_layanan' => ['required', Rule::exists(LayananModel::class, 'id_layanan')],
            'items.*.jumlah'     => 'required|numeric|min:0.01',
        ]);

        $pesanan = $this->service->buat($data, $request->user());

        return response()->json($pesanan->load('pelanggan', 'detail.layanan'), 201);
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

    public function bayar(Request $request, PesananModel $pesanan)
    {
        $this->service->tandaiLunas($pesanan, $request->user());
        return response()->json($pesanan->fresh());
    }
}
