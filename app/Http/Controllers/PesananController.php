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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $items = PesananModel::with('pelanggan')
            ->when($request->status_kerja, fn ($q, $v) => $q->where('status_kerja', $v))
            ->when($request->status_pembayaran, fn ($q, $v) => $q->where('status_pembayaran', $v))
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('nomor_pesanan', 'like', "%$s%")
                ->orWhereHas('pelanggan', fn ($p) => $p->where('nama_pelanggan', 'like', "%$s%"))))
            ->orderByDesc('id_pesanan')->paginate(10)->withQueryString();

        return view('pesanan.index', compact('items'));
    }

    public function create()
    {
        return view('pesanan.create', [
            'pelanggan' => PelangganModel::where('status', 'aktif')->orderBy('nama_pelanggan')->get(),
            'layanan'   => LayananModel::orderBy('nama_layanan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
        'id_pelanggan'       => ['required', Rule::exists(PelangganModel::class, 'id_pelanggan')->where('status', 'aktif')],
        'items'              => 'required|array|min:1',
        'items.*.id_layanan' => ['required', Rule::exists(LayananModel::class, 'id_layanan')],
        'items.*.jumlah'     => 'required|numeric|min:0.01',
        ]);

        $pesanan = DB::transaction(function () use ($data, $request) {
            // Harga selalu dihitung ulang di server (tidak percaya angka dari browser)
            $layanan = LayananModel::whereIn('id_layanan', collect($data['items'])->pluck('id_layanan'))->get()->keyBy('id_layanan');

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
                'id_user'                  => $request->user()->id_user,
                'total_tagihan'            => $total,
                'status_pembayaran'        => 'belum_lunas',
                'tanggal_pesanan_dibuat'   => $now,
                'tanggal_estimasi_selesai' => $now->copy()->addHours($maxJam),
                'status_kerja'             => 'antrian',
            ]);
            $p->detail()->createMany($rows);
            $p->riwayat()->create([
                'id_user' => $request->user()->id_user, 'status_detail' => 'Pesanan diterima',
                'keterangan' => 'Pesanan dibuat oleh kasir.', 'waktu_update' => $now,
            ]);
            return $p;
        });

        return redirect()->route('pesanan.show', $pesanan)->with('success', 'Pesanan ' . $pesanan->nomor_pesanan . ' berhasil dibuat.');
    }


    /**
     * Display the specified resource.
     */
    public function show(PesananModel $pesanan)
    {
        $pesanan->load(['pelanggan', 'kasir', 'detail.layanan', 'riwayat.petugas']);
        return view('pesanan.show', compact('pesanan'));
    }

    public function updateStatus(Request $request, PesananModel $pesanan)
    {
        $data = $request->validate([
            'status_kerja'  => ['required', Rule::in(array_keys(PesananModel::STATUS_KERJA))],
            'status_detail' => 'nullable|string|max:255',
            'keterangan'    => 'nullable|string',
        ]);

        // Aturan bisnis: cucian baru dianggap selesai (diambil) kalau sudah lunas
        if ($data['status_kerja'] === 'selesai' && $pesanan->status_pembayaran !== 'lunas') {
            return back()->with('error', 'Pesanan belum lunas, tidak bisa ditandai selesai.');
        }

        DB::transaction(function () use ($pesanan, $data, $request) {
            $pesanan->update(['status_kerja' => $data['status_kerja']]);
            $pesanan->riwayat()->create([
                'id_user'       => $request->user()->id_user,
                'status_detail' => $data['status_detail'] ?: PesananModel::STATUS_KERJA[$data['status_kerja']],
                'keterangan'    => $data['keterangan'] ?? null,
                'waktu_update'  => now(),
            ]);
        });

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function bayar(Request $request, PesananModel $pesanan)
    {
        if ($pesanan->status_pembayaran === 'lunas') {
            return back()->with('error', 'Pesanan sudah lunas.');
        }
        DB::transaction(function () use ($pesanan, $request) {
            $pesanan->update(['status_pembayaran' => 'lunas']);
            $pesanan->riwayat()->create([
                'id_user' => $request->user()->id_user, 'status_detail' => 'Pembayaran lunas',
                'keterangan' => null, 'waktu_update' => now(),
            ]);
        });
        return back()->with('success', 'Pembayaran dicatat sebagai lunas.');
    }
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
