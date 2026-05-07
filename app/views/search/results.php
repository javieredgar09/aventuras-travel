<!-- BUSCADOR DE DESTINOS – RESULTADOS – AVENTURAS TRAVEL -->

<!-- ══ HERO SEARCH BAR ══════════════════════════════════════════════════════ -->
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, #0D2432 0%, #1B3A4B 50%, #00687A 100%);">
    <!-- Decoración de fondo -->
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 50%, #4ABED9 0%, transparent 50%), radial-gradient(circle at 80% 20%, #4ABED9 0%, transparent 40%);"></div>
    <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-5" style="background: #4ABED9; transform: translate(30%, -30%);"></div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 md:px-8 py-8 sm:py-10 md:py-12">
        <!-- Label -->
        <div class="flex items-center gap-2 mb-3">
            <span class="material-symbols-outlined text-turquesa text-xl" style="color:#4ABED9;">travel_explore</span>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/60">Buscador de Destinos · Aventuras Travel</p>
        </div>

        <!-- Resultado actual -->
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white mb-2 leading-tight">
            Explorando <span style="color:#4ABED9;"><?= htmlspecialchars($query ?? 'tu destino') ?></span>
        </h1>
        <p class="text-white/60 text-sm mb-6">Clima, hospedajes, experiencias y tipo de cambio en tiempo real.</p>

        <!-- Search bar -->
        <div class="relative" id="resultsSearchWrapper">
            <form action="<?= Router::url('/search/results') ?>" method="GET"
                  class="flex items-center gap-2 p-2 rounded-2xl shadow-2xl"
                  style="background: rgba(255,255,255,0.08); border: 1px solid rgba(74,190,217,0.3); backdrop-filter: blur(12px);">
                <span class="material-symbols-outlined text-white/50 ml-2 text-xl">search</span>
                <input id="searchInput" type="text" name="q"
                       value="<?= htmlspecialchars($query ?? '') ?>"
                       autocomplete="off"
                       class="flex-1 border-none bg-transparent px-2 py-3 text-base sm:text-lg font-semibold text-white placeholder:text-white/40 focus:ring-0 focus:outline-none"
                       placeholder="¿A dónde quieres viajar? Ej: Cusco, Cancún, París...">
                <button type="submit"
                    class="px-5 sm:px-8 py-3 font-bold rounded-xl transition-all active:scale-95 flex items-center gap-2 whitespace-nowrap text-white shadow-lg"
                    style="background: linear-gradient(135deg, #4ABED9, #00687A);">
                    <span class="material-symbols-outlined text-lg">travel_explore</span>
                    <span class="hidden sm:inline">Buscar</span>
                </button>
            </form>
            <!-- Autocomplete dropdown -->
            <div id="resultsSuggestions" class="results-ac-dropdown hidden"></div>
        </div>

        <!-- Chips de destinos populares -->
        <div class="flex flex-wrap gap-2 mt-4">
            <?php
            $popularDests = ['Cusco', 'Cancún', 'Punta Cana', 'París', 'Miami', 'Bali'];
            foreach ($popularDests as $dest):
                $isActive = strtolower($query ?? '') === strtolower($dest);
            ?>
            <a href="<?= Router::url('/search/results?q=' . urlencode($dest)) ?>"
               class="px-3 py-1.5 rounded-full text-xs font-bold transition-all"
               style="<?= $isActive
                   ? 'background:#4ABED9; color:#0D2432;'
                   : 'background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.7); border:1px solid rgba(74,190,217,0.25);' ?>">
                <?= $dest ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ══ CONTENIDO DESTINO ═══════════════════════════════════════════════════ -->
