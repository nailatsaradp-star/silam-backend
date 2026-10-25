<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LayananModel;
use App\Models\PelangganModel;

class DataController extends Controller
{
    public function layanan()
    {
        return response()->json(LayananModel::orderBy('nama_layanan')->get());
    }

    public function pelanggan()
    {
        return response()->json(
            PelangganModel::where('status', 'aktif')->orderBy('nama_pelanggan')
                ->get(['id_pelanggan', 'nama_pelanggan', 'nomor_telepon'])
        );
    }
}
