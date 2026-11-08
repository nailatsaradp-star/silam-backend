<?php

namespace App\Http\Controllers;

use App\Models\PesananModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // 1. Tambahkan import DB

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $role = auth()->user()->role;

        // Stat ringkasan pengerjaan cucian
        $totalAntrian   = PesananModel::where('status_kerja', 'antrian')->count();
        $totalProses    = PesananModel::where('status_kerja', 'proses')->count();
        $totalSiapAmbil = PesananModel::where('status_kerja', 'siap_ambil')->count();
        $totalSelesai   = PesananModel::where('status_kerja', 'selesai')->count();
        $omzetHariIni   = PesananModel::whereDate('tanggal_pesanan_dibuat', today())->sum('total_tagihan');
        $belumLunas     = PesananModel::where('status_pembayaran', 'belum_lunas')->count();

        $perStatus      = PesananModel::selectRaw('status_kerja, COUNT(*) as total')
                            ->groupBy('status_kerja')
                            ->pluck('total', 'status_kerja');

        // Mengambil pesanan terbaru (cukup 1 kali query)
        $terbaru        = PesananModel::with('pelanggan')->latest('id_pesanan')->take(5)->get();

        // Cucian yang perlu segera diproses (Prioritas Antrian & Proses)
        $pesananKerja   = PesananModel::with(['pelanggan', 'detail.layanan'])
                            ->whereIn('status_kerja', ['antrian', 'proses'])
                            ->orderBy('tanggal_pesanan_dibuat', 'asc')
                            ->take(8)
                            ->get();

        // 2. Ambil data chart DI SINI (sebelum if/return) agar $chartData terisi
        $chartData      = $this->getPendapatanBulananData();

        // Cek berdasarkan role masing-masing
        if ($role === 'administrator') {
            return view('hak_akses.admin.dashboard', compact(
                'totalAntrian',
                'totalProses',
                'totalSiapAmbil',
                'totalSelesai',
                'pesananKerja',
                'omzetHariIni',
                'belumLunas',
                'perStatus',
                'terbaru',
                'chartData' // Terisi dengan benar
            ));
        }

        if ($role === 'kasir') {
            return view('hak_akses.kasir.dashboard', compact(
                'totalAntrian',
                'totalProses',
                'totalSiapAmbil',
                'totalSelesai',
                'pesananKerja',
                'omzetHariIni',
                'belumLunas',
                'perStatus',
                'terbaru'
            ));
        }

        if ($role === 'pegawai') {
            return view('hak_akses.pegawai.dashboard', compact(
                'totalAntrian',
                'totalProses',
                'totalSiapAmbil',
                'totalSelesai',
                'pesananKerja'
            ));
        }

        // Fallback jika role tidak dikenali
        abort(403, 'Unauthorized action.');
    }

    // Endpoint JSON untuk auto-update real-time via AJAX/Fetch
    public function chartData()
    {
        return response()->json($this->getPendapatanBulananData());
    }

    // Helper fungsi data grafik pendapatan bulanan
    private function getPendapatanBulananData()
    {
        $tahun = date('Y');

        // 3. Gunakan PesananModel dan kolom 'tanggal_pesanan_dibuat'
        $pendapatan = PesananModel::selectRaw('MONTH(tanggal_pesanan_dibuat) as bulan, SUM(total_tagihan) as total')
            ->whereYear('tanggal_pesanan_dibuat', $tahun)
            // ->where('status_pembayaran', 'lunas') // Aktifkan jika hanya menghitung yang lunas
            ->groupBy(DB::raw('MONTH(tanggal_pesanan_dibuat)'))
            ->pluck('total', 'bulan')
            ->toArray();

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $data   = [];

        // Isi 12 bulan (bulan yang tidak ada transaksi diberi nilai 0)
        for ($b = 1; $b <= 12; $b++) {
            $data[] = (int) ($pendapatan[$b] ?? 0);
        }

        return [
            'labels' => $labels,
            'data'   => $data,
            'tahun'  => $tahun,
        ];
    }
}