<section class="px-4 sm:px-6 md:px-8 py-8 sm:py-10 md:py-12 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">

        <!-- COLUMNA IZQUIERDA (8 cols) -->
        <div class="lg:col-span-8 space-y-8">

            <!-- Hero Image -->
            <div class="relative h-[280px] sm:h-[380px] md:h-[480px] rounded-2xl overflow-hidden group" id="heroContainer">
                <div id="heroSkeleton" class="absolute inset-0 animate-pulse flex items-center justify-center" style="background: linear-gradient(135deg, rgba(27,58,75,0.15), rgba(74,190,217,0.12));">
                    <span class="material-symbols-outlined text-6xl animate-bounce" style="color: rgba(74,190,217,0.4);">landscape</span>
                </div>
                <img id="heroImage" alt="" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-0"
                    onload="this.classList.remove('opacity-0');this.classList.add('opacity-100');document.getElementById('heroSkeleton').style.display='none';">
                <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);"></div>
                <div class="absolute bottom-4 sm:bottom-6 md:bottom-8 left-4 sm:left-6 md:left-8 text-white">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-3 sm:mb-4 inline-block" style="background:rgba(74,190,217,0.25); color:#4ABED9; backdrop-filter:blur(8px); border:1px solid rgba(74,190,217,0.3);">Top Choice 2026</span>
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight mb-1 sm:mb-2" id="destName"><?= htmlspecialchars($query ?? 'Cusco, Peru') ?></h2>
                    <p class="text-base sm:text-lg text-white/80 max-w-xl" id="destDesc">Cargando descripción...</p>
                </div>
                <div class="absolute bottom-3 right-4 text-[10px] text-white/40" id="unsplashCredit"></div>
            </div>

            <!-- Info Bento Grid: Clima + Temporada + Esenciales -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-5">
                <!-- Clima -->
                <div class="p-6 rounded-2xl flex flex-col gap-4 shadow-xl" style="background: linear-gradient(135deg, #0D2432, #1B3A4B);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(74,190,217,0.2);">
                            <span class="material-symbols-outlined text-2xl" style="color:#4ABED9;">thermostat</span>
                        </div>
                        <h3 class="font-bold text-white text-xs uppercase tracking-widest">Clima Local</h3>
                    </div>
                    <div class="flex items-end gap-3">
                        <span class="text-4xl font-black text-white leading-none" id="weatherTemp">--°C</span>
                        <span class="text-sm font-semibold mb-1" style="color:#4ABED9;" id="weatherDesc">Cargando...</span>
                    </div>
                    <div class="text-xs space-y-1 border-t pt-3" style="color:rgba(255,255,255,0.45); border-color:rgba(255,255,255,0.1);">
                        <p id="weatherHumidity">Humedad: --%</p>
                        <p id="weatherWind">Viento: -- km/h</p>
                    </div>
                </div>

                <!-- Temporada -->
                <div class="p-6 rounded-2xl flex flex-col gap-4 shadow-xl" style="background: linear-gradient(135deg, #00687A, #4ABED9);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                            <span class="material-symbols-outlined text-2xl text-white">calendar_month</span>
                        </div>
                        <h3 class="font-bold text-white text-xs uppercase tracking-widest">Mejor Temporada</h3>
                    </div>
                    <div class="text-xl font-black text-white leading-tight" id="peakSeason">Mayo — Sep</div>
                    <p class="text-xs leading-relaxed" style="color:rgba(255,255,255,0.8);" id="seasonTip">Temporada seca con cielos claros.</p>
                </div>

                <!-- Esenciales -->
                <div class="p-6 rounded-2xl flex flex-col gap-4 shadow-sm bg-white" style="border: 2px solid rgba(27,58,75,0.08);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(27,58,75,0.08);">
                            <span class="material-symbols-outlined text-2xl" style="color:#1B3A4B;">payments</span>
                        </div>
                        <h3 class="font-bold text-xs uppercase tracking-widest" style="color:#1B3A4B;">Info Esencial</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-2" style="border-bottom:1px solid rgba(27,58,75,0.08);">
                            <span class="text-xs uppercase tracking-widest font-semibold" style="color:rgba(27,58,75,0.4);">Moneda</span>
                            <span class="font-black text-sm" style="color:#1B3A4B;" id="currencyInfo">--</span>
                        </div>
                        <div class="flex justify-between items-center pb-2" style="border-bottom:1px solid rgba(27,58,75,0.08);">
                            <span class="text-xs uppercase tracking-widest font-semibold" style="color:rgba(27,58,75,0.4);">Idioma</span>
                            <span class="font-black text-sm" style="color:#1B3A4B;" id="languageInfo">--</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs uppercase tracking-widest font-semibold" style="color:rgba(27,58,75,0.4);">Cambio</span>
                            <span class="font-black text-sm" style="color:#059669;" id="exchangeRate">--</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Experiencias Imperdibles -->
            <div class="space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-1.5 h-8 rounded-full" style="background: linear-gradient(180deg, #00687A, #4ABED9);"></div>
                    <h2 class="text-2xl font-black tracking-tight" style="color:#1B3A4B;">Experiencias Imperdibles</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5" id="experiencesGrid">
                    <!-- JS carga experiencias -->
                </div>
            </div>

            <!-- Galería Unsplash -->
            <div class="space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-1.5 h-8 rounded-full" style="background: linear-gradient(180deg, #00687A, #4ABED9);"></div>
                    <h2 class="text-2xl font-black tracking-tight" style="color:#1B3A4B;">Galería del Destino</h2>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4" id="galleryGrid">
                    <!-- JS carga galería de Unsplash -->
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA (4 cols) -->
        <aside class="lg:col-span-4 space-y-5 order-first lg:order-last">
            <!-- Mapa -->
            <div class="rounded-2xl overflow-hidden h-[280px] relative shadow-lg" style="border:2px solid rgba(27,58,75,0.08);" id="mapContainer">
                <div class="absolute inset-0 flex flex-col items-center justify-center" style="background:#EAF0F2;">
                    <span class="material-symbols-outlined text-6xl mb-2" style="color:#4ABED9;">map</span>
                    <p class="font-bold" style="color:#00687A;">Mapa Interactivo</p>
                    <p class="text-xs" style="color:rgba(27,58,75,0.4);">Clic para explorar</p>
                </div>
            </div>

            <!-- Hoteles -->
            <div class="p-5 sm:p-6 rounded-2xl shadow-xl" style="background: linear-gradient(135deg, #0D2432, #1B3A4B);">
                <h3 class="text-lg font-black text-white mb-5 flex items-center gap-2">
                    <span class="material-symbols-outlined" style="color:#4ABED9;">hotel</span>
                    Hospedajes Recomendados
                </h3>
                <div class="space-y-4" id="hotelsGrid">
                    <div class="text-center py-8">
                        <span class="material-symbols-outlined text-4xl animate-spin" style="color:rgba(255,255,255,0.2);">progress_activity</span>
                        <p class="text-sm mt-2" style="color:rgba(255,255,255,0.4);">Buscando hoteles...</p>
                    </div>
                </div>
                <div class="space-y-3 mt-5 pt-5" style="border-top:1px solid rgba(255,255,255,0.1);">
                    <button class="w-full py-3 font-bold rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2 text-white shadow-lg"
                            style="background: linear-gradient(135deg, #4ABED9, #00687A);">
                        <span class="material-symbols-outlined">local_offer</span>
                        Cotizar otras opciones
                    </button>
                    <a href="https://wa.me/51976324716?text=Hola%20Aventuras%20Travel%2C%20me%20interesa%20consultar%20sobre%20hospedaje" target="_blank"
                       class="flex items-center justify-center gap-2 w-full py-3 font-bold rounded-xl transition-all active:scale-95 text-white shadow-md"
                       style="background: linear-gradient(135deg, #22c55e, #16a34a);">
                        <span class="material-symbols-outlined text-lg">chat</span>
                        Escribirnos al WhatsApp
                    </a>
                </div>
            </div>

            <!-- CTA Asesoría -->
            <div class="p-6 rounded-2xl text-white shadow-lg" style="background: linear-gradient(135deg, #4ABED9, #00687A);">
                <span class="material-symbols-outlined text-4xl mb-3 block" style="color:rgba(255,255,255,0.7);">support_agent</span>
                <h3 class="text-xl font-black mb-2">¿Quieres este destino?</h3>
                <p class="text-sm mb-4 leading-relaxed" style="color:rgba(255,255,255,0.8);">Nuestros asesores diseñan tu viaje a medida. Cotización gratuita.</p>
                <a href="<?= Router::url('/asesoria') ?>"
                   class="inline-flex items-center gap-2 bg-white font-black px-5 py-2.5 rounded-xl hover:shadow-lg transition-all active:scale-95 text-sm"
                   style="color:#00687A;">
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    Solicitar Asesoría
                </a>
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
    'lima': { hero: 'photo-1577587230708-187fdbef4d91', gallery: [] },
    'iquitos': { hero: 'photo-1619546952812-520e98064a52', gallery: [] },
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
                <div class="flex gap-3 items-center p-3 rounded-xl" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08);">
                    <div class="w-14 h-14 rounded-lg overflow-hidden shrink-0" style="background:rgba(74,190,217,0.15);">
                        <img src="${h.image || ''}" alt="${h.name}" class="w-full h-full object-cover"
                            onerror="this.parentElement.innerHTML='<div style=\\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;\\'><span class=\\'material-symbols-outlined\\' style=\\'color:#4ABED9;\\'>hotel</span></div>'">
                    </div>
                    <div class="flex-grow min-w-0">
                        <h4 class="font-bold text-sm text-white truncate">${h.name}</h4>
                        <div class="flex items-center gap-0.5 mt-1" style="color:#4ABED9;">
                            ${'<span class="material-symbols-outlined text-[13px]" style="font-variation-settings:\'FILL\' 1">star</span>'.repeat(Math.min(Math.round(parseFloat(h.rating) || 4), 5))}
                            <span class="text-[11px] ml-1" style="color:rgba(255,255,255,0.4);">${h.rating || ''}</span>
                        </div>
                        <p class="text-xs font-black mt-0.5" style="color:#4ABED9;">${h.price || 'Consultar precio'}</p>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p style="color:rgba(255,255,255,0.35);" class="text-sm text-center py-4">No se encontraron hoteles</p>';
        }
    } catch(e) {
        container.innerHTML = '<p style="color:rgba(255,255,255,0.35);" class="text-sm text-center py-4">Error al cargar hoteles</p>';
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

<!-- Autocomplete styles (resultados) -->
<style>
.results-ac-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 30;
    margin-top: 0.4rem;
    background: rgba(255,255,255,0.97);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-radius: 0.875rem;
    box-shadow: 0 16px 48px rgba(27,58,75,0.15);
    border: 1px solid rgba(27,58,75,0.06);
    overflow: hidden;
    max-height: 360px;
    overflow-y: auto;
}
.results-ac-dropdown.hidden { display: none; }
.res-ac-item {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 0.8rem 1.25rem;
    cursor: pointer;
    transition: background 0.15s ease;
    text-decoration: none;
    color: inherit;
    border-bottom: 1px solid rgba(27,58,75,0.04);
}
.res-ac-item:last-child { border-bottom: none; }
.res-ac-item:hover, .res-ac-item.res-ac-active { background: rgba(74,190,217,0.08); }
.res-ac-icon { width:2.5rem; height:2.5rem; border-radius:0.6rem; background:linear-gradient(135deg,rgba(74,190,217,0.12),rgba(0,104,122,0.08)); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.25rem; }
.res-ac-name { font-weight:700; font-size:0.875rem; color:#1B3A4B; }
.res-ac-name mark { background:rgba(74,190,217,0.25); color:#00687A; border-radius:2px; padding:0 1px; }
.res-ac-region { font-size:0.7rem; color:rgba(27,58,75,0.45); margin-top:0.1rem; }
.res-ac-loading { display:flex; align-items:center; justify-content:center; gap:0.5rem; padding:1rem; color:rgba(27,58,75,0.35); font-size:0.8rem; font-weight:600; }
.res-ac-empty { padding:1.25rem; text-align:center; color:rgba(27,58,75,0.35); font-size:0.8rem; }
</style>

<script>
/* ── Autocomplete Search Results ── */
(function() {
    const input    = document.getElementById('searchInput');
    const dropdown = document.getElementById('resultsSuggestions');
    const wrapper  = document.getElementById('resultsSearchWrapper');
    if (!input || !dropdown || !wrapper) return;

    let debounce   = null;
    let activeIdx  = -1;
    let results    = [];
    const BASE     = '<?= Router::url("/search/results") ?>';

    function flag(code) {
        if (!code || code.length !== 2) return '🌍';
        const c = code.toUpperCase();
        return String.fromCodePoint(...[...c].map(l => 0x1F1E6 - 65 + l.charCodeAt(0)));
    }
    function hl(text, q) {
        if (!q) return text;
        return text.replace(new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')','gi'),'<mark>$1</mark>');
    }
    function show() { dropdown.classList.remove('hidden'); }
    function hide() { dropdown.classList.add('hidden'); activeIdx = -1; results = []; }

    function render(q) {
        dropdown.innerHTML = results.map((r,i) => {
            const name   = r.name || '';
            const region = [r.admin1, r.country].filter(Boolean).join(', ');
            return '<a class="res-ac-item' + (i===activeIdx?' res-ac-active':'') + '" data-idx="'+i+'" href="'+BASE+'?q='+encodeURIComponent(name+(r.country?', '+r.country:''))+'">' +
                   '<div class="res-ac-icon"><span style="font-size:1.3rem">' + flag(r.country_code) + '</span></div>' +
                   '<div class="flex-1 min-w-0"><div class="res-ac-name">' + hl(name,q) + '</div><div class="res-ac-region">' + region + '</div></div>' +
                   '<span class="material-symbols-outlined text-turquesa-dark" style="font-size:16px">arrow_forward</span></a>';
        }).join('');
    }

    async function suggest(q) {
        if (q.length < 2) { hide(); return; }
        show();
        dropdown.innerHTML = '<div class="res-ac-loading"><span class="material-symbols-outlined animate-spin">progress_activity</span> Buscando destinos...</div>';
        try {
            const res  = await fetch('https://geocoding-api.open-meteo.com/v1/search?name='+encodeURIComponent(q)+'&count=6&language=es&format=json');
            const data = await res.json();
            if (!data.results || !data.results.length) {
                dropdown.innerHTML = '<div class="res-ac-empty">No se encontraron destinos para "'+q+'"</div>';
                results = []; return;
            }
            results   = data.results;
            activeIdx = -1;
            render(q);
        } catch(e) { dropdown.innerHTML = '<div class="res-ac-empty">Error al buscar</div>'; results = []; }
    }

    input.addEventListener('input', function() {
        clearTimeout(debounce);
        const q = this.value.trim();
        if (q.length < 2) { hide(); return; }
        debounce = setTimeout(() => suggest(q), 280);
    });

    input.addEventListener('keydown', function(e) {
        if (dropdown.classList.contains('hidden') || !results.length) return;
        if (e.key === 'ArrowDown')  { e.preventDefault(); activeIdx = Math.min(activeIdx+1, results.length-1); updateActive(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); activeIdx = Math.max(activeIdx-1, -1); updateActive(); }
        else if (e.key === 'Enter' && activeIdx >= 0) { e.preventDefault(); const el = dropdown.querySelector('.res-ac-item[data-idx="'+activeIdx+'"]'); if(el) window.location.href = el.href; }
        else if (e.key === 'Escape') { hide(); }
    });

    function updateActive() {
        dropdown.querySelectorAll('.res-ac-item').forEach((el,i) => el.classList.toggle('res-ac-active', i === activeIdx));
        if (activeIdx >= 0 && results[activeIdx]) input.value = results[activeIdx].name;
    }

    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) hide();
    });

    input.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && results.length > 0) { show(); render(this.value.trim()); }
    });
})();
</script>

<!-- Floating WhatsApp Button -->
<a href="https://wa.me/51976324716?text=<?= rawurlencode('¡Hola Aventuras Travel! 🌴✈️ Estoy explorando destinos y me gustaría recibir asesoría personalizada.') ?>"
   target="_blank" rel="noopener"
   class="fixed bottom-6 right-6 z-50 flex items-center gap-3 pl-4 pr-5 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-full shadow-2xl shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all active:scale-95"
   title="Escríbenos por WhatsApp">
    <svg class="w-6 h-6 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    <span class="hidden sm:inline text-sm">¿Necesitas ayuda?</span>
</a>
