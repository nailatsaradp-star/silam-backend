@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="container-fluid p-0">
    {{-- SUBHEADER DASHBOARD --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <small class="text-muted">Ringkasan aktivitas transaksi &amp; status antrean laundry hari ini</small>
        </div>
        <a href="{{ route('kasir.index') }}" class="btn text-white rounded-pill px-4 fw-semibold shadow-sm" style="background: #2C4679;">
            <i class="bi bi-plus-circle-fill me-1"></i> Transaksi Baru
        </a>
    </div>

    {{-- ========================================================
         KARTU STATISTIK OMSET PENJUALAN HARI INI
         (Omset Harian, Transaksi Tunai/Cash, & Non-Tunai QRIS/Transfer)
         ======================================================== --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Omset Hari Ini --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100 d-flex flex-column justify-content-between"
                 style="border-left: 4px solid #2C4679 !important;">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">
                            Total Omset Hari Ini
                        </span>
                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center"
                             style="width: 42px; height: 42px; background: #CFDCF8; color: #2C4679;">
                            <i class="bi bi-wallet2 fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: #2C4679;">
                        Rp {{ number_format($omzetHariIni ?? 0, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="pt-3 mt-2 border-top border-light d-flex align-items-center justify-content-between">
                    <span class="badge rounded-pill bg-light text-muted border px-2.5 py-1 small fw-normal">
                        <i class="bi bi-calendar3 me-1 text-primary"></i> {{ now()->translatedFormat('d F Y') }}
                    </span>
                    <small class="text-muted">Total Pendapatan</small>
                </div>
            </div>
        </div>

        {{-- Card 2: Pembayaran Tunai (Cash) --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100 d-flex flex-column justify-content-between"
                 style="border-left: 4px solid #10B981 !important;">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">
                            Transaksi Tunai (Cash)
                        </span>
                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center"
                             style="width: 42px; height: 42px; background: #DCFCE7; color: #16A34A;">
                            <i class="bi bi-cash-stack fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">
                        Rp {{ number_format($omzetTunai ?? 0, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="pt-3 mt-2 border-top border-light d-flex align-items-center justify-content-between">
                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2.5 py-1 small fw-medium">
                        <i class="bi bi-check2-circle me-1"></i> {{ $transaksiTunaiCount ?? 0 }} Transaksi Cash
                    </span>
                    <small class="text-muted">Uang Tunai Kasir</small>
                </div>
            </div>
        </div>

        {{-- Card 3: Non-Tunai (QRIS & Transfer Bank) --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100 d-flex flex-column justify-content-between"
                 style="border-left: 4px solid #4F46E5 !important;">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">
                            Non-Tunai (QRIS &amp; Transfer)
                        </span>
                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center"
                             style="width: 42px; height: 42px; background: #EEF2FF; color: #4F46E5;">
                            <i class="bi bi-qr-code-scan fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: #4F46E5;">
                        Rp {{ number_format($omzetNonTunai ?? 0, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="pt-3 mt-2 border-top border-light d-flex align-items-center justify-content-between">
                    <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 small fw-medium">
                        <i class="bi bi-phone me-1"></i> {{ $transaksiNonTunaiCount ?? 0 }} Transaksi Digital
                    </span>
                    <small class="text-muted">QRIS &amp; Transfer</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================
         TABEL PESANAN TERBARU
         (Sederhana & konsisten seperti halaman Daftar Pesanan,
          pesanan baru otomatis di paling atas & detail langsung ke page)
         ======================================================== --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        {{-- Header Tabel --}}
        <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-3 p-2 d-inline-flex" style="background: #CFDCF8; color: #2C4679;">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="color: #2C4679;">Pesanan Baru Ditambahkan</h6>
                    <small class="text-muted">Transaksi terkini masuk paling atas</small>
                </div>
            </div>
            <a href="{{ route('pesanan.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                Lihat Semua <i class="bi bi-chevron-right ms-1"></i>
            </a>
        </div>

        {{-- Tabel Data Transaksi (Sesuai Struktur Daftar Pesanan) --}}
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
                    @forelse($terbaru ?? [] as $item)
                    <tr>
                        <td class="ps-4 fw-bold" style="color: #2C4679;">
                            #{{ $item->nomor_pesanan }}
                        </td>
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
                            @if(strtolower($item->status_pembayaran) === 'lunas')
                                <span class="badge rounded-pill px-2.5 py-1 bg-success-subtle text-success">
                                    <i class="bi bi-check-circle me-1"></i>Lunas
                                </span>
                            @else
                                <span class="badge rounded-pill px-2.5 py-1 bg-danger-subtle text-danger">
                                    <i class="bi bi-clock me-1"></i>Belum Lunas
                                </span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                {{-- Tombol Detail langsung mengarah ke halaman Detail Pesanan --}}
                                <a href="{{ route('pesanan.show', $item->id_pesanan) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Detail & Tracking">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                                <a href="{{ route('pesanan.print', $item->id_pesanan) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 31px; height: 31px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="Cetak Nota">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada pesanan yang ditambahkan hari ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
