<!-- DESTINOS – PÁGINA PRINCIPAL – AVENTURAS TRAVEL PUCALLPA -->

<!-- HERO - MEJORADO -->
<section class="relative h-[55vh] sm:h-[65vh] md:h-[72vh] min-h-[380px] sm:min-h-[460px] overflow-hidden group">
    <!-- Imagen de Amazonas mejorada -->
    <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1600&h=900&fit=crop&q=80" alt="Destinos - Aventuras Travel"
         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
    <!-- Gradientes mejorados -->
    <div class="absolute inset-0 bg-gradient-to-r from-petroleo-dark/95 via-petroleo/85 to-turquesa-dark/40"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark/98 via-transparent to-transparent"></div>

    <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 md:px-8 h-full flex flex-col justify-center items-center text-center">
        <!-- Badge animado mejorado -->
        <span class="inline-block bg-gradient-to-r from-turquesa/40 to-coral/30 backdrop-blur-md text-turquesa-light px-5 py-2.5 rounded-full text-xs font-black uppercase tracking-widest mb-6 sm:mb-8 border border-turquesa/60 shadow-lg shadow-turquesa/30 animate-pulse">
            ✨ Exploración Global · Aventuras Travel Pucallpa
        </span>
        
        <!-- Título mejorado con gradiente -->
        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black text-white tracking-tight leading-[0.95] mb-4 sm:mb-6 drop-shadow-2xl">
            ¿A dónde quieres <span class="text-transparent bg-clip-text bg-gradient-to-r from-turquesa-light to-coral">explorar</span>?
        </h1>
        
        <!-- Subtítulo mejorado -->
        <p class="text-base sm:text-lg md:text-xl text-white/90 max-w-2xl mb-8 sm:mb-12 drop-shadow-lg leading-relaxed font-medium">
            Encuentra tu destino perfecto. <span class="text-turquesa-light font-bold">Clima en tiempo real, moneda local, mapas interactivos</span> y los mejores hospedajes — todo en un lugar.
        </p>

        <!-- Buscador con autocomplete -->
        <div class="w-full max-w-2xl relative animate-fadeInUp" id="destSearchWrapper" style="animation-delay:.24s">
            <form action="<?= Router::url('/search/results') ?>" method="GET" id="destSearchForm"
                  class="bg-white rounded-2xl shadow-[0_8px_40px_rgba(0,0,0,0.35)] p-1.5 sm:p-2 flex items-center relative z-20">
                <span class="material-symbols-outlined text-petroleo/40 ml-2 sm:ml-4 text-xl sm:text-2xl">search</span>
                <input type="text" name="q" id="destSearchInput" autocomplete="off"
                    placeholder="Cusco, Cancún, París, Punta Cana..."
                    class="flex-1 border-none bg-white px-2 sm:px-4 py-3 sm:py-4 text-sm sm:text-base md:text-lg font-medium text-petroleo focus:outline-none focus:ring-0 placeholder:text-slate-400">
                <button type="submit"
                    class="px-4 sm:px-6 md:px-8 py-3 sm:py-4 bg-gradient-to-r from-turquesa-dark to-turquesa text-white text-sm sm:text-base font-bold rounded-xl hover:shadow-lg transition-all active:scale-95 whitespace-nowrap shrink-0">
                    Explorar
                </button>
            </form>
            <!-- Autocomplete dropdown -->
            <div id="destSuggestions" class="dest-ac-dropdown hidden"></div>
        </div>
    </div>
</section>

<!-- STATS BAND -->
<div class="bg-white border-b border-petroleo/5 py-4 px-4 sm:px-6">
    <div class="max-w-4xl mx-auto flex justify-center items-center gap-6 sm:gap-12 flex-wrap">
        <div class="flex items-center gap-2 text-sm text-petroleo/60">
            <span class="material-symbols-outlined text-turquesa text-xl">thermostat</span>
            <span>Clima en tiempo real</span>
        </div>
        <div class="w-px h-5 bg-petroleo/10 hidden sm:block"></div>
        <div class="flex items-center gap-2 text-sm text-petroleo/60">
            <span class="material-symbols-outlined text-turquesa text-xl">currency_exchange</span>
            <span>Tipo de cambio actual</span>
        </div>
        <div class="w-px h-5 bg-petroleo/10 hidden sm:block"></div>
        <div class="flex items-center gap-2 text-sm text-petroleo/60">
            <span class="material-symbols-outlined text-turquesa text-xl">hotel</span>
            <span>Hospedajes recomendados</span>
        </div>
        <div class="w-px h-5 bg-petroleo/10 hidden sm:block"></div>
        <div class="flex items-center gap-2 text-sm text-petroleo/60">
            <span class="material-symbols-outlined text-turquesa text-xl">map</span>
            <span>Mapa interactivo</span>
        </div>
    </div>
