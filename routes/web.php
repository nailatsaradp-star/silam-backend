<?php

use App\Http\Controllers\AuthController;
use App\Models\PesananModel;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/silam-app', function () {
    return view('page.beranda');
});

Route::get('/beranda', function () {
    return view('page.beranda');
});

Route::get('/outlet', function () {
    return view('page.outlet');
});

// Status
Route::get('/status', function (\Illuminate\Http\Request $request) {
    $q = trim($request->input('q', ''));
    $dicari = !empty($q);
    $order = null;
    $daftar = collect();
    $info = null;

    if ($dicari) {
        // Cari berdasarkan nomor pesanan ATAU nomor HP pelanggan
        $daftar = PesananModel::with(['pelanggan', 'detail.layanan', 'riwayat' => function ($r) {
                $r->orderByDesc('waktu_update');
            }])
            ->where('nomor_pesanan', $q)
            ->orWhereHas('pelanggan', function ($p) use ($q) {
                // Sesuaikan 'no_hp' dengan nama kolom no HP di tabel pelanggan Anda
                $p->where('no_hp', 'like', "%{$q}%")
                  ->orWhere('telepon', 'like', "%{$q}%");
            })
            ->orderByDesc('id_pesanan')
            ->get();

        $order = $daftar->first();

        if ($order) {
            // Tentukan tahapan pengerjaan (1 - 4)
            $tahap = match ($order->status_kerja) {
                'antrian'    => 1,
                'proses'     => 2,
                'siap_ambil' => 3,
                'selesai'    => 4,
                default      => 1,
            };

            // Susun info pendukung
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

// Login (modal)
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => redirect('/beranda')->with('buka_login', true))->name('login');
    Route::post('/loginaksi', [LoginController::class, 'loginaksi'])->name('loginaksi');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Administrator saja: master layanan & user
    Route::middleware('role:administrator')->group(function () {
        Route::resource('layanan', LayananController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('user', UserController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // Administrator + Kasir: pelanggan, buat pesanan, pembayaran
    Route::middleware('role:administrator,kasir')->group(function () {
        Route::resource('pelanggan', PelangganController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('/pesanan/create', [PesananController::class, 'create'])->name('pesanan.create');
        Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
        Route::patch('/pesanan/{pesanan}/bayar', [PesananController::class, 'bayar'])->name('pesanan.bayar');
    });

    // Semua role: lihat pesanan & update status kerja
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
    Route::patch('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])->name('pesanan.status');
});
