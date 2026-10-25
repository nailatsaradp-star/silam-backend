@extends('layouts.app')

@section('title', 'Laporan Keuangan & Transaksi Admin')

@section('content')
<div class="container-fluid p-0">

    {{-- FILTER PERIODE BULAN & TAHUN --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white d-print-none">
        <form method="GET" action="{{ route('laporan.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold" style="color: #2C4679;">Pilih Bulan</label>
                <select name="bulan" class="form-select bg-light">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (request('bulan') ?? date('m')) == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold" style="color: #2C4679;">Pilih Tahun</label>
                <select name="tahun" class="form-select bg-light">
                    @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ (request('tahun') ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn text-white fw-semibold rounded-3 flex-grow-1" style="background: #2C4679;">
                    <i class="bi bi-funnel me-1"></i> Tampilkan
                </button>
                <button type="button" onclick="window.print()" class="btn btn-outline-secondary rounded-3" title="Print / PDF">
                    <i class="bi bi-printer me-1"></i> Cetak PDF
                </button>
            </div>
        </form>
    </div>

    {{-- KARTU REKAPITULASI LAPORAN --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-top: 4px solid #2C4679 !important;">
                <span class="text-muted small fw-semibold">Total Transaksi</span>
                <h4 class="fw-bold mb-0" style="color: #2C4679;">{{ $totalTransaksi ?? 142 }} <small class="fs-6 fw-normal text-muted">Nota</small></h4>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-top: 4px solid #7FA2E8 !important;">
                <span class="text-muted small fw-semibold">Total Pendapatan (Omset)</span>
                <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($totalPendapatan ?? 12450000, 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-top: 4px solid #f87171 !important;">
                <span class="text-muted small fw-semibold">Piutang (Belum Lunas)</span>
                <h4 class="fw-bold mb-0 text-danger">Rp {{ number_format($belumLunas ?? 420000, 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-top: 4px solid #E1EEA2 !important;">
                <span class="text-muted small fw-semibold">Sudah Selesai &amp; Diambil</span>
                <h4 class="fw-bold mb-0" style="color: #2C4679;">{{ $sudahDiambil ?? 136 }} <small class="fs-6 fw-normal text-muted">Selesai</small></h4>
            </div>
        </div>
    </div>

    {{-- TABEL RINCIAN LAPORAN --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="fw-bold mb-0" style="color: #2C4679;">Rincian Transaksi Periode Terpilih</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0 small">
                <thead style="background: #2C4679; color: #ffffff;">
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Kode Nota</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Status Kerja</th>
                        <th>Pembayaran</th>
                        <th class="text-end pe-4">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan ?? [] as $row)
                    <tr>
                        <td class="ps-4 text-muted">{{ $row->created_at?->format('d/m/Y') ?? '18/10/2026' }}</td>
                        <td class="fw-bold">#{{ $row->kode_transaksi ?? 'SLM-001' }}</td>
                        <td>{{ $row->pelanggan->nama_pelanggan ?? 'Umum' }}</td>
                        <td>{{ $row->layanan->nama_layanan ?? 'Cuci Komplit' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $row->status_kerja ?? 'Selesai' }}</span></td>
                        <td>
                            <span class="badge {{ ($row->status_pembayaran ?? 'Lunas') === 'Lunas' ? 'bg-success' : 'bg-danger' }}">
                                {{ $row->status_pembayaran ?? 'Lunas' }}
                            </span>
                        </td>
                        <td class="text-end pe-4 fw-bold">Rp {{ number_format($row->total_bayar ?? 25000, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada rekaman laporan pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