</div>

<!-- DESTINOS POPULARES -->
<section class="px-4 sm:px-6 md:px-8 py-10 sm:py-14 md:py-16 max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between sm:items-end gap-2 mb-6 sm:mb-8">
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-turquesa-dark">Populares</span>
            <h2 class="text-2xl sm:text-3xl font-black text-petroleo tracking-tight mt-1">Destinos Favoritos</h2>
        </div>
        <span class="text-xs font-bold uppercase tracking-widest text-petroleo/30">Haz clic para explorar</span>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
        <?php
        $populares = [
            ['Cusco',        'Perú',              'castle',          'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=600&q=80'],
            ['Machu Picchu', 'Perú',              'landscape',       'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=600&q=80'],
            ['Cancún',       'México',            'beach_access',    'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?w=600&q=80'],
            ['Punta Cana',   'Rep. Dominicana',   'pool',            'https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=600&q=80'],
            ['París',        'Francia',           'flag',            'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=600&q=80'],
            ['Iquitos',      'Perú',              'forest',          'https://images.unsplash.com/photo-1619546952812-520e98064a52?w=600&q=80'],
            ['Bali',         'Indonesia',         'temple_buddhist', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&q=80'],
            ['Roma',         'Italia',            'church',          'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=600&q=80'],
        ];
        foreach ($populares as $dest):
        ?>
        <a href="<?= Router::url('/search/results') ?>?q=<?= urlencode($dest[0]) ?>"
           class="group relative overflow-hidden rounded-2xl h-44 sm:h-52 block">
            <img src="<?= $dest[3] ?>" alt="<?= $dest[0] ?>"
                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>
            <div class="absolute inset-0 p-4 flex flex-col justify-end">
                <p class="font-black text-white text-base sm:text-lg leading-tight"><?= $dest[0] ?></p>
                <p class="text-[11px] text-white/60 uppercase tracking-widest font-bold mt-0.5"><?= $dest[1] ?></p>
            </div>
            <div class="absolute top-3 right-3 w-8 h-8 bg-white/0 group-hover:bg-white/15 backdrop-blur-sm rounded-full flex items-center justify-center transition-all duration-300">
                <span class="material-symbols-outlined text-white text-base opacity-0 group-hover:opacity-100 transition-opacity duration-300">arrow_forward</span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- REGIONES -->
<section class="py-10 sm:py-12 px-4 sm:px-6 md:px-8 bg-superficie">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-8">
            <span class="text-xs font-bold uppercase tracking-widest text-turquesa-dark">Categorías</span>
            <h2 class="text-2xl sm:text-3xl font-black text-petroleo tracking-tight mt-1">Viaja por el Mundo</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
            <a href="<?= Router::url('/search/results') ?>?q=Cusco"
               class="group bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-petroleo/5 hover:shadow-lg hover:border-turquesa/20 transition-all text-center">
                <div class="w-14 h-14 bg-turquesa/10 group-hover:bg-turquesa transition-all rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-turquesa-dark group-hover:text-white text-3xl transition-colors">landscape</span>
                </div>
                <h3 class="font-black text-petroleo text-lg mb-1">Perú y Amazonía</h3>
                <p class="text-petroleo/50 text-sm">Cusco, Iquitos, Machu Picchu y más destinos nacionales extraordinarios.</p>
            </a>
            <a href="<?= Router::url('/search/results') ?>?q=Cancún"
               class="group bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-petroleo/5 hover:shadow-lg hover:border-turquesa/20 transition-all text-center">
                <div class="w-14 h-14 bg-turquesa/10 group-hover:bg-turquesa transition-all rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-turquesa-dark group-hover:text-white text-3xl transition-colors">beach_access</span>
                </div>
                <h3 class="font-black text-petroleo text-lg mb-1">Caribe y América</h3>
                <p class="text-petroleo/50 text-sm">Cancún, Punta Cana, Cartagena y los mejores rincones americanos.</p>
            </a>
            <a href="<?= Router::url('/search/results') ?>?q=París"
               class="group bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-petroleo/5 hover:shadow-lg hover:border-turquesa/20 transition-all text-center">
                <div class="w-14 h-14 bg-turquesa/10 group-hover:bg-turquesa transition-all rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-turquesa-dark group-hover:text-white text-3xl transition-colors">public</span>
                </div>
                <h3 class="font-black text-petroleo text-lg mb-1">Europa y Mundo</h3>
                <p class="text-petroleo/50 text-sm">París, Roma, Londres, Bali, Tokio y los grandes iconos del planeta.</p>
            </a>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-12 sm:py-16 px-4 sm:px-6 md:px-8">
    <div class="max-w-4xl mx-auto text-center">
        <span class="text-xs font-bold uppercase tracking-widest text-turquesa-dark">¿No sabes a dónde ir?</span>
        <h2 class="text-2xl sm:text-3xl font-black text-petroleo tracking-tight mt-2 mb-3">Déjate asesorar por expertos</h2>
        <p class="text-petroleo/50 text-sm sm:text-base max-w-lg mx-auto mb-7">
            Nuestro equipo en Pucallpa diseña itinerarios personalizados para familias, grupos escolares y corporativos.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
            <a href="https://wa.me/51976324716?text=<?= rawurlencode('¡Hola Aventuras Travel! 🌴✈️ Quisiera asesoría para elegir mi próximo destino.') ?>"
               target="_blank" rel="noopener"
               class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl transition-all active:scale-95 shadow-lg shadow-emerald-500/20">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Consultar por WhatsApp
            </a>
            <a href="<?= Router::url('/asesoria') ?>"
               class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-petroleo text-white font-bold rounded-xl hover:bg-petroleo-light transition-all">
                <span class="material-symbols-outlined text-lg">support_agent</span>
                Ver Asesorías
            </a>
        </div>
    </div>
</section>

<!-- Autocomplete styles + script (igual que homepage) -->
<style>
.dest-ac-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 30;
    margin-top: 0.5rem;
    background: rgba(255,255,255,0.97);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-radius: 1rem;
    box-shadow: 0 20px 60px rgba(27,58,75,0.18);
    border: 1px solid rgba(27,58,75,0.06);
    overflow: hidden;
    max-height: 380px;
    overflow-y: auto;
}
.dest-ac-dropdown.hidden { display: none; }
.ac-item {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 0.875rem 1.25rem;
    cursor: pointer;
    transition: background 0.15s ease;
    text-decoration: none;
    color: inherit;
    border-bottom: 1px solid rgba(27,58,75,0.04);
}
.ac-item:last-child { border-bottom: none; }
.ac-item:hover, .ac-item.ac-active { background: rgba(74,190,217,0.08); }
.ac-icon { width:2.75rem; height:2.75rem; border-radius:0.75rem; background:linear-gradient(135deg,rgba(74,190,217,0.12),rgba(0,104,122,0.08)); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.25rem; }
.ac-info { flex:1; min-width:0; }
.ac-name { font-weight:700; font-size:0.9375rem; color:#1B3A4B; line-height:1.2; }
.ac-name mark { background:rgba(74,190,217,0.25); color:#00687A; border-radius:2px; padding:0 1px; }
.ac-region { font-size:0.75rem; color:rgba(27,58,75,0.45); margin-top:0.125rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ac-meta { display:flex; flex-direction:column; align-items:flex-end; gap:0.125rem; flex-shrink:0; }
.ac-type { font-size:0.625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:rgba(27,58,75,0.3); }
.ac-loading { display:flex; align-items:center; justify-content:center; gap:0.5rem; padding:1.25rem; color:rgba(27,58,75,0.35); font-size:0.8125rem; font-weight:600; }
.ac-empty { padding:1.5rem; text-align:center; color:rgba(27,58,75,0.35); font-size:0.8125rem; }
</style>
<script>
(function() {
    const input    = document.getElementById('destSearchInput');
    const dropdown = document.getElementById('destSuggestions');
    const form     = document.getElementById('destSearchForm');
    if (!input || !dropdown) return;

    let debounceTimer = null;
    let activeIdx = -1;
    let currentResults = [];
    const SEARCH_URL = '<?= Router::url("/search/results") ?>';

    function countryFlag(code) {
        if (!code || code.length !== 2) return '🌍';
        const c = code.toUpperCase();
        return String.fromCodePoint(...[...c].map(l => 0x1F1E6 - 65 + l.charCodeAt(0)));
    }

    function highlight(text, query) {
        if (!query) return text;
        const r = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
        return text.replace(r, '<mark>$1</mark>');
    }

    async function fetchSuggestions(query) {
        if (query.length < 2) { hide(); return; }
        show();
        dropdown.innerHTML = '<div class="ac-loading"><span class="material-symbols-outlined text-lg animate-spin">progress_activity</span>Buscando destinos...</div>';
        try {
            const res  = await fetch('https://geocoding-api.open-meteo.com/v1/search?name=' + encodeURIComponent(query) + '&count=6&language=es&format=json');
            const data = await res.json();
            if (!data.results || data.results.length === 0) {
                dropdown.innerHTML = '<div class="ac-empty"><span class="material-symbols-outlined text-2xl block mb-1" style="opacity:.3">location_off</span>No se encontraron destinos para "' + query + '"</div>';
                currentResults = []; return;
            }
            currentResults = data.results;
            activeIdx = -1;
            renderResults(query);
        } catch (e) {
            dropdown.innerHTML = '<div class="ac-empty">Error al buscar destinos</div>';
            currentResults = [];
        }
    }

    function renderResults(query) {
        dropdown.innerHTML = currentResults.map(function(r, i) {
            const name    = r.name || '';
            const admin   = r.admin1 || '';
            const country = r.country || '';
            const cc      = r.country_code || '';
            const flag    = countryFlag(cc);
            const region  = [admin, country].filter(Boolean).join(', ');
            const typeMap = { 'PPLC':'Capital','PPLA':'Ciudad','PPLA2':'Ciudad','PPL':'Localidad' };
            const fcode   = typeMap[r.feature_code] || '';
            return '<a class="ac-item' + (i === activeIdx ? ' ac-active' : '') + '" data-idx="' + i + '" href="' + SEARCH_URL + '?q=' + encodeURIComponent(name + (country ? ', ' + country : '')) + '">'
                + '<div class="ac-icon"><span style="font-size:1.5rem">' + flag + '</span></div>'
                + '<div class="ac-info">'
                + '<div class="ac-name">' + highlight(name, query) + '</div>'
                + '<div class="ac-region">' + region + '</div>'
                + '</div>'
                + '<div class="ac-meta">' + (fcode ? '<span class="ac-type">' + fcode + '</span>' : '') + '<span style="font-size:0.8rem;color:#00687A"><span class="material-symbols-outlined" style="font-size:14px;vertical-align:middle">arrow_forward</span></span></div>'
                + '</a>';
        }).join('');
    }

    function show() { dropdown.classList.remove('hidden'); }
    function hide() { dropdown.classList.add('hidden'); activeIdx = -1; currentResults = []; }

    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 2) { hide(); return; }
        debounceTimer = setTimeout(function() { fetchSuggestions(q); }, 280);
    });

    input.addEventListener('keydown', function(e) {
        if (dropdown.classList.contains('hidden') || currentResults.length === 0) return;
        if (e.key === 'ArrowDown')      { e.preventDefault(); activeIdx = Math.min(activeIdx + 1, currentResults.length - 1); updateActive(); }
        else if (e.key === 'ArrowUp')   { e.preventDefault(); activeIdx = Math.max(activeIdx - 1, -1); updateActive(); }
        else if (e.key === 'Enter' && activeIdx >= 0) { e.preventDefault(); const item = dropdown.querySelector('.ac-item[data-idx="' + activeIdx + '"]'); if (item) window.location.href = item.href; }
        else if (e.key === 'Escape')    { hide(); }
    });

    function updateActive() {
        dropdown.querySelectorAll('.ac-item').forEach(function(el, i) { el.classList.toggle('ac-active', i === activeIdx); });
        if (activeIdx >= 0 && currentResults[activeIdx]) input.value = currentResults[activeIdx].name;
    }

    document.addEventListener('click', function(e) {
        if (!document.getElementById('destSearchWrapper').contains(e.target)) hide();
    });

    input.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && currentResults.length > 0) { show(); renderResults(this.value.trim()); }
    });
})();
</script>
