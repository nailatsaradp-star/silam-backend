@extends('layouts.app')

@section('title', 'Dashboard Operasional Pegawai')

@section('content')
<div class="container-fluid p-0">

    {{-- KARTU RINGKASAN STATUS PENGERJAAN --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted fw-semibold">Antrian Masuk</span>
                    <div class="rounded-circle p-2" style="background: #E2E8F0; color: #475569;">
                        <i class="bi bi-inbox-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ $totalAntrian ?? 0 }}</h3>
                <small class="text-muted fs-7">Perlu dicuci</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted fw-semibold">Sedang Diproses</span>
                    <div class="rounded-circle p-2" style="background: #CFDCF8; color: #2C4679;">
                        <i class="bi bi-gear-wide-connected fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0" style="color: #2C4679;">{{ $totalProses ?? 0 }}</h3>
                <small class="text-muted fs-7">Cuci / Pengeringan</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted fw-semibold">Siap Diambil</span>
                    <div class="rounded-circle p-2" style="background: #FEF08A; color: #854D0E;">
                        <i class="bi bi-check2-square fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-warning-emphasis">{{ $totalSiapAmbil ?? 0 }}</h3>
                <small class="text-muted fs-7">Sudah rapi &amp; packing</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small text-muted fw-semibold">Selesai Diserahkan</span>
                    <div class="rounded-circle p-2" style="background: #DCFCE7; color: #166534;">
                        <i class="bi bi-bag-check-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-success">{{ $totalSelesai ?? 0 }}</h3>
                <small class="text-muted fs-7">Diambil pelanggan</small>
            </div>
        </div>
    </div>

    {{-- DAFTAR CUKUP PENGERJAAN PRIORITAS --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold mb-0" style="color: #2C4679;">
                    <i class="bi bi-arrow-repeat me-1"></i> Daftar Antrian Pengerjaan Cucian
                </h6>
                <small class="text-muted">Daftar cucian yang harus segera dikerjakan berdasarkan urutan masuk</small>
            </div>
            <a href="{{ route('pesanan.index') }}" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold">
                Lihat Semua Pesanan
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #FAF8F5; color: #2C4679;" class="small fw-bold">
                    <tr>
                        <th class="ps-4">No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Rincian Paket</th>
                        <th>Status Pengerjaan</th>
                        <th class="text-center pe-4">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($pesananKerja ?? [] as $item)
                    <tr>
                        <td class="ps-4 fw-bold" style="color: #2C4679;">
                            {{ $item->nomor_pesanan }}
                            <div class="text-muted small fw-normal">
                                {{ $item->tanggal_pesanan_dibuat ? $item->tanggal_pesanan_dibuat->format('d/m/Y H:i') : '-' }}
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->pelanggan->nama_pelanggan ?? 'Umum' }}</div>
                            <small class="text-muted">{{ $item->pelanggan->nomor_telepon ?? '-' }}</small>
                        </td>
                        <td>
                            @foreach($item->detail ?? [] as $det)
                                <span class="badge bg-light text-dark border rounded-pill me-1 mb-1">
                                    {{ $det->layanan->nama_layanan ?? '-' }} ({{ $det->jumlah_berat }} Kg)
                                </span>
                            @endforeach
                        </td>
                        <td>
                            @php
                                $badgeColor = match($item->status_kerja) {
                                    'antrian'    => 'bg-secondary',
                                    'proses'     => 'bg-primary',
                                    'siap_ambil' => 'bg-warning text-dark',
                                    default      => 'bg-success',
                                };
                            @endphp
                            <span class="badge rounded-pill px-3 py-1 {{ $badgeColor }}">
                                {{ $item->label_kerja }}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <a href="{{ route('pesanan.show', $item->id_pesanan) }}" class="btn btn-sm text-white rounded-pill px-3" style="background: #2C4679;">
                                <i class="bi bi-pencil-square me-1"></i> Update Status
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                            Semua cucian antrian &amp; proses telah dikerjakan!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
