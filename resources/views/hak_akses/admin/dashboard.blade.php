@extends('layouts.app')

@section('title', 'Dashboard Pemilik')

@section('content')
<div class="container-fluid p-0">

    {{-- 1. KARTU STATISTIK UTAMA (Ringkasan Keuangan & Pelanggan) --}}
    <div class="row g-3 mb-4">
        {{-- Pendapatan Hari Ini --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; border-left: 5px solid #7FA2E8 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Pendapatan Hari Ini</span>
                    <span class="p-2 rounded-3" style="background: #CFDCF8; color: #2C4679;"><i class="bi bi-wallet2 fs-5"></i></span>
                </div>
                <h4 class="fw-bold mb-1" style="color: #2C4679;">Rp {{ number_format($omzetHariIni, 0, ',', '.') }}</h4>
                <small class="text-success fw-semibold"><i class="bi bi-arrow-up-right me-1"></i> Hari ini</small>
            </div>
        </div>

        {{-- Pendapatan Bulan Ini / Total Order --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; border-left: 5px solid #2C4679 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Pesanan Belum Lunas</span>
                    <span class="p-2 rounded-3" style="background: #FEE2E2; color: #DC2626;"><i class="bi bi-exclamation-circle fs-5"></i></span>
                </div>
                <h4 class="fw-bold mb-1 text-danger">{{ $belumLunas }} <span class="fs-6 fw-normal text-muted">Pesanan</span></h4>
                <small class="text-danger small">Perlu Penagihan</small>
            </div>
        </div>

        {{-- Total Order --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; border-left: 5px solid #7FA2E8 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Total Orderan</span>
                    <span class="p-2 rounded-3" style="background: #CFDCF8; color: #2C4679;"><i class="bi bi-bag-check fs-5"></i></span>
                </div>
                <h4 class="fw-bold mb-1" style="color: #2C4679;">{{ count($terbaru ?? []) }} <span class="fs-6 fw-normal text-muted">Pesanan</span></h4>                <small class="text-primary fw-semibold"><i class="bi bi-graph-up me-1"></i> Data terbaru</small>
            </div>
        </div>

        {{-- Total Pelanggan / Antrian --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; border-left: 5px solid #E1EEA2 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold small">Total Antrian</span>
                    <span class="p-2 rounded-3" style="background: #FAF8F5; color: #2C4679; border: 1px solid #CFDCF8;"><i class="bi bi-people fs-5"></i></span>
                </div>
                <h4 class="fw-bold mb-1" style="color: #2C4679;">{{ $perStatus['antrian'] ?? ($perStatus['baru'] ?? 0) }} <span class="fs-6 fw-normal text-muted">Pesanan</span></h4>
                <small class="text-muted small">Sedang antri</small>
            </div>
        </div>
    </div>

    {{-- 2. PIPELINE STATUS CUCIAN (3 KOTAK KUSUS: Antrian, Proses, Siap Ambil) --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <h6 class="fw-bold mb-3" style="color: #2C4679;"><i class="bi bi-arrow-repeat me-2"></i>Status Operasional Cucian Saat Ini</h6>
        <div class="row g-3 text-center">
            <div class="col-md-4">
                <div class="p-3 rounded-4" style="background: #FFFBEB; border: 1px solid #FDE68A;">
                    <span class="d-block text-muted small fw-bold text-uppercase">Antrian (Baru)</span>
                    <span class="display-6 fw-bold text-warning">{{ $perStatus['antrian'] ?? ($perStatus['baru'] ?? 0) }}</span>
                    <small class="d-block text-muted mt-1">Belum mulai diproses</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-4" style="background: #EFF6FF; border: 1px solid #CFDCF8;">
                    <span class="d-block text-muted small fw-bold text-uppercase" style="color: #2C4679;">Sedang Dicuci / Setrika</span>
                    <span class="display-6 fw-bold" style="color: #7FA2E8;">{{ $perStatus['proses'] ?? ($perStatus['dicuci'] ?? 0) }}</span>
                    <small class="d-block text-muted mt-1">Dalam pengerjaan mesin</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-4" style="background: #F0FDF4; border: 1px solid #BBF7D0;">
                    <span class="d-block text-muted small fw-bold text-uppercase text-success">Siap Diambil</span>
                    <span class="display-6 fw-bold text-success">{{ $perStatus['siap_ambil'] ?? ($perStatus['selesai'] ?? 0) }}</span>
                    <small class="d-block text-muted mt-1">Selesai dikemas &amp; wangi</small>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. GRAFIK 7 HARI TERAKHIR & ORDER TERBARU --}}
    <div class="row g-4">
        {{-- Grafik Penjualan 7 Hari --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0" style="color: #2C4679;"><i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Tren Pendapatan 7 Hari Terakhir</h6>
                    <span class="badge rounded-pill fw-semibold px-3 py-2" style="background: #CFDCF8; color: #2C4679;">Realtime</span>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="chart7Hari"></canvas>
                </div>
            </div>
        </div>

        {{-- Tabel Orderan Terbaru --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0" style="color: #2C4679;"><i class="bi bi-clock-history me-2"></i>Pesanan Terbaru</h6>
                    <a href="{{ route('pesanan.index') }}" class="small fw-semibold text-decoration-none" style="color: #7FA2E8;">Lihat Semua <i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Pelanggan</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($terbaru as $p)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p->pelanggan->nama_pelanggan }}</div>
                                    <small class="text-muted"><a href="{{ route('pesanan.show', $p) }}" class="text-decoration-none text-muted">{{ $p->nomor_pesanan }}</a></small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-2 py-1 bg-{{ $p->badge_kerja }}">
                                        {{ $p->label_kerja }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-dark">Rp {{ number_format($p->total_tagihan, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Belum ada transaksi hari ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('chart7Hari');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: [450000, 620000, 580000, 810000, 750000, 1100000, 850000],
                    borderColor: '#7FA2E8',
                    backgroundColor: 'rgba(207, 220, 248, 0.35)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#2C4679',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        grid: { color: 'rgba(207, 220, 248, 0.4)' },
                        ticks: { callback: v => 'Rp ' + (v/1000) + 'k' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
