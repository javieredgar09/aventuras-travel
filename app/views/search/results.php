<!-- BUSCADOR DE DESTINOS – TAILWIND – UNSPLASH API -->

<!-- Barra de búsqueda SIMPLIFICADA -->
<section class="bg-superficie px-8 py-10 mt-4 mx-4 rounded-xl">
    <div class="max-w-4xl mx-auto">
        <label class="block text-xs font-bold uppercase tracking-widest text-turquesa-dark mb-3 px-1">Exploración Global</label>
        <div class="flex gap-3">
            <div class="relative flex-grow">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-petroleo/30">search</span>
                <input id="searchInput" type="text" value="<?= htmlspecialchars($query ?? '') ?>"
                    class="w-full pl-12 pr-4 py-4 bg-white border-none rounded-xl focus:ring-2 focus:ring-turquesa/30 text-lg font-medium text-petroleo shadow-sm" 
                    placeholder="¿A dónde quieres viajar? Ej: Cusco, Cancún, París...">
            </div>
            <button onclick="searchDestination()" class="px-10 py-4 bg-gradient-to-r from-turquesa-dark to-turquesa text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all active:scale-95 whitespace-nowrap flex items-center gap-2">
                <span class="material-symbols-outlined">travel_explore</span>
                Buscar
            </button>
        </div>
    </div>
</section>

<!-- Contenido destino -->
<section class="px-8 py-12 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- COLUMNA IZQUIERDA (8 cols) -->
        <div class="lg:col-span-8 space-y-8">

            <!-- Hero Image -->
            <div class="relative h-[480px] rounded-2xl overflow-hidden group" id="heroContainer">
                <!-- Skeleton loader -->
                <div id="heroSkeleton" class="absolute inset-0 bg-gradient-to-br from-petroleo/10 to-turquesa/10 animate-pulse flex items-center justify-center">
                    <span class="material-symbols-outlined text-6xl text-turquesa/30 animate-bounce">landscape</span>
                </div>
                <img id="heroImage" alt="" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-0"
                    onload="this.classList.remove('opacity-0');this.classList.add('opacity-100');document.getElementById('heroSkeleton').style.display='none';">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-8 left-8 text-white">
                    <span class="bg-turquesa/20 backdrop-blur-sm text-turquesa px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-4 inline-block">Top Choice 2026</span>
                    <h1 class="text-5xl font-black tracking-tight mb-2" id="destName"><?= htmlspecialchars($query ?? 'Cusco, Peru') ?></h1>
                    <p class="text-xl text-white/80 max-w-xl" id="destDesc">Cargando descripción...</p>
                </div>
                <!-- Unsplash credit -->
                <div class="absolute bottom-3 right-4 text-[10px] text-white/40" id="unsplashCredit"></div>
            </div>

            <!-- Info Bento Grid: Clima + Temporada + Esenciales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Clima -->
                <div class="bg-superficie p-6 rounded-xl flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-turquesa-dark text-3xl">thermostat</span>
                        <h3 class="font-bold text-turquesa-dark">Clima Local</h3>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl font-black text-petroleo" id="weatherTemp">--°C</span>
                        <span class="text-sm text-petroleo/50 mb-1" id="weatherDesc">Cargando...</span>
                    </div>
                    <div class="text-xs font-medium text-petroleo/40 space-y-1">
                        <p id="weatherHumidity">Humedad: --%</p>
                        <p id="weatherWind">Viento: -- km/h</p>
                    </div>
                </div>
                <!-- Temporada -->
                <div class="bg-superficie p-6 rounded-xl flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-turquesa-dark text-3xl">calendar_month</span>
                        <h3 class="font-bold text-turquesa-dark">Mejor Temporada</h3>
                    </div>
                    <div class="text-xl font-bold text-petroleo" id="peakSeason">Mayo — Septiembre</div>
                    <p class="text-xs text-petroleo/40 leading-relaxed" id="seasonTip">Temporada seca con cielos claros, ideal para trekking y turismo.</p>
                </div>
                <!-- Esenciales -->
                <div class="bg-superficie p-6 rounded-xl flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-turquesa-dark text-3xl">payments</span>
                        <h3 class="font-bold text-turquesa-dark">Información Esencial</h3>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs uppercase tracking-widest text-petroleo/40">Moneda</span>
                            <span class="font-bold text-sm text-petroleo" id="currencyInfo">--</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs uppercase tracking-widest text-petroleo/40">Idioma</span>
                            <span class="font-bold text-sm text-petroleo" id="languageInfo">--</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs uppercase tracking-widest text-petroleo/40">Cambio</span>
                            <span class="font-bold text-sm text-turquesa-dark" id="exchangeRate">--</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Experiencias Imperdibles -->
            <div class="space-y-6">
                <h2 class="text-2xl font-black tracking-tight text-turquesa-dark">Experiencias Imperdibles</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="experiencesGrid">
                    <!-- JS carga experiencias -->
                </div>
            </div>

            <!-- Galería Unsplash -->
            <div class="space-y-6">
                <h2 class="text-2xl font-black tracking-tight text-turquesa-dark">Galería del Destino</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="galleryGrid">
                    <!-- JS carga galería de Unsplash -->
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA (4 cols) -->
        <aside class="lg:col-span-4 space-y-8">
            <!-- Mapa -->
            <div class="bg-superficie rounded-xl overflow-hidden h-[300px] relative" id="mapContainer">
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="material-symbols-outlined text-turquesa text-6xl mb-2">map</span>
                    <p class="font-bold text-turquesa-dark">Mapa Interactivo</p>
                    <p class="text-xs text-petroleo/40">Clic para explorar</p>
                </div>
            </div>

            <!-- Hoteles -->
            <div class="bg-superficie p-8 rounded-xl">
                <h3 class="text-xl font-black text-turquesa-dark mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined">hotel</span>
                    Hospedajes Recomendados
                </h3>
                <div class="space-y-5" id="hotelsGrid">
                    <div class="text-center py-8">
                        <span class="material-symbols-outlined text-4xl text-petroleo/20 animate-spin">progress_activity</span>
                        <p class="text-sm text-petroleo/40 mt-2">Buscando hoteles...</p>
                    </div>
                </div>
                <button class="w-full mt-6 py-3 bg-turquesa-dark text-white rounded-xl font-bold hover:bg-petroleo transition-all">
                    Ver Todos los Hospedajes
                </button>
            </div>
        </aside>
    </div>
