@extends('landing')

@section('title_menu', 'Beranda')

@section('isikonten')

@push('styles')
    <link rel="stylesheet" href="{{ asset('bs/css/silam-hero.css') }}">
    <link rel="stylesheet" href="{{ asset('bs/css/silam-testimoni.css') }}">
    <link rel="stylesheet" href="{{ asset('bs/css/silam-sections.css') }}">
    {{-- CSS Khusus Animasi Interaktif --}}
    <link rel="stylesheet" href="{{ asset('bs/css/silam-beranda.css') }}">
@endpush

{{-- ============ HERO SECTION ============ --}}
<section id="beranda" class="hero-section py-5">
    <div class="container text-center position-relative" style="z-index: 1;">

        <span class="badge rounded-pill bg-white text-secondary border shadow-sm fw-medium px-3 py-2 mb-3">
            Pilih Layananmu Sekarang <i class="bi bi-chevron-right ms-1"></i>
        </span>

        <h1 class="display-5 fw-bold text-body-emphasis mb-5">
            Solusi Layanan Laundry<br>
            Bersama <span class="bg-silam text-white rounded-3 px-2">Silam</span>
        </h1>

        <div class="row align-items-center justify-content-center g-4 text-start">

            {{-- Kiri: Card Status & Pill Live Activity --}}
            <div class="col-md-6 col-lg-3 order-2 order-lg-1">
                <div class="card border-0 shadow rounded-4 mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold text-silam mb-2">Cek Status Cucianmu!</h6>
                        <p class="text-secondary small mb-3">
                            Tidak perlu ribet harus ke outlet, cukup hanya dengan memasukkan kode unik,
                            anda sudah bisa mengecek statusmu.
                        </p>
                        <a href="#" class="link-primary link-underline-opacity-0 small fw-medium"
                           data-bs-toggle="modal" data-bs-target="#trackingModal">
                            Lihat Selengkapnya <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>

                {{-- Pill Notifikasi 1 (Hidup & Otomatis Berganti) --}}
                <div class="d-none d-lg-flex align-items-center gap-2 bg-white border shadow-sm rounded-pill p-2 pe-3 mb-2 hero-notif-pill">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0 notif-icon-circle"
                          style="width: 28px; height: 28px;"><i class="bi bi-check2"></i></span>
                    <span class="flex-grow-1" style="line-height: 1.2;">
                        <span class="d-flex align-items-center gap-1">
                            <span class="notif-live-dot"></span>
                            <span class="fw-bold small text-dark notif-title" style="font-size: 0.75rem;">Pesanan #SL-8201</span>
                        </span>
                        <span class="text-muted d-block notif-desc" style="font-size: 0.68rem;">Selesai disetrika &amp; wangi</span>
                    </span>
                </div>

                {{-- Pill Notifikasi 2 (Hidup & Otomatis Berganti) --}}
                <div class="d-none d-lg-flex align-items-center gap-2 bg-white border shadow-sm rounded-pill p-2 pe-3 ms-4 hero-notif-pill">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-dark text-white flex-shrink-0 notif-icon-circle"
                          style="width: 28px; height: 28px;"><i class="bi bi-bell-fill"></i></span>
                    <span class="flex-grow-1" style="line-height: 1.2;">
                        <span class="d-block fw-bold small text-dark notif-title" style="font-size: 0.75rem;">Notifikasi WhatsApp</span>
                        <span class="text-muted d-block notif-desc" style="font-size: 0.68rem;">Terkirim otomatis ke pemesan</span>
                    </span>
                </div>
            </div>

            {{-- Tengah: Mesin Cuci 3D Mouse Parallax --}}
            <div class="col-10 col-md-8 col-lg-5 order-1 order-lg-2 text-center">
                <img src="{{ asset('picture/mesin_cuci.png') }}" alt="Mesin cuci" class="img-fluid hero-machine-img">
            </div>

            {{-- Kanan: Layanan Ekspres dengan Circle Progress Counter --}}
            <div class="col-md-6 col-lg-3 order-3">
                <div class="card border-0 shadow rounded-4">
                    <div class="card-body">
                        <span class="badge rounded-pill text-silam border border-secondary-subtle bg-white fw-medium w-100 py-2 mb-3">
                            Pelayanan Terbaik
                        </span>
                        <p class="small fw-semibold text-secondary mb-2">Layanan Ekspress</p>

                        <div class="d-flex align-items-center gap-3 mb-2">
                            {{-- Lingkaran progres teranimasi otomatis --}}
                            <div class="position-relative flex-shrink-0" style="width: 64px; height: 64px;">
                                <svg width="64" height="64" viewBox="0 0 64 64" aria-hidden="true">
                                    <circle cx="32" cy="32" r="28" fill="none" stroke="#E2E8F0" stroke-width="6"/>
                                    <circle class="hero-circle-progress" cx="32" cy="32" r="28" fill="none" stroke="#2563EB" stroke-width="6"
                                            stroke-linecap="round" stroke-dasharray="0 175.9"
                                            transform="rotate(-90 32 32)"/>
                                </svg>
                                <span class="position-absolute top-50 start-50 translate-middle fw-bold text-primary small hero-progress-number">0%</span>
                            </div>
                            <p class="small fw-semibold text-body-emphasis mb-0 lh-sm">
                                Cuma Rp12.000/kg, baju bersih &amp; rapi dalam 6 jam! Yuk, drop pakaianmu sekarang!
                            </p>
                        </div>

                        <p class="text-secondary mb-3" style="font-size: .65rem;">70% pelanggan memilih layanan ini</p>
                        <a href="#layanan" class="link-primary link-underline-opacity-0 small fw-medium">
                            Lihat Selengkapnya <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ============ TENTANG KAMI ============ --}}
