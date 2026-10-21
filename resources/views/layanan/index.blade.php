@extends('layouts.app')

@section('title', 'Layanan & Tarif Harga')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color: #2C4679;">Katalog Layanan &amp; Tarif</h5>
            <small class="text-muted">Kelola jenis paket cuci, tarif per Kg/satuan, dan durasi pengerjaan</small>
        </div>
        <button type="button" class="btn text-white rounded-pill px-4 fw-semibold" style="background: #2C4679;"
                data-bs-toggle="modal" data-bs-target="#formModal" data-store-url="{{ route('layanan.store') }}">
            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Layanan
        </button>
    </div>

    {{-- TABEL KATALOG LAYANAN --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #FAF8F5; color: #2C4679;" class="small fw-bold">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Layanan</th>
                        <th>Jenis Satuan</th>
                        <th>Tarif Harga</th>
                        <th>Estimasi Waktu</th>
                        <th>Status Layanan</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($layanan ?? [] as $index => $lay)
                    <tr>
                        <td class="ps-4 fw-semibold text-muted">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $lay->nama_layanan }}</div>
                            <small class="text-muted">{{ $lay->deskripsi ?? 'Proses pencucian profesional rapi & wangi' }}</small>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 py-1" style="background: #CFDCF8; color: #2C4679;">
                                {{ strtoupper($lay->satuan ?? 'KG') }}
                            </span>
                        </td>
                        <td class="fw-bold fs-6" style="color: #2C4679;">
                            Rp {{ number_format($lay->harga ?? 8000, 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">/ {{ $lay->satuan ?? 'kg' }}</span>
                        </td>
                        <td>
                            <span class="text-muted"><i class="bi bi-clock me-1"></i> {{ $lay->estimasi_hari ?? 3 }} Hari</span>
                        </td>
                        <td>
                            @if(($lay->status ?? 'aktif') === 'aktif')
                                <span class="badge rounded-pill px-2 py-1" style="background: #E1EEA2; color: #2C4679;">Aktif</span>
                            @else
                                <span class="badge rounded-pill px-2 py-1 bg-secondary-subtle text-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#formModal"
                                        data-item="{{ json_encode($lay) }}" data-url="{{ route('layanan.update', $lay->id ?? 1) }}" title="Ubah Tarif">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form method="POST" action="{{ route('layanan.destroy', $lay->id ?? 1) }}" data-confirm="Nonaktifkan layanan ini?" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada katalog layanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODAL TAMBAH / UBAH LAYANAN --}}
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content rounded-4 border-0 p-3" method="POST">
            @csrf
            <input type="hidden" name="_method" value="POST">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" style="color: #2C4679;">Tambah Layanan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Nama Paket Layanan</label>
                    <input type="text" name="nama_layanan" class="form-control rounded-3" required placeholder="Contoh: Cuci Kering Setrika">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark">Satuan</label>
                        <select name="satuan" class="form-select rounded-3">
                            <option value="kg">Per Kilogram (Kg)</option>
                            <option value="satuan">Per Satuan / Pcs</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark">Tarif Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control rounded-3" required placeholder="8000">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Estimasi Waktu Pengerjaan (Hari / Jam)</label>
                    <input type="text" name="estimasi_hari" class="form-control rounded-3" placeholder="Contoh: 1 Hari / 12 Jam">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Status</label>
                    <select name="status" class="form-select rounded-3">
                        <option value="aktif">Aktif Ditampilkan</option>
                        <option value="nonaktif">Nonaktifkan</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn text-white rounded-pill px-4" style="background: #2C4679;">Simpan Layanan</button>
            </div>
        </form>
    </div>
</div>
@endsection
