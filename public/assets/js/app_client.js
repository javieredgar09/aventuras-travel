/**
 * app_client.js - inicialización específica para el layout cliente
 */

document.addEventListener('DOMContentLoaded', function () {
    if (window.AV && window.AV.UI) {
        window.AV.UI.init();
    }

    // Manejar parámetros de búsqueda en la URL (si vienen de enlaces internos)
    try {
        const params = new URLSearchParams(window.location.search);
        const q = params.get('q');
        if (q && window.AV && window.AV.DestinationSearch) {
            // Si existe el contenedor de resultados lo mostramos, sino guardamos en local y navegamos
            const results = document.getElementById('search-results');
            if (results) {
                window.AV.DestinationSearch.search(q);
            }
        }
    } catch (e) {
        console.warn('No se pudo analizar query params', e);
    }

    // Inicializaciones client-specific pequeñas
    // abrir modal de subir comprobante si ?upload=1
    const params = new URLSearchParams(window.location.search);
    if (params.get('upload') === '1') {
        const uploadBtn = document.querySelector('.open-upload-panel');
        uploadBtn?.click();
    }
});
