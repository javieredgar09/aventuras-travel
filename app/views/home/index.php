<!-- HOMEPAGE – TAILWIND – AVENTURAS TRAVEL PUCALLPA -->

<!-- HERO -->
<section class="relative h-[85vh] min-h-[600px] overflow-hidden">
    <img src="/aventuras/img/machu.jpg" alt="Aventuras Travel" class="absolute inset-0 w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1526392060635-9d6019884377?w=1600&q=80'">
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-8 h-full flex flex-col justify-center items-center text-center">
        <span class="inline-block bg-turquesa/20 backdrop-blur-sm text-turquesa px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6">Explora Pucallpa y el Mundo</span>
        <h1 class="text-5xl md:text-7xl font-black text-white tracking-tight leading-[0.95] mb-4">
            El Horizonte de<br>la <span class="text-turquesa">Posibilidad</span>
        </h1>
        <p class="text-lg md:text-xl text-white/70 max-w-2xl mb-10">
            Diseñamos experiencias, no solo boletos. Descubre viajes curados desde el corazón de la Amazonía hasta los íconos del mundo.
        </p>

        <!-- Buscador centrado dentro del hero -->
        <form action="<?= Router::url('/search/results') ?>" method="GET" class="w-full max-w-2xl bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-2 flex items-center">
            <span class="material-symbols-outlined text-petroleo/30 ml-4 text-2xl">search</span>
            <input type="text" name="q" placeholder="¿A dónde quieres viajar?" 
                class="flex-1 border-none bg-transparent px-4 py-4 text-lg font-medium text-petroleo focus:ring-0 placeholder:text-petroleo/30">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-turquesa-dark to-turquesa text-white font-bold rounded-xl hover:shadow-lg transition-all active:scale-95 whitespace-nowrap">
                Explorar
            </button>
        </form>

        <div class="flex gap-4 mt-8">
            <a href="<?= Router::url('/search') ?>" class="px-8 py-3 bg-white/10 backdrop-blur-md text-white font-semibold rounded-xl border border-white/20 hover:bg-white/20 transition-all text-sm">
                Ver Destinos
            </a>
            <a href="#asesoria" class="px-8 py-3 bg-white/10 backdrop-blur-md text-white font-semibold rounded-xl border border-white/20 hover:bg-white/20 transition-all text-sm">
                Asesoría Personalizada
            </a>
        </div>
    </div>
</section>

<!-- DESTINOS NACIONALES -->
<section class="pt-16 pb-16 px-8 max-w-7xl mx-auto">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-black text-petroleo tracking-tight">Destinos Nacionales</h2>
            <p class="text-petroleo/50 mt-1">Exploraciones curadas por la majestuosidad del Perú</p>
        </div>
        <span class="text-xs font-bold uppercase tracking-widest text-turquesa-dark">Perú Collection / 2026</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="nationalDestinations">
        <!-- JS carga destinos dinámicamente -->
    </div>
</section>

<!-- PROMOCIONES -->
<section class="py-12 px-8">
    <div class="max-w-7xl mx-auto bg-gradient-to-r from-petroleo to-petroleo-light rounded-2xl p-10 flex flex-col md:flex-row items-center gap-8">
        <div class="flex-1">
            <h2 class="text-3xl font-black text-white mb-2">Promociones Activas</h2>
            <p class="text-white/60">Ofertas por tiempo limitado. Diseñando valor y aventura.</p>
        </div>
        <div class="flex gap-4" id="promoCards">
            <?php if (!empty($promociones)): ?>
                <?php foreach (array_slice($promociones, 0, 2) as $promo): ?>
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-5 text-center min-w-[140px] border border-white/10">
                    <div class="text-2xl font-black text-turquesa"><?= htmlspecialchars($promo['descuento'] ?? '') ?></div>
                    <div class="text-xs text-white/50 uppercase tracking-widest mt-1"><?= htmlspecialchars($promo['destino'] ?? '') ?></div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- DESTINOS INTERNACIONALES -->
