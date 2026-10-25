<?php

namespace App\Http\Controllers;

use App\Models\LayananModel;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LayananController extends Controller
{
    private function rules(): array
    {
        return [
            'nama_layanan'     => 'required|string|max:255',
            'satuan'           => 'required|in:kg,meter',
            'kategori'         => 'required|in:cuci,setrika,cuci_setrika',
            'harga_satuan'     => 'required|numeric|min:0',
            'estimasi_selesai' => 'required|integer|min:1',
            'deskripsi'        => 'nullable|string'
        ];
    }

    public function index(Request $request)
{
    $items = LayananModel::orderBy('nama_layanan')->paginate(10);

    // Jika diakses oleh Kasir / via route kasir.layanan.index
    if ($request->user()->role === 'kasir' || $request->routeIs('kasir.*')) {
        return view('hak_akses.kasir.layanan.index', compact('items'));
    }

    // Default untuk Admin
    return view('hak_akses.admin.layanan.index', compact('items'));
}

    public function store(Request $request)
    {
        LayananModel::create($request->validate($this->rules()));

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function update(Request $request, LayananModel $layanan)
    {
        $layanan->update($request->validate($this->rules()));

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(LayananModel $layanan)
    {
        try {
            $layanan->delete();

            return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('admin.layanan.index')->with('error', 'Layanan tidak bisa dihapus karena masih dipakai di pesanan.');
        }
    }
}
