<?php

namespace App\Http\Controllers;

use App\Models\PesananModel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input filter bulan & tahun dari request user
        // (Jika tidak diisi, otomatis menggunakan bulan dan tahun saat ini berdasarkan sistem)
        $bulan = (int) $request->input('bulan', date('m'));
        $tahun = (int) $request->input('tahun', date('Y'));

        // 2. Membuat query dasar untuk mengambil data pesanan beserta relasinya (pelanggan dan detail layanan)
        // Difilter berdasarkan bulan dan tahun dari kolom 'tanggal_pesanan_dibuat'
        $query = PesananModel::with(['pelanggan', 'detail.layanan'])
            ->whereMonth('tanggal_pesanan_dibuat', $bulan)
            ->whereYear('tanggal_pesanan_dibuat', $tahun);

        // 3. Menghitung Kartu Ringkasan (Rekapitulasi data pada periode tersebut)
        // Menggunakan clone query agar objek query utama tidak terpengaruh oleh method tambahan seperti count() atau sum()

        // Total keseluruhan transaksi/pesanan pada periode terpilih
        $totalTransaksi  = (clone $query)->count();

        // Total omset / pendapatan dari pesanan yang status pembayarannya sudah 'lunas'
        $totalPendapatan = (clone $query)->where('status_pembayaran', 'lunas')->sum('total_tagihan');

        // Total piutang / sisa tagihan dari pesanan yang status pembayarannya masih 'belum_lunas'
        $belumLunas      = (clone $query)->where('status_pembayaran', 'belum_lunas')->sum('total_tagihan');

        // Jumlah pesanan yang proses kerjanya sudah 'selesai' atau 'siap_ambil'
        $sudahDiambil    = (clone $query)->whereIn('status_kerja', ['selesai', 'siap_ambil'])->count();

        // 4. Mengambil daftar rincian pesanan untuk ditampilkan pada tabel halaman web
        // Diurutkan dari tanggal pesanan terbaru dan ID pesanan terbesar, dibatasi 10 data per halaman (paginasi)
        $laporan = (clone $query)
        ->orderBy('tanggal_pesanan_dibuat', 'desc')
        ->orderBy('id_pesanan', 'desc')
        ->paginate(10)
        ->withQueryString();

        // Mengirimkan semua variabel yang dibutuhkan ke view Blade admin
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

    /**
     * Method khusus untuk mencetak SELURUH data laporan pada periode terpilih (tanpa paginasi).
     */
    public function print(Request $request)
    {
        // Mengambil input filter bulan dan tahun yang sama seperti pada method index
        $bulan = (int) $request->input('bulan', date('m'));
        $tahun = (int) $request->input('tahun', date('Y'));

        // Query dasar untuk memfilter data pesanan berdasarkan bulan dan tahun
        $query = PesananModel::with(['pelanggan', 'detail.layanan'])
            ->whereMonth('tanggal_pesanan_dibuat', $bulan)
            ->whereYear('tanggal_pesanan_dibuat', $tahun);

        // Rekapitulasi ringkasan data untuk ditampilkan pada lembar cetak
        $totalTransaksi  = (clone $query)->count();
        $totalPendapatan = (clone $query)->where('status_pembayaran', 'lunas')->sum('total_tagihan');
        $belumLunas      = (clone $query)->where('status_pembayaran', 'belum_lunas')->sum('total_tagihan');
        $sudahDiambil    = (clone $query)->whereIn('status_kerja', ['selesai', 'siap_ambil'])->count();

        // Mengambil SEMUA DATA sekaligus (menggunakan get() bukan paginate)
        // Diurutkan dari tanggal tertua ke terbaru agar laporan cetak lebih rapi secara kronologis
        $laporan = (clone $query)
            ->orderBy('tanggal_pesanan_dibuat', 'asc')
            ->orderBy('id_pesanan', 'asc')
            ->get();

        // Mengirimkan data lengkap ke view khusus cetak (print view)
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
