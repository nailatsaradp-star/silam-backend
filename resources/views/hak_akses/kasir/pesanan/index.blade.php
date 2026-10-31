@extends('layouts.app')

@section('title', 'Data Transaksi Pesanan Kasir')

@section('content')
<div class="container-fluid p-0">

    {{-- FILTER & SEARCH --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <form method="GET" action="{{ route('pesanan.index') }}" class="row g-3 align-items-end">
            <div class="col-lg-4 col-md-6">
                <label class="form-label small fw-bold" style="color: #2C4679;">Pencarian</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Nomor nota atau nama pelanggan..." value="{{ request('q') }}">
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold" style="color: #2C4679;">Status Pengerjaan</label>
                <select name="status_kerja" class="form-select bg-light">
                    <option value="">Semua Status Pengerjaan</option>
                    <option value="antrian" {{ request('status_kerja') == 'antrian' ? 'selected' : '' }}>Antrian</option>
                    <option value="proses" {{ request('status_kerja') == 'proses' ? 'selected' : '' }}>Proses</option>
                    <option value="siap_ambil" {{ request('status_kerja') == 'siap_ambil' ? 'selected' : '' }}>Siap Ambil</option>
                    <option value="selesai" {{ request('status_kerja') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold" style="color: #2C4679;">Status Pembayaran</label>
                <select name="status_pembayaran" class="form-select bg-light">
                    <option value="">Semua Status Bayar</option>
                    <option value="lunas" {{ request('status_pembayaran') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="belum_lunas" {{ request('status_pembayaran') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                </select>
            </div>

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
            <span class="badge rounded-pill px-3 py-2" style="background: #CFDCF8; color: #2C4679;">
                Total: {{ method_exists($items, 'total') ? $items->total() : count($items) }} Pesanan
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #FAF8F5; color: #2C4679;" class="small fw-bold">
                    <tr>
                        <th class="ps-4">No. Nota</th>
                        <th>Pelanggan</th>
                        <th>Status Pengerjaan</th>
                        <th>Pembayaran</th>
                        <th class="text-end">Total Biaya</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($items as $item)
                    <tr>
                        <td class="ps-4 fw-bold" style="color: #2C4679;">#{{ $item->nomor_pesanan }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->pelanggan->nama_pelanggan ?? '-' }}</div>
                            <small class="text-muted">{{ $item->pelanggan->nomor_telepon ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 py-1 text-capitalize" style="background: #CFDCF8; color: #2C4679;">
                                {{ str_replace('_', ' ', $item->status_kerja) }}
                            </span>
                        </td>
                        <td>
                            @if($item->status_pembayaran === 'lunas')
                                <span class="badge rounded-pill px-2 py-1 bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>Lunas</span>
                            @else
                                <span class="badge rounded-pill px-2 py-1 bg-danger-subtle text-danger"><i class="bi bi-clock me-1"></i>Belum Lunas</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <a href="{{ route('pesanan.show', $item->id_pesanan) }}" class="btn btn-sm btn-outline-primary rounded-3" title="Detail & Tracking">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                <a href="{{ route('pesanan.print', $item->id_pesanan) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-3" title="Cetak Nota">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            Tidak ada data pesanan yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if(method_exists($items, 'links'))
        <div class="p-3 border-top custom-pagination">
            {{ $items->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>

{{-- CSS Tambahan agar Tombol Pagination Selaras dengan Tema Biru SILAM (#2C4679) --}}
<style>
    .custom-pagination .page-item.active .page-link {
        background-color: #2C4679 !important;
        border-color: #2C4679 !important;
        color: #fff !important;
    }
    .custom-pagination .page-link {
        color: #2C4679;
        font-size: 0.875rem;
    }
    .custom-pagination .page-link:hover {
        background-color: #CFDCF8;
        color: #2C4679;
    }
    .custom-pagination nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .custom-pagination .pagination {
        margin-bottom: 0;
    }
</style>
@endsection
