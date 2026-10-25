@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color: #2C4679;">Dashboard Kasir</h5>
            <small class="text-muted">Ringkasan aktivitas transaksi &amp; status antrean laundry hari ini</small>
        </div>
        <a href="{{ route('kasir.index') }}" class="btn text-white rounded-pill px-4 fw-semibold" style="background: #2C4679;">
            <i class="bi bi-plus-circle-fill me-1"></i> Transaksi Baru
        </a>
    </div>

    {{-- KARTU RINGKASAN --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block mb-1">Pesanan Hari Ini</span>
                        <h4 class="fw-bold mb-0" style="color: #2C4679;">{{ $pesananHariIni ?? 0 }}</h4>
                    </div>
                    <div class="rounded-circle p-3" style="background: #CFDCF8; color: #2C4679;">
                        <i class="bi bi-bag-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block mb-1">Dalam Proses</span>
                        <h4 class="fw-bold text-warning mb-0">{{ $dalamProses ?? 0 }}</h4>
                    </div>
                    <div class="rounded-circle p-3 bg-warning-subtle text-warning">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block mb-1">Siap Diambil</span>
                        <h4 class="fw-bold text-info mb-0">{{ $siapAmbil ?? 0 }}</h4>
                    </div>
                    <div class="rounded-circle p-3 bg-info-subtle text-info">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block mb-1">Belum Lunas</span>
                        <h4 class="fw-bold text-danger mb-0">{{ $belumLunas ?? 0 }}</h4>
                    </div>
                    <div class="rounded-circle p-3 bg-danger-subtle text-danger">
                        <i class="bi bi-exclamation-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