</section>

<script>
const API_BASE = '<?= Router::url("/api") ?>';
const query = '<?= htmlspecialchars($query ?? "Cusco") ?>';

// Banco de imágenes Unsplash de alta calidad por destino (carga instantánea)
const DEST_IMAGES = {
    'cusco': { hero: 'photo-1526392060635-9d6019884377', gallery: ['photo-1580619305218-8423a7ef79b4','photo-1548820237-f5bbb9a6ee6d','photo-1531065208531-4036c0dba3ca','photo-1587595431973-160d0d94add1','photo-1569982175971-d92b01cf8694','photo-1531065208531-4036c0dba3ca'] },
    'machu': { hero: 'photo-1587595431973-160d0d94add1', gallery: ['photo-1526392060635-9d6019884377','photo-1580619305218-8423a7ef79b4','photo-1548820237-f5bbb9a6ee6d'] },
    'cancun': { hero: 'photo-1510414842594-a61c69b5ae57', gallery: ['photo-1506929562872-bb421503ef21','photo-1507525428034-b723cf961d3e','photo-1519046904884-53103b34b206','photo-1471922694854-ff1b63b20054','photo-1476514525535-07fb3b4ae5f1','photo-1544551763-46a013bb70d5'] },
    'cancún': { hero: 'photo-1510414842594-a61c69b5ae57', gallery: ['photo-1506929562872-bb421503ef21','photo-1507525428034-b723cf961d3e','photo-1519046904884-53103b34b206','photo-1471922694854-ff1b63b20054','photo-1476514525535-07fb3b4ae5f1'] },
    'paris': { hero: 'photo-1502602898657-3e91760cbb34', gallery: ['photo-1551634979-2b11f8c218da','photo-1499856871958-5b9627545d1a','photo-1431274172761-fca41d930114','photo-1549144511-f099e773c147','photo-1520939817895-060bdaf4fe1b','photo-1478391679764-b2d8b3cd1e94'] },
    'punta cana': { hero: 'photo-1506929562872-bb421503ef21', gallery: ['photo-1510414842594-a61c69b5ae57','photo-1507525428034-b723cf961d3e','photo-1519046904884-53103b34b206','photo-1544551763-46a013bb70d5','photo-1471922694854-ff1b63b20054'] },
    'bali': { hero: 'photo-1537996194471-e657df975ab4', gallery: ['photo-1555400038-63f5ba517a47','photo-1558862107-d49ef2a04d72','photo-1573790387438-4da905039392','photo-1604999333679-b86d54738315','photo-1544644181-1484b3fdfc62'] },
    'roma': { hero: 'photo-1552832230-c0197dd311b5', gallery: ['photo-1529260830199-42c24126f198','photo-1531572753322-ad063cecc140','photo-1515542622106-78bda8ba0e5b'] },
    'tokio': { hero: 'photo-1540959733332-eab4deabeeaf', gallery: ['photo-1536098561742-ca998e48cbcc','photo-1503899036084-c55cdd92da26','photo-1528360983277-13d401cdc186'] },
    'new york': { hero: 'photo-1485871981521-5b1fd3805eee', gallery: ['photo-1534430480872-3498386e7856','photo-1496442226666-8d4d0e62e6e9','photo-1522083165195-3424ed14428d'] },
    'miami': { hero: 'photo-1533106497176-45ae19e68ba2', gallery: ['photo-1514214246283-d427a95c5d2f','photo-1535498730771-e735b998cd64'] },
    'londres': { hero: 'photo-1513635269975-59663e0ac1ad', gallery: ['photo-1526129318478-62ed807ebdf9','photo-1529655683826-aba9b3e77383','photo-1505761671935-60b3a7427bad'] },
    'orlando': { hero: 'photo-1575089976121-8ed7b2a54265', gallery: ['photo-1597466599360-3b9775f55145'] },
    'cartagena': { hero: 'photo-1583531352515-8884af8ae22d', gallery: ['photo-1559128010-7c1ad6e1b6a5'] },
    'rio': { hero: 'photo-1483729558449-99ef09a8c325', gallery: ['photo-1516306580123-e6e52b1b7b5f','photo-1544989164-31dc3291c2b1'] },
    'buenos aires': { hero: 'photo-1589909202802-8f4aadce1849', gallery: [] },
    'lima': { hero: 'photo-1531968455001-5c5272a67c71', gallery: [] },
    'iquitos': { hero: 'photo-1563514227147-6d2ff665a6a0', gallery: [] },
    'bogota': { hero: 'photo-1568632234157-ce7aecd03d0d', gallery: [] },
};

