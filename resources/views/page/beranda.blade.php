@extends('landing')

@section('title_menu', 'Beranda')

@section('isikonten')

@push('styles')
    <link rel="stylesheet" href="{{ asset('bs/css/silam-hero.css') }}">
@endpush

<section id="beranda" class="hero-section py-5">
    <div class="container text-center">

        <span class="badge rounded-pill bg-white text-secondary border shadow-sm fw-medium px-3 py-2 mb-3">
            Pilih Layananmu Sekarang <i class="bi bi-chevron-right ms-1"></i>
        </span>

        {{-- Judul --}}
        <h1 class="display-5 fw-bold text-body-emphasis mb-5">
            Solusi Layanan Laundry<br>
            Bersama <span class="bg-silam text-white rounded-3 px-2">Silam</span>
        </h1>

        <div class="row align-items-center justify-content-center g-4 text-start">

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

                <div class="d-none d-lg-flex align-items-center gap-2 bg-white border shadow-sm rounded-pill p-2 pe-4 mb-2 w-75">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0"
                          style="width: 28px; height: 28px;"><i class="bi bi-check2"></i></span>
                    <span class="flex-grow-1">
                        <span class="placeholder placeholder-xs col-12 rounded-pill mb-1"></span>
                        <span class="placeholder placeholder-xs col-7 rounded-pill"></span>
                    </span>
                </div>
                <div class="d-none d-lg-flex align-items-center gap-2 bg-white border shadow-sm rounded-pill p-2 pe-4 ms-4 w-75">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-dark text-white flex-shrink-0"
                          style="width: 28px; height: 28px;"><i class="bi bi-bell-fill"></i></span>
                    <span class="flex-grow-1">
                        <span class="placeholder placeholder-xs col-12 rounded-pill mb-1"></span>
                        <span class="placeholder placeholder-xs col-5 rounded-pill"></span>
                    </span>
                </div>
            </div>

            {{-- ============ TENGAH: mesin cuci ============ --}}
            <div class="col-10 col-md-8 col-lg-5 order-1 order-lg-2">
                <img src="{{ asset('picture/mesin_cuci.png') }}" alt="Mesin cuci" class="img-fluid">

            </div>

            {{-- ============ KANAN: kartu layanan ekspres ============ --}}
            <div class="col-md-6 col-lg-3 order-3">
                <div class="card border-0 shadow rounded-4">
                    <div class="card-body">
                        <span class="badge rounded-pill text-silam border border-secondary-subtle bg-white fw-medium w-100 py-2 mb-3">
                            Pelayanan Terbaik
                        </span>
                        <p class="small fw-semibold text-secondary mb-2">Layanan Ekspress</p>

                        <div class="d-flex align-items-center gap-3 mb-2">
                            {{-- Lingkaran progres 70% --}}
                            <div class="position-relative flex-shrink-0" style="width: 64px; height: 64px;">
                                <svg width="64" height="64" viewBox="0 0 64 64" aria-hidden="true">
                                    <circle cx="32" cy="32" r="28" fill="none" stroke="#E2E8F0" stroke-width="6"/>
                                    <circle cx="32" cy="32" r="28" fill="none" stroke="#2563EB" stroke-width="6"
                                            stroke-linecap="round" stroke-dasharray="123.1 175.9"
                                            transform="rotate(-90 32 32)"/>
                                </svg>
                                <span class="position-absolute top-50 start-50 translate-middle fw-bold text-primary small">70%</span>
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

<section id="tentang" class="about-section">
    <div class="container">

        <div class="text-center mb-5">
            <span class="badge rounded-pill bg-white text-secondary border shadow-sm fw-medium px-3 py-2">Tentang Kami</span>
        </div>

        <div class="row align-items-center g-5">
            <div class="col-md-5 col-lg-4">
                <div class="ratio ratio-4x4 mx-auto" style="max-width: 280px;">
                    <img src="{{ asset('picture/outlet.png') }}" alt="Outlet At_Hura Laundry"
                         class="rounded-4 shadow object-fit-cover" loading="lazy">
                </div>
            </div>

            <div class="col-lg-7">
                    <h2 class="display-6 fw-bold text-silam mb-3">
                        "Solusi Cuci Mandiri: Cepat, Rapi dan Terpercaya."
                    </h2>
                    <p class="text-primary mb-0">
                        <strong>SILAM (Sistem Informasi Laundry Mandiri)</strong>
                        adalah platform sistem manajemen laundry modern yang dirancang
                        untuk memberikan kemudahan, kecepatan dan kenyamanan penuh bagi pelanggan.
                        Lewat integrasi sistem yang serba otomatis, Anda bisa dengan mudah memantau
                        status cucian secara real-time, melihat nota, hingga menerima notifikasi langsung
                        saat pakaian siap ambil. SILAM menghadirkan standar baru dalam menikmati layanan cuci
                        pakaian yang rapi, bersih, dan terpercaya.
                    </p>
            </div>
        </div>
    </div>
</section>

