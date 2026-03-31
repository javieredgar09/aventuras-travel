/**
 * Aventuras Travel Pucallpa - JavaScript Principal
 * Búsqueda de destinos, validaciones, interacciones UI
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
        const langMap = {
            'US': 'Inglés', 'GB': 'Inglés', 'AU': 'Inglés', 'CA': 'Inglés, Francés', 'NZ': 'Inglés',
            'MX': 'Español', 'CO': 'Español', 'PE': 'Español, Quechua', 'AR': 'Español',
            'CL': 'Español', 'EC': 'Español', 'CR': 'Español', 'DO': 'Español', 'ES': 'Español',
            'FR': 'Francés', 'IT': 'Italiano', 'DE': 'Alemán', 'JP': 'Japonés',
            'KR': 'Coreano', 'CN': 'Mandarín', 'BR': 'Portugués', 'TH': 'Tailandés',
            'ID': 'Indonesio', 'IN': 'Hindi, Inglés', 'TR': 'Turco', 'RU': 'Ruso',
            'GR': 'Griego', 'CH': 'Alemán, Francés, Italiano', 'ZA': 'Inglés, Afrikáans',
        };
        return langMap[countryCode?.toUpperCase()] || 'Idioma local';
    },

    getCurrencyName(code) {
        const names = {
            'USD': 'Dólar Americano', 'EUR': 'Euro', 'GBP': 'Libra Esterlina', 'JPY': 'Yen Japonés',
            'MXN': 'Peso Mexicano', 'COP': 'Peso Colombiano', 'PEN': 'Sol Peruano', 'BRL': 'Real Brasileño',
            'ARS': 'Peso Argentino', 'CLP': 'Peso Chileno', 'THB': 'Baht Tailandés', 'IDR': 'Rupia Indonesia',
            'INR': 'Rupia India', 'CHF': 'Franco Suizo', 'AUD': 'Dólar Australiano', 'CAD': 'Dólar Canadiense',
            'TRY': 'Lira Turca', 'KRW': 'Won Surcoreano', 'CNY': 'Yuan Chino', 'RUB': 'Rublo Ruso',
        };
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

        container.innerHTML = `
            <div class="animate-fadeInUp">
                <div class="label-sm mb-2" style="color:var(--primary);">TOP CHOICE ${new Date().getFullYear()}</div>
                <h1 class="display-md mb-2">${this.escapeHtml(geo.name)}, ${this.escapeHtml(geo.country || '')}</h1>
                <p class="text-muted body-lg mb-6">${this.escapeHtml(geo.admin1 || '')}${geo.admin1 ? ', ' : ''}${this.escapeHtml(geo.country || '')}</p>

                <div class="grid" style="grid-template-columns: 1.6fr 1fr; gap: var(--spacing-6);">
                    <!-- Imagen del destino + Mapa -->
                    <div class="flex flex-col gap-6">
                        <div class="card-image" style="height:20rem;border-radius:var(--radius-xl);overflow:hidden;position:relative;">
                            <img src="https://source.unsplash.com/800x400/?${encodeURIComponent(geo.name + ' landscape')}" 
                                 alt="${this.escapeHtml(geo.name)}" 
                                 style="width:100%;height:100%;object-fit:cover;"
                                 onerror="this.src='https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800&q=80'">
                        </div>

                        <!-- Info Cards -->
                        <div class="grid grid-3 gap-4">
                            <div class="card" style="text-align:center;padding:1.5rem;">
                                <div style="font-size:1.5rem;">${weatherIcon}</div>
                                <div class="label-sm text-muted mt-2">CLIMA LOCAL</div>
                                <div class="headline-md mt-1">${Math.round(currentWeather.temperature || 0)}°C</div>
                                <div class="body-sm text-muted">${weatherDesc}</div>
                                <div class="body-sm text-muted mt-1">Viento: ${currentWeather.windspeed || 0} km/h</div>
                            </div>
                            <div class="card" style="text-align:center;padding:1.5rem;">
                                <div style="font-size:1.5rem;">📅</div>
                                <div class="label-sm text-muted mt-2">MEJOR TEMPORADA</div>
                                <div class="title-md mt-1">${bestSeason}</div>
                                <div class="body-sm text-muted mt-1">Temporada seca con cielos despejados.</div>
                            </div>
                            <div class="card" style="text-align:center;padding:1.5rem;">
                                <div style="font-size:1.5rem;">🌐</div>
                                <div class="label-sm text-muted mt-2">DATOS ESENCIALES</div>
                                <div class="body-sm mt-1"><strong>MONEDA:</strong> ${currencyName} (${currency.currency})</div>
                                <div class="body-sm mt-1"><strong>IDIOMA:</strong> ${language}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Mapa + Tipo de cambio -->
                    <div class="flex flex-col gap-6">
                        <div class="map-container" style="height:16rem;border-radius:var(--radius-xl);overflow:hidden;">
                            <iframe src="https://maps.google.com/maps?q=${geo.latitude},${geo.longitude}&z=12&output=embed"
                                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                    style="width:100%;height:100%;border:0;"></iframe>
                        </div>
                        <div class="text-center body-sm text-muted">
                            <span>📍 Mapa Interactivo</span> · <span>Haz clic para explorar ${this.escapeHtml(geo.name)}</span>
                        </div>

                        <div class="card" style="padding:1.5rem;">
                            <div class="label-sm text-primary mb-2">TIPO DE CAMBIO</div>
                            <div class="headline-md">1.00 ${currency.base} = ${currency.rate.toFixed(2)} ${currency.currency}</div>
                            <div class="body-sm text-muted mt-1">${currencyName}</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    },

    escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
};

// ==================== VALIDACIONES DE FORMULARIOS ====================
const FormValidator = {
    validate(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');

        requiredFields.forEach(field => {
            this.clearError(field);
            if (!field.value.trim()) {
                this.showError(field, 'Este campo es obligatorio');
                isValid = false;
            }
        });

        const emailFields = form.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => {
            if (field.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
                this.showError(field, 'Por favor ingresa un email válido');
                isValid = false;
            }
        });

        return isValid;
    },

    showError(field, message) {
        field.style.borderColor = 'var(--error)';
        const error = document.createElement('div');
        error.className = 'form-error';
        error.style.cssText = 'color: var(--error); font-size: 0.75rem; margin-top: 0.25rem;';
        error.textContent = message;
        field.parentNode.appendChild(error);
    },

    clearError(field) {
        field.style.borderColor = '';
        const error = field.parentNode.querySelector('.form-error');
        if (error) error.remove();
    }
};

// ==================== SUBIDA DE ARCHIVOS ====================
const FileUpload = {
    init() {
        $$('.upload-area').forEach(area => {
            const input = area.querySelector('input[type="file"]');
            if (!input) return;

            area.addEventListener('click', () => input.click());
            area.addEventListener('dragover', (e) => {
                e.preventDefault();
                area.classList.add('drag-over');
            });
            area.addEventListener('dragleave', () => area.classList.remove('drag-over'));
            area.addEventListener('drop', (e) => {
                e.preventDefault();
                area.classList.remove('drag-over');
                input.files = e.dataTransfer.files;
                this.showFileName(area, input.files[0]);
            });
            input.addEventListener('change', () => {
                if (input.files[0]) this.showFileName(area, input.files[0]);
            });
        });
    },

    showFileName(area, file) {
        const maxSize = 5 * 1024 * 1024;
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];

        if (!allowedTypes.includes(file.type)) {
            area.innerHTML = '<p class="text-error">❌ Solo se permiten archivos PDF, JPG, PNG</p>';
            return;
        }
        if (file.size > maxSize) {
            area.innerHTML = '<p class="text-error">❌ El archivo excede el límite de 5MB</p>';
            return;
        }

        const sizeKB = (file.size / 1024).toFixed(1);
        area.innerHTML = `
            <div class="text-primary">📄</div>
            <p class="title-md">${file.name}</p>
            <p class="text-muted body-sm">${sizeKB} KB</p>
        `;
    }
};

// ==================== INTERACCIONES UI ====================
const UI = {
    init() {
        this.initFlashMessages();
        this.initForms();
        this.initSearch();
        this.initSmoothScroll();
        FileUpload.init();
    },

    initFlashMessages() {
        $$('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-0.5rem)';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    },

    initForms() {
        $$('form[data-validate]').forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!FormValidator.validate(form)) {
                    e.preventDefault();
                }
            });
        });
    },

    initSearch() {
        const searchForm = $('#search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const input = searchForm.querySelector('input[name="q"]');
                if (input?.value.trim()) {
                    DestinationSearch.search(input.value.trim());
                }
            });
        }

        const heroSearch = $('#hero-search-form');
        if (heroSearch) {
            heroSearch.addEventListener('submit', (e) => {
                e.preventDefault();
                const input = heroSearch.querySelector('input');
                if (input?.value.trim()) {
                    window.location.href = `${CONFIG.basePath}/search/results?q=${encodeURIComponent(input.value.trim())}`;
                }
            });
        }
    },

    initSmoothScroll() {
        $$('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                target?.scrollIntoView({ behavior: 'smooth' });
            });
        });
    }
};

// ==================== INIT ====================
document.addEventListener('DOMContentLoaded', () => {
    UI.init();

    const urlParams = new URLSearchParams(window.location.search);
    const q = urlParams.get('q');
    if (q && $('#search-results')) {
        DestinationSearch.search(q);
    }
    // --- Group toggle for admin payments table ---
    function toggleGroupByIndex(idx, expand = null) {
        const rows = document.querySelectorAll('tr[data-group-index-row="' + idx + '"]');
        const btn = document.querySelector('.group-toggle[data-group-toggle="' + idx + '"]');
        const icon = btn?.querySelector('.material-symbols-outlined');
        if (!rows.length || !btn) return;
        const shouldExpand = expand === null ? rows[0].classList.contains('hidden') : !!expand;
        rows.forEach(r => {
            if (shouldExpand) r.classList.remove('hidden'); else r.classList.add('hidden');
        });
        if (icon) {
            if (shouldExpand) icon.classList.add('rotated'); else icon.classList.remove('rotated');
        }
    }

    document.querySelectorAll('.group-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const idx = this.dataset.groupToggle;
            toggleGroupByIndex(idx);
        });
        // inicial: si data-expanded = 1, expandir
        if (btn.dataset.expanded === '1') {
            toggleGroupByIndex(btn.dataset.groupToggle, true);
        }
    });

    // Toggle all groups
    const toggleAllBtn = document.getElementById('toggle-all-groups');
    if (toggleAllBtn) {
        toggleAllBtn.addEventListener('click', () => {
            const anyHidden = !!document.querySelector('tr.group-row.hidden');
            document.querySelectorAll('.group-toggle').forEach(btn => {
                toggleGroupByIndex(btn.dataset.groupToggle, anyHidden);
            });
            toggleAllBtn.textContent = anyHidden ? 'Colapsar todo' : 'Expandir todo';
        });
    }

    // --- Confirm modal handling for forms with data-confirm ---
    const confirmModal = document.createElement('div');
    confirmModal.id = 'confirm-modal';
    confirmModal.className = 'fixed inset-0 bg-petroleo/60 z-50 hidden items-center justify-center p-4';
    confirmModal.innerHTML = `
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
            <div class="p-4 border-b border-petroleo/5 flex justify-between items-center bg-superficie/30">
                <h3 class="font-black text-petroleo">Confirmación</h3>
                <button id="confirm-cancel" class="text-petroleo/40 hover:text-red-500"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div class="p-4">
                <p id="confirm-message" class="text-petroleo mb-4">¿Confirmar acción?</p>
                <div class="flex gap-3 justify-end">
                    <button id="confirm-decline" class="px-4 py-2 rounded text-sm border">Cancelar</button>
                    <button id="confirm-accept" class="px-4 py-2 rounded text-sm bg-emerald-500 text-white">Confirmar</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(confirmModal);

    let _pendingForm = null;
    const openConfirm = (message, form) => {
        _pendingForm = form || null;
        document.getElementById('confirm-message').textContent = message || '¿Confirmar acción?';
        confirmModal.classList.remove('hidden');
        confirmModal.classList.add('flex');
    };
    const closeConfirm = () => {
        confirmModal.classList.add('hidden');
        confirmModal.classList.remove('flex');
        _pendingForm = null;
    };

    document.getElementById('confirm-cancel').addEventListener('click', closeConfirm);
    document.getElementById('confirm-decline').addEventListener('click', closeConfirm);
    document.getElementById('confirm-accept').addEventListener('click', () => {
        if (_pendingForm) _pendingForm.submit();
        closeConfirm();
    });

    // Attach to forms/buttons with data-confirm
    document.querySelectorAll('form[data-confirm], button[data-confirm], a[data-confirm]').forEach(el => {
        if (el.tagName.toLowerCase() === 'form') {
            el.addEventListener('submit', (e) => {
                e.preventDefault();
                openConfirm(el.dataset.confirm, el);
            });
        } else if (el.tagName.toLowerCase() === 'button' || el.tagName.toLowerCase() === 'a') {
            el.addEventListener('click', (e) => {
                e.preventDefault();
                const targetFormSelector = el.dataset.targetForm;
                const form = targetFormSelector ? document.querySelector(targetFormSelector) : el.closest('form');
                openConfirm(el.dataset.confirm, form);
            });
        }
    });
});