function getUnsplashUrl(photoId, w = 1200) {
    return `https://images.unsplash.com/${photoId}?w=${w}&q=80&auto=format&fit=crop`;
}

function findDestImages(dest) {
    const d = dest.toLowerCase();
    for (const [key, val] of Object.entries(DEST_IMAGES)) {
        if (d.includes(key)) return val;
    }
    return null;
}

document.addEventListener('DOMContentLoaded', () => {
    loadDestinationData(query);
});

function searchDestination() {
    const q = document.getElementById('searchInput').value.trim();
    if (q) window.location.href = '<?= Router::url("/search/results") ?>?q=' + encodeURIComponent(q);
}

document.getElementById('searchInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') searchDestination();
});

async function loadDestinationData(dest) {
    loadHeroImage(dest);
    loadWeather(dest);
    loadCurrency(dest);
    loadHotels(dest);
    loadExperiences(dest);
    loadMap(dest);
    setDestDescription(dest);
    loadGallery(dest);
    loadSeasonInfo(dest);
}

/**
 * Hero Image — Unsplash curada o SerpAPI fallback
 */
function loadHeroImage(dest) {
    const heroImg = document.getElementById('heroImage');
    const credit = document.getElementById('unsplashCredit');
    const mapped = findDestImages(dest);
    
    if (mapped) {
        heroImg.src = getUnsplashUrl(mapped.hero, 1400);
        credit.innerHTML = 'Foto: <a href="https://unsplash.com" target="_blank" class="underline">Unsplash</a>';
    } else {
        // Fallback: SerpAPI proxy para destinos no mapeados
        heroImg.src = API_BASE + '/images?q=' + encodeURIComponent(dest + ' travel landscape panoramic');
        credit.textContent = '';
    }
}

