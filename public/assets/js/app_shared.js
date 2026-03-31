/**
 * app_shared.js - Lógica JS compartida para cliente y admin
 * Extraído de app.js para evitar conflictos entre layouts.
 */

// ==================== CONFIG ====================
const CONFIG = {
    weatherApi: 'https://api.open-meteo.com/v1/forecast',
    geocodingApi: 'https://geocoding-api.open-meteo.com/v1/search',
    currencyApi: 'https://cdn.moneyconvert.net/api/latest.json',
    basePath: '/aventuras'
};

// ==================== DOM HELPERS ====================
const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => document.querySelectorAll(sel);

// ==================== BÚSQUEDA DE DESTINOS ====================
const DestinationSearch = {
    async search(destination) {
        if (!destination || destination.trim().length < 2) return;

        const resultsContainer = $('#search-results');
        if (!resultsContainer) return;

        resultsContainer.innerHTML = '<div class="text-center p-6"><div class="loading-spinner"></div><p class="text-muted mt-4">Buscando ' + this.escapeHtml(destination) + '...</p></div>';

        try {
            const geoData = await this.fetchGeocode(destination);
            if (!geoData) {
                resultsContainer.innerHTML = '<div class="alert alert-warning">No se encontraron resultados para "' + this.escapeHtml(destination) + '". Intenta con otro destino.</div>';
                return;
            }

            const weatherData = await this.fetchWeather(geoData.latitude, geoData.longitude);
            const currencyData = await this.fetchCurrency(geoData.country_code);

            this.saveToLocalStorage(destination, geoData, weatherData, currencyData);
            this.renderResults(destination, geoData, weatherData, currencyData);

        } catch (error) {
            console.error('Error de búsqueda:', error);
            resultsContainer.innerHTML = '<div class="alert alert-error">Ocurrió un error al buscar. Por favor intenta de nuevo.</div>';
        }
    },

    async fetchGeocode(name) {
        const res = await fetch(`${CONFIG.geocodingApi}?name=${encodeURIComponent(name)}&count=1&language=es`);
        const data = await res.json();
        return data.results?.[0] || null;
    },

    async fetchWeather(lat, lon) {
        const res = await fetch(`${CONFIG.weatherApi}?latitude=${lat}&longitude=${lon}&current_weather=true&timezone=auto`);
        return await res.json();
    },

    async fetchCurrency(countryCode) {
        try {
            const res = await fetch(CONFIG.currencyApi);
            const data = await res.json();
            const currencyMap = {
                'US': 'USD', 'EU': 'EUR', 'GB': 'GBP', 'JP': 'JPY', 'MX': 'MXN',
                'CO': 'COP', 'PE': 'PEN', 'BR': 'BRL', 'AR': 'ARS', 'CL': 'CLP',
                'CR': 'CRC', 'EC': 'USD', 'DO': 'DOP', 'GR': 'EUR', 'FR': 'EUR',
                'IT': 'EUR', 'ES': 'EUR', 'DE': 'EUR', 'TH': 'THB', 'ID': 'IDR',
                'IN': 'INR', 'CH': 'CHF', 'AU': 'AUD', 'CA': 'CAD', 'NZ': 'NZD',
                'TR': 'TRY', 'ZA': 'ZAR', 'KR': 'KRW', 'CN': 'CNY', 'RU': 'RUB',
            };
            const currency = currencyMap[countryCode?.toUpperCase()] || 'USD';
            const rate = data.rates?.[currency] || 1;
            return { currency, rate, base: data.base || 'USD' };
        } catch {
            return { currency: 'USD', rate: 1, base: 'USD' };
        }
    },

    saveToLocalStorage(destination, geo, weather, currency) {
        const searches = JSON.parse(localStorage.getItem('aventuras_searches') || '[]');
        searches.unshift({
            destination,
            geo,
            weather: weather?.current_weather,
            currency,
            timestamp: Date.now()
        });
        localStorage.setItem('aventuras_searches', JSON.stringify(searches.slice(0, 10)));
    },

    getWeatherDescription(code) {
        const descriptions = {
            0: 'Cielo Despejado', 1: 'Mayormente Despejado', 2: 'Parcialmente Nublado', 3: 'Nublado',
            45: 'Niebla', 48: 'Niebla Densa', 51: 'Llovizna Ligera', 53: 'Llovizna Moderada',
            55: 'Llovizna Densa', 61: 'Lluvia Ligera', 63: 'Lluvia Moderada', 65: 'Lluvia Fuerte',
            71: 'Nieve Ligera', 73: 'Nieve Moderada', 75: 'Nieve Fuerte', 80: 'Chubascos Ligeros',
            81: 'Chubascos Moderados', 82: 'Chubascos Fuertes', 95: 'Tormenta Eléctrica',
        };
        return descriptions[code] || 'Desconocido';
    },

    getWeatherIcon(code) {
        if (code <= 1) return '☀️';
        if (code <= 3) return '⛅';
        if (code <= 48) return '🌫️';
        if (code <= 55) return '🌧️';
        if (code <= 65) return '🌧️';
        if (code <= 75) return '❄️';
        if (code <= 82) return '🌦️';
        return '⛈️';
    },

    getBestSeason(lat) {
        if (lat > 0) return 'Mayo — Septiembre';
        return 'Noviembre — Marzo';
    },

    getLanguage(countryCode) {
        const langMap = { 'US': 'Inglés', 'GB': 'Inglés', 'AU': 'Inglés', 'CA': 'Inglés, Francés', 'NZ': 'Inglés', 'MX': 'Español', 'CO': 'Español', 'PE': 'Español, Quechua', 'AR': 'Español', 'CL': 'Español' };
        return langMap[countryCode?.toUpperCase()] || 'Idioma local';
    },

    getCurrencyName(code) {
        const names = { 'USD': 'Dólar Americano', 'EUR': 'Euro', 'GBP': 'Libra Esterlina', 'JPY': 'Yen Japonés', 'MXN': 'Peso Mexicano', 'COP': 'Peso Colombiano', 'PEN': 'Sol Peruano' };
        return names[code] || code;
    },

    renderResults(destination, geo, weather, currency) {
        const container = $('#search-results');
        if (!container) return;
        const currentWeather = weather?.current_weather || {};
        const weatherIcon = this.getWeatherIcon(currentWeather.weathercode);
        const weatherDesc = this.getWeatherDescription(currentWeather.weathercode);
        const bestSeason = this.getBestSeason(geo.latitude);
        const language = this.getLanguage(geo.country_code);
        const currencyName = this.getCurrencyName(currency.currency);

        container.innerHTML = `...`;
    },

    escapeHtml(str) { const div = document.createElement('div'); div.textContent = str; return div.innerHTML; }
};

