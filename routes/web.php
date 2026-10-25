<?php

use App\Http\Controllers\AuthController;
use App\Models\PesananModel;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\UserController;
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
    $dicari = !empty($q);
    $order = null;
    $daftar = collect();
    $info = null;

    if ($dicari) {
        $daftar = PesananModel::with(['pelanggan', 'detail.layanan', 'riwayat' => function ($r) {
            $r->orderByDesc('waktu_update');
        }])
            ->where('nomor_pesanan', $q)
            ->orWhereHas('pelanggan', function ($p) use ($q) {
                $p->where('no_hp', 'like', "%{$q}%")
                  ->orWhere('telepon', 'like', "%{$q}%");
            })
            ->orderByDesc('id_pesanan')
            ->get();

        $order = $daftar->first();

        if ($order) {
            $tahap = match ($order->status_kerja) {
                'antrian'    => 1,
                'proses'     => 2,
                'siap_ambil' => 3,
                'selesai'    => 4,
                default      => 1,
            };

            $info = [
                'tahap' => $tahap,
                'nama'  => $order->pelanggan->nama_pelanggan ?? '-',
                'paket' => $order->detail->map(fn($d) => $d->layanan->nama_layanan ?? '-')->filter()->unique()->implode(', ') ?: '-',
                'berat' => $order->detail->sum('jumlah_berat') . ' Kg',
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

    // 1. KHUSUS ADMINISTRATOR
    Route::middleware('role:administrator')
        ->name('admin.')
        ->group(function () {
            Route::resource('layanan', LayananController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::resource('user', UserController::class)->only(['index', 'store', 'update', 'destroy']);
        });

    // 2. ADMINISTRATOR + KASIR
    Route::middleware('role:administrator,kasir')->group(function () {
        Route::resource('pelanggan', PelangganController::class)->only(['index', 'store', 'update', 'destroy']);

        // Terminal POS & Pembayaran
        Route::get('/pesanan/create', [PesananController::class, 'create'])->name('pesanan.create');
        Route::get('/kasir', [PesananController::class, 'create'])->name('kasir.index');
        Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
        Route::patch('/pesanan/{pesanan}/bayar', [PesananController::class, 'bayar'])->name('pesanan.bayar');

        // Katalog Layanan Kasir
        Route::get('/kasir/layanan', [LayananController::class, 'index'])->name('kasir.layanan.index');
    });

    // 3. SEMUA ROLE (ADMINISTRATOR, KASIR, PEGAWAI)
    Route::middleware('role:administrator,kasir,pegawai')->group(function () {
        // Monitoring Daftar Pesanan & Detail
        Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
        Route::get('/pesanan/{pesanan}/print', [PesananController::class, 'show'])->name('pesanan.print');

        // FITUR UTAMA PEGAWAI: Update Status Kerja & Insert Riwayat Status
        Route::patch('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])->name('pesanan.status');
    });
});