/**
 * Galería — Unsplash curada o SerpAPI para no mapeados
 */
function loadGallery(dest) {
    const container = document.getElementById('galleryGrid');
    const mapped = findDestImages(dest);
    
    if (mapped && mapped.gallery.length > 0) {
        container.innerHTML = mapped.gallery.map((id, i) => `
            <div class="relative overflow-hidden rounded-xl group ${i === 0 ? 'col-span-2 row-span-2' : ''}">
                <img src="${getUnsplashUrl(id, i === 0 ? 800 : 400)}" alt="${dest}" 
                    class="w-full h-full object-cover min-h-[160px] ${i === 0 ? 'min-h-[340px]' : ''} group-hover:scale-105 transition-transform duration-500" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
        `).join('');
    } else {
        container.innerHTML = `
            <div class="col-span-3 text-center py-12 bg-superficie rounded-xl">
                <span class="material-symbols-outlined text-4xl text-turquesa/30 mb-2">photo_library</span>
                <p class="text-petroleo/40 text-sm">Busca un destino popular para ver la galería</p>
            </div>
        `;
    }
}

async function loadWeather(dest) {
    try {
        const geocode = await fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(dest)}&count=1`);
        const geoData = await geocode.json();
        if (geoData.results && geoData.results.length > 0) {
            const loc = geoData.results[0];
            const weather = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${loc.latitude}&longitude=${loc.longitude}&current=temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code`);
            const wData = await weather.json();
            const cur = wData.current;
            document.getElementById('weatherTemp').textContent = Math.round(cur.temperature_2m) + '°C';
            document.getElementById('weatherHumidity').textContent = 'Humedad: ' + cur.relative_humidity_2m + '%';
            document.getElementById('weatherWind').textContent = 'Viento: ' + Math.round(cur.wind_speed_10m) + ' km/h';

            const weatherCodes = {0:'Cielo despejado',1:'Parcialmente nublado',2:'Nublado',3:'Muy nublado',45:'Neblina',51:'Llovizna',61:'Lluvia ligera',63:'Lluvia',65:'Lluvia fuerte',71:'Nieve ligera',80:'Chubascos'};
            document.getElementById('weatherDesc').textContent = weatherCodes[cur.weather_code] || 'Variable';
        }
    } catch(e) { console.log('Weather error:', e); }
}

async function loadCurrency(dest) {
    try {
        const res = await fetch('https://cdn.moneyconvert.net/api/latest.json');
        const data = await res.json();
        const countryInfo = getCountryInfo(dest);
        document.getElementById('currencyInfo').textContent = countryInfo.currency;
        document.getElementById('languageInfo').textContent = countryInfo.language;
        if (data.rates && data.rates[countryInfo.code]) {
            document.getElementById('exchangeRate').textContent = '1 USD = ' + data.rates[countryInfo.code].toFixed(2) + ' ' + countryInfo.code;
        }
    } catch(e) { console.log('Currency error:', e); }
}

