/**
 * SILAM Kasir POS Logic
 * - Jam Realtime Digital
 * - Keranjang Dinamis & Hitung Otomatis (Subtotal & Grand Total)
 * - Quick Cash Buttons & Hitung Kembalian Otomatis
 * - Cetak Nota Thermal Struk
 */
document.addEventListener('DOMContentLoaded', function () {

    // 1. Jam Digital Kasir Realtime
    const clockEl = document.getElementById('posLiveClock');
    if (clockEl) {
        setInterval(() => {
            const now = new Date();
            clockEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
        }, 1000);
    }

    // 2. State Keranjang Kasir
    let cart = [];

    const cartTbody = document.getElementById('posCartTableBody');
    const emptyRow = document.getElementById('posCartEmptyRow');
    const displayTotal = document.getElementById('posDisplayTotal');
    const inputTotalBayar = document.getElementById('posInputTotalBayar');
    const inputUangDiterima = document.getElementById('posInputUangDiterima');
    const textKembalian = document.getElementById('posTextKembalian');

    // Menambah Layanan dari Katalog Cepat
    document.querySelectorAll('.pos-service-chip').forEach(chip => {
        chip.addEventListener('click', function () {
            const id = this.dataset.id;
            const nama = this.dataset.nama;
            const harga = parseFloat(this.dataset.harga) || 0;
            const satuan = this.dataset.satuan || 'kg';

            // Cek apakah sudah ada di keranjang
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.qty = parseFloat((existing.qty + 1).toFixed(1));
            } else {
                cart.push({
                    id: id,
                    nama: nama,
                    harga: harga,
                    satuan: satuan,
                    qty: 1.0,
                    catatan: ''
                });
            }
            renderCart();
        });
    });

    // Render Ulang Tabel Keranjang
    function renderCart() {
        if (!cartTbody) return;

        cartTbody.innerHTML = '';

        if (cart.length === 0) {
            if (emptyRow) cartTbody.appendChild(emptyRow);
            updateTotals(0);
            return;
        }

        let grandTotal = 0;

        cart.forEach((item, index) => {
            const subtotal = item.qty * item.harga;
            grandTotal += subtotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="ps-3">
                    <span class="fw-bold text-dark d-block">${item.nama}</span>
                    <small class="text-muted">Rp ${item.harga.toLocaleString('id-ID')} / ${item.satuan}</small>
                    <input type="hidden" name="items[${index}][id_layanan]" value="${item.id}">
                    <input type="hidden" name="items[${index}][catatan]" value="${item.catatan}">
                </td>
                <td class="text-center">
                    <div class="d-inline-flex align-items-center gap-1">
                        <button type="button" class="btn-qty-step" data-action="minus" data-index="${index}">-</button>
                        <input type="number" step="0.1" min="0.1" class="pos-qty-input" name="items[${index}][berat_qty]" value="${item.qty}" data-index="${index}">
                        <button type="button" class="btn-qty-step" data-action="plus" data-index="${index}">+</button>
                    </div>
                </td>
                <td class="text-end fw-bold text-dark">
                    Rp ${Math.round(subtotal).toLocaleString('id-ID')}
                </td>
                <td class="text-center pe-3">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" data-action="delete" data-index="${index}" title="Hapus Item">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            cartTbody.appendChild(tr);
        });

        updateTotals(grandTotal);
    }

    // Event Listener Tombol +/- dan Input Qty pada Keranjang
    if (cartTbody) {
        cartTbody.addEventListener('click', function (e) {
            const btn = e.target.closest('button');
            if (!btn) return;

            const action = btn.dataset.action;
            const index = parseInt(btn.dataset.index);

            if (action === 'plus') {
                cart[index].qty = parseFloat((cart[index].qty + 0.5).toFixed(1));
                renderCart();
            } else if (action === 'minus') {
                if (cart[index].qty > 0.5) {
                    cart[index].qty = parseFloat((cart[index].qty - 0.5).toFixed(1));
                    renderCart();
                }
            } else if (action === 'delete') {
                cart.splice(index, 1);
                renderCart();
            }
        });

        cartTbody.addEventListener('input', function (e) {
            if (e.target.classList.contains('pos-qty-input')) {
                const index = parseInt(e.target.dataset.index);
                const val = parseFloat(e.target.value) || 0;
                if (val >= 0) {
                    cart[index].qty = val;
                    updateTotals(calcGrandTotal());
                }
            }
        });
    }

    function calcGrandTotal() {
        return cart.reduce((sum, item) => sum + (item.qty * item.harga), 0);
    }

    function updateTotals(total) {
        if (displayTotal) displayTotal.textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
        if (inputTotalBayar) inputTotalBayar.value = Math.round(total);
        calculateChange();
    }

    // 3. Tombol Cepat Uang Nominal (Quick Cash)
    document.querySelectorAll('.btn-cash-chip').forEach(btn => {
        btn.addEventListener('click', function () {
            const total = calcGrandTotal();
            const action = this.dataset.nominal;

            if (action === 'pas') {
                inputUangDiterima.value = total;
            } else {
                inputUangDiterima.value = parseFloat(action);
            }
            calculateChange();
        });
    });

    // Hitung Kembalian Otomatis
    if (inputUangDiterima) {
        inputUangDiterima.addEventListener('input', calculateChange);
    }

    function calculateChange() {
        if (!inputUangDiterima || !textKembalian) return;
        const total = calcGrandTotal();
        const diterima = parseFloat(inputUangDiterima.value) || 0;
        const kembalian = diterima - total;

        if (kembalian >= 0) {
            textKembalian.textContent = 'Rp ' + Math.round(kembalian).toLocaleString('id-ID');
            textKembalian.className = 'fw-bold fs-5 text-success';
        } else {
            textKembalian.textContent = 'Kurang Rp ' + Math.round(Math.abs(kembalian)).toLocaleString('id-ID');
            textKembalian.className = 'fw-bold fs-6 text-danger';
        }
    }

    // 4. Modal Cetak Nota / Struk
    const formPos = document.getElementById('posFormTransaction');
    if (formPos) {
        formPos.addEventListener('submit', function (e) {
            if (cart.length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal 1 paket layanan cucian terlebih dahulu!');
                return;
            }

            const statusBayar = document.querySelector('input[name="status_pembayaran"]:checked')?.value;
            const diterima = parseFloat(inputUangDiterima.value) || 0;
            const total = calcGrandTotal();

            if (statusBayar === 'Lunas' && diterima < total) {
                e.preventDefault();
                alert('Uang yang diterima kurang dari total biaya tagihan!');
                return;
            }
        });
    }
});
