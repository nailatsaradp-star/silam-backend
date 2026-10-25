<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | SILAM</title>

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- CSS Dashboard Khusus -->
    <link rel="stylesheet" href="{{ asset('bs/css/silam-dashboard-admin.css') }}">
    @stack('styles')
</head>
<body class="dashboard-body">

<div class="dashboard-wrapper">

    <!-- Backdrop untuk Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- ================= SIDEBAR ================= -->
    <aside class="silam-sidebar" id="silamSidebar">
        <div>
            <!-- Logo & Brand Header -->
            <div class="d-flex align-items-center justify-content-between mb-2">
                <a href="{{ route('dashboard') }}" class="sidebar-brand-box p-0">
                    <div class="brand-icon-circle">
                        <i class="bi bi-droplet-fill"></i>
                    </div>
                    <div>
                        <span class="brand-title">SILAM</span>
                        <span class="brand-subtitle">Laundry Mandiri</span>
                    </div>
                </a>
                <!-- Tombol Close di Mobile -->
                <button type="button" class="btn btn-sm btn-light rounded-circle d-lg-none" id="btnCloseSidebar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <hr style="border-color: rgba(207, 220, 248, 0.6); margin: 1.5rem 0 1.25rem;">

            <!-- Daftar Menu Navigasi Sesuai 3 Hak Akses -->
            @php $role = auth()->user()->role; @endphp
            <ul class="sidebar-menu-list">
                {{-- DASHBOARD: BISA DIAKSES SEMUA ROLE (ADMIN, KASIR, PEGAWAI) --}}
                <li>
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                {{-- TERMINAL KASIR: KHUSUS ADMINISTRATOR & KASIR --}}
                @if(in_array($role, ['administrator', 'kasir']))
                <li>
                    <a href="{{ route('kasir.index') }}" class="sidebar-link {{ request()->routeIs('kasir.*') || request()->routeIs('pesanan.create') ? 'active' : '' }}">
                        <i class="bi bi-cart-plus-fill"></i>
                        <span>Terminal Kasir</span>
                    </a>
                </li>
                @endif

                {{-- DAFTAR PESANAN: BISA DIAKSES SEMUA ROLE (ADMIN, KASIR, PEGAWAI) --}}
                <li>
                    <a href="{{ route('pesanan.index') }}" class="sidebar-link {{ request()->routeIs('pesanan.index') || request()->routeIs('pesanan.show') ? 'active' : '' }}">
                        <i class="bi bi-bag-check-fill"></i>
                        <span>Daftar Pesanan</span>
                    </a>
                </li>

                {{-- PELANGGAN: KHUSUS ADMINISTRATOR & KASIR --}}
                @if(in_array($role, ['administrator', 'kasir']))
                <li>
                    <a href="{{ route('pelanggan.index') }}" class="sidebar-link {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Pelanggan</span>
                    </a>
                </li>
                @endif

                {{-- KATALOG LAYANAN READ-ONLY: KHUSUS KASIR --}}
                @if($role === 'kasir')
                <li>
                    <a href="{{ route('kasir.layanan.index') }}" class="sidebar-link {{ request()->routeIs('kasir.layanan.*') ? 'active' : '' }}">
                        <i class="bi bi-tags-fill"></i>
                        <span>Katalog Layanan</span>
                    </a>
                </li>
                @endif

                {{-- MASTER DATA & USER: KHUSUS ADMINISTRATOR --}}
                @if($role === 'administrator')
                <li>
                    <a href="{{ route('admin.layanan.index') }}" class="sidebar-link {{ request()->routeIs('admin.layanan.*') ? 'active' : '' }}">
                        <i class="bi bi-tags-fill"></i>
                        <span>Kelola Layanan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.user.index') }}" class="sidebar-link {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                        <i class="bi bi-person-gear"></i>
                        <span>Manajemen User</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <!-- Widget Bawah Sidebar -->
        <div class="sidebar-bottom-widget mt-4">
            <div class="widget-icon-pill">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="widget-title">Sistem SILAM</div>
            <p class="widget-desc">Akses resmi sebagai <strong>{{ ucfirst($role) }}</strong> aktif.</p>

            <!-- Tombol Keluar (Membuka Modal Konfirmasi) -->
            <button type="button" class="btn btn-sm btn-outline-danger w-100 rounded-pill fw-semibold py-1"
                    data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right me-1"></i> Keluar
            </button>
        </div>
    </aside>

    <!-- ================= AREA KONTEN UTAMA ================= -->
    <div class="dashboard-main-area">

        <!-- Header Khusus Mobile (Hamburger Menu) -->
        <div class="mobile-header-bar d-none align-items-center justify-content-between mb-3 bg-white p-3 rounded-4 shadow-sm border border-light">
            <button class="btn btn-primary rounded-3" id="btnToggleSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="fw-bold text-dark"><i class="bi bi-droplet-fill text-primary"></i> SILAM</span>
            <span class="badge-role-soft">{{ $role }}</span>
        </div>

        <header class="dashboard-topbar d-print-none">
            <div>
                <span class="topbar-greeting">Selamat Datang Kembali, {{ auth()->user()->nama_user ?? auth()->user()->name }}</span>
                <h1 class="topbar-page-title">@yield('title', 'Dashboard')</h1>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="user-profile-pill">
                    <div class="user-avatar-circle">
                        {{ strtoupper(substr(auth()->user()->nama_user ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <span class="badge-role-soft">{{ $role }}</span>
                    </div>
                </div>

                <!-- Tombol Keluar Topbar -->
                <button type="button" class="btn-logout-soft d-none d-sm-inline-flex"
                        data-bs-toggle="modal" data-bs-target="#logoutModal" title="Keluar">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar</span>
                </button>
            </div>
        </header>

        <!-- Flash Messages (Notifikasi Sukses / Error) -->
        @if(session('success'))
            <div class="alert alert-success alert-modern alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-modern alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-modern alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-x-circle-fill me-2"></i> Terjadi kesalahan:
                <ul class="mb-0 mt-2 ps-3">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Halaman Konten Dinamis -->
        <main class="flex-grow-1">
            @yield('content')
        </main>

    </div>

</div>

<!-- ================= POP UP DIALOG KONFIRMASI KELUAR ================= -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-logout-dialog">
        <div class="modal-content modal-logout-content">

            <!-- Ikon Pintu / Logout -->
            <div class="modal-logout-icon-box">
                <i class="bi bi-box-arrow-right"></i>
            </div>

            <!-- Teks Judul & Keterangan -->
            <h4 class="modal-logout-title" id="logoutModalLabel">Yakin Ingin Keluar?</h4>
            <p class="modal-logout-desc">
                Sesi kerja Anda saat ini akan diakhiri. Pastikan semua transaksi atau perubahan data telah tersimpan sebelum keluar.
            </p>

            <!-- Tombol Aksi -->
            <div class="modal-logout-actions">
                <button type="button" class="btn btn-batal-modal" data-bs-dismiss="modal">
                    Tetap di Sini
                </button>
                <form method="POST" action="{{ route('logout') }}" class="m-0 flex-grow-1">
                    @csrf
                    <button type="submit" class="btn btn-keluar-modal w-100">
                        Ya, Keluar
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script Dashboard Terpisah -->
<script src="{{ asset('bs/js/silam-dashboard-admin.js') }}"></script>
@stack('scripts')

</body>
</html>
