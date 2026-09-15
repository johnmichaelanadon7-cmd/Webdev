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

        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                nav.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.textContent = '☰';
            });
        });

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

    /* ── Sign-up client-side validation ─────────────────── */
    const signupForm = document.querySelector('form[action="signup_function.php"]');

    if (signupForm) {
        function showError(input, message) {
            const group = input.closest('.form-group');
            if (!group) return;
            group.classList.add('has-error');
            let el = group.querySelector('.field-error');
            if (!el) {
                el = document.createElement('span');
                el.className = 'field-error';
                input.after(el);
            }
            el.textContent = message;
        }

        function clearError(input) {
            const group = input.closest('.form-group');
            if (!group) return;
            group.classList.remove('has-error');
        }

        function validateSignup(e) {
            let valid = true;

            const username = signupForm.querySelector('#username');
            const email    = signupForm.querySelector('#email');
            const number   = signupForm.querySelector('#number');
            const password = signupForm.querySelector('#password');
            const confirm  = signupForm.querySelector('#confirmation_password');

            [username, email, number, password, confirm].forEach(clearError);

            if (!username.value.trim()) {
                showError(username, 'Username is required.');
                valid = false;
            }

            if (!email.value.trim()) {
                showError(email, 'Email is required.');
                valid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                showError(email, 'Enter a valid email address.');
                valid = false;
            }

            if (!number.value.trim()) {
                showError(number, 'Phone number is required.');
                valid = false;
            }

            if (!password.value) {
                showError(password, 'Password is required.');
                valid = false;
            } else if (password.value.length < 8) {
                showError(password, 'Password must be at least 8 characters.');
                valid = false;
            }

            if (!confirm.value) {
                showError(confirm, 'Please confirm your password.');
                valid = false;
            } else if (password.value !== confirm.value) {
                showError(confirm, 'Passwords do not match.');
                valid = false;
            }

            if (!valid) e.preventDefault();
        }

        signupForm.addEventListener('submit', validateSignup);
    }

    /* ── Login client-side validation ───────────────────── */
    const loginForm = document.querySelector('form[action="login_function.php"]');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            let valid = true;

            const email    = loginForm.querySelector('#email');
            const password = loginForm.querySelector('#password');

            [email, password].forEach(function (input) {
                const group = input.closest('.form-group');
                if (group) group.classList.remove('has-error');
            });

            function showErr(input, message) {
                const group = input.closest('.form-group');
                if (!group) return;
                group.classList.add('has-error');
                let el = group.querySelector('.field-error');
                if (!el) {
                    el = document.createElement('span');
                    el.className = 'field-error';
                    input.after(el);
                }
                el.textContent = message;
            }

            if (!email.value.trim()) {
                showErr(email, 'Email is required.');
                valid = false;
            }
            if (!password.value) {
                showErr(password, 'Password is required.');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    }

    /* ── Checkout client-side validation ────────────────── */
    const checkoutForm = document.querySelector('form[action="checkout_function.php"]');

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            const numberInput = checkoutForm.querySelector('input[name="number"]');
            if (numberInput && !numberInput.value.trim()) {
                e.preventDefault();
                numberInput.focus();
                numberInput.style.borderColor = '#b00000';
                numberInput.style.boxShadow   = '0 0 0 3px rgba(176,0,0,0.08)';
                const hint = checkoutForm.querySelector('.number-hint') || document.createElement('span');
                hint.className  = 'field-hint';
                hint.style.color = '#b00000';
                hint.textContent = 'A contact number is required.';
                hint.classList.add('number-hint');
                numberInput.after(hint);
            }
        });
    }

    /* ── Cart badge (nav icon) ───────────────────────────── */
    function updateCartBadge(count) {
        const badge = document.getElementById('cartBadge');
        if (!badge) return;
        count = parseInt(count, 10) || 0;
        badge.textContent = count > 99 ? '99+' : count;
        badge.classList.toggle('is-empty', count < 1);
    }

    /* ── VIEW ITEM modal (info only, index.php) ──────────── */
    const modalOverlay  = document.getElementById('productModal');

    if (modalOverlay) {
        const modalCloseBtn = document.getElementById('modalClose');
        const modalImg      = document.getElementById('modalImg');
        const modalTitle    = document.getElementById('modalTitle');
        const modalDesc     = document.getElementById('modalDesc');
        const modalPrice    = document.getElementById('modalPrice');
        const modalStock    = document.getElementById('modalStock');
        const modalBadge    = document.getElementById('modalBadge');

        function openModal(btn) {
            const stockNum = parseInt(btn.dataset.stock, 10);
            modalImg.src           = 'image/' + btn.dataset.image;
            modalImg.alt           = btn.dataset.name;
            modalTitle.textContent = btn.dataset.name;
            modalDesc.textContent  = btn.dataset.desc || 'A freshly baked artisan product from our bakery.';
            modalPrice.textContent = '$' + btn.dataset.price + ' USD';
            modalStock.textContent = stockNum > 0 ? stockNum + ' items in stock' : 'Currently sold out';
            modalBadge.textContent = stockNum > 0 ? '✔ Available' : '✘ Sold Out';
            modalBadge.style.background  = stockNum > 0 ? '#fff4e6' : '#fce8e8';
            modalBadge.style.color       = stockNum > 0 ? '#e07000' : '#c0392b';
            modalBadge.style.borderColor = stockNum > 0 ? '#f5c68a' : '#e8a0a0';
            modalOverlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modalOverlay.classList.remove('open');
            document.body.style.overflow = '';
        }

        document.querySelectorAll('.view-item-btn').forEach(function (btn) {
            btn.addEventListener('click', function () { openModal(btn); });
        });
        modalCloseBtn.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', function (e) {
            if (e.target === modalOverlay) closeModal();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });
    }

    /* ── ADD TO CART — AJAX with toast (index.php) ───────── */
    const cartToast = document.getElementById('cartToast');

    if (cartToast) {
        let toastTimer;

        function showToast(msg) {
            cartToast.textContent = msg;
            cartToast.classList.add('show');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(function () {
                cartToast.classList.remove('show');
            }, 2800);
        }

        document.querySelectorAll('.ajax-cart-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const qtyInput = form.querySelector('[name="quantity"]');
                const qty      = parseInt(qtyInput.value, 10);

                if (!qty || qty < 1) {
                    showToast('⚠️ Please enter a quantity of at least 1.');
                    return;
                }

                const data = new FormData(form);

                fetch('cart_add.php', {
                    method: 'POST',
                    body:   data
                }).then(function (response) {
                    if (response.status === 401) {
                        showToast('🔒 Please log in to order.');
                        setTimeout(function () {
                            window.location.href = 'login.php';
                        }, 1200);
                        return;
                    }

                    showToast('🛒 Item added to cart successfully!');
                    qtyInput.value = 0;

                    fetch('cart_count.php')
                        .then(function (r) { return r.text(); })
                        .then(updateCartBadge)
                        .catch(function () {});
                }).catch(function () {
                    showToast('❌ Something went wrong. Please try again.');
                });
            });
        });
    }

})();
