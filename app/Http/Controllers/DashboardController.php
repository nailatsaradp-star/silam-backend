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

        // Cucian yang perlu segera diproses (Prioritas Antrian & Proses)
        $pesananKerja = PesananModel::with(['pelanggan', 'detail.layanan'])
            ->whereIn('status_kerja', ['antrian', 'proses'])
            ->orderBy('tanggal_pesanan_dibuat', 'asc')
            ->take(8)
            ->get();

        if ($role === 'pegawai') {
            return view('hak_akses.pegawai.dashboard', compact(
                'totalAntrian',
                'totalProses',
                'totalSiapAmbil',
                'totalSelesai',
                'pesananKerja'
            ));
        }

        // Tampilan default Admin / Kasir
        return view('hak_akses.admin.dashboard', compact(
            'totalAntrian',
            'totalProses',
            'totalSiapAmbil',
            'totalSelesai',
            'pesananKerja'
        ));
    }
}