<section id="tentang" class="about-section-modern">
    <div class="container">

        <div class="text-center mb-5">
            <span class="badge rounded-pill bg-white text-secondary border shadow-sm fw-medium px-4 py-2">
                Tentang Kami
            </span>
        </div>

        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="about-image-wrapper">
                    <div class="about-image-backdrop"></div>
                    <img src="{{ asset('picture/outlet.png') }}" alt="Outlet At_Hura Laundry" class="about-img-main" loading="lazy">

                    <div class="about-floating-badge badge-pos-top">
                        <div class="about-badge-icon"><i class="bi bi-shield-check"></i></div>
                        <div>
                            <span class="d-block fw-bold text-dark small">100% Higienis</span>
                            <small class="text-muted" style="font-size: 0.7rem;">Standar Bersih &amp; Rapi</small>
                        </div>
                    </div>

                    <div class="about-floating-badge badge-pos-bottom">
                        <div class="about-badge-icon"><i class="bi bi-clock-history"></i></div>
                        <div>
                            <span class="d-block fw-bold text-dark small">Tepat Waktu</span>
                            <small class="text-muted" style="font-size: 0.7rem;">Jaminan Selesai Jadwal</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 about-content-col">
                <h2 class="about-title">
                    "Solusi Cuci Mandiri: <br>
                    <span class="text-primary">Cepat, Rapi, dan Terpercaya.</span>"
                </h2>

                <p class="about-desc">
                    <strong class="text-dark">SILAM (Sistem Informasi Laundry Mandiri)</strong> adalah platform manajemen laundry modern yang dirancang untuk memberikan kemudahan, kecepatan, dan kenyamanan penuh bagi pelanggan. Lewat integrasi sistem serba otomatis, Anda bisa dengan mudah memantau status cucian secara real-time, melihat nota digital, hingga menerima notifikasi langsung saat pakaian siap ambil.
                </p>

                <div class="about-feature-grid">
                    <div class="about-feature-item">
                        <i class="bi bi-lightning-charge-fill"></i>
                        <div>
                            <span class="fw-bold d-block text-dark small">Proses Cepat</span>
                            <small class="text-muted" style="font-size: 0.75rem;">Pilihan reguler &amp; ekspres</small>
                        </div>
                    </div>

                    <div class="about-feature-item">
                        <i class="bi bi-phone-fill"></i>
                        <div>
                            <span class="fw-bold d-block text-dark small">Digital Tracking</span>
                            <small class="text-muted" style="font-size: 0.75rem;">Pantau status cucian live</small>
                        </div>
                    </div>

                    <div class="about-feature-item">
                        <i class="bi bi-patch-check-fill"></i>
                        <div>
                            <span class="fw-bold d-block text-dark small">Aman &amp; Rapi</span>
                            <small class="text-muted" style="font-size: 0.75rem;">Pewangi &amp; kemasan rapi</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ============ LAYANAN KAMI ============ --}}
