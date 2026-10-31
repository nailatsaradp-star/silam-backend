@extends('layouts.app')

@section('title', 'Terminal Kasir POS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('bs/css/silam-kasir.css') }}">
@endpush

@section('content')
<div class="pos-wrapper">

    {{-- HEADER BAR TERMINAL KASIR --}}
    <div class="pos-header-bar d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 44px; height: 44px; background: #CFDCF8; color: #2C4679;">
                <i class="bi bi-calculator fs-4"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0" style="color: #2C4679;">Kasir Transaksi Baru</h5>
                <small class="text-muted">Kasir Pemroses: <strong class="text-dark">{{ auth()->user()->nama_user }}</strong></small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="pos-clock-pill">
                <i class="bi bi-clock-fill text-primary me-1"></i>
                <span id="posLiveClock">{{ date('H:i:s') }} WIB</span>
            </span>
            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: #E1EEA2; color: #2C4679;">
                Terminal Aktif
            </span>
        </div>
    </div>

    {{-- FORM UTAMA TRANSAKSI --}}
    <form id="posFormTransaction" method="POST" action="{{ route('pesanan.store') }}">
        @csrf

        {{-- Alert error global dihapus agar tidak double/menumpuk dengan pesan di bawah input --}}

        <div class="row g-4">

            {{-- SISI KIRI (PELANGGAN & KATALOG) --}}
            <div class="col-lg-7 col-xl-8">

                {{-- DATA PELANGGAN --}}
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label fw-bold mb-0" style="color: #2C4679;">
                            <i class="bi bi-person-fill me-1"></i> Data Pelanggan
                        </label>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold"
                                data-bs-toggle="modal" data-bs-target="#modalTambahPelanggan">
                            <i class="bi bi-person-plus-fill me-1"></i> + Pelanggan Baru
                        </button>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-7">
                            <select name="id_pelanggan" id="selectPelanggan" class="form-select rounded-3 bg-light @error('id_pelanggan') is-invalid @enderror" required>
                                <option value="">-- Cari / Pilih Pelanggan --</option>
                                @foreach($pelanggan ?? [] as $pel)
                                    <option value="{{ $pel->id_pelanggan }}"
                                            data-hp="{{ $pel->nomor_telepon }}"
                                            data-alamat="{{ $pel->alamat }}"
                                            {{ (string) request('selected_id') === (string) $pel->id_pelanggan ? 'selected' : '' }}>
                                        {{ $pel->nama_pelanggan }} ({{ $pel->nomor_telepon }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_pelanggan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-5">
                            <input type="text" id="infoPelangganHp" class="form-control rounded-3 bg-light text-muted" placeholder="WhatsApp / No. HP" readonly>
                        </div>
                    </div>
                </div>

                {{-- KATALOG PAKET LAYANAN --}}
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0" style="color: #2C4679;"><i class="bi bi-grid-fill me-2"></i>Katalog Paket Layanan</h6>
                    <small class="text-muted">Klik paket untuk menambahkan ke keranjang</small>
                </div>

                <div class="pos-catalog-grid mb-4">
                    @forelse($layanan ?? [] as $lay)
                    <div class="pos-service-chip p-3 rounded-4 bg-white shadow-sm border border-light cursor-pointer mb-2"
                         data-id="{{ $lay->id_layanan }}"
                         data-nama="{{ $lay->nama_layanan }}"
                         data-harga="{{ $lay->harga_satuan }}"
                         data-satuan="{{ $lay->satuan ?? 'kg' }}">
                        <span class="pos-chip-title fw-bold d-block text-dark">{{ $lay->nama_layanan }}</span>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="pos-chip-price fw-bold" style="color: #2C4679;">Rp {{ number_format($lay->harga_satuan, 0, ',', '.') }}</span>
                            <span class="badge rounded-pill bg-light text-muted small border">{{ strtoupper($lay->satuan ?? 'Kg') }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 p-3 bg-white rounded-3 text-center text-muted">
                        Belum ada master data layanan.
                    </div>
                    @endforelse
                </div>

                {{-- KERANJANG ITEM --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold" style="color: #2C4679;"><i class="bi bi-basket2-fill me-2"></i>Rincian Cucian Masuk</span>
                        <small class="text-muted">Timbang dan sesuaikan berat/jumlah</small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Paket Layanan</th>
                                    <th class="text-center" style="width: 170px;">Berat / Qty</th>
                                    <th class="text-end" style="width: 140px;">Subtotal</th>
                                    <th class="text-center pe-3" style="width: 60px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="posCartTableBody">
                                <tr id="posCartEmptyRow">
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-cart-x fs-2 d-block mb-2 text-secondary"></i>
                                        Keranjang masih kosong. Klik paket layanan di atas untuk menambahkan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- SISI KANAN (CHECKOUT & PEMBAYARAN) --}}
            <div class="col-lg-5 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">

                    <div class="p-3 rounded-4 mb-3 text-center" style="background: #2C4679; color: #ffffff;">
                        <span class="small text-uppercase opacity-75 d-block">Total Tagihan</span>
                        <h2 class="fw-bold mb-0" id="posDisplayTotal">Rp 0</h2>
                    </div>

                    {{-- STATUS PEMBAYARAN --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold" style="color: #2C4679;">Status Pembayaran</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="status_pembayaran" id="payLunas" value="lunas"
                                       {{ old('status_pembayaran', 'lunas') === 'lunas' ? 'checked' : '' }}>
                                <label class="btn btn-outline-success w-100 rounded-3 fw-bold py-2" for="payLunas">
                                    <i class="bi bi-check2-circle me-1"></i> Lunas
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="status_pembayaran" id="payNanti" value="belum_lunas"
                                       {{ old('status_pembayaran') === 'belum_lunas' ? 'checked' : '' }}>
                                <label class="btn btn-outline-warning w-100 rounded-3 fw-bold py-2 text-dark" for="payNanti">
                                    <i class="bi bi-hourglass-split me-1"></i> Saat Ambil
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- METODE PEMBAYARAN --}}
                    <div class="mb-3" id="boxMetode">
                        <label class="form-label small fw-bold" style="color: #2C4679;">Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="selectMetode" class="form-select rounded-3 bg-light fw-semibold">
                            <option value="Tunai"    @selected(old('metode_pembayaran') === 'Tunai')>💵 Uang Tunai (Cash)</option>
                            <option value="QRIS"     @selected(old('metode_pembayaran') === 'QRIS')>📱 QRIS / E-Wallet</option>
                            <option value="Transfer" @selected(old('metode_pembayaran') === 'Transfer')>🏦 Transfer Bank</option>
                        </select>
                    </div>

                    {{-- NOMINAL DITERIMA & KEMBALIAN --}}
                    <div id="boxTunai">
                        <div class="mb-3">
                            <label class="form-label small fw-bold" style="color: #2C4679;">Nominal Diterima (Rp)</label>
                            <input type="number" name="uang_diterima" id="posInputUangDiterima" min="0" step="any"
                                   value="{{ old('uang_diterima') }}"
                                   class="form-control form-control-lg rounded-3 fw-bold text-end @error('uang_diterima') is-invalid @enderror" placeholder="0">
                            <div id="posInfoUang" class="small text-danger fw-semibold mt-1"></div>
                            @error('uang_diterima')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-4 bg-light">
                            <span class="fw-bold small text-muted">Kembalian:</span>
                            <span id="posTextKembalian" class="fw-bold fs-5 text-dark">Rp 0</span>
                        </div>
                    </div>

                    <div id="posPesanError" class="alert alert-danger d-none py-2 small rounded-3 mb-3"></div>

                    <button type="submit" id="btnProses" class="btn text-white w-100 rounded-pill py-3 fw-bold" style="background: #2C4679;">
                        <i class="bi bi-printer-fill me-2"></i> Proses &amp; Cetak Nota
                    </button>

                </div>
            </div>

        </div>
    </form>

</div>

{{-- MODAL TAMBAH PELANGGAN CEPAT --}}
<div class="modal fade" id="modalTambahPelanggan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content rounded-4 border-0 p-3" method="POST" action="{{ route('pelanggan.store') }}">
            @csrf
            <input type="hidden" name="from_kasir" value="1">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" style="color: #2C4679;">Tambah Pelanggan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Lengkap</label>
                    <input type="text" name="nama_pelanggan" class="form-control rounded-3" required placeholder="Contoh: Rian Anggara">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nomor WhatsApp / HP</label>
                    <input type="text" name="nomor_telepon" class="form-control rounded-3" required placeholder="08xxxxxxxx">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Alamat</label>
                    <textarea name="alamat" class="form-control rounded-3" rows="2" placeholder="Alamat rumah / kos..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn text-white rounded-pill px-4" style="background: #2C4679;">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rupiah = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
    const keAngka = t => parseFloat(String(t).replace(',', '.')) || 0;

    // Jam
    setInterval(() => {
        document.getElementById('posLiveClock').textContent = new Date().toLocaleTimeString('id-ID') + ' WIB';
    }, 1000);

    // Info HP pelanggan
    const selectPelanggan = document.getElementById('selectPelanggan');
    function updatePelangganInfo() {
        const hp = document.getElementById('infoPelangganHp');
        if (selectPelanggan && selectPelanggan.selectedIndex > 0) {
            hp.value = selectPelanggan.options[selectPelanggan.selectedIndex].dataset.hp || '';
        } else {
            hp.value = '';
        }
    }
    if (selectPelanggan) {
        selectPelanggan.addEventListener('change', updatePelangganInfo);
        updatePelangganInfo();
    }

    // Elemen
    const form         = document.getElementById('posFormTransaction');
    const radioLunas    = document.getElementById('payLunas');
    const boxMetode     = document.getElementById('boxMetode');
    const selectMetode  = document.getElementById('selectMetode');
    const boxTunai      = document.getElementById('boxTunai');
    const inputUang     = document.getElementById('posInputUangDiterima');
    const textKembalian = document.getElementById('posTextKembalian');
    const infoUang      = document.getElementById('posInfoUang');
    const pesanError    = document.getElementById('posPesanError');
    const tbody         = document.getElementById('posCartTableBody');

    let cart = [];
    let sudahCoba = false;
    const totalSekarang = () => cart.reduce((s, i) => s + i.harga * i.jumlah, 0);

    // Pulihkan keranjang jika form dikembalikan karena error validasi dari server
    Object.values(@json(old('items', []))).forEach(o => {
        const chip = document.querySelector(`.pos-service-chip[data-id="${o.id_layanan}"]`);
        if (chip) {
            cart.push({
                id: String(o.id_layanan), nama: chip.dataset.nama,
                harga: parseFloat(chip.dataset.harga), satuan: chip.dataset.satuan,
                jumlah: keAngka(o.jumlah) || 1,
            });
        }
    });

    function refreshPembayaran() {
        const lunas = radioLunas.checked;
        const tunai = lunas && selectMetode.value === 'Tunai';

        boxMetode.classList.toggle('d-none', !lunas);
        selectMetode.disabled = !lunas;
        boxTunai.classList.toggle('d-none', !tunai);
        inputUang.disabled = !tunai;
        inputUang.required = tunai;

        let kurang = false;
        if (tunai) {
            const selisih = (parseFloat(inputUang.value) || 0) - totalSekarang();
            kurang = selisih < 0;
            textKembalian.textContent = rupiah(Math.max(selisih, 0));
            infoUang.textContent = (sudahCoba && kurang) ? 'Uang kurang ' + rupiah(-selisih) : '';
        } else {
            infoUang.textContent = '';
        }

        if (sudahCoba && cart.length > 0) pesanError.classList.add('d-none');
        return { tunai, kurang };
    }

    function perbaruiTotal() {
        document.getElementById('posDisplayTotal').textContent = rupiah(totalSekarang());
        refreshPembayaran();
    }

    function renderCart() {
        tbody.innerHTML = '';

        if (cart.length === 0) {
            tbody.innerHTML = `
                <tr id="posCartEmptyRow">
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="bi bi-cart-x fs-2 d-block mb-2 text-secondary"></i>
                        Keranjang masih kosong. Klik paket layanan di atas untuk menambahkan.
                    </td>
                </tr>`;
            perbaruiTotal();
            return;
        }

        cart.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="ps-3 fw-bold text-dark">
                    ${item.nama}
                    <input type="hidden" name="items[${index}][id_layanan]" value="${item.id}">
                </td>
                <td class="text-center">
                    <input type="text" inputmode="decimal" autocomplete="off"
                           name="items[${index}][jumlah]"
                           class="form-control form-control-sm text-center rounded-3 input-qty"
                           data-index="${index}" value="${item.jumlah}" required>
                </td>
                <td class="text-end fw-bold cell-subtotal" style="color: #2C4679;">${rupiah(item.harga * item.jumlah)}</td>
                <td class="text-center pe-3">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-item" data-index="${index}"><i class="bi bi-trash"></i></button>
                </td>`;
            tbody.appendChild(tr);
        });
        perbaruiTotal();
    }

    // Klik paket = tambah ke keranjang
    document.querySelectorAll('.pos-service-chip').forEach(chip => {
        chip.addEventListener('click', function () {
            const existing = cart.find(i => i.id === this.dataset.id);
            if (existing) {
                existing.jumlah = Math.round((existing.jumlah + 1) * 100) / 100;
            } else {
                cart.push({
                    id: this.dataset.id, nama: this.dataset.nama,
                    harga: parseFloat(this.dataset.harga), satuan: this.dataset.satuan, jumlah: 1,
                });
            }
            pesanError.classList.add('d-none');
            renderCart();
        });
    });

    tbody.addEventListener('input', function (e) {
        if (!e.target.classList.contains('input-qty')) return;

        const bersih = e.target.value.replace(/[^0-9.,]/g, '');
        if (bersih !== e.target.value) e.target.value = bersih;

        const item = cart[e.target.dataset.index];
        item.jumlah = keAngka(bersih);
        e.target.closest('tr').querySelector('.cell-subtotal').textContent = rupiah(item.harga * item.jumlah);
        pesanError.classList.add('d-none');
        perbaruiTotal();
    });

    tbody.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-delete-item');
        if (btn) {
            cart.splice(parseInt(btn.dataset.index, 10), 1);
            renderCart();
        }
    });

    document.querySelectorAll('input[name="status_pembayaran"]').forEach(r => r.addEventListener('change', refreshPembayaran));
    selectMetode.addEventListener('change', refreshPembayaran);
    inputUang.addEventListener('input', refreshPembayaran);

    form.addEventListener('submit', function (e) {
        sudahCoba = true;
        const { kurang } = refreshPembayaran();

        if (cart.length === 0) {
            e.preventDefault();
            pesanError.textContent = 'Keranjang masih kosong. Pilih paket layanan terlebih dulu.';
            pesanError.classList.remove('d-none');
            return;
        }
        if (cart.some(i => i.jumlah < 0.1)) {
            e.preventDefault();
            pesanError.textContent = 'Berat / jumlah minimal 0,1.';
            pesanError.classList.remove('d-none');
            return;
        }
        if (kurang) {
            e.preventDefault();
            inputUang.focus();
            return;
        }

        document.querySelectorAll('.input-qty').forEach(el => { el.value = String(keAngka(el.value)); });
    });

    renderCart();
});
</script>
@endpush
