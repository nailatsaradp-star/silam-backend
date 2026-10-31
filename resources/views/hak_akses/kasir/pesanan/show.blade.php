@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $pesanan->nomor_pesanan)

@section('content')
<div class="container-fluid p-0">

    {{-- NOTIFIKASI SUKSES --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-4 text-success"></i>
            <div>
                <strong class="d-block">Berhasil!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @if(auth()->user()->role !== 'pegawai')
        <a href="{{ route('pesanan.print', $pesanan->id_pesanan) }}" target="_blank" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-printer-fill me-1"></i> Cetak Nota Sekarang
        </a>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <div>
            <h5 class="fw-bold mb-1" style="color: #2C4679;">Pesanan #{{ $pesanan->nomor_pesanan }}</h5>
            <small class="text-muted">Rincian status pengerjaan, tagihan, dan riwayat pesanan</small>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->role !== 'pegawai')
            <a href="{{ route('pesanan.print', $pesanan->id_pesanan) }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-printer me-1"></i> Cetak Nota
            </a>
            @endif
            <a href="{{ route('pesanan.index') }}" class="btn btn-light rounded-pill px-3 border">Kembali</a>
        </div>
    </div>

    <div class="row g-4">
        {{-- SISI KIRI: NOTA & UPDATE STATUS --}}
        <div class="col-lg-7">

            {{-- RINGKASAN PESANAN --}}
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Pelanggan</small>
                        <span class="fw-bold text-dark fs-6">{{ $pesanan->pelanggan->nama_pelanggan ?? '-' }}</span>
                        <div class="text-muted small">{{ $pesanan->pelanggan->nomor_telepon ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <small class="text-muted d-block">Tanggal Masuk</small>
                        <span class="fw-semibold text-dark">{{ $pesanan->tanggal_pesanan_dibuat ? \Carbon\Carbon::parse($pesanan->tanggal_pesanan_dibuat)->format('d/m/Y H:i') : '-' }}</span>
                        <small class="text-muted d-block mt-1">Estimasi Selesai</small>
                        <span class="fw-semibold text-primary">{{ $pesanan->tanggal_estimasi_selesai ? \Carbon\Carbon::parse($pesanan->tanggal_estimasi_selesai)->format('d/m/Y H:i') : '-' }}</span>
                    </div>
                </div>

                <div class="table-responsive my-3">
                    <table class="table table-sm align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Layanan</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesanan->detail as $d)
                            <tr>
                                <td class="fw-bold text-dark">{{ $d->layanan->nama_layanan ?? '-' }}</td>
                                <td class="text-center">{{ $d->jumlah_berat }} {{ $d->layanan->satuan ?? 'Kg' }}</td>
                                <td class="text-end">Rp {{ number_format($d->layanan->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Tagihan:</th>
                                <th class="text-end fs-6 text-primary">Rp {{ number_format($pesanan->total_tagihan, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $pesanan->status_pembayaran === 'lunas' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-2">
                            {{ strtoupper($pesanan->status_pembayaran) }}
                        </span>
                        <span class="badge bg-primary rounded-pill px-3 py-2 text-capitalize">
                            {{ str_replace('_', ' ', $pesanan->status_kerja) }}
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <small class="text-muted">Kasir: {{ $pesanan->kasir->nama_user ?? '-' }}</small>
                        @if($pesanan->status_pembayaran !== 'lunas' && auth()->user()->role !== 'pegawai')
                            <button type="button" class="btn btn-sm text-white rounded-pill px-3 py-2 fw-semibold shadow-sm d-print-none"
                                    style="background: #2C4679;"
                                    data-bs-toggle="modal" data-bs-target="#modalBayarPesanan{{ $pesanan->id_pesanan }}">
                                <i class="bi bi-wallet2 me-1"></i> Tandai Lunas
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- UPDATE STATUS PENGERJAAN --}}
            @if(auth()->user()->role !== 'kasir')
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white d-print-none">
                <h6 class="fw-bold mb-3" style="color: #2C4679;">Update Status Pengerjaan</h6>

                @if(session('error'))
                    <div class="alert alert-danger rounded-3 py-2 small mb-3">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('pesanan.status', $pesanan->id_pesanan) }}">
                    @csrf @method('PATCH')
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Status Kerja</label>
                            <select name="status_kerja" class="form-select rounded-3" required>
                                @php
                                    $semuaStatus = \App\Models\PesananModel::STATUS_KERJA;
                                    $urutanStatus = ['antrian' => 1, 'proses' => 2, 'siap_ambil' => 3, 'selesai' => 4];
                                    $posisiSekarang = $urutanStatus[$pesanan->status_kerja] ?? 1;
                                @endphp

                                <option value="">-- Pilih Status Berikutnya --</option>
                                @foreach($semuaStatus as $k => $l)
                                    {{-- Hanya tampilkan status yang urutannya di atas status saat ini --}}
                                    @if(($urutanStatus[$k] ?? 0) > $posisiSekarang)
                                        <option value="{{ $k }}">{{ $l }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Status saat ini: <b class="text-capitalize">{{ str_replace('_', ' ', $pesanan->status_kerja) }}</b></small>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Detail Status Tracking</label>
                            <input name="status_detail" class="form-control rounded-3" list="detailList" placeholder="mis. Sedang dicuci">
                            <datalist id="detailList">
                                <option value="Sedang dicuci">
                                <option value="Sedang dikeringkan">
                                <option value="Sedang disetrika">
                                <option value="Siap diambil di outlet">
                            </datalist>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Keterangan Tambahan</label>
                            <input name="keterangan" class="form-control rounded-3" placeholder="Opsional...">
                        </div>
                    </div>
                    <button class="btn text-white rounded-pill px-4 mt-3" style="background: #2C4679;">Simpan Status Baru</button>
                </form>
            </div>
            @endif
        </div>

        {{-- SISI KANAN: RIWAYAT TRACKING --}}
        <div class="col-lg-5 d-print-none">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h6 class="fw-bold mb-3" style="color: #2C4679;">Riwayat Tracking Pengerjaan</h6>
                <div class="timeline">
                    @forelse($pesanan->riwayat as $r)
                    <div class="d-flex mb-3 pb-2 border-bottom">
                        <div class="me-3 text-primary"><i class="bi bi-clock-history fs-5"></i></div>
                        <div>
                            <div class="fw-bold text-dark">{{ $r->status_detail }}</div>
                            @if($r->keterangan)<div class="small text-muted">{{ $r->keterangan }}</div>@endif
                            <div class="small text-muted mt-1">
                                {{ \Carbon\Carbon::parse($r->waktu_update)->format('d/m/Y H:i') }} &middot; {{ $r->petugas->nama_user ?? 'Kasir' }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-muted small">Belum ada catatan riwayat tracking.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- MODAL KONFIRMASI PEMBAYARAN (Hanya jika belum lunas & bukan pegawai) --}}
@if($pesanan->status_pembayaran !== 'lunas' && auth()->user()->role !== 'pegawai')
<div class="modal fade" id="modalBayarPesanan{{ $pesanan->id_pesanan }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content rounded-4 border-0 p-3" method="POST" action="{{ route('pesanan.bayar', $pesanan->id_pesanan) }}">
            @csrf
            @method('PATCH')

            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold mb-1" style="color: #2C4679;">Konfirmasi Pembayaran</h5>
                    <small class="text-muted">Pesanan #{{ $pesanan->nomor_pesanan }} &bull; {{ $pesanan->pelanggan->nama_pelanggan ?? '-' }}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body py-3">
                <div class="p-3 rounded-4 mb-3 text-center" style="background: #EEF4FD; border: 1px solid #CFDCF8;">
                    <small class="text-muted fw-semibold d-block mb-1">TOTAL YANG HARUS DIBAYAR</small>
                    <h3 class="fw-bold mb-0" style="color: #2C4679;">Rp {{ number_format($pesanan->total_tagihan, 0, ',', '.') }}</h3>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" style="color: #2C4679;">Metode Pembayaran</label>
                    <select name="metode_pembayaran" id="modalSelectMetode" class="form-select rounded-3 bg-light fw-semibold" required>
                        <option value="Tunai">💵 Uang Tunai (Cash)</option>
                        <option value="QRIS">📱 QRIS / E-Wallet</option>
                        <option value="Transfer">🏦 Transfer Bank</option>
                    </select>
                </div>

                <div id="modalBoxTunai">
                    <div class="mb-3">
                        <label class="form-label small fw-bold" style="color: #2C4679;">Nominal Uang Diterima (Rp)</label>
                        <input type="number" name="uang_diterima" id="modalInputUangDiterima" min="0" step="any"
                               value="{{ $pesanan->total_tagihan }}"
                               class="form-control form-control-lg rounded-3 fw-bold text-end"
                               placeholder="0">
                        <div id="modalInfoKurang" class="small text-danger fw-semibold mt-1 d-none"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-2 bg-light">
                        <span class="fw-bold small text-muted">Kembalian:</span>
                        <span id="modalTextKembalian" class="fw-bold fs-5 text-success">Rp 0</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" id="modalBtnBayar" class="btn text-white rounded-pill px-4 fw-bold" style="background: #2C4679;">
                    <i class="bi bi-check-circle-fill me-1"></i> Tandai Lunas
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const totalTagihan = {{ (float) $pesanan->total_tagihan }};
    const selectMetode = document.getElementById('modalSelectMetode');
    const boxTunai = document.getElementById('modalBoxTunai');
    const inputUang = document.getElementById('modalInputUangDiterima');
    const textKembalian = document.getElementById('modalTextKembalian');
    const infoKurang = document.getElementById('modalInfoKurang');
    const btnBayar = document.getElementById('modalBtnBayar');

    function hitungKembalian() {
        if (!inputUang || !selectMetode) return;

        if (selectMetode.value !== 'Tunai') {
            boxTunai.style.display = 'none';
            inputUang.removeAttribute('required');
            if (btnBayar) btnBayar.disabled = false;
            return;
        }

        boxTunai.style.display = 'block';
        inputUang.setAttribute('required', 'required');

        const uang = parseFloat(inputUang.value) || 0;
        const selisih = uang - totalTagihan;

        if (selisih < 0) {
            infoKurang.classList.remove('d-none');
            infoKurang.textContent = 'Uang kurang Rp ' + Math.abs(selisih).toLocaleString('id-ID');
            textKembalian.textContent = 'Rp 0';
            textKembalian.className = 'fw-bold fs-5 text-danger';
            if (btnBayar) btnBayar.disabled = true;
        } else {
            infoKurang.classList.add('d-none');
            textKembalian.textContent = 'Rp ' + selisih.toLocaleString('id-ID');
            textKembalian.className = 'fw-bold fs-5 text-success';
            if (btnBayar) btnBayar.disabled = false;
        }
    }

    if (selectMetode) selectMetode.addEventListener('change', hitungKembalian);
    if (inputUang) inputUang.addEventListener('input', hitungKembalian);

    hitungKembalian();
});
</script>
@endpush
