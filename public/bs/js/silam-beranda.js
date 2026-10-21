/**
 * SILAM - Interactive Landing Page Animations
 * 1. Gelembung Sabun Melayang di Hero
 * 2. Counter Angka & Progress Circle Hero (0% -> 70%)
 * 3. 3D Mouse Parallax pada Gambar Mesin Cuci & Outlet
 * 4. Efek Spotlight Kursor pada Kartu Layanan
 * 5. Water Ripple Effect (Riak Air) Saat Klik
 * 6. Staggered Scroll-Reveal Otomatis
 * 7. Simulasi Notifikasi Aktivitas Cucian Real-time
 */
document.addEventListener('DOMContentLoaded', function () {

    // ========================================================
    // 1. GENERATOR GELEMBUNG SABUN MELAYANG DI HERO
    // ========================================================
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        function createBubble() {
            const bubble = document.createElement('div');
            bubble.classList.add('silam-bubble');

            const size = Math.random() * 28 + 14; // 14px - 42px
            const left = Math.random() * 92 + 2;
            const duration = Math.random() * 5 + 7; // 7s - 12s

            bubble.style.width = `${size}px`;
            bubble.style.height = `${size}px`;
            bubble.style.left = `${left}%`;
            bubble.style.animationDuration = `${duration}s`;

            heroSection.appendChild(bubble);

            setTimeout(() => {
                bubble.remove();
            }, duration * 1000);
        }

        // Munculkan gelembung secara berkala
        for (let i = 0; i < 4; i++) {
            setTimeout(createBubble, i * 700);
        }
        setInterval(createBubble, 2400);
    }

    // ========================================================
    // 2. COUNTER ANGKA & PROGRESS CIRCLE HERO (0% -> 70%)
    // ========================================================
    const progressCircle = document.querySelector('.hero-circle-progress');
    const progressText = document.querySelector('.hero-progress-number');

    if (progressCircle && progressText) {
        let currentPercent = 0;
        const targetPercent = 70;
        const totalCircumference = 175.9; // 2 * pi * 28

        const interval = setInterval(() => {
            currentPercent++;
            progressText.textContent = `${currentPercent}%`;

            const filledLength = (currentPercent / 100) * totalCircumference;
            progressCircle.setAttribute('stroke-dasharray', `${filledLength.toFixed(1)} ${totalCircumference}`);

            if (currentPercent >= targetPercent) {
                clearInterval(interval);
            }
        }, 22);
    }

    // ========================================================
    // 3. 3D MOUSE PARALLAX PADA MESIN CUCI HERO
    // ========================================================
    const heroRow = document.querySelector('.hero-section .row');
    const machineImg = document.querySelector('.hero-machine-img');

    if (heroRow && machineImg) {
        heroRow.addEventListener('mousemove', function (e) {
            const rect = heroRow.getBoundingClientRect();
            const x = (e.clientX - rect.left - rect.width / 2) / 25;
            const y = (e.clientY - rect.top - rect.height / 2) / 25;

            machineImg.style.transform = `translate3d(${x * 1.5}px, ${y * 1.5}px, 0) rotate(${x * 0.3}deg)`;
        });

        heroRow.addEventListener('mouseleave', function () {
            machineImg.style.transform = 'translate3d(0, 0, 0) rotate(0deg)';
        });
    }

    // ========================================================
    // 4. SPOTLIGHT GLOW PADA KARTU LAYANAN & KEUTAMAAN
    // ========================================================
    const glowingCards = document.querySelectorAll('.service-card-modern, .keutamaan-card-modern');
    glowingCards.forEach(card => {
        // Buat elemen spotlight jika belum ada
        let glow = card.querySelector('.card-spotlight-glow');
        if (!glow) {
            glow = document.createElement('div');
            glow.className = 'card-spotlight-glow';
            card.appendChild(glow);
        }

        card.addEventListener('mousemove', function (e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            glow.style.left = `${x}px`;
            glow.style.top = `${y}px`;
        });
    });

    // ========================================================
    // 5. WATER RIPPLE EFFECT (RIAK AIR) SAAT DIKLIK
    // ========================================================
    document.querySelectorAll('.service-card-modern, .btn, .btn-cek-status').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.classList.add('silam-ripple-wave');

            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
            ripple.style.top = `${e.clientY - rect.top - size / 2}px`;

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 650);
        });
    });

    // ========================================================
    // 6. STAGGERED SCROLL REVEAL PADA SETIAP SECTION
    // ========================================================
    const revealTargets = document.querySelectorAll(
        '.hero-section .card, .about-section-modern .row, .service-card-modern, .keutamaan-card-modern, .testi-card-box, .cta-banner-card'
    );

    revealTargets.forEach((el, index) => {
        el.classList.add('silam-reveal-item');
    });

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -40px 0px'
    });

    revealTargets.forEach(el => revealObserver.observe(el));

    // ========================================================
    // 7. SIMULASI NOTIFIKASI AKTIVITAS CUCIAN REAL-TIME DI HERO
    // ========================================================
    const notifPills = document.querySelectorAll('.hero-notif-pill');
    const orderActivities = [
        { title: "Pesanan #SL-8201", desc: "Selesai disetrika & wangi", icon: "bi-check2", bg: "bg-primary" },
        { title: "Pesanan #SL-8205", desc: "Masuk proses pencucian", icon: "bi-droplet-fill", bg: "bg-info" },
        { title: "Pesanan #SL-8198", desc: "Siap diambil di outlet", icon: "bi-bag-check-fill", bg: "bg-success" },
        { title: "Notifikasi WhatsApp", desc: "Terkirim ke +62 821****", icon: "bi-bell-fill", bg: "bg-dark" }
    ];

    if (notifPills.length >= 2) {
        let index = 0;
        setInterval(() => {
            const targetPill = notifPills[index % 2];
            const data = orderActivities[Math.floor(Math.random() * orderActivities.length)];

            targetPill.style.opacity = '0';
            targetPill.style.transform = 'translateY(-6px)';

            setTimeout(() => {
                const titleEl = targetPill.querySelector('.notif-title');
                const descEl = targetPill.querySelector('.notif-desc');
                const iconContainer = targetPill.querySelector('.notif-icon-circle');

                if (titleEl) titleEl.textContent = data.title;
                if (descEl) descEl.textContent = data.desc;
                if (iconContainer) {
                    iconContainer.className = `d-inline-flex align-items-center justify-content-center rounded-circle ${data.bg} text-white flex-shrink-0 notif-icon-circle`;
                    iconContainer.innerHTML = `<i class="bi ${data.icon}"></i>`;
                }

                targetPill.style.opacity = '1';
                targetPill.style.transform = 'translateY(0)';
            }, 300);

            index++;
        }, 4000);
    }
});
