<?php

namespace App\Http\Controllers;

use App\Models\LayananModel;
use App\Models\PelangganModel;
use App\Models\PesananModel;
use App\Services\PesananService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PesananController extends Controller
{
    protected $pesananService;

    // Inject PesananService agar transaksi & notifikasi WA terpusat
    public function __construct(PesananService $pesananService)
    {
        $this->pesananService = $pesananService;
    }

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

        $layanan = LayananModel::whereIn('id_layanan', collect($data['items'])->pluck('id_layanan'))
            ->get()->keyBy('id_layanan');

        $totalTagihan = 0;
        foreach ($data['items'] as $item) {
            $l = $layanan[$item['id_layanan']];
            $totalTagihan += $l->harga_satuan * $item['jumlah'];
        }
        $totalTagihan = round($totalTagihan, 2);

        $langsungLunas = $data['status_pembayaran'] === 'lunas';
        $tunai = $langsungLunas && strcasecmp($data['metode_pembayaran'] ?? '', 'Tunai') === 0;

        if ($tunai) {
            $uang = (float) $data['uang_diterima'];

            if ($uang < $totalTagihan) {
                throw ValidationException::withMessages([
                    'uang_diterima' => 'Uang kurang Rp ' . number_format($totalTagihan - $uang, 0, ',', '.')
                        . ' (total tagihan Rp ' . number_format($totalTagihan, 0, ',', '.') . ').',
                ]);
            }
        }

        // Simpan via Service (Otomatis pemicu notifikasi WA)
        $pesanan = $this->pesananService->buat($data, auth()->user());

        return redirect()->route('pesanan.show', $pesanan->id_pesanan)
            ->with('success', 'Transaksi pesanan baru #' . $pesanan->nomor_pesanan . ' berhasil dibuat & notifikasi WA terkirim!');
    }

    public function show($id)
    {
        $pesanan = PesananModel::with(['pelanggan', 'kasir', 'detail.layanan', 'riwayat.petugas'])
            ->findOrFail($id);

        // Jika user yang login adalah Administrator -> tampilkan view Read-Only khusus Admin
        if (auth()->user()->role === 'administrator') {
            return view('hak_akses.admin.pesanan.show', compact('pesanan'));
        }

        // Jika Kasir atau Pegawai -> tampilkan view operasional
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

        $this->pesananService->tandaiLunas($pesanan, auth()->user());

        $pesanan->update([
            'metode_pembayaran' => $data['metode_pembayaran'],
            'uang_diterima'     => $data['uang_diterima'] ?? $total,
        ]);

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

        // Urutan status kerja
        $urutanStatus = [
            'antrian'    => 1,
            'proses'     => 2,
            'siap_ambil' => 3,
            'selesai'    => 4,
        ];

        $statusSekarang = $urutanStatus[$pesanan->status_kerja] ?? 1;
        $statusBaru = $urutanStatus[$data['status_kerja']] ?? 1;

        if ($statusBaru <= $statusSekarang) {
            return back()->with('error', 'Status tidak valid. Anda tidak dapat memilih status yang sama atau kembali ke status sebelumnya.');
        }

        $this->pesananService->ubahStatus($pesanan, $data, auth()->user());

        return redirect()->back()->with('success', 'Status pengerjaan cucian berhasil diperbarui.');
    }

    public function print($id)
    {
        $pesanan = PesananModel::with(['pelanggan', 'detail.layanan', 'kasir'])->findOrFail($id);

        return view('hak_akses.kasir.pesanan.print', compact('pesanan'));
    }
}