<section class="py-16 px-8 bg-superficie" id="experiencias">
    <div class="max-w-7xl mx-auto">
        <span class="text-xs font-bold uppercase tracking-widest text-turquesa-dark">Red Global</span>
        <h2 class="text-3xl font-black text-petroleo tracking-tight mt-2 mb-8">Destinos Internacionales</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="internationalDestinations">
            <!-- JS carga destinos dinámicamente -->
        </div>
    </div>
</section>

<!-- ASESORÍA PERSONALIZADA (CON DATOS DE JAVIER) -->
<section class="py-20 px-8" id="asesoria" id="contacto">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <span class="text-xs font-bold uppercase tracking-widest text-turquesa-dark">Tu Viaje, Nuestra Pasión</span>
            <h2 class="text-4xl font-black text-petroleo tracking-tight mt-2">Asesoría Personalizada</h2>
            <p class="text-petroleo/50 max-w-xl mx-auto mt-3">Cada viaje es único. Contáctanos y diseñaremos la experiencia perfecta para ti y tu familia.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <!-- Card de Javier -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-petroleo/5">
                <div class="bg-gradient-to-r from-petroleo to-petroleo-light p-8 flex items-center gap-6">
                    <div class="w-28 h-28 rounded-2xl overflow-hidden border-4 border-white/20 shadow-lg shrink-0">
                        <img src="/aventuras/img/javier.jpg" alt="Javier Edgar Sandy Da Cruz" class="w-full h-full object-cover">
                    </div>
                    <div class="text-white">
                        <h3 class="text-2xl font-black">Javier Edgar Sandy Da Cruz</h3>
                        <p class="text-turquesa text-sm font-bold uppercase tracking-widest mt-1">CEO & Fundador</p>
                        <p class="text-white/60 text-sm mt-2">Responsable de tus aventuras</p>
                    </div>
                </div>
                <div class="p-8 space-y-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-turquesa/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-turquesa-dark text-2xl">location_on</span>
                        </div>
                        <div>
                            <p class="text-xs text-petroleo/40 uppercase tracking-widest font-bold">Dirección</p>
                            <p class="text-sm font-semibold text-petroleo">Jirón Zavala 568A, Pucallpa</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-turquesa/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-turquesa-dark text-2xl">call</span>
                        </div>
                        <div>
                            <p class="text-xs text-petroleo/40 uppercase tracking-widest font-bold">Teléfono / WhatsApp</p>
                            <p class="text-sm font-semibold text-petroleo">976 324 716</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-turquesa/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-turquesa-dark text-2xl">mail</span>
                        </div>
                        <div>
                            <p class="text-xs text-petroleo/40 uppercase tracking-widest font-bold">Email</p>
                            <p class="text-sm font-semibold text-petroleo">reservas.aventurastravelpcl@gmail.com</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-turquesa/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-turquesa-dark text-2xl">badge</span>
                        </div>
                        <div>
                            <p class="text-xs text-petroleo/40 uppercase tracking-widest font-bold">RUC</p>
                            <p class="text-sm font-semibold text-petroleo">10475951587</p>
                        </div>
                    </div>

                    <!-- Botón WhatsApp -->
                    <a href="https://wa.me/51976324716?text=Hola%20Javier%2C%20me%20interesa%20información%20sobre%20un%20viaje" target="_blank"
                        class="flex items-center justify-center gap-3 w-full py-4 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl transition-all active:scale-95 shadow-lg hover:shadow-xl mt-6">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Escríbeme por WhatsApp
                    </a>
                </div>
            </div>

            <!-- Info lateral -->
            <div class="space-y-8">
                <div class="bg-superficie rounded-2xl p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <img src="/aventuras/img/a_color.png" alt="Logo Aventuras Travel" class="h-14">
                        <div>
                            <h3 class="text-xl font-black text-petroleo">Aventuras Travel Pucallpa</h3>
                            <p class="text-xs text-petroleo/40 uppercase tracking-widest font-bold">Agencia de viajes certificada</p>
                        </div>
                    </div>
                    <p class="text-petroleo/60 leading-relaxed text-sm">
                        Somos tu puerta local a experiencias globales. Con años de experiencia organizando viajes familiares, 
                        escolares y corporativos, garantizamos que cada aventura sea inolvidable. Desde la selva amazónica hasta 
                        las playas del Caribe y las ciudades más emblemáticas del mundo.
                    </p>
                </div>

                <!-- Valores -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-petroleo/5">
                        <span class="material-symbols-outlined text-3xl text-turquesa mb-2">verified</span>
                        <p class="text-xs font-bold text-petroleo/60 uppercase tracking-widest">Certificada</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-petroleo/5">
                        <span class="material-symbols-outlined text-3xl text-turquesa mb-2">support_agent</span>
                        <p class="text-xs font-bold text-petroleo/60 uppercase tracking-widest">Soporte 24/7</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-petroleo/5">
                        <span class="material-symbols-outlined text-3xl text-turquesa mb-2">public</span>
                        <p class="text-xs font-bold text-petroleo/60 uppercase tracking-widest">Red Global</p>
                    </div>
                </div>

                <!-- Mapa de ubicación -->
                <div class="rounded-2xl overflow-hidden shadow-sm border border-petroleo/5 h-[200px]">
                    <iframe src="https://maps.google.com/maps?q=Jiron+Zavala+568A+Pucallpa&output=embed&z=16" 
                        class="w-full h-full border-0" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Imágenes curadas de Unsplash por destino
    const IMAGES = {
        'Cusco': 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=800&q=80&auto=format&fit=crop',
        'Machu Picchu': 'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=800&q=80&auto=format&fit=crop',
        'Iquitos': 'https://images.unsplash.com/photo-1563514227147-6d2ff665a6a0?w=800&q=80&auto=format&fit=crop',
        'Cancún, México': 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?w=800&q=80&auto=format&fit=crop',
        'París, Francia': 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&q=80&auto=format&fit=crop',
        'Punta Cana': 'https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=800&q=80&auto=format&fit=crop',
    };

    const nationals = [
        { name: 'Cusco', desc: 'Capital ancestral del Imperio Inca, donde la arquitectura colonial se funde con la piedra sagrada.', badge: 'Patrimonio' },
        { name: 'Machu Picchu', desc: 'La ciudadela inca entre las nubes, una de las maravillas del mundo moderno.', badge: 'Maravilla' },
        { name: 'Iquitos', desc: 'Puerta de la Amazonía peruana, biodiversidad infinita y ríos legendarios.', badge: 'Amazónico' },
    ];
    const internationals = [
        { name: 'Cancún, México', desc: 'Horizontes turquesa infinitos y vibrante cultura maya.', badge: 'Caribe' },
        { name: 'París, Francia', desc: 'El pináculo del arte, gastronomía y herencia arquitectónica.', badge: 'Europa' },
        { name: 'Punta Cana', desc: 'Resort todo incluido con playas de arena blanca y noches tropicales.', badge: 'All Inclusive' },
    ];

    function renderDestCard(dest, container) {
        const imgUrl = IMAGES[dest.name] || '<?= Router::url("/api/images") ?>?q=' + encodeURIComponent(dest.name + ' travel destination');
        const card = document.createElement('a');
        card.href = '<?= Router::url("/search/results") ?>?q=' + encodeURIComponent(dest.name);
        card.className = 'group relative h-72 rounded-2xl overflow-hidden cursor-pointer block';
        card.innerHTML = `
            <img src="${imgUrl}" alt="${dest.name}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
            <span class="absolute top-4 right-4 bg-turquesa/20 backdrop-blur-sm text-turquesa px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">${dest.badge}</span>
            <div class="absolute bottom-6 left-6 text-white">
                <h3 class="text-2xl font-black mb-1">${dest.name}</h3>
                <p class="text-sm text-white/70 max-w-xs">${dest.desc}</p>
            </div>
        `;
        container.appendChild(card);
    }

    const natContainer = document.getElementById('nationalDestinations');
    const intContainer = document.getElementById('internationalDestinations');
    nationals.forEach(d => renderDestCard(d, natContainer));
    internationals.forEach(d => renderDestCard(d, intContainer));
});
</script>