<section id="layanan" class="layanan-section-modern">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge rounded-pill bg-white text-secondary border shadow-sm fw-medium px-4 py-2 mb-3">
                Layanan Kami
            </span>
            <h2 class="display-6 fw-bold text-dark mb-2">Layanan At_Hura Laundry</h2>
            <p class="text-secondary mb-0">Pilihan paket terbaik untuk kebutuhan pakaian bersih Anda</p>
        </div>

        <div class="row g-4 justify-content-center">

            {{-- 1. Cuci Kering Setrika --}}
            <div class="col-md-6 col-lg-4">
                <div class="service-card-modern" role="button" onclick="openOrderModal('Cuci Kering Setrika')">
                    <div>
                        <div class="service-icon-box">
                            <svg width="34" height="34" viewBox="0 0 48 48" fill="currentColor">
                                <path d="M24 6C22.34 6 21 7.34 21 9C21 10.33 21.86 11.45 23.07 11.83L14 17.5V14H10V20.06L5.3 23.05C4.48 23.57 4 24.47 4 25.44C4 27.23 5.67 28.53 7.42 28.14L10 27.57V40C10 41.1 10.9 42 12 42H36C37.1 42 38 41.1 38 40V27.57L40.58 28.14C42.33 28.53 44 27.23 44 25.44C44 24.47 43.52 23.57 42.7 23.05L38 20.06V14H34V17.5L24.93 11.83C26.14 11.45 27 10.33 27 9C27 7.34 25.66 6 24 6ZM24 9C24.55 9 25 9.45 25 10C25 10.55 24.55 11 24 11C23.45 11 23 10.55 23 10C23 9.45 23.45 9 24 9ZM24 14.5L30 18.25L24 22L18 18.25L24 14.5Z"/>
                            </svg>
                        </div>
                        <h4 class="service-title">Cuci Kering Setrika</h4>
                        <p class="service-desc">
                            Paket komplit pakaian dicuci bersih higienis, dikeringkan optimal, lalu disetrika rapi dan wangi siap pakai.
                        </p>
                        <ul class="service-perks">
                            <li><i class="bi bi-check-circle-fill"></i> Pewangi &amp; Pelembut Premium</li>
                            <li><i class="bi bi-check-circle-fill"></i> Setrika Uap Rapi Bebas Kusut</li>
                            <li><i class="bi bi-check-circle-fill"></i> Packing Rapi Siap Lemari</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- 2. Setrika Saja --}}
            <div class="col-md-6 col-lg-4">
                <div class="service-card-modern" role="button" onclick="openOrderModal('Setrika Saja')">
                    <div>
                        <div class="service-icon-box">
                            <svg width="34" height="34" viewBox="0 0 48 48" fill="currentColor">
                                <path d="M42 28V36C42 37.1 41.1 38 40 38H8C6.9 38 6 37.1 6 36C6 26.5 13.5 19 23 19H34V13C34 11.9 34.9 11 36 11C37.1 11 38 11.9 38 13V24C38 26.2 39.8 28 42 28ZM23 23C15.7 23 9.8 28.9 9.8 36H34V23H23ZM24 8C23.45 8 23 8.45 23 9V12C23 12.55 23.45 13 24 13C24.55 13 25 12.55 25 12V9C25 8.45 24.55 8 24 8ZM29 8C28.45 8 28 8.45 28 9V12C28 12.55 28.45 13 29 13C29.55 13 30 12.55 30 12V9C30 8.45 29.55 8 29 8Z"/>
                            </svg>
                        </div>
                        <h4 class="service-title">Setrika Saja</h4>
                        <p class="service-desc">
                            Solusi tepat bagi Anda yang sudah mencuci sendiri di rumah, serahkan urusan menyetrika pakaian pada kami.
                        </p>
                        <ul class="service-perks">
                            <li><i class="bi bi-check-circle-fill"></i> Menyetrika Presisi &amp; Licin</li>
                            <li><i class="bi bi-check-circle-fill"></i> Semprotan Pewangi Tahan Lama</li>
                            <li><i class="bi bi-check-circle-fill"></i> Pengerjaan Cepat &amp; Hati-hati</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- 3. Cuci Kering Lipat --}}
            <div class="col-md-6 col-lg-4">
                <div class="service-card-modern" role="button" onclick="openOrderModal('Cuci Kering Lipat')">
                    <div>
                        <div class="service-icon-box">
                            <svg width="34" height="34" viewBox="0 0 48 48" fill="currentColor">
                                <path d="M38 18H28V12C28 10.9 27.1 10 26 10H14C12.9 10 12 10.9 12 12V18H8C6.9 18 6 18.9 6 20V36C6 37.1 6.9 38 8 38H40C41.1 38 42 37.1 42 36V22C42 19.8 40.2 18 38 18ZM16 14H24V18H16V14ZM38 34H10V22H38V34ZM14 26H24V28H14V26ZM14 30H20V32H14V30Z"/>
                            </svg>
                        </div>
                        <h4 class="service-title">Cuci Kering Lipat</h4>
                        <p class="service-desc">
                            Pakaian dicuci bersih maksimal, dikeringkan hingga tuntas, lalu dilipat teratur rapi dengan harga ekonomis.
                        </p>
                        <ul class="service-perks">
                            <li><i class="bi bi-check-circle-fill"></i> Cuci Bersih &amp; Anti Bakteri</li>
                            <li><i class="bi bi-check-circle-fill"></i> Pengering Mesin 100% Kering</li>
                            <li><i class="bi bi-check-circle-fill"></i> Lipatan Rapi &amp; Hemat Biaya</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ============ KEUTAMAAN KAMI ============ --}}
