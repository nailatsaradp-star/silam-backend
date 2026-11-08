@extends('layouts.app')

@section('title', 'Laporan Keuangan & Transaksi Admin')

@section('content')
<div class="container-fluid p-0">

    {{-- FILTER PERIODE BULAN & TAHUN (Sembunyi saat cetak) --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white d-print-none">
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold" style="color: #2C4679;">Pilih Bulan</label>
                <select name="bulan" class="form-select bg-light">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ ($bulan ?? date('m')) == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold" style="color: #2C4679;">Pilih Tahun</label>
                <select name="tahun" class="form-select bg-light">
                    @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ ($tahun ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn text-white fw-semibold rounded-3 flex-grow-1" style="background: #2C4679;">
                    <i class="bi bi-funnel me-1"></i> Tampilkan
                </button>

                <a href="{{ route('admin.laporan.print', ['bulan' => $bulan ?? date('m'), 'tahun' => $tahun ?? date('Y')]) }}"
                    target="_blank"
                    class="btn btn-outline-danger rounded-3"
                    title="Cetak PDF Resmi">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
                </a>

            </div>
        </form>
    </div>

    {{-- JUDUL LAPORAN UNTUK MODE CETAK (Hanya tampil saat print) --}}
    <div class="d-none d-print-block mb-4 text-center">
        <h4 class="fw-bold mb-1">LAPORAN KEUANGAN &amp; OPERASIONAL</h4>
        <p class="text-muted small">Periode: {{ date('F', mktime(0, 0, 0, $bulan ?? date('m'), 1)) }} {{ $tahun ?? date('Y') }}</p>
        <hr>
    </div>

    {{-- KARTU REKAPITULASI LAPORAN (Tetap menghitung total keseluruhan bulan ini) --}}
    <div class="row g-3 mb-4">
        {{-- Total Transaksi --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-top: 4px solid #2C4679 !important;">
                <span class="text-muted small fw-semibold">Total Transaksi</span>
                <h4 class="fw-bold mb-0" style="color: #2C4679;">{{ $totalTransaksi ?? 0 }} <small class="fs-6 fw-normal text-muted">Nota</small></h4>
            </div>
        </div>

        {{-- Total Pendapatan --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-top: 4px solid #7FA2E8 !important;">
                <span class="text-muted small fw-semibold">Total Pendapatan (Omset)</span>
                <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        {{-- Piutang --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-top: 4px solid #f87171 !important;">
                <span class="text-muted small fw-semibold">Piutang (Belum Lunas)</span>
                <h4 class="fw-bold mb-0 text-danger">Rp {{ number_format($belumLunas ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        {{-- Sudah Selesai --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-top: 4px solid #E1EEA2 !important;">
                <span class="text-muted small fw-semibold">Sudah Selesai &amp; Diambil</span>
                <h4 class="fw-bold mb-0" style="color: #2C4679;">{{ $sudahDiambil ?? 0 }} <small class="fs-6 fw-normal text-muted">Pesanan</small></h4>
            </div>
        </div>
    </div>

    {{-- TABEL RINCIAN LAPORAN --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0" style="color: #2C4679;">
                Rincian Transaksi Periode {{ date('F', mktime(0, 0, 0, $bulan ?? date('m'), 1)) }} {{ $tahun ?? date('Y') }}
            </h6>
            <span class="badge rounded-pill fw-normal px-3 py-2 bg-light text-muted d-print-none">
                Total: {{ $laporan->total() ?? 0 }} Data
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0 small">
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
                    @forelse($laporan as $row)
                    <tr>
                        {{-- Tanggal dibuat --}}
                        <td class="ps-4 text-muted">
                            {{ \Carbon\Carbon::parse($row->tanggal_pesanan_dibuat ?? $row->created_at)->format('d/m/Y') }}
                        </td>

                        {{-- Nomor / Kode Pesanan --}}
                        <td class="fw-bold text-dark">
                            #{{ $row->nomor_pesanan ?? ($row->kode_transaksi ?? $row->id_pesanan) }}
                        </td>

                        {{-- Nama Pelanggan --}}
                        <td>{{ $row->pelanggan->nama_pelanggan ?? 'Umum' }}</td>

                        {{-- Daftar Layanan --}}
                        <td>
                            @if(isset($row->detail) && $row->detail->isNotEmpty())
                                {{ $row->detail->pluck('layanan.nama_layanan')->filter()->implode(', ') ?: '-' }}
                            @else
                                {{ $row->layanan->nama_layanan ?? '-' }}
                            @endif
                        </td>

                        {{-- Status Pengerjaan Cucian --}}
                        <td>
                            @php
                                $badgeColors = [
                                    'selesai'    => 'success',
                                    'siap_ambil' => 'success',
                                    'proses'     => 'info',
                                    'dicuci'     => 'info',
                                    'antrian'    => 'warning',
                                ];
                                $warnaBadge = $row->badge_kerja ?? ($badgeColors[$row->status_kerja ?? ''] ?? 'secondary');
                                $labelTeks  = $row->label_kerja ?? str_replace('_', ' ', $row->status_kerja ?? '-');
                            @endphp
                            <span class="badge rounded-pill text-uppercase px-2 py-1 bg-{{ $warnaBadge }}">
                                {{ $labelTeks }}
                            </span>
                        </td>

                        {{-- Status Pembayaran --}}
                        <td>
                            @if(($row->status_pembayaran ?? '') === 'lunas')
                                <span class="badge rounded-pill bg-success px-2 py-1">Lunas</span>
                            @else
                                <span class="badge rounded-pill bg-danger px-2 py-1">Belum Lunas</span>
                            @endif
                        </td>

                        {{-- Nominal Tagihan --}}
                        <td class="text-end pe-4 fw-bold text-dark">
                            Rp {{ number_format($row->total_tagihan ?? ($row->total_bayar ?? 0), 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                            Belum ada rekaman laporan pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION NAVIGATION (10 per halaman) --}}
        @if($laporan->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-print-none">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <small class="text-muted">
                    Menampilkan <b>{{ $laporan->firstItem() }}</b> sampai <b>{{ $laporan->lastItem() }}</b> dari <b>{{ $laporan->total() }}</b> laporan
                </small>
                <div>
                    {{ $laporan->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif

    </div>

</div>
@endsection
