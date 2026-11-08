<?php

use App\Http\Controllers\AuthController;
use App\Models\PesananModel;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController; // Tambahkan jika ada controller laporan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Redirect Awal & Halaman Publik
Route::redirect('/', '/login');

Route::get('/silam-app', fn () => view('page.beranda'));
Route::get('/beranda', fn () => view('page.beranda'));
Route::get('/outlet', fn () => view('page.outlet'));

// Status tracking pelanggan
Route::get('/status', function (Request $request) {
    $q = trim($request->input('q', ''));
    $dicari = $q !== '';
    $order = null;
    $daftar = collect();
    $info = null;

    if ($dicari) {
        $dengan = [
            'pelanggan',
            'detail.layanan',
            'riwayat' => fn ($r) => $r->orderByDesc('waktu_update'),
        ];

        $order = PesananModel::with($dengan)->where('nomor_pesanan', strtoupper($q))->first();
        $daftar = $order ? collect([$order]) : collect();

        if (! $order && preg_match('/^[0-9+\-\s]{8,20}$/', $q)) {
            $no = preg_replace('/[^0-9]/', '', $q);
            if (str_starts_with($no, '62')) {
                $no = '0' . substr($no, 2);
            }

            $daftar = PesananModel::with($dengan)
                ->whereHas('pelanggan', fn ($p) => $p->where('nomor_telepon', $no))
                ->orderByDesc('id_pesanan')
                ->limit(5)
                ->get();

            $order = $daftar->first();
        }

        if ($order) {
            $bagian = preg_split('/\s+/', trim($order->pelanggan->nama_pelanggan ?? '-'));
            $depan = array_shift($bagian);
            $inisial = collect($bagian)->map(fn ($b) => mb_substr($b, 0, 1) . '.')->implode(' ');

            $berat = $order->detail
                ->groupBy(fn ($d) => $d->layanan->satuan ?? '')
                ->map(fn ($g, $satuan) => rtrim(rtrim(number_format($g->sum('jumlah_berat'), 2, ',', '.'), '0'), ',') . ' ' . $satuan)
                ->implode(' + ');

            $info = [
                'tahap' => match ($order->status_kerja) {
                    'antrian'    => 1,
                    'proses'     => 2,
                    'siap_ambil' => 3,
                    'selesai'    => 4,
                    default      => 1,
                },
                'nama'  => trim($depan . ' ' . $inisial),
                'paket' => $order->detail->map(fn ($d) => $d->layanan->nama_layanan ?? '-')->filter()->unique()->implode(', ') ?: '-',
                'berat' => $berat ?: '-',
            ];
        }
    }

        return view('page.status', compact('dicari', 'order', 'daftar', 'info'));
    })->name('status');

    // Login & Auth Guest
    Route::middleware('guest')->group(function () {
        Route::get('/login', fn () => redirect('/beranda')->with('buka_login', true))->name('login');
        Route::post('/loginaksi', [LoginController::class, 'loginaksi'])->name('loginaksi');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    // ================= AREA AUTHENTICATED =================
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        // 1. KHUSUS ADMINISTRATOR (Master Data, User, Laporan, & Monitoring View Pesanan)
        Route::middleware('role:administrator')
            ->name('admin.')
            ->group(function () {
                Route::resource('layanan', LayananController::class)->only(['index', 'store', 'update', 'destroy']);
                Route::resource('user', UserController::class)->only(['index', 'store', 'update', 'destroy']);

                // Route Halaman Laporan Admin
                Route::get('/laporan', function () {
                    return view('hak_akses.admin.laporan.index');
                })->name('laporan.index');
            });

        // 2. KHUSUS KASIR (POS, Pelanggan, & Katalog Read-Only)
        Route::middleware('role:kasir')->group(function () {
            Route::resource('pelanggan', PelangganController::class)->only(['index', 'store', 'update', 'destroy']);

            // Terminal POS & Pembayaran Kasir
            Route::get('/pesanan/create', [PesananController::class, 'create'])->name('pesanan.create');
            Route::get('/kasir', [PesananController::class, 'create'])->name('kasir.index');
            Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
            Route::patch('/pesanan/{pesanan}/bayar', [PesananController::class, 'bayar'])->name('pesanan.bayar');

            // Katalog Layanan Kasir
            Route::get('/kasir/layanan', [LayananController::class, 'index'])->name('kasir.layanan.index');
        });

        // 3. SEMUA ROLE (ADMINISTRATOR, KASIR, PEGAWAI)
        Route::middleware('role:administrator,kasir,pegawai')->group(function () {
            Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
            Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
            Route::get('/pesanan/{pesanan}/print', [PesananController::class, 'print'])->name('pesanan.print');
        });

        // 4. KHUSUS KASIR & PEGAWAI (Update Status Kerja/Pengerjaan)
        Route::middleware('role:kasir,pegawai')->group(function () {
            Route::patch('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])->name('pesanan.status');
        });
    });
