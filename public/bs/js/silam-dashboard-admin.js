/**
 * Interaksi Dashboard SILAM
 * - Toggle Sidebar Mobile & Overlay
 * - Handler Modal Form (Pelanggan, Layanan, User) & Konfirmasi Delete
 */
document.addEventListener('DOMContentLoaded', function () {

    // 1. Toggle Sidebar untuk Mode Mobile
    const toggleBtn = document.getElementById('btnToggleSidebar');
    const closeBtn = document.getElementById('btnCloseSidebar');
    const sidebar = document.getElementById('silamSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');

    function openSidebar() {
        if (sidebar && backdrop) {
            sidebar.classList.add('show-sidebar');
            backdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSidebar() {
        if (sidebar && backdrop) {
            sidebar.classList.remove('show-sidebar');
            backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (backdrop) backdrop.addEventListener('click', closeSidebar);

    // 2. Modal Tambah/Ubah Otomatis (Layanan, Pelanggan, User)
    const modal = document.getElementById('formModal');
    if (modal) {
        const form = modal.querySelector('form');
        const method = form.querySelector('[name=_method]');
        const title = modal.querySelector('.modal-title');

        modal.addEventListener('show.bs.modal', function (e) {
            const button = e.relatedTarget;
            form.reset();

            if (button && button.dataset.item) {
                const item = JSON.parse(button.dataset.item);
                form.action = button.dataset.url;
                if (method) method.value = 'PUT';
                if (title) title.textContent = 'Ubah Data';

                Object.entries(item).forEach(([key, val]) => {
                    const el = form.elements[key];
                    if (el && el.type !== 'password') {
                        el.value = val ?? '';
                    }
                });
            } else {
                form.action = form.dataset.storeUrl;
                if (method) method.value = 'POST';
                if (title) title.textContent = 'Tambah Data';
            }
        });
    }

    // 3. Konfirmasi Hapus Data
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
});
