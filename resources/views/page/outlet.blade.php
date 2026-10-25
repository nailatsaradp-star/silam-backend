@extends('landing')

@section('title_menu', 'Outlet LandingPage')

@section('isikonten')

@push('styles')
    {{-- CSS Khusus Halaman Outlet --}}
    <link rel="stylesheet" href="{{ asset('bs/css/silam-outlet.css') }}">
@endpush

<section class="outlet-page-section">
    <div class="container">
        <div class="outlet-main-card">
            <div class="row align-items-stretch g-4 g-lg-5">

                {{-- ============ SISI KIRI: FOTO OUTLET ============ --}}
                <div class="col-lg-5 outlet-left-col">
                    <div class="outlet-photo-card">
                        {{-- Badge Buka Sekarang --}}
                        <div class="outlet-status-pill">
                            <span class="outlet-status-dot"></span>
                            <span>Buka Sekarang &bull; 08:00 - 21:00</span>
                        </div>

                        <img src="{{ asset('picture/outlet.png') }}" alt="Outlet At_Hura Laundry & Dry Cleaning" class="outlet-photo-img" loading="lazy">
                    </div>
                </div>

                {{-- ============ SISI KANAN: DETAIL & PRICELIST ============ --}}
                <div class="col-lg-7 outlet-right-col d-flex flex-column justify-content-between">
                    <div>
                        {{-- Judul Outlet --}}
                        <h1 class="outlet-header-title">
                            At_Hura Laundry &amp; Dry Cleaning
                        </h1>

                        {{-- Alamat Google Maps --}}
                        <div class="outlet-address-group">
                            <i class="bi bi-geo-alt-fill outlet-pin-icon"></i>
                            <a href="https://maps.app.goo.gl/CuhUM7FrzaZj1t2R7"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="outlet-address-link">
                                Jl. Puri Cipageran Indah 1 Blok H2 No.72
                            </a>
                        </div>

                        {{-- Tombol Aksi: Gmaps & Hubungi Outlet --}}
                        <div class="outlet-action-buttons">
                            <a href="https://maps.app.goo.gl/CuhUM7FrzaZj1t2R7"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="btn-outlet-action">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>Lokasi Gmaps</span>
                            </a>

                            <a href="https://wa.me/628219793595?text=Halo%20At_Hura%20Laundry,%20saya%20ingin%20bertanya%20mengenai%20layanan%20laundry"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="btn-outlet-action">
                                <i class="bi bi-whatsapp"></i>
                                <span>Hubungi Outlet</span>
                            </a>
                        </div>
                    </div>

                    {{-- Card Pricelist Sesuai Foto --}}
                    <div class="outlet-pricelist-card">
                        <h3 class="pricelist-title">Pricelist</h3>

                        <div class="pricelist-list">
                            {{-- Item 1: Regular --}}
                            <div class="pricelist-item">
                                <div>
                                    <span class="item-name">Regular</span>
                                    <span class="item-estimate">Estimasi 3 Hari</span>
                                </div>
                                <span class="item-price">Rp8.000,00/Kg</span>
                            </div>

                            {{-- Item 2: Express --}}
                            <div class="pricelist-item">
                                <div>
                                    <span class="item-name">Express</span>
                                    <span class="item-estimate">Estimasi 1 Hari</span>
                                </div>
                                <span class="item-price">Rp10.000,00/Kg</span>
                            </div>

                            {{-- Item 3: Quick --}}
                            <div class="pricelist-item">
                                <div>
                                    <span class="item-name">Quick</span>
                                    <span class="item-estimate">Estimasi 12 Jam</span>
                                </div>
                                <span class="item-price">Rp12.000,00/Kg</span>
                            </div>

                            {{-- Item 4: Setrika --}}
                            <div class="pricelist-item">
                                <div>
                                    <span class="item-name">Setrika</span>
                                    <span class="item-estimate">Estimasi 3 Hari</span>
                                </div>
                                <span class="item-price">Rp8.000,00/Kg</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>

@push('scripts')
    {{-- JS Interaktif & Animasi Khusus Outlet --}}
    <script src="{{ asset('bs/js/silam-outlet.js') }}"></script>
@endpush

@endsection