<section id="layanan" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge rounded-pill bg-white text-secondary border shadow-sm fw-medium px-3 py-2 mb-4">Layanan Kami</span>
            <h2 class="display-6 fw-bold text-silam mb-1">Layanan At_Hura Laundry</h2>
            <p class="text-secondary mb-0">Yuk Laundry sekarang juga!</p>
        </div>

        <div class="row g-4 justify-content-center">
            {{-- Cuci Kering Setrika --}}
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow rounded-4 text-center" role="button"
                     onclick="openOrderModal('Cuci Kering Setrika')">
                    <div class="card-body py-4">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="currentColor" class="text-primary mb-2" aria-hidden="true">
                            <path d="M24 6C22.34 6 21 7.34 21 9C21 10.33 21.86 11.45 23.07 11.83L14 17.5V14H10V20.06L5.3 23.05C4.48 23.57 4 24.47 4 25.44C4 27.23 5.67 28.53 7.42 28.14L10 27.57V40C10 41.1 10.9 42 12 42H36C37.1 42 38 41.1 38 40V27.57L40.58 28.14C42.33 28.53 44 27.23 44 25.44C44 24.47 43.52 23.57 42.7 23.05L38 20.06V14H34V17.5L24.93 11.83C26.14 11.45 27 10.33 27 9C27 7.34 25.66 6 24 6ZM24 9C24.55 9 25 9.45 25 10C25 10.55 24.55 11 24 11C23.45 11 23 10.55 23 10C23 9.45 23.45 9 24 9ZM24 14.5L30 18.25L24 22L18 18.25L24 14.5Z"/>
                        </svg>
                        <h5 class="fw-bold text-silam mb-0">Cuci Kering Setrika</h5>
                    </div>
                </div>
            </div>

            {{-- Setrika --}}
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow rounded-4 text-center" role="button"
                     onclick="openOrderModal('Setrika Saja')">
                    <div class="card-body py-4">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="currentColor" class="text-primary mb-2" aria-hidden="true">
                            <path d="M42 28V36C42 37.1 41.1 38 40 38H8C6.9 38 6 37.1 6 36C6 26.5 13.5 19 23 19H34V13C34 11.9 34.9 11 36 11C37.1 11 38 11.9 38 13V24C38 26.2 39.8 28 42 28ZM23 23C15.7 23 9.8 28.9 9.8 36H34V23H23ZM24 8C23.45 8 23 8.45 23 9V12C23 12.55 23.45 13 24 13C24.55 13 25 12.55 25 12V9C25 8.45 24.55 8 24 8ZM29 8C28.45 8 28 8.45 28 9V12C28 12.55 28.45 13 29 13C29.55 13 30 12.55 30 12V9C30 8.45 29.55 8 29 8Z"/>
                        </svg>
                        <h5 class="fw-bold text-silam mb-0">Setrika</h5>
                    </div>
                </div>
            </div>

            {{-- Cuci Kering Lipat --}}
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow rounded-4 text-center" role="button"
                     onclick="openOrderModal('Cuci Kering Lipat')">
                    <div class="card-body py-4">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="currentColor" class="text-primary mb-2" aria-hidden="true">
                            <path d="M38 18H28V12C28 10.9 27.1 10 26 10H14C12.9 10 12 10.9 12 12V18H8C6.9 18 6 18.9 6 20V36C6 37.1 6.9 38 8 38H40C41.1 38 42 37.1 42 36V22C42 19.8 40.2 18 38 18ZM16 14H24V18H16V14ZM38 34H10V22H38V34ZM14 26H24V28H14V26ZM14 30H20V32H14V30Z"/>
                        </svg>
                        <h5 class="fw-bold text-silam mb-0">Cuci Kering Lipat</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="keutamaan" class="py-5 bg-primary-subtle">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge rounded-pill bg-white text-secondary border shadow-sm fw-medium px-3 py-2">Keutamaan Kami</span>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow rounded-4 text-center">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-body-emphasis mb-3">Tracking Digital Real-Time Lewat SILAM</h5>
                        <p class="small text-body mb-0">
                            Melalui sistem SILAM (Sistem Layanan Laundry Mandiri), Anda dapat memantau setiap
                            tahapan proses cucian mulai dari penerimaan, pencucian, penyetrikaan, hingga siap
                            diambil secara transparan langsung dari genggaman secara realtime.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow rounded-4 text-center">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-body-emphasis mb-3">Jaminan Ketepatan Waktu (Garansi Tepat Waktu)</h5>
                        <p class="small text-body mb-0">
                            Kami menghargai waktu Anda. Setiap pesanan diproses dengan estimasi jadwal yang
                            terukur presisi di dalam sistem. Jika ada keterlambatan, kami siap memberikan
                            kompensasi layanan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow rounded-4 text-center">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-body-emphasis mb-3">Sistem Penimbangan &amp; Nota Digital Transparan</h5>
                        <p class="small text-body mb-0">
                            Tidak ada biaya tersembunyi. Semua rincian berat, jenis layanan, dan total harga
                            tercatat secara sistematis dan otomatis dalam nota digital yang dapat Anda akses
                            kapan saja.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow rounded-4 text-center">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-body-emphasis mb-3">Notifikasi Otomatis Langsung ke WhatsApp</h5>
                        <p class="small text-body mb-0">
                            Begitu pakaian Anda selesai diproses dan siap diambil, sistem kami akan langsung
                            mengirimkan notifikasi resmi secara otomatis. Praktis, tepat waktu, dan tanpa jeda.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow rounded-4 text-center">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-body-emphasis mb-3">Pembayaran Mudah</h5>
                        <p class="small text-body mb-0">
                            Kami menyediakan beberapa metode pembayaran yang memudahkan untuk customer. Mulai
                            dari uang tunai (cash), transfer bank, hingga uang elektronik seperti QRIS.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
