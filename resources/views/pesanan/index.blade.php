@extends('layouts.app')

@section('title', 'Data Transaksi Pesanan')

@section('content')
<div class="container-fluid p-0">

    {{-- BARIS FILTER & SEARCH --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <form method="GET" action="{{ route('pesanan.index') }}" class="row g-3 align-items-end">
            {{-- Search Bar --}}
            <div class="col-lg-4 col-md-6">
                <label class="form-label small fw-bold" style="color: #2C4679;">Pencarian</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Kode nota, nama pelanggan, kasir..." value="{{ request('q') }}">
                </div>
            </div>

            {{-- Filter Status Kerja --}}
            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold" style="color: #2C4679;">Status Pengerjaan</label>
                <select name="status_kerja" class="form-select bg-light">
                    <option value="">Semua Status Pengerjaan</option>
                    <option value="Antrian" {{ request('status_kerja') == 'Antrian' ? 'selected' : '' }}>Antrian</option>
                    <option value="Proses" {{ request('status_kerja') == 'Proses' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="Siap Ambil" {{ request('status_kerja') == 'Siap Ambil' ? 'selected' : '' }}>Siap Diambil</option>
                    <option value="Selesai" {{ request('status_kerja') == 'Selesai' ? 'selected' : '' }}>Sudah Diambil</option>
                </select>
            </div>

            {{-- Filter Status Bayar --}}
            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold" style="color: #2C4679;">Status Pembayaran</label>
                <select name="status_pembayaran" class="form-select bg-light">
                    <option value="">Semua Status Bayar</option>
                    <option value="Lunas" {{ request('status_pembayaran') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="Belum Lunas" {{ request('status_pembayaran') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                </select>
            </div>

            {{-- Tombol Aksi Filter --}}
            <div class="col-lg-2 col-md-6 d-flex gap-2">
                <button type="submit" class="btn text-white w-100 rounded-3 fw-semibold" style="background: #2C4679;">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('pesanan.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>

    {{-- TABEL DATA TRANSAKSI --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0" style="color: #2C4679;">Daftar Transaksi Masuk</h6>
            <span class="badge rounded-pill px-3 py-2" style="background: #CFDCF8; color: #2C4679;">Total: {{ count($pesanan ?? []) }} Pesanan</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #FAF8F5; color: #2C4679;" class="small fw-bold">
                    <tr>
                        <th class="ps-4">No. Nota</th>
                        <th>Pelanggan</th>
                        <th>Kasir / Petugas</th>
                        <th>Layanan &amp; Berat</th>
                        <th>Status Pengerjaan</th>
                        <th>Pembayaran</th>
                        <th class="text-end">Total Biaya</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($pesanan ?? [] as $item)
                    <tr>
                        <td class="ps-4 fw-bold" style="color: #2C4679;">#{{ $item->kode_transaksi }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->pelanggan->nama_pelanggan ?? 'Umum' }}</div>
                            <small class="text-muted">{{ $item->pelanggan->no_hp ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="d-inline-flex align-items-center gap-1 text-muted">
                                <i class="bi bi-person-badge"></i> {{ $item->user->nama_user ?? 'Kasir 1' }}
                            </span>
                        </td>
                        <td>
                            <div>{{ $item->layanan->nama_layanan ?? 'Cuci Kering Setrika' }}</div>
                            <small class="text-muted fw-semibold">{{ $item->berat_qty ?? 4.5 }} Kg</small>
                        </td>
                        <td>
                            @php
                                $badgeColor = match($item->status_kerja ?? 'Proses') {
                                    'Antrian' => 'background: #FFFBEB; color: #D97706; border: 1px solid #FDE68A;',
                                    'Proses' => 'background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE;',
                                    'Siap Ambil' => 'background: #E1EEA2; color: #2C4679;',
                                    default => 'background: #F1F5F9; color: #64748B;'
                                };
                            @endphp
                            <span class="badge rounded-pill px-2 py-1" style="{{ $badgeColor }}">
                                {{ $item->status_kerja ?? 'Proses' }}
                            </span>
                        </td>
                        <td>
                            @if(($item->status_pembayaran ?? 'Lunas') === 'Lunas')
                                <span class="badge rounded-pill px-2 py-1" style="background: #DCFCE7; color: #166534;"><i class="bi bi-check-circle me-1"></i>Lunas</span>
                            @else
                                <span class="badge rounded-pill px-2 py-1 bg-danger-subtle text-danger"><i class="bi bi-clock me-1"></i>Belum Lunas</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rp {{ number_format($item->total_bayar ?? 45000, 0, ',', '.') }}
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <button type="button" class="btn btn-outline-primary rounded-start-3"
                                        data-bs-toggle="modal" data-bs-target="#detailModal"
                                        data-order="{{ json_encode($item) }}" title="Detail Pesanan">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <a href="{{ route('pesanan.show', $item->id ?? 1) }}?print=true" target="_blank" class="btn btn-outline-secondary rounded-end-3" title="Cetak Nota">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            Tidak ada data pesanan yang sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODAL DETAIL PESANAN --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: #2C4679;">Rincian Pesanan Laundry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="p-3 rounded-4 mb-3" style="background: #F5F7FC;">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">No. Transaksi</span>
                        <span class="fw-bold text-dark" id="modalNoNota">-</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Nama Pelanggan</span>
                        <span class="fw-bold text-dark" id="modalNamaPelanggan">-</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Kasir Pemroses</span>
                        <span class="fw-bold text-dark" id="modalNamaKasir">-</span>
                    </div>
                </div>

                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span id="modalLayanan">Layanan Cuci</span>
                        <span class="fw-bold" id="modalHarga">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>Status Pengerjaan</span>
                        <span class="fw-bold" id="modalStatusKerja">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>Status Pembayaran</span>
                        <span class="fw-bold" id="modalStatusBayar">-</span>
                    </li>
                </ul>

                <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background: #CFDCF8; color: #2C4679;">
                    <span class="fw-bold">Total Pembayaran</span>
                    <h5 class="fw-bold mb-0" id="modalTotalBayar">Rp 0</h5>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailModal = document.getElementById('detailModal');
    if (detailModal) {
        detailModal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            if (btn && btn.dataset.order) {
                const data = JSON.parse(btn.dataset.order);
                document.getElementById('modalNoNota').textContent = '#' + (data.kode_transaksi || '-');
                document.getElementById('modalNamaPelanggan').textContent = data.pelanggan?.nama_pelanggan || 'Umum';
                document.getElementById('modalNamaKasir').textContent = data.user?.nama_user || 'Kasir';
                document.getElementById('modalLayanan').textContent = `${data.layanan?.nama_layanan || 'Layanan'} (${data.berat_qty || 0} Kg)`;
                document.getElementById('modalStatusKerja').textContent = data.status_kerja || '-';
                document.getElementById('modalStatusBayar').textContent = data.status_pembayaran || '-';
                document.getElementById('modalTotalBayar').textContent = 'Rp ' + Number(data.total_bayar || 0).toLocaleString('id-ID');
            }
        });
    }
});
</script>
@endpush
@endsection