<section id="keutamaan" class="keutamaan-section-modern">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge rounded-pill bg-white text-secondary border shadow-sm fw-medium px-4 py-2 mb-3">
                Keutamaan Kami
            </span>
            <h2 class="display-6 fw-bold text-dark mb-2">Mengapa Memilih SILAM?</h2>
            <p class="text-secondary mb-0">Komitmen kami memberikan standar layanan laundry terbaik untuk Anda</p>
        </div>

        <div class="row g-4 justify-content-center">

            <div class="col-md-6 col-lg-4">
                <div class="keutamaan-card-modern">
                    <span class="keutamaan-watermark-num">01</span>
                    <div class="keutamaan-icon icon-theme-blue">
                        <i class="bi bi-phone"></i>
                    </div>
                    <h5 class="keutamaan-title">Tracking Digital Real-Time Lewat SILAM</h5>
                    <p class="keutamaan-desc">
                        Pantau setiap tahapan cucian Anda mulai dari penerimaan, pencucian, penyetrikaan, hingga siap diambil langsung dari HP secara transparan.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="keutamaan-card-modern">
                    <span class="keutamaan-watermark-num">02</span>
                    <div class="keutamaan-icon icon-theme-amber">
                        <i class="bi bi-stopwatch"></i>
                    </div>
                    <h5 class="keutamaan-title">Jaminan Garansi Ketepatan Waktu</h5>
                    <p class="keutamaan-desc">
                        Kami sangat menghargai waktu Anda. Setiap pesanan memiliki estimasi jadwal otomatis. Jika ada keterlambatan, kami siap berikan kompensasi.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="keutamaan-card-modern">
                    <span class="keutamaan-watermark-num">03</span>
                    <div class="keutamaan-icon icon-theme-emerald">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <h5 class="keutamaan-title">Penimbangan &amp; Nota Digital Transparan</h5>
                    <p class="keutamaan-desc">
                        Bebas biaya tersembunyi. Detail berat cucian, tarif layanan, dan total biaya tercatat otomatis ke dalam e-receipt yang dapat diunduh kapan saja.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="keutamaan-card-modern">
                    <span class="keutamaan-watermark-num">04</span>
                    <div class="keutamaan-icon icon-theme-teal">
                        <i class="bi bi-whatsapp"></i>
                    </div>
                    <h5 class="keutamaan-title">Notifikasi Otomatis Langsung ke WhatsApp</h5>
                    <p class="keutamaan-desc">
                        Saat cucian Anda selesai disetrika dan dikemas siap ambil, sistem SILAM langsung mengirim pesan WhatsApp otomatis ke nomor Anda tanpa jeda.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="keutamaan-card-modern">
                    <span class="keutamaan-watermark-num">05</span>
                    <div class="keutamaan-icon icon-theme-purple">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <h5 class="keutamaan-title">Metode Pembayaran Fleksibel &amp; Lengkap</h5>
                    <p class="keutamaan-desc">
                        Transaksi mudah dan fleksibel. Kami menerima pembayaran uang tunai (Cash), Transfer Bank, hingga scan QRIS (GoPay, OVO, ShopeePay, Dana).
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ============ TESTIMONI PELANGGAN ============ --}}
<section id="testimoni" class="testimoni-wrapper">
    <div class="container testimoni-inner">

        <div class="text-center">
            <span class="testi-pill-badge">Testimoni Pelanggan</span>
            <h2 class="testi-heading">
                <span class="testi-glow-text">Dipercaya</span> Oleh Ribuan Orang
            </h2>
            <p class="testi-subtext">"Solusi Cuci Mandiri: Cepat, Rapi, dan Terpercaya."</p>
        </div>

        <div class="testi-cards-container">
            <div class="testi-row-cards testi-row-top">
                <div class="testi-card-box card-tilt-1">
                    <svg class="testi-quote-svg" viewBox="0 0 24 20">
                        <path d="M0 12.2C0 5.8 4.2 1.2 9.8 0.2L10.5 3C6.8 3.7 4.8 5.9 4.4 9H9.6V19.5H0V12.2ZM13.4 12.2C13.4 5.8 17.6 1.2 23.2 0.2L23.9 3C20.2 3.7 18.2 5.9 17.8 9H23V19.5H13.4V12.2Z"/>
                    </svg>
                    <p class="testi-comment-text">
                        Cepat, Wangi,<br>Mahasiswa<br>Friendly, Combo<br>Banget!
                    </p>
                    <div class="testi-card-footer">
                        <span class="testi-rate-label">Memberikan</span>
                        <div class="testi-stars-group">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>

                <div class="testi-card-box card-tilt-2">
                    <svg class="testi-quote-svg" viewBox="0 0 24 20">
                        <path d="M0 12.2C0 5.8 4.2 1.2 9.8 0.2L10.5 3C6.8 3.7 4.8 5.9 4.4 9H9.6V19.5H0V12.2ZM13.4 12.2C13.4 5.8 17.6 1.2 23.2 0.2L23.9 3C20.2 3.7 18.2 5.9 17.8 9H23V19.5H13.4V12.2Z"/>
                    </svg>
                    <p class="testi-comment-text">
                        Pengerjaannya Tepat<br>Waktu Gaakan<br>Kehabisan Baju,<br>Mantap
                    </p>
                    <div class="testi-card-footer">
                        <span class="testi-rate-label">Memberikan</span>
                        <div class="testi-stars-group">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>

                <div class="testi-card-box card-tilt-3">
                    <svg class="testi-quote-svg" viewBox="0 0 24 20">
                        <path d="M0 12.2C0 5.8 4.2 1.2 9.8 0.2L10.5 3C6.8 3.7 4.8 5.9 4.4 9H9.6V19.5H0V12.2ZM13.4 12.2C13.4 5.8 17.6 1.2 23.2 0.2L23.9 3C20.2 3.7 18.2 5.9 17.8 9H23V19.5H13.4V12.2Z"/>
                    </svg>
                    <p class="testi-comment-text">
                        Tempat Laundry<br>Langganan Sih!
                    </p>
                    <div class="testi-card-footer">
                        <span class="testi-rate-label">Memberikan</span>
                        <div class="testi-stars-group">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="testi-row-cards testi-row-bottom">
                <div class="testi-card-box card-tilt-4">
                    <svg class="testi-quote-svg" viewBox="0 0 24 20">
                        <path d="M0 12.2C0 5.8 4.2 1.2 9.8 0.2L10.5 3C6.8 3.7 4.8 5.9 4.4 9H9.6V19.5H0V12.2ZM13.4 12.2C13.4 5.8 17.6 1.2 23.2 0.2L23.9 3C20.2 3.7 18.2 5.9 17.8 9H23V19.5H13.4V12.2Z"/>
                    </svg>
                    <p class="testi-comment-text">
                        Rekomen Banget<br>Buat Anak Kos
                    </p>
                    <div class="testi-card-footer">
                        <span class="testi-rate-label">Memberikan</span>
                        <div class="testi-stars-group">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>

                <div class="testi-card-box card-tilt-5">
                    <svg class="testi-quote-svg" viewBox="0 0 24 20">
                        <path d="M0 12.2C0 5.8 4.2 1.2 9.8 0.2L10.5 3C6.8 3.7 4.8 5.9 4.4 9H9.6V19.5H0V12.2ZM13.4 12.2C13.4 5.8 17.6 1.2 23.2 0.2L23.9 3C20.2 3.7 18.2 5.9 17.8 9H23V19.5H13.4V12.2Z"/>
                    </svg>
                    <p class="testi-comment-text">
                        Servicesnya<br>Baguss, Approve
                    </p>
                    <div class="testi-card-footer">
                        <span class="testi-rate-label">Memberikan</span>
                        <div class="testi-stars-group">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BANNER CTA --}}
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card cta-banner-card">
                    <div class="row align-items-center g-4">
                        <div class="col-md-7 col-lg-8">
                            <h4 class="cta-banner-title">
                                Jangan ragu untuk menghubungi kami, 100% layanan kami terpercaya. Maksimalkan waktu anda, dan pilih layanan kami.
                            </h4>
                            <a href="#" class="btn-cek-status" data-bs-toggle="modal" data-bs-target="#trackingModal">
                                <span>Cek Status Pesananmu</span>
                                <span class="btn-arrow-circle"><i class="bi bi-chevron-right"></i></span>
                            </a>
                        </div>
                        <div class="col-md-5 col-lg-4 text-center">
                            <img src="{{ asset('picture/mesin_cuci.png') }}" alt="Mesin Cuci" class="banner-img-washing img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- Script JS Terpisah --}}
@push('scripts')
    <script src="{{ asset('bs/js/silam-testimoni.js') }}"></script>
    <script src="{{ asset('bs/js/silam-sections.js') }}"></script>
    {{-- JS Tambahan Animasi Interaktif --}}
    <script src="{{ asset('bs/js/silam-beranda.js') }}"></script>
@endpush

@endsection
