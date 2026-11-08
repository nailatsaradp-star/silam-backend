<?php

namespace App\Http\Controllers;

use App\Models\PesananModel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input filter bulan & tahun (default: bulan dan tahun saat ini)
        $bulan = (int) $request->input('bulan', date('m'));
        $tahun = (int) $request->input('tahun', date('Y'));

        // 2. Query dasar pesanan pada periode bulan & tahun yang dipilih
        // Catatan: sesuaikan 'tanggal_pesanan_dibuat' jika menggunakan 'created_at'
        $query = PesananModel::with(['pelanggan', 'detail.layanan'])
            ->whereMonth('tanggal_pesanan_dibuat', $bulan)
            ->whereYear('tanggal_pesanan_dibuat', $tahun);

        // 3. Hitung Kartu Ringkasan (Rekapitulasi)
        $totalTransaksi  = (clone $query)->count();

        // Total Omset / Pendapatan (yang sudah lunas)
        $totalPendapatan = (clone $query)->where('status_pembayaran', 'lunas')->sum('total_tagihan');

        // Total Piutang (nominal pesanan yang belum lunas)
        $belumLunas      = (clone $query)->where('status_pembayaran', 'belum_lunas')->sum('total_tagihan');

        // Jumlah pesanan yang sudah selesai atau sudah diambil
        $sudahDiambil    = (clone $query)->whereIn('status_kerja', ['selesai', 'siap_ambil'])->count();

        // 4. Ambil daftar rincian pesanan untuk tabel (urutkan dari yang terbaru)
        $laporan = (clone $query)
        ->orderBy('tanggal_pesanan_dibuat', 'desc')
        ->orderBy('id_pesanan', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('hak_akses.admin.laporan.index', compact(
            'bulan',
            'tahun',
            'totalTransaksi',
            'totalPendapatan',
            'belumLunas',
            'sudahDiambil',
            'laporan'
        ));
    }

        // Method khusus untuk mencetak SELURUH data laporan periode terpilih
    public function print(Request $request)
    {
        $bulan = (int) $request->input('bulan', date('m'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $query = PesananModel::with(['pelanggan', 'detail.layanan'])
            ->whereMonth('tanggal_pesanan_dibuat', $bulan)
            ->whereYear('tanggal_pesanan_dibuat', $tahun);

        // Rekapitulasi
        $totalTransaksi  = (clone $query)->count();
        $totalPendapatan = (clone $query)->where('status_pembayaran', 'lunas')->sum('total_tagihan');
        $belumLunas      = (clone $query)->where('status_pembayaran', 'belum_lunas')->sum('total_tagihan');
        $sudahDiambil    = (clone $query)->whereIn('status_kerja', ['selesai', 'siap_ambil'])->count();

        // AMBIL SEMUA DATA (bukan cuma 10 per halaman) diurutkan tanggal tertua ke terbaru
        $laporan = (clone $query)
            ->orderBy('tanggal_pesanan_dibuat', 'asc')
            ->orderBy('id_pesanan', 'asc')
            ->get();

        return view('hak_akses.admin.laporan.print', compact(
            'bulan',
            'tahun',
            'totalTransaksi',
            'totalPendapatan',
            'belumLunas',
            'sudahDiambil',
            'laporan'
        ));
    }
}
