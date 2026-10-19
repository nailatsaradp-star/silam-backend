/**
 * Interaktivitas & Animasi Tracking SILAM
 */
document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. DYNAMIC TYPING PLACEHOLDER (Ketik Otomatis)
    // ==========================================
    const searchInput = document.querySelector('.tracking-input-pill input[name="q"]');

    if (searchInput && !searchInput.value) {
        const textOptions = [
            "Nomor pesanan (cth: SILAM-20261019-0001)",
            "Nomor HP terdaftar (cth: 08123456789)",
            "Lacak cucian Anda di sini..."
        ];

        let textIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        let isFocused = false;

        searchInput.addEventListener('focus', () => { isFocused = true; });
        searchInput.addEventListener('blur', () => {
            if (!searchInput.value) {
                isFocused = false;
                typeEffect();
            }
        });

        function typeEffect() {
            if (isFocused || searchInput.value) return;

            const currentText = textOptions[textIndex];

            if (isDeleting) {
                searchInput.setAttribute('placeholder', currentText.substring(0, charIndex - 1));
                charIndex--;
            } else {
                searchInput.setAttribute('placeholder', currentText.substring(0, charIndex + 1));
                charIndex++;
            }

            let typeSpeed = isDeleting ? 35 : 70;

            if (!isDeleting && charIndex === currentText.length) {
                typeSpeed = 2200; // Diam sejenak saat teks selesai diketik
                isDeleting = true;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                textIndex = (textIndex + 1) % textOptions.length;
                typeSpeed = 400;
            }

            setTimeout(typeEffect, typeSpeed);
        }

        typeEffect();
    }

    // ==========================================
    // 2. ANIMASI POP GELEMBUNG SAAT DIKLIK
    // ==========================================
    const bubbles = document.querySelectorAll('.tracking-hero-card .bubble');
    bubbles.forEach(bubble => {
        bubble.addEventListener('click', function () {
            this.classList.add('popped');
            // Munculkan kembali gelembung setelah 2.5 detik
            setTimeout(() => {
                this.classList.remove('popped');
            }, 2500);
        });
    });

    // ==========================================
    // 3. EFEK LOADING SAAT FORM DISUBMIT
    // ==========================================
    const form = document.querySelector('.tracking-search-box-wrap form');
    const submitBtn = document.querySelector('.btn-search-bubble');

    if (form && submitBtn) {
        form.addEventListener('submit', function () {
            submitBtn.classList.add('is-loading');
            const icon = submitBtn.querySelector('i');
            if (icon) {
                icon.className = 'bi bi-arrow-repeat';
            }
        });
    }

});
