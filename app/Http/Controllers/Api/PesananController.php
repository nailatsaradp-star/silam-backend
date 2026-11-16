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
        $query = PesananModel::with([
                'pelanggan:id_pelanggan,nama_pelanggan,nomor_telepon',
                'detail.layanan'
            ])
            ->when($request->status_kerja, fn ($q, $v) => $q->where('status_kerja', $v))
            ->when($request->status_pembayaran, fn ($q, $v) => $q->where('status_pembayaran', $v))
            ->when($request->q, fn ($q, $s) => $q->where('nomor_pesanan', 'like', "%$s%"));

        // Jika membuka Riwayat (status_kerja=selesai), urutkan dari yang paling baru selesai
        if ($request->status_kerja === 'selesai') {
            $query->orderByDesc('updated_at')->orderByDesc('id_pesanan');
        } else {
            $query->orderByDesc('id_pesanan');
        }

        return response()->json($query->paginate($request->per_page ?? 15));
    }

    public function show($id)
    {
        $pesanan = PesananModel::with([
            'pelanggan',
            'kasir:id_user,nama_user',
            'detail.layanan',
            'riwayat' => fn ($q) => $q->orderByDesc('waktu_update'),
            'riwayat.petugas:id_user,nama_user',
        ])->findOrFail($id);

        return response()->json($pesanan);
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

        $urutanStatus = [
            'antrian'    => 1,
            'proses'     => 2,
            'siap_ambil' => 3,
            'selesai'    => 4,
        ];

        $statusSekarang = $urutanStatus[$pesanan->status_kerja] ?? 1;
        $statusBaru = $urutanStatus[$data['status_kerja']] ?? 1;

        if ($statusBaru <= $statusSekarang) {
            return response()->json([
                'message' => 'Status tidak valid. Anda tidak dapat memilih status yang sama atau kembali ke status sebelumnya.'
            ], 422);
        }

        $petugas = $request->user() ?? auth()->user();

        $this->service->ubahStatus($pesanan, $data, $petugas);

        return response()->json([
            'message' => 'Status pengerjaan cucian berhasil diperbarui.',
            'data'    => $pesanan->fresh(['pelanggan', 'detail.layanan', 'riwayat'])
        ]);
    }

    public function bayar(Request $request, PesananModel $pesanan)
    {
        $this->service->tandaiLunas($pesanan, $request->user());
        return response()->json($pesanan->fresh());
    }
}
