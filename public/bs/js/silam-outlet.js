/**
 * Interaksi & Animasi Halaman Outlet SILAM
 * - Efek Masuk (Fade-in & Slide-up saat pertama dibuka)
 * - 3D Perspective Tilt Interaktif pada Foto Outlet & Card Pricelist
 */
document.addEventListener('DOMContentLoaded', function () {

    // 1. Animasi Masuk Halus saat Halaman Terbuka
    const leftCol = document.querySelector('.outlet-left-col');
    const rightCol = document.querySelector('.outlet-right-col');

    if (leftCol && rightCol) {
        leftCol.style.opacity = '0';
        leftCol.style.transform = 'translateY(30px)';
        leftCol.style.transition = 'all 0.7s cubic-bezier(0.16, 1, 0.3, 1)';

        rightCol.style.opacity = '0';
        rightCol.style.transform = 'translateY(30px)';
        rightCol.style.transition = 'all 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.15s';

        setTimeout(() => {
            leftCol.style.opacity = '1';
            leftCol.style.transform = 'translateY(0)';
            rightCol.style.opacity = '1';
            rightCol.style.transform = 'translateY(0)';
        }, 100);
    }

    // 2. Efek 3D Dynamic Tilt saat Kursor Mouse Bergerak
    const photoCard = document.querySelector('.outlet-photo-card');
    const pricelistCard = document.querySelector('.outlet-pricelist-card');

    [photoCard, pricelistCard].forEach(card => {
        if (!card) return;

        card.addEventListener('mousemove', function (e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;

            const rotateX = -(y / rect.height) * 8;
            const rotateY = (x / rect.width) * 8;

            card.style.transform = `perspective(900px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) translateY(-6px)`;
        });

        card.addEventListener('mouseleave', function () {
            card.style.transform = '';
        });
    });
});
