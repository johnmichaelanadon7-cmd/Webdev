/* Harvest Bread Co. — script.js */

(function () {
    'use strict';

    /* ── Hamburger menu ──────────────────────────────────── */
    const toggle = document.querySelector('.menu-toggle');
    const nav    = document.querySelector('.main-nav');

    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            const isOpen = nav.classList.toggle('open');
            toggle.setAttribute('aria-expanded', isOpen);
            toggle.textContent = isOpen ? '✕' : '☰';
        });

        /* Close menu when a nav link is tapped */
        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                nav.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.textContent = '☰';
            });
        });

        /* Close menu when clicking outside */
        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target) && !toggle.contains(e.target)) {
                nav.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.textContent = '☰';
            }
        });
    }

    /* ── Active nav link on scroll ───────────────────────── */
    const sections = document.querySelectorAll('section[id], footer[id]');
    const navLinks = document.querySelectorAll('.main-nav a[href^="#"]');

    function setActiveLink() {
        let current = '';
        sections.forEach(function (section) {
            const top = section.getBoundingClientRect().top;
            if (top <= 80) current = section.id;
        });

        navLinks.forEach(function (link) {
            link.style.color = '';
            if (link.getAttribute('href') === '#' + current) {
                link.style.color = 'var(--orange)';
            }
        });
    }

    window.addEventListener('scroll', setActiveLink, { passive: true });
    setActiveLink();

})();