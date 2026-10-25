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

    public function updateStatus(Request $request, PesananModel $pesanan)
    {
        $data = $request->validate([
            'status_kerja'  => ['required', Rule::in(array_keys(PesananModel::STATUS_KERJA))],
            'status_detail' => 'nullable|string|max:255',
            'keterangan'    => 'nullable|string',
        ]);

        $this->service->ubahStatus($pesanan, $data, $request->user());

        return response()->json($pesanan->fresh()->load('riwayat.petugas:id_user,nama_user'));
    }

    public function bayar(Request $request, PesananModel $pesanan)
    {
        $this->service->tandaiLunas($pesanan, $request->user());
        return response()->json($pesanan->fresh());
    }
}
