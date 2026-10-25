<?php

namespace App\Http\Controllers;

use App\Models\PesananModel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $role = auth()->user()->role;

        // Stat ringkasan pengerjaan cucian untuk Pegawai
        $totalAntrian   = PesananModel::where('status_kerja', 'antrian')->count();
        $totalProses    = PesananModel::where('status_kerja', 'proses')->count();
        $totalSiapAmbil = PesananModel::where('status_kerja', 'siap_ambil')->count();
        $totalSelesai   = PesananModel::where('status_kerja', 'selesai')->count();
        $omzetHariIni   = PesananModel::whereDate('tanggal_pesanan_dibuat', today())->sum('total_tagihan');
        $belumLunas   = PesananModel::where('status_pembayaran', 'belum_lunas')->count();
        $terbaru      = PesananModel::with('pelanggan')->latest('id_pesanan')->limit(8)->get();
        $perStatus = PesananModel::selectRaw('status_kerja, COUNT(*) as total')->groupBy('status_kerja')->pluck('total', 'status_kerja');
        $terbaru = PesananModel::with('pelanggan')->latest()->take(5)->get(); // Jangan sampai terlewat/null


        // Cucian yang perlu segera diproses (Prioritas Antrian & Proses)
        $pesananKerja = PesananModel::with(['pelanggan', 'detail.layanan'])
            ->whereIn('status_kerja', ['antrian', 'proses'])
            ->orderBy('tanggal_pesanan_dibuat', 'asc')
            ->take(8)
            ->get();

        // Cek berdasarkan role masing-masing
        if ($role === 'admin') {
            return view('hak_akses.admin.dashboard', compact(
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
}
