<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SILAM - Sistem Laundry Mandiri</title>

    <link rel="preconnect" href="https://api.fontshare.com" crossorigin>
    <link rel="stylesheet" href="https://api.fontshare.com/v2/css?f[]=general-sans@400,500,600,700&display=swap">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('bs/css/silam-layout.css') }}">
    @stack('styles')
</head>
<body>

    <header class="sticky-top px-3 pt-3">
        <nav class="navbar navbar-expand-lg navbar-silam bg-white bg-opacity-75 border rounded-5 shadow-sm mx-auto px-3 py-2"
             style="max-width: 1120px; backdrop-filter: blur(10px);">

            <a class="navbar-brand d-flex align-items-center gap-2 m-0" href="#beranda">
                <img src="{{ asset('picture/silam_kecil.png') }}" alt="Logo SILAM" width="40" height="40">
                <span class="d-flex flex-column lh-1">
                    <span class="fw-bold fs-6 footer-navy">SILAM</span>
                    <small class="text-secondary" style="font-size: .6rem;">Sistem Laundry Mandiri</small>
                </span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarContent"
                    aria-controls="navbarContent" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto align-items-lg-center small">
                    <li class="nav-item">
                        <a class="nav-link active" href="/beranda" aria-current="page">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#tentang" role="button" data-bs-toggle="dropdown" aria-expanded="false">Tentang</a>
                        <ul class="dropdown-menu border-0 shadow rounded-4 p-2 mt-3">
                            <li><a class="dropdown-item rounded-3 small" href="#tentang">Tentang Kami</a></li>
                            <li><a class="dropdown-item rounded-3 small" href="#keutamaan">Layanan</a></li>
                            <li><a class="dropdown-item rounded-3 small" href="#keutamaan">Keutamaan</a></li>
                            <li><a class="dropdown-item rounded-3 small" href="#testimoni">Testimoni</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#outletModal">Outlet</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#trackingModal">Cek Status Cucianmu</a>
                    </li>
                </ul>

                <button type="button" class="btn btn-silam btn-sm rounded-pill px-4 fw-semibold"
                        data-bs-toggle="modal" data-bs-target="#loginModal">
                    Login
                </button>
            </div>
        </nav>
    </header>

    <main class="container-fluid px-4 pt-4">
        @yield('isikonten')
    </main>

    <footer class="site-footer mt-5 pt-5">
        <div class="container">
            <div class="row g-4 justify-content-between">

                <div class="col-lg-4 col-md-6">
                    <a class="d-flex align-items-center gap-2 text-decoration-none" href="#beranda">
                        <img src="{{ asset('picture/silam_kecil.png') }}" alt="Logo SILAM" width="40" height="40">
                        <span class="d-flex flex-column lh-1">
                            <span class="fw-bold footer-navy">SILAM</span>
                            <small class="text-secondary" style="font-size: .6rem;">Sistem Laundry Mandiri</small>
                        </span>
                    </a>
                    <p class="text-secondary small mt-3 mb-0" style="max-width: 300px;">
                        Platform sistem manajemen laundry modern yang dirancang untuk memberikan
                        kemudahan, kecepatan, penuh bagi pelanggan.
                    </p>
                </div>

                <div class="col-lg-3 col-6">
                    <h6 class="fw-bold footer-navy mb-3">Menu</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><a class="link-secondary link-underline-opacity-0" href="/Beranda">Beranda</a></li>
                        <li class="mb-2"><a class="link-secondary link-underline-opacity-0" href="#tentang">Tentang</a></li>
                        <li class="mb-2"><a class="link-secondary link-underline-opacity-0" href="#layanan">Layanan</a></li>
                        <li class="mb-2"><a class="link-secondary link-underline-opacity-0" href="#keutamaan">Keutamaan</a></li>
                        <li class="mb-2"><a class="link-secondary link-underline-opacity-0" href="#testimoni">Testimoni</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-6">
                    <h6 class="fw-bold footer-navy mb-3">Kontak Kami</h6>
                    <ul class="list-unstyled small text-secondary mb-0">
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-telephone-fill footer-navy"></i><span>+62 8752 7272 272</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-whatsapp footer-navy"></i><span>+62 8211 9793 095</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-top text-center small py-3 mt-5">
                Copyright &copy; SILAM Laundry {{ date('Y') }} | Managed By: MESH Team
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
