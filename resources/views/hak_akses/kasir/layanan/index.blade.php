@extends('layouts.app')

@section('title', 'Katalog Layanan & Tarif')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color: #2C4679;">Katalog Layanan &amp; Tarif Harga</h5>
            <small class="text-muted">Daftar tarif paket cuci dan estimasi durasi pengerjaan (Mode Lihat)</small>
        </div>
        <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background: #CFDCF8; color: #2C4679;">
            <i class="bi bi-eye-fill me-1"></i> Mode Baca (Kasir)
        </span>
    </div>

    {{-- TABEL KATALOG LAYANAN READ-ONLY --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #FAF8F5; color: #2C4679;" class="small fw-bold">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Layanan</th>
                        <th>Kategori</th>
                        <th>Jenis Satuan</th>
                        <th>Tarif Harga</th>
                        <th class="pe-4">Estimasi Selesai</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($items ?? [] as $index => $lay)
                    <tr>
                        <td class="ps-4 fw-semibold text-muted">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $lay->nama_layanan }}</div>
                            <small class="text-muted">{{ $lay->deskripsi ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 py-1 bg-light text-dark border">
                                {{ ucfirst(str_replace('_', ' & ', $lay->kategori ?? 'Layanan')) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 py-1" style="background: #CFDCF8; color: #2C4679;">
                                {{ strtoupper($lay->satuan ?? 'Kg') }}
                            </span>
                        </td>
                        <td class="fw-bold fs-6" style="color: #2C4679;">
                            Rp {{ number_format($lay->harga_satuan, 0, ',', '.') }}
                            <span class="fs-6 fw-normal text-muted">/ {{ $lay->satuan ?? 'kg' }}</span>
                        </td>
                        <td class="pe-4">
                            <span class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                @php
                                    $jam = $lay->estimasi_selesai ?? 0;
                                @endphp

                                @if($jam >= 24)
                                    {{ floor($jam / 24) }} Hari {{ $jam % 24 > 0 ? ($jam % 24) . ' Jam' : '' }}
                                @else
                                    {{ $jam }} Jam
                                @endif
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada katalog layanan yang tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
