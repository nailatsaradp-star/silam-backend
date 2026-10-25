@extends('landing')

@section('title_menu', 'Cek Status LandingPage')

{{-- Import File CSS Terpisah --}}
<link rel="stylesheet" href="{{ asset('bs/css/silam-cek-status.css') }}">

@section('isikonten')

@php
    $waCs = '6282119793595';
@endphp

<div class="container my-4">

    <!-- ===== HERO + KOLOM PENCARIAN ===== -->
    <div class="tracking-hero-card">
        <div class="bubble bubble-1" title="Klik aku!"></div>
        <div class="bubble bubble-2" title="Klik aku!"></div>
        <div class="bubble bubble-3" title="Klik aku!"></div>
        <div class="bubble bubble-4" title="Klik aku!"></div>
        <div class="bubble bubble-5" title="Klik aku!"></div>
        <div class="bubble bubble-6" title="Klik aku!"></div>
        <div class="bubble bubble-7" title="Klik aku!"></div>
        <div class="bubble bubble-8" title="Klik aku!"></div>

        <h1 class="tracking-hero-title">
            Lacak Pesananmu secara real-time!<br>
            Dengan memasukkan nomor pesanan atau nomor HP.
        </h1>

        <div class="tracking-search-box-wrap">
            <form action="{{ route('status') }}" method="GET">
                <div class="tracking-input-pill">
                    <input type="text" name="q" placeholder="Nomor pesanan / nomor HP"
                           value="{{ request('q') }}" autocomplete="off" required>
                    <button type="submit" class="btn-search-bubble" aria-label="Cari pesanan">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== TIDAK DITEMUKAN ===== -->
    @if(($dicari ?? false) && ! ($order ?? null))
        <div class="alert alert-warning rounded-4 mt-4 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Pesanan tidak ditemukan. Periksa kembali nomor pesanan atau nomor HP yang Anda masukkan.
        </div>
    @endif

    <!-- ===== HASIL ===== -->
    @if($order)
        @php
            $tahap = $info['tahap'] ?? 1;
            $siap  = $tahap >= 3; // siap_ambil atau selesai
            $tahapList = [
                1 => ['Pesanan Diterima (Antrian)', 'Pakaian sudah ditimbang dan nota tercatat di kasir. Menunggu giliran dikerjakan.'],
                2 => ['Sedang Diproses',            'Pakaian sedang dicuci, dikeringkan, dan disetrika sesuai layanan yang dipilih.'],
                3 => ['Siap Diambil',               'Pakaian sudah dikemas rapi dan siap diambil di outlet.'],
                4 => ['Selesai',                    'Pesanan sudah diambil. Terima kasih telah mempercayakan cucian Anda kepada SILAM.'],
            ];
        @endphp

        <div class="row justify-content-center mt-4">
            <div class="col-lg-12">

                {{-- Pilihan pesanan lain (muncul jika dicari lewat nomor HP dan punya beberapa pesanan) --}}
                @if(isset($daftar) && $daftar->count() > 1)
                    <div class="mb-3 small">
                        <span class="text-muted">Pesanan atas nomor ini:</span>
                        @foreach($daftar as $d)
                            <a href="{{ route('status', ['q' => $d->nomor_pesanan]) }}"
                               class="badge rounded-pill text-bg-light border text-decoration-none ms-1
                                      {{ $d->id_pesanan === $order->id_pesanan ? 'fw-bold' : '' }}">
                                {{ $d->nomor_pesanan }} &middot; {{ $d->label_kerja ?? $d->status_kerja }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="status-result-card">

                    <!-- Header -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 border-bottom">
                        <div>
                            <h4 class="fw-bold mb-1" style="color: var(--navy-main);">No. Pesanan: {{ $order->nomor_pesanan }}</h4>
                            <div class="text-muted small">
                                Waktu Penerimaan: <span>{{ $order->tanggal_pesanan_dibuat ? $order->tanggal_pesanan_dibuat->format('d/m/Y, H:i') : '-' }} WIB</span>
                            </div>
                        </div>
                        <div>
                            @if($siap)
                                <span class="status-badge-pill badge-ready-green">
                                    <i class="bi bi-check-circle-fill"></i> {{ $order->label_kerja ?? 'Siap Diambil' }}
                                </span>
                            @else
                                <span class="status-badge-pill badge-progress-blue">
                                    <i class="bi bi-arrow-repeat"></i>
                                    {{ $order->status_kerja === 'antrian' ? 'Dalam Antrian' : 'Sedang Diproses' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Rincian pesanan -->
                    <div class="order-info-box">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <div class="order-info-label">Nama Pelanggan</div>
                                <div class="order-info-value">{{ $info['nama'] ?? '-' }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="order-info-label">Paket Layanan</div>
                                <div class="order-info-value">{{ $info['paket'] ?? '-' }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="order-info-label">Jumlah &amp; Biaya</div>
                                <div class="order-info-value">
                                    {{ $info['berat'] ?? '-' }} (Rp {{ number_format($order->total_tagihan, 0, ',', '.') }})
                                    <span class="badge {{ $order->status_pembayaran === 'lunas' ? 'text-bg-success' : 'text-bg-danger' }} ms-1">
                                        {{ $order->status_pembayaran === 'lunas' ? 'Lunas' : 'Belum lunas' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="order-info-label">Estimasi Selesai</div>
                                <div class="order-info-value text-primary">
                                    {{ $order->tanggal_estimasi_selesai ? $order->tanggal_estimasi_selesai->format('d/m/Y, H:i') : '-' }} WIB
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tahapan progres (dari status_kerja) -->
                    <h6 class="fw-bold mt-4 mb-3" style="color: var(--navy-main);">
                        <i class="bi bi-clock-history me-1 text-primary"></i> Progres Pesanan
                    </h6>

                    <div class="laundry-timeline">
                        @foreach($tahapList as $no => [$judul, $ket])
                            @php
                                $kelas = $tahap > $no ? 'is-done' : ($tahap == $no ? 'is-active' : '');
                                if ($no == 4 && $tahap == 4) $kelas = 'is-done is-active';
                            @endphp
                            <div class="timeline-step-item {{ $kelas }}">
                                <div class="timeline-circle">
                                    @if($tahap > $no || ($no == 4 && $tahap == 4)) <i class="bi bi-check-lg"></i> @else {{ $no }} @endif
                                </div>
                                <div class="timeline-heading">
                                    {{ $no }}. {{ $judul }}
                                    @if($tahap == $no && $no < 4)
                                        <span class="badge bg-primary-subtle text-primary ms-1">Saat ini</span>
                                    @endif
                                </div>
                                <div class="timeline-subtext">{{ $ket }}</div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Riwayat pengerjaan (dari tabel riwayat status) -->
                    @if(!empty($order->riwayat) && $order->riwayat->count() > 0)
                        <h6 class="fw-bold mt-4 mb-3" style="color: var(--navy-main);">
                            <i class="bi bi-list-check me-1 text-primary"></i> Riwayat Pengerjaan
                        </h6>

                        <div class="laundry-timeline">
                            @foreach($order->riwayat as $r)
                                <div class="timeline-step-item {{ $loop->first ? 'is-active' : 'is-done' }}">
                                    <div class="timeline-circle">
                                        @if($loop->first) <i class="bi bi-record-circle"></i> @else <i class="bi bi-check-lg"></i> @endif
                                    </div>
                                    <div class="timeline-heading">{{ $r->status_detail }}</div>
                                    <div class="timeline-subtext">
                                        {{ $r->waktu_update ? $r->waktu_update->format('d/m/Y, H:i') : '' }} WIB
                                        @if($r->keterangan) &middot; {{ $r->keterangan }} @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Tombol aksi -->
                    <div class="d-flex flex-wrap gap-2 justify-content-end mt-4 pt-3 border-top">
                        <button class="btn btn-outline-secondary rounded-pill btn-sm px-3" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> Cetak Bukti Status
                        </button>
                        <a href="https://wa.me/{{ $waCs }}?text={{ rawurlencode('Halo SILAM, saya ingin konfirmasi pesanan ' . $order->nomor_pesanan) }}"
                           target="_blank" rel="noopener" class="btn btn-success rounded-pill btn-sm px-4">
                            <i class="bi bi-whatsapp me-1"></i> Hubungi CS Outlet
                        </a>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>

{{-- Import File JS Terpisah --}}
<script src="{{ asset('bs/js/silam-cek-status.js') }}"></script>

@endsection
