@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color: #2C4679;">Daftar Pelanggan SILAM</h5>
            <small class="text-muted">Pantau aktivitas riwayat order setiap pelanggan setia</small>
        </div>
        <button type="button" class="btn text-white rounded-pill px-4 fw-semibold" style="background: #2C4679;"
                data-bs-toggle="modal" data-bs-target="#formModal" data-store-url="{{ route('pelanggan.store') }}">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pelanggan
        </button>
    </div>

    {{-- TABEL PELANGGAN --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #FAF8F5; color: #2C4679;" class="small fw-bold">
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Nama Pelanggan</th>
                        <th>Nomor WhatsApp / HP</th>
                        <th>Alamat</th>
                        <th class="text-center">Total Order</th>
                        <th class="text-end">Total Akumulasi Belanja</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($pelanggan ?? [] as $index => $p)
                    <tr>
                        <td class="ps-4 fw-semibold text-muted">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $p->nama_pelanggan }}</div>
                            <small class="text-muted">Terdaftar: {{ $p->created_at?->format('d M Y')}}</small>
                        </td>
                        <td>
                            <a href="https://wa.me/62{{ preg_replace('/[^0-9]/', '', $p->nomor_telepon) }}" target="_blank" class="text-decoration-none fw-semibold" style="color: #2C4679;">
                                <i class="bi bi-whatsapp text-success me-1"></i> {{ $p->nomor_telepon }}
                            </a>
                        </td>
                        <td class="text-muted">{{ Str::limit($p->alamat) }}</td>
                        <td class="text-center">
                            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: #CFDCF8; color: #2C4679;">
                                {{ $p->pesanan_count }}x Order
                            </span>
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rp {{ number_format($p->total_belanja, 0, ',', '.') }}
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1" data-bs-toggle="modal" data-bs-target="#formModal"
                                        data-item="{{ json_encode($p) }}" data-url="{{ route('pelanggan.update', $p->id_pelanggan ?? 1) }}" title="Ubah Data">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="{{ route('pelanggan.index', $p->id_pelanggan ?? 1) }}?print=true" target="_blank" class="btn btn-outline-secondary btn btn-sm btn-outline-primary rounded-3 px-2 py-1" title="Cetak Riwayat">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada pelanggan terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODAL FORM TAMBAH / UBAH PELANGGAN --}}
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content rounded-4 border-0 p-3" method="POST">
            @csrf
            <input type="hidden" name="_method" value="POST">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" style="color: #2C4679;">Tambah Data Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" class="form-control rounded-3" required placeholder="Contoh: Budi Santoso">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="nomor_telepon" class="form-control rounded-3" required placeholder="08xxxxxxxx">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control rounded-3" rows="3" placeholder="Alamat pelanggan..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn text-white rounded-pill px-4" style="background: #2C4679;">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
