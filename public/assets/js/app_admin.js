/**
 * app_admin.js - inicialización específica para el layout admin
 */

document.addEventListener('DOMContentLoaded', function () {
    if (window.AV && window.AV.UI) {
        window.AV.UI.init();
    }

    // Funcionalidades administrativas específicas
    function toggleGroupByIndex(idx) {
        const sections = document.querySelectorAll('.group-section');
        sections.forEach((s, i) => { s.style.display = (i === idx ? 'block' : 'none'); });
    }

    document.querySelectorAll('[data-toggle-group]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const idx = parseInt(btn.getAttribute('data-toggle-group'), 10);
            if (!isNaN(idx)) toggleGroupByIndex(idx);
        });
    });
});
