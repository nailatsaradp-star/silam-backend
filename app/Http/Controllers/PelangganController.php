<?php

namespace App\Http\Controllers;

use App\Models\PelangganModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PelangganController extends Controller
{
    private function rules(?PelangganModel $pelanggan = null): array
    {
        return [
            'nama_pelanggan' => 'required|string|max:255',
            'nomor_telepon'  => [
                'required',
                'regex:/^0[0-9]{8,14}$/',
                Rule::unique(PelangganModel::class, 'nomor_telepon')
                    ->ignore($pelanggan ? $pelanggan->id_pelanggan : null, 'id_pelanggan')
            ],
            'alamat'         => 'nullable|string',
        ];
    }

    private function pesan(): array
    {
        return [
            'nomor_telepon.unique' => 'Nomor telepon sudah terdaftar atas nama pelanggan lain.',
            'nomor_telepon.regex'  => 'Nomor telepon harus diawali 0 dan terdiri dari 9-15 angka.',
        ];
    }

    // Samakan format sebelum divalidasi: buang spasi/tanda hubung, ubah 62/+62 menjadi 0
    private function normalisasi(Request $request): void
    {
        $no = preg_replace('/[^0-9]/', '', (string) $request->nomor_telepon);
        if (str_starts_with($no, '62')) {
            $no = '0' . substr($no, 2);
        }
        $request->merge(['nomor_telepon' => $no]);
    }

    public function index(Request $request)
    {
        $pelanggan = PelangganModel::when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('nama_pelanggan', 'like', "%$s%")
                ->orWhere('nomor_telepon', 'like', "%$s%")))
            ->orderBy('nama_pelanggan')->paginate(10)->withQueryString();

        return view('pelanggan.index', compact('pelanggan'));
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $this->normalisasi($request);
        PelangganModel::create($request->validate($this->rules(), $this->pesan()));
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(PelangganModel $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, PelangganModel $pelanggan)
    {
        $this->normalisasi($request);
        $pelanggan->update($request->validate($this->rules($pelanggan), $this->pesan()));
        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan diperbarui.');
    }

    public function destroy(PelangganModel $pelanggan)
    {
        if ($pelanggan->pesanan()->exists()) {
            $pelanggan->update(['status' => 'non_aktif']);
            return redirect()->route('pelanggan.index')
                ->with('success', 'Pelanggan punya riwayat pesanan, jadi dinonaktifkan (tidak dihapus).');
        }
        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan dihapus.');
    }
}
