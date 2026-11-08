@extends('layouts.app')

@section('title', 'Monitoring Detail Pesanan #' . $pesanan->nomor_pesanan)

@section('content')
<div class="container-fluid p-0">

    {{-- HEADER NAVIGASI & INFORMASI READ-ONLY --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 d-print-none">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h5 class="fw-bold mb-0" style="color: #2C4679;">Pesanan #{{ $pesanan->nomor_pesanan }}</h5>
                <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background: #CFDCF8; color: #2C4679;">
                    <i class="bi bi-shield-check me-1"></i> Mode Monitoring (Admin)
                </span>
            </div>
            <small class="text-muted">Rincian transaksi, rincian biaya, dan log audit perjalanan pesanan</small>
        </div>
        <div>
            <a href="{{ route('pesanan.index') }}" class="btn btn-light rounded-pill px-4 border shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pesanan
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- SISI KIRI: DETAIL NOTA & RINGKASAN BIAYA --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                {{-- INFO PELANGGAN & WAKTU --}}
                <div class="row mb-3 pb-3 border-bottom">
                    <div class="col-sm-6 mb-2 mb-sm-0">
                        <small class="text-muted d-block fw-semibold">Data Pelanggan</small>
                        <span class="fw-bold text-dark fs-6 d-block">{{ $pesanan->pelanggan->nama_pelanggan ?? '-' }}</span>
                        <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $pesanan->pelanggan->nomor_telepon ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <small class="text-muted d-block fw-semibold">Tanggal Masuk Transaksi</small>
                        <span class="fw-semibold text-dark d-block">
                            {{ $pesanan->tanggal_pesanan_dibuat ? \Carbon\Carbon::parse($pesanan->tanggal_pesanan_dibuat)->format('d/m/Y H:i') : '-' }}
                        </span>
                        <small class="text-muted d-block mt-1 fw-semibold">Estimasi Penyelesaian</small>
                        <span class="fw-semibold text-primary">
                            {{ $pesanan->tanggal_estimasi_selesai ? \Carbon\Carbon::parse($pesanan->tanggal_estimasi_selesai)->format('d/m/Y H:i') : '-' }}
                        </span>
                    </div>
                </div>

                {{-- TABEL ITEM LAYANAN --}}
                <div class="table-responsive my-2">
                    <table class="table table-sm align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Layanan Paket</th>
                                <th class="text-center">Jumlah / Berat</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach($pesanan->detail as $d)
                            <tr>
                                <td class="fw-bold text-dark">{{ $d->layanan->nama_layanan ?? '-' }}</td>
                                <td class="text-center">{{ $d->jumlah_berat }} {{ $d->layanan->satuan ?? 'Kg' }}</td>
                                <td class="text-end">Rp {{ number_format($d->layanan->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end pt-3">Total Tagihan Transaksi:</th>
                                <th class="text-end fs-6 text-primary pt-3">Rp {{ number_format($pesanan->total_tagihan, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- STATUS INDIKATOR & INFO KASIR --}}
                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $pesanan->status_pembayaran === 'lunas' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-2">
                            <i class="bi bi-credit-card-fill me-1"></i> {{ strtoupper($pesanan->status_pembayaran) }}
                        </span>
                        <span class="badge bg-primary rounded-pill px-3 py-2 text-capitalize">
                            <i class="bi bi-gear-fill me-1"></i> {{ str_replace('_', ' ', $pesanan->status_kerja) }}
                        </span>
                    </div>

                    <div class="text-muted small">
                        <i class="bi bi-person-badge me-1"></i> Ditangani Kasir: <strong>{{ $pesanan->kasir->nama_user ?? '-' }}</strong>
                    </div>
                </div>
            </div>

            {{-- CATATAN PENJELASAN READ-ONLY --}}
            <div class="alert alert-info border-0 rounded-4 shadow-sm p-3 d-flex align-items-center gap-3">
                <i class="bi bi-info-circle-fill fs-3 text-info"></i>
                <div class="small">
                    <strong>Catatan Administrator:</strong> Halaman ini bersifat <em>Read-Only</em>. Penanganan pembayaran dilakukan oleh <strong>Kasir</strong>, sedangkan perubahan status kerja fisik laundry diproses oleh <strong>Pegawai/Petugas Operational</strong>.
                </div>
            </div>
        </div>

        {{-- SISI KANAN: AUDIT LOG RIWAYAT TRACKING --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h6 class="fw-bold mb-3" style="color: #2C4679;">
                    <i class="bi bi-clock-history me-1"></i> Log Riwayat Pengerjaan
                </h6>

                <div class="timeline">
                    @forelse($pesanan->riwayat as $r)
                    <div class="d-flex mb-3 pb-3 border-bottom">
                        <div class="me-3">
                            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="background: #EEF4FD; color: #2C4679; width: 38px; height: 38px;">
                                <i class="bi bi-check2-circle fs-5"></i>
                            </div>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $r->status_detail }}</div>
                            @if($r->keterangan)
                                <div class="small text-muted bg-light p-2 rounded-3 mt-1">{{ $r->keterangan }}</div>
                            @endif
                            <div class="small text-muted mt-1">
                                <i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($r->waktu_update)->format('d/m/Y H:i') }} &middot;
                                <i class="bi bi-person me-1"></i>{{ $r->petugas->nama_user ?? 'Petugas' }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-muted small text-center py-4">
                        <i class="bi bi-inbox d-block fs-3 mb-1 opacity-50"></i>
                        Belum ada catatan log riwayat pengerjaan.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
