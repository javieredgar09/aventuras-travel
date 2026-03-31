/* client.js — Aventuras Travel Client Portal */

document.addEventListener('DOMContentLoaded', function () {

    /* ======= User dropdown toggle ======= */
    const userBtn = document.getElementById('user-menu-btn');
    const userDrop = document.getElementById('user-dropdown');
    if (userBtn && userDrop) {
        userBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            userDrop.classList.toggle('hidden');
        });
        document.addEventListener('click', function () {
            userDrop.classList.add('hidden');
        });
    }

    /* ======= Flash auto-dismiss ======= */
    document.querySelectorAll('[data-flash]').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 400);
        }, 5000);
    });

    /* ======= Countdown timer ======= */
    const countdownEl = document.getElementById('countdown-days');
    if (countdownEl) {
        const target = countdownEl.dataset.target;
        if (target) {
            var diff = Math.max(0, Math.ceil((new Date(target) - new Date()) / 86400000));
            countdownEl.textContent = diff;
        }
    }

    /* ======= File upload label ======= */
    document.querySelectorAll('input[type="file"]').forEach(function (input) {
        input.addEventListener('change', function () {
            var label = this.closest('label') || this.parentElement;
            var span = label ? label.querySelector('.file-name') : null;
            if (span && this.files.length) {
                span.textContent = this.files[0].name;
            }
        });
    });

    /* ======= Modal open/close ======= */
    document.querySelectorAll('[data-modal-target]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            var modal = document.getElementById(this.dataset.modalTarget);
            if (modal) modal.classList.remove('hidden');
        });
    });
    document.querySelectorAll('[data-modal-close]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var modal = this.closest('[id]');
            if (modal) modal.classList.add('hidden');
        });
    });

    /* ======= Mobile bottom nav active highlight ======= */
    var path = window.location.pathname;
    document.querySelectorAll('.mobile-nav-link').forEach(function (link) {
        if (path.indexOf(link.getAttribute('href')) !== -1) {
            link.classList.add('text-primary');
        }
    });
});
