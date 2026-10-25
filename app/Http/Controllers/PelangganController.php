<?php

namespace App\Http\Controllers;

use App\Models\PelangganModel;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $pelanggan = PelangganModel::withCount('pesanan')
            ->when($request->q, function ($query, $q) {
                $query->where('nama_pelanggan', 'like', "%{$q}%")
                      ->orWhere('nomor_telepon', 'like', "%{$q}%");
            })
            ->orderBy('nama_pelanggan')
            ->paginate(10)
            ->withQueryString();

        return view('hak_akses.admin.pelanggan.index', compact('pelanggan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'nomor_telepon'  => 'required|string|max:20',
            'alamat'         => 'nullable|string',
        ]);

        $baru = PelangganModel::create($data);

        if ($request->has('from_kasir')) {
            return redirect()->route('kasir.index', ['selected_id' => $baru->id_pelanggan])
                ->with('success', 'Pelanggan berhasil ditambahkan.');
        }

        return redirect()->back()->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $pelanggan = PelangganModel::findOrFail($id);

        $data = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'nomor_telepon'  => 'required|string|max:20',
            'alamat'         => 'nullable|string',
        ]);

        $pelanggan->update($data);

        return redirect()->back()->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pelanggan = PelangganModel::findOrFail($id);
        $pelanggan->delete();

        return redirect()->back()->with('success', 'Data pelanggan berhasil dihapus.');
    }
}