function getCountryInfo(dest) {
    const d = dest.toLowerCase();
    const map = {
        'cusco': { currency: 'Sol Peruano (PEN)', code: 'PEN', language: 'Español, Quechua' },
        'peru': { currency: 'Sol Peruano (PEN)', code: 'PEN', language: 'Español, Quechua' },
        'machu': { currency: 'Sol Peruano (PEN)', code: 'PEN', language: 'Español, Quechua' },
        'lima': { currency: 'Sol Peruano (PEN)', code: 'PEN', language: 'Español' },
        'iquitos': { currency: 'Sol Peruano (PEN)', code: 'PEN', language: 'Español' },
        'pucallpa': { currency: 'Sol Peruano (PEN)', code: 'PEN', language: 'Español' },
        'cancun': { currency: 'Peso Mexicano (MXN)', code: 'MXN', language: 'Español' },
        'cancún': { currency: 'Peso Mexicano (MXN)', code: 'MXN', language: 'Español' },
        'mexico': { currency: 'Peso Mexicano (MXN)', code: 'MXN', language: 'Español' },
        'paris': { currency: 'Euro (EUR)', code: 'EUR', language: 'Francés' },
        'francia': { currency: 'Euro (EUR)', code: 'EUR', language: 'Francés' },
        'roma': { currency: 'Euro (EUR)', code: 'EUR', language: 'Italiano' },
        'italia': { currency: 'Euro (EUR)', code: 'EUR', language: 'Italiano' },
        'madrid': { currency: 'Euro (EUR)', code: 'EUR', language: 'Español' },
        'barcelona': { currency: 'Euro (EUR)', code: 'EUR', language: 'Español, Catalán' },
        'punta cana': { currency: 'Peso Dominicano (DOP)', code: 'DOP', language: 'Español' },
        'dominicana': { currency: 'Peso Dominicano (DOP)', code: 'DOP', language: 'Español' },
        'bali': { currency: 'Rupia (IDR)', code: 'IDR', language: 'Indonesio' },
        'tokio': { currency: 'Yen Japonés (JPY)', code: 'JPY', language: 'Japonés' },
        'tokyo': { currency: 'Yen Japonés (JPY)', code: 'JPY', language: 'Japonés' },
        'new york': { currency: 'Dólar (USD)', code: 'USD', language: 'Inglés' },
        'miami': { currency: 'Dólar (USD)', code: 'USD', language: 'Inglés, Español' },
        'orlando': { currency: 'Dólar (USD)', code: 'USD', language: 'Inglés' },
        'london': { currency: 'Libra (GBP)', code: 'GBP', language: 'Inglés' },
        'londres': { currency: 'Libra (GBP)', code: 'GBP', language: 'Inglés' },
        'brasil': { currency: 'Real (BRL)', code: 'BRL', language: 'Portugués' },
        'rio': { currency: 'Real (BRL)', code: 'BRL', language: 'Portugués' },
        'bogota': { currency: 'Peso Colombiano (COP)', code: 'COP', language: 'Español' },
        'colombia': { currency: 'Peso Colombiano (COP)', code: 'COP', language: 'Español' },
        'cartagena': { currency: 'Peso Colombiano (COP)', code: 'COP', language: 'Español' },
        'santiago': { currency: 'Peso Chileno (CLP)', code: 'CLP', language: 'Español' },
        'chile': { currency: 'Peso Chileno (CLP)', code: 'CLP', language: 'Español' },
        'buenos aires': { currency: 'Peso Argentino (ARS)', code: 'ARS', language: 'Español' },
        'argentina': { currency: 'Peso Argentino (ARS)', code: 'ARS', language: 'Español' },
    };
    for (const [key, val] of Object.entries(map)) {
        if (d.includes(key)) return val;
    }
    return { currency: 'USD', code: 'USD', language: 'Varios' };
}

