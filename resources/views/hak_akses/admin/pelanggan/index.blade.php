@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <small class="text-muted">Kelola basis data pelanggan dan riwayat transaksi</small>
        </div>
        <button type="button" class="btn text-white rounded-pill px-4 fw-semibold" style="background: #2C4679;"
                data-bs-toggle="modal" data-bs-target="#formModal" data-store-url="{{ route('pelanggan.store') }}">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pelanggan
        </button>
    </div>

    {{-- FILTER SEARCH --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <form method="GET" action="{{ route('pelanggan.index') }}" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="q" class="form-control rounded-3" placeholder="Cari nama atau nomor telepon..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn text-white w-100 rounded-3" style="background: #2C4679;">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
        </form>
    </div>

    {{-- TABEL PELANGGAN --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #FAF8F5; color: #2C4679;" class="small fw-bold">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Pelanggan</th>
                        <th>Nomor Telepon</th>
                        <th>Alamat</th>
                        <th>Total Pesanan</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($pelanggan as $index => $p)
                    <tr>
                        <td class="ps-4 fw-semibold text-muted">{{ $pelanggan->firstItem() + $index }}</td>
                        <td class="fw-bold text-dark">{{ $p->nama_pelanggan }}</td>
                        <td>{{ $p->nomor_telepon }}</td>
                        <td>{{ $p->alamat ?? '-' }}</td>
                        <td>
                            <span class="badge rounded-pill px-3 py-1" style="background: #CFDCF8; color: #2C4679;">
                                {{ $p->pesanan_count ?? 0 }} Transaksi
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1"
                                        data-bs-toggle="modal" data-bs-target="#formModal"
                                        data-item="{{ json_encode($p) }}"
                                        data-url="{{ route('pelanggan.update', $p->id_pelanggan) }}"
                                        title="Ubah Data">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form method="POST" action="{{ route('pelanggan.destroy', $p->id_pelanggan) }}" class="d-inline m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1"
                                            onclick="return confirm('Hapus data pelanggan ini?')" title="Hapus Data">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada data pelanggan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($pelanggan, 'links'))
        <div class="p-3 border-top">
            {{ $pelanggan->links() }}
        </div>
        @endif
    </div>

</div>

{{-- MODAL TAMBAH / EDIT PELANGGAN --}}
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content rounded-4 border-0 p-3" method="POST">
            @csrf
            <input type="hidden" name="_method" value="POST">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" style="color: #2C4679;">Tambah Pelanggan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control rounded-3"
                           required placeholder="Contoh: Budi Santoso" pattern="[a-zA-Z\s']+" title="Hanya boleh diisi huruf dan spasi">
                    <div id="errorNamaFormModal" class="small text-danger mt-1 d-none">Nama hanya boleh berisi huruf dan spasi.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nomor Telepon / WA</label>
                    <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control rounded-3"
                           required placeholder="Contoh: 081234567890" maxlength="12" pattern="08[0-9]{8,10}" title="Format: 08xxx (10 - 12 digit)">
                    <div id="errorHpFormModal" class="small text-danger mt-1 d-none">Nomor HP wajib angka, diawali 08, dan maksimal 12 digit.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Alamat</label>
                    <textarea name="alamat" id="alamat" class="form-control rounded-3" rows="3" placeholder="Alamat lengkap..."></textarea>
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
    const formModal = document.getElementById('formModal');
    const inputNama = document.getElementById('nama_pelanggan');
    const inputHp   = document.getElementById('nomor_telepon');
    const errNama   = document.getElementById('errorNamaFormModal');
    const errHp     = document.getElementById('errorHpFormModal');

    // 1. DYNAMIC MODAL (STORE / UPDATE SWITCHING)
    if (formModal) {
        formModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const storeUrl = button.getAttribute('data-store-url');
            const updateUrl = button.getAttribute('data-url');
            const itemData = button.getAttribute('data-item');

            const form = formModal.querySelector('form');
            const title = formModal.querySelector('.modal-title');
            const methodInput = form.querySelector('input[name="_method"]');

            // Reset notifikasi error saat modal dibuka
            if (errNama) errNama.classList.add('d-none');
            if (errHp) errHp.classList.add('d-none');

            if (itemData) {
                const data = JSON.parse(itemData);
                title.textContent = 'Ubah Data Pelanggan';
                form.action = updateUrl;
                methodInput.value = 'PUT';

                form.querySelector('#nama_pelanggan').value = data.nama_pelanggan || '';
                form.querySelector('#nomor_telepon').value = data.nomor_telepon || '';
                form.querySelector('#alamat').value = data.alamat || '';
            } else {
                title.textContent = 'Tambah Pelanggan Baru';
                form.action = storeUrl;
                methodInput.value = 'POST';
                form.reset();
            }
        });
    }

    // 2. VALIDASI REAL-TIME INPUT NAMA (HANYA HURUF DAN SPASI)
    if (inputNama) {
        inputNama.addEventListener('input', function () {
            const bersih = this.value.replace(/[^a-zA-Z\s']/g, '');
            if (this.value !== bersih) {
                this.value = bersih;
                if (errNama) errNama.classList.remove('d-none');
            } else {
                if (errNama) errNama.classList.add('d-none');
            }
        });
    }

    // 3. VALIDASI REAL-TIME INPUT NOMOR HP (ANGKA, MAX 12 DIGIT, DIAWALI 08)
    if (inputHp) {
        inputHp.addEventListener('input', function () {
            let bersih = this.value.replace(/[^0-9]/g, '');

            if (bersih.length > 12) {
                bersih = bersih.substring(0, 12);
            }

            this.value = bersih;

            if (bersih.length >= 2 && !bersih.startsWith('08')) {
                if (errHp) {
                    errHp.textContent = 'Nomor WhatsApp harus diawali angka 08';
                    errHp.classList.remove('d-none');
                }
            } else {
                if (errHp) errHp.classList.add('d-none');
            }
        });
    }
});
</script>
@endpush
