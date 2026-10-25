@extends('layouts.app')

@section('title', 'Manajemen Pengguna (User)')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color: #2C4679;">Akun Petugas &amp; Staf SILAM</h5>
            <small class="text-muted">Kelola hak akses pengguna, kasir, dan akun pemilik</small>
        </div>
        <button type="button" class="btn text-white rounded-pill px-4 fw-semibold" style="background: #2C4679;"
                data-bs-toggle="modal" data-bs-target="#formModal" data-store-url="{{ route('admin.user.store') }}">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
        </button>
    </div>

    {{-- TABEL USER --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #FAF8F5; color: #2C4679;" class="small fw-bold">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Pengguna</th>
                        <th>Email Login</th>
                        <th>Role Akses</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($users ?? [] as $index => $u)
                    <tr>
                        <td class="ps-4 fw-semibold text-muted">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                     style="width: 36px; height: 36px; background: #CFDCF8; color: #2C4679;">
                                    {{ strtoupper(substr($u->nama_user ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="fw-bold text-dark d-block">{{ $u->nama_user }}</span>
                                    <small class="text-muted">ID: #USR-{{ $u->id_user ?? $u->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-muted">{{ $u->email }}</span></td>
                        <td>
                            <span class="badge rounded-pill px-3 py-1 text-capitalize fw-bold"
                                  style="{{ in_array($u->role ?? 'kasir', ['administrator', 'pemilik']) ? 'background: #2C4679; color: #ffffff;' : 'background: #CFDCF8; color: #2C4679;' }}">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1" data-bs-toggle="modal" data-bs-target="#formModal"
                                        data-item="{{ json_encode($u) }}" data-url="{{ route('admin.user.update', $u->id_user ?? $u->id) }}" title="Ubah User">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                @if(auth()->id() !== ($u->id_user ?? $u->id))
                                <form method="POST" action="{{ route('admin.user.destroy', $u->id_user ?? $u->id) }}" data-confirm="Hapus akun pengguna ini?" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada pengguna terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODAL FORM TAMBAH / UBAH USER --}}
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content rounded-4 border-0 p-3" method="POST">
            @csrf
            <input type="hidden" name="_method" value="POST">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" style="color: #2C4679;">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Nama Lengkap</label>
                    <input type="text" name="nama_user" id="nama_user" class="form-control rounded-3" required placeholder="Contoh: Rian Anggara">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Alamat Email</label>
                    <input type="email" name="email" id="email" class="form-control rounded-3" required placeholder="rian@silam.com">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Kata Sandi (Password)</label>
                    <input type="password" name="password" id="password" class="form-control rounded-3" placeholder="Masukkan password minimal 6 karakter">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Role Akses</label>
                    <select name="role" id="role" class="form-select rounded-3">
                        <option value="kasir">Kasir</option>
                        <option value="pegawai">Pegawai</option>
                        <option value="administrator">Administrator / Pemilik</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn text-white rounded-pill px-4" style="background: #2C4679;">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const formModal = document.getElementById('formModal');
    if (formModal) {
        formModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const storeUrl = button.getAttribute('data-store-url');
            const updateUrl = button.getAttribute('data-url');
            const itemData = button.getAttribute('data-item');

            const form = formModal.querySelector('form');
            const title = formModal.querySelector('.modal-title');
            const methodInput = form.querySelector('input[name="_method"]');

            if (itemData) {
                // Mode Edit
                const data = JSON.parse(itemData);
                title.textContent = 'Ubah Data Pengguna';
                form.action = updateUrl;
                methodInput.value = 'PUT';

                form.querySelector('#nama_user').value = data.nama_user || '';
                form.querySelector('#email').value = data.email || '';
                form.querySelector('#password').value = '';
                form.querySelector('#password').placeholder = 'Biarkan kosong jika tidak ingin mengubah password';
                form.querySelector('#role').value = data.role || 'kasir';

                form.querySelector('button[type="submit"]').textContent = 'Perbarui Pengguna';
            } else {
                // Mode Tambah Baru
                title.textContent = 'Tambah Pengguna Baru';
                form.action = storeUrl;
                methodInput.value = 'POST';
                form.reset();

                form.querySelector('#password').placeholder = 'Masukkan password minimal 6 karakter';
                form.querySelector('button[type="submit"]').textContent = 'Simpan Pengguna';
            }
        });
    }
});
</script>
@endpush