// ==================== VALIDACIONES DE FORMULARIOS ====================
const FormValidator = {
    validate(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => { this.clearError(field); if (!field.value.trim()) { this.showError(field, 'Este campo es obligatorio'); isValid = false; } });
        const emailFields = form.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => { if (field.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) { this.showError(field, 'Por favor ingresa un email válido'); isValid = false; } });
        return isValid;
    },
    showError(field, message) { field.style.borderColor = 'var(--error)'; const error = document.createElement('div'); error.className = 'form-error'; error.style.cssText = 'color: var(--error); font-size: 0.75rem; margin-top: 0.25rem;'; error.textContent = message; field.parentNode.appendChild(error); },
    clearError(field) { field.style.borderColor = ''; const error = field.parentNode.querySelector('.form-error'); if (error) error.remove(); }
};

// ==================== SUBIDA DE ARCHIVOS ====================
const FileUpload = {
    init() { $$('.upload-area').forEach(area => { const input = area.querySelector('input[type="file"]'); if (!input) return; area.addEventListener('click', () => input.click()); area.addEventListener('dragover', (e) => { e.preventDefault(); area.classList.add('drag-over'); }); area.addEventListener('dragleave', () => area.classList.remove('drag-over')); area.addEventListener('drop', (e) => { e.preventDefault(); area.classList.remove('drag-over'); input.files = e.dataTransfer.files; this.showFileName(area, input.files[0]); }); input.addEventListener('change', () => { if (input.files[0]) this.showFileName(area, input.files[0]); }); }); },
    showFileName(area, file) { const maxSize = 5 * 1024 * 1024; const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png']; if (!allowedTypes.includes(file.type)) { area.innerHTML = '<p class="text-error">❌ Solo se permiten archivos PDF, JPG, PNG</p>'; return; } if (file.size > maxSize) { area.innerHTML = '<p class="text-error">❌ El archivo excede el límite de 5MB</p>'; return; } const sizeKB = (file.size / 1024).toFixed(1); area.innerHTML = `<div class="text-primary">📄</div><p class="title-md">${file.name}</p><p class="text-muted body-sm">${sizeKB} KB</p>`; }
};

// ==================== INTERACCIONES UI ====================
const UI = {
    init() { this.initFlashMessages(); this.initForms(); this.initSearch(); this.initSmoothScroll(); FileUpload.init(); },
    initFlashMessages() { $$('.alert').forEach(alert => { setTimeout(() => { alert.style.opacity = '0'; alert.style.transform = 'translateY(-0.5rem)'; setTimeout(() => alert.remove(), 300); }, 5000); }); },
    initForms() { $$('form[data-validate]').forEach(form => { form.addEventListener('submit', (e) => { if (!FormValidator.validate(form)) { e.preventDefault(); } }); }); },
    initSearch() { const searchForm = $('#search-form'); if (searchForm) { searchForm.addEventListener('submit', (e) => { e.preventDefault(); const input = searchForm.querySelector('input[name="q"]'); if (input?.value.trim()) { DestinationSearch.search(input.value.trim()); } }); } const heroSearch = $('#hero-search-form'); if (heroSearch) { heroSearch.addEventListener('submit', (e) => { e.preventDefault(); const input = heroSearch.querySelector('input'); if (input?.value.trim()) { window.location.href = `${CONFIG.basePath}/search/results?q=${encodeURIComponent(input.value.trim())}`; } }); } },
    initSmoothScroll() { $$('a[href^="#"]').forEach(anchor => { anchor.addEventListener('click', (e) => { e.preventDefault(); const target = document.querySelector(anchor.getAttribute('href')); target?.scrollIntoView({ behavior: 'smooth' }); }); }); }
};

// Expose to global (no module system)
window.AV = window.AV || {};
window.AV.UI = UI;
window.AV.DestinationSearch = DestinationSearch;
window.AV.FileUpload = FileUpload;