async function loadHotels(dest) {
    const container = document.getElementById('hotelsGrid');
    try {
        const res = await fetch(API_BASE + '/hotels?q=' + encodeURIComponent(dest));
        const data = await res.json();
        if (data.hotels && data.hotels.length > 0) {
            container.innerHTML = data.hotels.slice(0, 4).map(h => `
                <div class="flex gap-4 items-start">
                    <div class="w-16 h-16 rounded-lg bg-white overflow-hidden shrink-0 shadow-sm">
                        <img src="${h.image || ''}" alt="${h.name}" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\\'w-full h-full bg-turquesa/10 flex items-center justify-center\\'><span class=\\'material-symbols-outlined text-turquesa\\'>hotel</span></div>'">
                    </div>
                    <div class="flex-grow min-w-0">
                        <h4 class="font-bold text-sm text-petroleo truncate">${h.name}</h4>
                        <div class="flex items-center gap-1 text-turquesa mt-1">
                            ${'<span class="material-symbols-outlined text-[14px]" style="font-variation-settings:\'FILL\' 1">star</span>'.repeat(Math.min(Math.round(parseFloat(h.rating) || 4), 5))}
                            <span class="text-[11px] text-petroleo/50 ml-1">${h.rating || ''}</span>
                        </div>
                        <p class="text-xs text-turquesa-dark font-bold mt-1">${h.price || ''}</p>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p class="text-sm text-petroleo/40 text-center py-4">No se encontraron hoteles</p>';
        }
    } catch(e) {
        container.innerHTML = '<p class="text-sm text-petroleo/40 text-center py-4">Error al cargar hoteles</p>';
    }
}

async function loadExperiences(dest) {
    const container = document.getElementById('experiencesGrid');
    try {
        const res = await fetch(API_BASE + '/places?q=' + encodeURIComponent(dest));
        const data = await res.json();
        if (data.places && data.places.length > 0) {
            container.innerHTML = data.places.slice(0, 4).map(p => {
                let imgSrc = p.image || '';
                if (!imgSrc) imgSrc = API_BASE + '/images?q=' + encodeURIComponent(p.name + ' ' + dest + ' turismo');
                return `
                    <div class="group relative overflow-hidden rounded-xl bg-white shadow-sm">
                        <img src="${imgSrc}" alt="${p.name}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy"
                            onerror="this.src='https://images.unsplash.com/photo-1526392060635-9d6019884377?w=400&q=60'">
                        <div class="p-4">
                            <h4 class="font-bold text-lg text-petroleo">${p.name}</h4>
                            ${p.rating ? '<div class="flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-turquesa text-sm" style="font-variation-settings:\'FILL\' 1">star</span><span class="text-xs text-petroleo/50">' + p.rating + '</span></div>' : ''}
                            <p class="text-sm text-petroleo/50 mt-1 line-clamp-2">${p.description || 'Lugar imperdible en ' + dest}</p>
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            container.innerHTML = '<p class="text-sm text-petroleo/40 col-span-2 text-center py-8">No se encontraron experiencias para este destino</p>';
        }
    } catch(e) {
        console.log('Experiences error:', e);
        container.innerHTML = '<p class="text-sm text-petroleo/40 col-span-2 text-center py-8">Error al cargar experiencias</p>';
    }
}

function loadMap(dest) {
    const container = document.getElementById('mapContainer');
    container.innerHTML = `<iframe src="https://maps.google.com/maps?q=${encodeURIComponent(dest)}&output=embed&z=12" class="w-full h-full border-0 rounded-xl" allowfullscreen loading="lazy"></iframe>`;
}

function setDestDescription(dest) {
    const d = dest.toLowerCase();
    const descs = {
        'cusco': 'La capital histórica del Imperio Inca, ubicada en lo alto de los Andes peruanos.',
        'machu picchu': 'La ciudadela inca perdida, una de las Siete Maravillas del Mundo Moderno.',
        'cancun': 'Paraíso caribeño con playas de arena blanca y aguas turquesa.',
        'cancún': 'Paraíso caribeño con playas de arena blanca y aguas turquesa.',
        'paris': 'La Ciudad de la Luz, capital mundial del arte, moda y gastronomía.',
        'punta cana': 'Destino todo incluido con resorts de lujo y playas paradisíacas.',
        'bali': 'Isla de los dioses, con templos ancestrales y naturaleza exuberante.',
        'orlando': 'La capital mundial de los parques temáticos y la diversión familiar.',
        'miami': 'Ciudad vibrante con playas, arte deco y vida nocturna inigualable.',
        'new york': 'La ciudad que nunca duerme. Iconos mundiales en cada esquina.',
        'roma': 'La Ciudad Eterna, donde cada calle cuenta miles de años de historia.',
        'tokio': 'Metrópolis ultramoderna que convive en armonía con templos ancestrales.',
        'londres': 'Capital del Reino Unido, rica en historia, cultura y modernidad.',
        'cartagena': 'Ciudad amurallada con historia colonial y playas caribeñas.',
        'rio de janeiro': 'Cidade maravilhosa con playas legendarias y el Cristo Redentor.',
    };
    for (const [key, desc] of Object.entries(descs)) {
        if (d.includes(key)) { document.getElementById('destDesc').textContent = desc; return; }
    }
    document.getElementById('destDesc').textContent = 'Descubre este increíble destino con Aventuras Travel Pucallpa.';
}

/**
 * Temporada recomendada según latitud y hemisferio
 */
function loadSeasonInfo(dest) {
    const d = dest.toLowerCase();
    const seasons = {
        'cusco': { peak: 'Mayo — Septiembre', tip: 'Temporada seca con cielos despejados, ideal para trekking y visitas a ruinas incas.' },
        'machu': { peak: 'Abril — Octubre', tip: 'Temporada seca. Evita enero-febrero por lluvias intensas y cierre del Camino Inca.' },
        'lima': { peak: 'Diciembre — Abril', tip: 'Verano limeño con sol. El resto del año hay garúa (neblina costera).' },
        'iquitos': { peak: 'Junio — Octubre', tip: 'Temporada seca amazónica con menos lluvias, ideal para explorar la selva.' },
        'pucallpa': { peak: 'Mayo — Septiembre', tip: 'Temporada seca. Menos lluvias e ideal para navegar por el río Ucayali.' },
        'cancun': { peak: 'Diciembre — Abril', tip: 'Temporada seca y cálida, perfecta para playa. Evita septiembre-noviembre (huracanes).' },
        'cancún': { peak: 'Diciembre — Abril', tip: 'Temporada seca y cálida, perfecta para playa. Evita septiembre-noviembre (huracanes).' },
        'punta cana': { peak: 'Diciembre — Abril', tip: 'Temperaturas ideales y baja probabilidad de lluvia. Temporada alta caribeña.' },
        'paris': { peak: 'Abril — Octubre', tip: 'Primavera y verano europeo con días largos y temperatura agradable.' },
        'roma': { peak: 'Abril — Junio, Sep — Oct', tip: 'Primavera y otoño son ideales. El verano puede ser muy caluroso.' },
        'bali': { peak: 'Abril — Octubre', tip: 'Temporada seca. Ideal para playas y templos sin las lluvias monzónicas.' },
        'tokio': { peak: 'Mar — Mayo, Oct — Nov', tip: 'Primavera (sakura) y otoño (momiji) son las estaciones más bellas.' },
        'new york': { peak: 'Abril — Junio, Sep — Nov', tip: 'Primavera y otoño ofrecen clima templado y el icónico follaje de Central Park.' },
        'miami': { peak: 'Noviembre — Abril', tip: 'Invierno suave y seco. Evita junio-noviembre por temporada de huracanes.' },
        'orlando': { peak: 'Febrero — Mayo', tip: 'Clima agradable y menos multitudes en los parques temáticos.' },
        'londres': { peak: 'Mayo — Septiembre', tip: 'Verano británico con días largos. Lleva siempre un paraguas por si acaso.' },
        'cartagena': { peak: 'Diciembre — Abril', tip: 'Temporada seca caribeña. Calor tropical con brisa marina.' },
        'rio': { peak: 'Junio — Septiembre', tip: 'Invierno carioca: menos lluvias y temperaturas agradables (20-25°C).' },
        'buenos aires': { peak: 'Marzo — Mayo, Sep — Nov', tip: 'Otoño y primavera. Clima templado ideal para pasear por la ciudad.' },
        'bogota': { peak: 'Diciembre — Marzo', tip: 'Temporada seca en la sabana. Lleva abrigo ligero (2600 msnm).' },
        'santiago': { peak: 'Octubre — Abril', tip: 'Primavera-verano chileno con cielos despejados y temperaturas cálidas.' },
    };
    let found = null;
    for (const [key, val] of Object.entries(seasons)) {
        if (d.includes(key)) { found = val; break; }
    }
    const peakEl = document.getElementById('peakSeason');
    const tipEl = document.getElementById('seasonTip');
    if (found) {
        peakEl.textContent = found.peak;
        tipEl.textContent = found.tip;
    } else {
        // Inferir por latitud desde la geocodificación
        fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(dest)}&count=1`)
            .then(r => r.json())
            .then(data => {
                if (data.results && data.results[0]) {
                    const lat = data.results[0].latitude;
                    if (lat > 0) {
                        peakEl.textContent = 'Mayo — Septiembre';
                        tipEl.textContent = 'Hemisferio norte: temporada de verano con días largos y clima cálido.';
                    } else {
                        peakEl.textContent = 'Noviembre — Marzo';
                        tipEl.textContent = 'Hemisferio sur: temporada de verano con temperaturas agradables.';
                    }
                }
            }).catch(() => {});
    }
}
</script>
