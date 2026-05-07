<!-- HOMEPAGE – TAILWIND – AVENTURAS TRAVEL PUCALLPA -->

<!-- HERO - MEJORADO CON IMAGEN MODERNA -->
<section class="relative h-[60vh] sm:h-[75vh] md:h-[85vh] min-h-[400px] sm:min-h-[500px] md:min-h-[600px] overflow-hidden group">
    <!-- Hero Image: Amazonas / Jungle -->
    <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1600&h=900&fit=crop&q=80" alt="Aventura en la Naturaleza" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
    <!-- Gradient mejorado con más contraste -->
    <div class="absolute inset-0 bg-gradient-to-r from-petroleo-dark/85 via-petroleo/70 to-turquesa-dark/50"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark/95 via-transparent to-transparent"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 md:px-8 h-full flex flex-col justify-center items-center text-center">
        <!-- Badge animado -->
        <span class="inline-block bg-gradient-to-r from-turquesa/40 to-coral/30 backdrop-blur-md text-turquesa-light px-4 sm:px-5 py-2 rounded-full text-[11px] sm:text-xs font-bold uppercase tracking-widest mb-6 sm:mb-8 border border-turquesa/60 shadow-lg shadow-turquesa/30 animate-pulse">
            ✨ Aventuras Travel Pucallpa - Donde comienzan los sueños
        </span>
        
        <!-- Headline mejorado con más impacto -->
        <h1 class="text-4xl sm:text-5xl md:text-7xl lg:text-8xl font-black text-white tracking-tight leading-[0.9] mb-4 sm:mb-6 drop-shadow-2xl">
            Vive <span class="text-transparent bg-clip-text bg-gradient-to-r from-turquesa-light via-turquesa to-coral">Experiencias</span><br><span class="text-coral drop-shadow-xl">Inolvidables</span>
        </h1>
        
        <!-- Subtitle mejorado -->
        <p class="text-sm sm:text-lg md:text-xl lg:text-2xl text-white/90 max-w-3xl mb-8 sm:mb-12 md:mb-14 drop-shadow-lg leading-relaxed font-medium">
            Desde el corazón de la Amazonía hasta los destinos más exóticos del mundo. <span class="text-turquesa-light font-bold">Descubre, explora y vive cada momento</span> con Aventuras Travel.
        </p>

        <!-- Buscador centrado dentro del hero con autocomplete -->
        <div class="w-full max-w-2xl relative" id="searchWrapper">
            <form action="<?= Router::url('/search/results') ?>" method="GET" id="heroSearchForm"
                  class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl p-1.5 sm:p-2 flex items-center relative z-20 border border-white/20">
                <span class="material-symbols-outlined text-turquesa-dark/60 ml-2 sm:ml-4 text-xl sm:text-2xl">search</span>
                <input type="text" name="q" id="heroSearchInput" autocomplete="off"
                    placeholder="¿A dónde quieres viajar? Ej: Cusco, Cancún, París..." 
                    class="flex-1 border-none bg-transparent px-2 sm:px-4 py-3 sm:py-4 text-sm sm:text-base md:text-lg font-medium text-petroleo focus:ring-0 placeholder:text-petroleo/30">
                <button type="submit" class="px-4 sm:px-6 md:px-8 py-3 sm:py-4 bg-gradient-to-r from-turquesa-dark to-turquesa text-white text-sm sm:text-base font-bold rounded-xl hover:shadow-xl transition-all active:scale-95 whitespace-nowrap shadow-lg">
                    Explorar
                </button>
            </form>
            <!-- Autocomplete dropdown -->
            <div id="searchSuggestions" class="ac-dropdown hidden"></div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 mt-5 sm:mt-8 w-full sm:w-auto">
            <a href="<?= Router::url('/search') ?>" class="px-6 sm:px-8 py-3 bg-turquesa/20 backdrop-blur-md text-white font-semibold rounded-xl border border-turquesa/40 hover:bg-turquesa/40 transition-all text-sm text-center shadow-lg shadow-turquesa/20">
                Ver Destinos
            </a>
            <a href="#asesoria" class="px-6 sm:px-8 py-3 bg-white/15 backdrop-blur-md text-white font-semibold rounded-xl border border-white/30 hover:bg-white/25 transition-all text-sm text-center shadow-lg">
                Asesoría Personalizada
            </a>
        </div>
    </div>
</section>

<!-- DESTINOS NACIONALES - MEJORADO -->
<section class="relative pt-16 sm:pt-20 md:pt-24 pb-16 sm:pb-20 md:pb-24 px-4 sm:px-6 md:px-8 max-w-7xl mx-auto">
    <!-- Decoración de fondo -->
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-turquesa/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-coral/5 rounded-full blur-3xl pointer-events-none"></div>
    
    <!-- Header -->
    <div class="relative z-10 mb-12 sm:mb-14 md:mb-16">
        <div class="flex items-center gap-2 mb-2">
            <span class="w-1 h-8 bg-gradient-to-b from-turquesa to-coral rounded-full"></span>
            <span class="text-xs font-black uppercase tracking-widest text-turquesa-dark">Nuestras Joyas</span>
        </div>
        <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-petroleo tracking-tight mb-2">Destinos Nacionales Premium</h2>
        <p class="text-petroleo/60 text-base sm:text-lg">Experiencias curadas en los rincones más espectaculares del Perú</p>
    </div>
    
    <!-- Grid de destinos mejorado -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6 relative z-10" id="nationalDestinations">
        <!-- Cusco / Machu Picchu -->
        <a href="<?= Router::url('/search/results') ?>?q=Cusco" class="group relative h-72 rounded-2xl overflow-hidden shadow-xl cursor-pointer transform transition-all duration-500 hover:scale-105 hover:shadow-2xl">
            <img src="https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=600&h=500&fit=crop&q=80" alt="Machu Picchu - Cusco" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark via-petroleo/40 to-transparent"></div>
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-r from-turquesa/40 to-coral/20 transition-opacity duration-500"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6">
                <h3 class="text-2xl font-black text-white mb-1">Machu Picchu</h3>
                <p class="text-turquesa-light text-sm font-semibold">Cusco, Perú</p>
            </div>
            <div class="absolute top-4 right-4 w-10 h-10 bg-turquesa/80 backdrop-blur-md rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:scale-110">
                <span class="material-symbols-outlined text-white text-lg">arrow_forward</span>
            </div>
        </a>

        <!-- Iquitos / Amazonía -->
        <a href="<?= Router::url('/search/results') ?>?q=Iquitos" class="group relative h-72 rounded-2xl overflow-hidden shadow-xl cursor-pointer transform transition-all duration-500 hover:scale-105 hover:shadow-2xl">
            <img src="https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=600&h=500&fit=crop&q=80" alt="Iquitos - Amazonía" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark via-petroleo/40 to-transparent"></div>
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-r from-turquesa/40 to-coral/20 transition-opacity duration-500"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6">
                <h3 class="text-2xl font-black text-white mb-1">Iquitos</h3>
                <p class="text-turquesa-light text-sm font-semibold">Selva Amazónica, Perú</p>
            </div>
            <div class="absolute top-4 right-4 w-10 h-10 bg-turquesa/80 backdrop-blur-md rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:scale-110">
                <span class="material-symbols-outlined text-white text-lg">arrow_forward</span>
            </div>
        </a>

        <!-- Lima / Capital -->
        <a href="<?= Router::url('/search/results') ?>?q=Lima" class="group relative h-72 rounded-2xl overflow-hidden shadow-xl cursor-pointer transform transition-all duration-500 hover:scale-105 hover:shadow-2xl">
            <img src="https://images.unsplash.com/photo-1577587230708-187fdbef4d91?w=600&h=500&fit=crop&q=80" alt="Lima - Capital" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark via-petroleo/40 to-transparent"></div>
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-r from-turquesa/40 to-coral/20 transition-opacity duration-500"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6">
                <h3 class="text-2xl font-black text-white mb-1">Lima</h3>
                <p class="text-turquesa-light text-sm font-semibold">Capital Peruana</p>
            </div>
            <div class="absolute top-4 right-4 w-10 h-10 bg-turquesa/80 backdrop-blur-md rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:scale-110">
                <span class="material-symbols-outlined text-white text-lg">arrow_forward</span>
            </div>
        </a>
    </div>
</section>

<!-- PROMOCIONES - MEJORADO CON GRADIENTES Y COLORES VIBRANTES -->
<section class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 md:px-8">
    <div class="max-w-7xl mx-auto relative overflow-hidden rounded-[2.5rem] shadow-2xl" 
         style="background: linear-gradient(135deg, #1B3A4B 0%, #0D2432 25%, #00687A 50%, #1B3A4B 75%, #2D5468 100%);">
        
        <!-- Decoraciones de fondo mejoradas -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-turquesa/15 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-coral/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4 pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-gold/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>

        <div class="relative z-10 p-8 sm:p-12 lg:p-16 flex flex-col lg:flex-row items-center gap-10 lg:gap-16">
            
            <!-- Left Content - Mejorado -->
            <div class="flex-1 text-center lg:text-left">
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-gradient-to-r from-turquesa/20 to-coral/20 border border-turquesa/40 text-turquesa-light text-xs font-black uppercase tracking-widest mb-6">
                    <span class="material-symbols-outlined text-base animate-spin" style="animation-duration: 3s;">local_fire_department</span>
                    Ofertas Exclusivas 2026
                </div>
                <h2 class="text-4xl sm:text-5xl font-black text-white mb-4 tracking-tight">
                    Viajes a Precio de <span class="text-transparent bg-clip-text bg-gradient-to-r from-turquesa-light to-coral">Fuego</span>
                </h2>
                <p class="text-white/75 text-base sm:text-lg max-w-lg mx-auto lg:mx-0 mb-8 leading-relaxed">
                    Cupos limitados. ¡No dejes pasar la oportunidad de vivir la aventura de tu vida con Aventuras Travel! Descuentos de hasta 40% en destinos premium.
                </p>
                <a href="<?= Router::url('/promotions') ?>" class="inline-flex items-center gap-3 bg-gradient-to-r from-turquesa-dark to-turquesa text-white font-black py-4 px-8 rounded-xl hover:-translate-y-1 hover:shadow-2xl hover:shadow-turquesa/40 transition-all duration-300 text-lg shadow-lg shadow-turquesa/30 group">
                    Explorar Ofertas
                    <span class="material-symbols-outlined text-xl group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>

            <!-- Right Promo Cards - Mejorado -->
            <div class="flex flex-col sm:flex-row gap-5 w-full lg:w-auto">
                <?php if (!empty($promociones)): ?>
                    <?php foreach (array_slice($promociones, 0, 2) as $promo): 
                        $promoSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $promo['titulo'] ?? 'promo'), '-'));
                    ?>
                    <a href="<?= Router::url('/promotions') ?>#<?= htmlspecialchars($promoSlug) ?>" class="group relative flex-1 min-w-[260px] bg-gradient-to-br from-white/15 to-white/5 hover:from-white/25 hover:to-white/10 border border-white/20 hover:border-turquesa/60 backdrop-blur-xl rounded-2xl p-6 transition-all duration-400 hover:-translate-y-2 overflow-hidden shadow-xl hover:shadow-2xl hover:shadow-turquesa/30">
                        
                        <!-- Glow animado -->
                        <div class="absolute inset-0 bg-gradient-to-br from-turquesa/0 via-turquesa/0 to-turquesa/0 group-hover:from-turquesa/20 group-hover:via-transparent group-hover:to-coral/10 transition-all duration-500"></div>
                        
                        <div class="relative z-10">
                            <!-- Badge -->
                            <div class="inline-flex items-center gap-1.5 bg-gradient-to-r from-coral/30 to-gold/30 text-coral text-xs font-black uppercase tracking-widest px-3 py-1.5 rounded-lg mb-4 border border-coral/40">
                                <span class="material-symbols-outlined text-sm">local_offer</span>
                                Limitado
                            </div>
                            
                            <!-- Título -->
                            <h3 class="text-xl font-black text-white mb-2 text-left"><?= htmlspecialchars($promo['titulo'] ?? 'Promoción') ?></h3>
                            
<!-- HOMEPAGE – TAILWIND – AVENTURAS TRAVEL PUCALLPA -->

<!-- HERO - MEJORADO CON IMAGEN MODERNA -->
<section class="relative h-[60vh] sm:h-[75vh] md:h-[85vh] min-h-[400px] sm:min-h-[500px] md:min-h-[600px] overflow-hidden group">
    <!-- Hero Image: Amazonas / Jungle -->
    <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1600&h=900&fit=crop&q=80" alt="Aventura en la Naturaleza" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                                <span class="text-xs text-white/50 font-semibold">Válido hasta <?= !empty($promo['fecha_fin']) ? date('M d', strtotime($promo['fecha_fin'])) : 'fin de mes' ?></span>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- DESTINOS INTERNACIONALES -->
<section class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 md:px-8 max-w-7xl mx-auto relative">
    <!-- Decoración de fondo -->
    <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-96 h-96 bg-coral/5 rounded-full blur-3xl pointer-events-none"></div>
    
    <!-- Grid de destinos internacionales -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6 relative z-10">
        <!-- Cancún, México -->
        <a href="<?= Router::url('/search/results') ?>?q=Cancún" class="group relative h-72 rounded-2xl overflow-hidden shadow-xl cursor-pointer transform transition-all duration-500 hover:scale-105 hover:shadow-2xl">
            <img src="https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?w=600&h=500&fit=crop&q=80" alt="Cancún - Playas" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark via-petroleo/40 to-transparent"></div>
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-r from-turquesa/40 to-coral/20 transition-opacity duration-500"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6">
                <h3 class="text-2xl font-black text-white mb-1">Cancún</h3>
                <p class="text-turquesa-light text-sm font-semibold">México</p>
            </div>
            <div class="absolute top-4 right-4 w-10 h-10 bg-coral/80 backdrop-blur-md rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:scale-110">
                <span class="material-symbols-outlined text-white text-lg">arrow_forward</span>
            </div>
        </a>

        <!-- Punta Cana, República Dominicana -->
        <a href="<?= Router::url('/search/results') ?>?q=Punta%20Cana" class="group relative h-72 rounded-2xl overflow-hidden shadow-xl cursor-pointer transform transition-all duration-500 hover:scale-105 hover:shadow-2xl">
            <img src="https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=600&h=500&fit=crop&q=80" alt="Punta Cana" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark via-petroleo/40 to-transparent"></div>
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-r from-turquesa/40 to-coral/20 transition-opacity duration-500"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6">
                <h3 class="text-2xl font-black text-white mb-1">Punta Cana</h3>
                <p class="text-turquesa-light text-sm font-semibold">Rep. Dominicana</p>
            </div>
            <div class="absolute top-4 right-4 w-10 h-10 bg-coral/80 backdrop-blur-md rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:scale-110">
                <span class="material-symbols-outlined text-white text-lg">arrow_forward</span>
            </div>
        </a>

        <!-- París, Francia -->
        <a href="<?= Router::url('/search/results') ?>?q=París" class="group relative h-72 rounded-2xl overflow-hidden shadow-xl cursor-pointer transform transition-all duration-500 hover:scale-105 hover:shadow-2xl">
            <img src="https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=600&h=500&fit=crop&q=80" alt="París" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark via-petroleo/40 to-transparent"></div>
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-r from-turquesa/40 to-coral/20 transition-opacity duration-500"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6">
                <h3 class="text-2xl font-black text-white mb-1">París</h3>
                <p class="text-turquesa-light text-sm font-semibold">Francia</p>
            </div>
            <div class="absolute top-4 right-4 w-10 h-10 bg-coral/80 backdrop-blur-md rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:scale-110">
                <span class="material-symbols-outlined text-white text-lg">arrow_forward</span>
            </div>
        </a>

        <!-- Bali, Indonesia -->
        <a href="<?= Router::url('/search/results') ?>?q=Bali" class="group relative h-72 rounded-2xl overflow-hidden shadow-xl cursor-pointer transform transition-all duration-500 hover:scale-105 hover:shadow-2xl">
            <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&h=500&fit=crop&q=80" alt="Bali" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark via-petroleo/40 to-transparent"></div>
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-r from-turquesa/40 to-coral/20 transition-opacity duration-500"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6">
                <h3 class="text-2xl font-black text-white mb-1">Bali</h3>
                <p class="text-turquesa-light text-sm font-semibold">Indonesia</p>
            </div>
            <div class="absolute top-4 right-4 w-10 h-10 bg-coral/80 backdrop-blur-md rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:scale-110">
                <span class="material-symbols-outlined text-white text-lg">arrow_forward</span>
            </div>
        </a>

        <!-- Roma, Italia -->
        <a href="<?= Router::url('/search/results') ?>?q=Roma" class="group relative h-72 rounded-2xl overflow-hidden shadow-xl cursor-pointer transform transition-all duration-500 hover:scale-105 hover:shadow-2xl">
            <img src="https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=600&h=500&fit=crop&q=80" alt="Roma" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark via-petroleo/40 to-transparent"></div>
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-r from-turquesa/40 to-coral/20 transition-opacity duration-500"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6">
                <h3 class="text-2xl font-black text-white mb-1">Roma</h3>
                <p class="text-turquesa-light text-sm font-semibold">Italia</p>
            </div>
            <div class="absolute top-4 right-4 w-10 h-10 bg-coral/80 backdrop-blur-md rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:scale-110">
                <span class="material-symbols-outlined text-white text-lg">arrow_forward</span>
            </div>
        </a>

        <!-- Miami, USA -->
        <a href="<?= Router::url('/search/results') ?>?q=Miami" class="group relative h-72 rounded-2xl overflow-hidden shadow-xl cursor-pointer transform transition-all duration-500 hover:scale-105 hover:shadow-2xl">
            <img src="https://images.unsplash.com/photo-1533106497176-45ae19e68ba2?w=600&h=500&fit=crop&q=80" alt="Miami" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark via-petroleo/40 to-transparent"></div>
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-gradient-to-r from-turquesa/40 to-coral/20 transition-opacity duration-500"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6">
                <h3 class="text-2xl font-black text-white mb-1">Miami</h3>
                <p class="text-turquesa-light text-sm font-semibold">Estados Unidos</p>
            </div>
            <div class="absolute top-4 right-4 w-10 h-10 bg-coral/80 backdrop-blur-md rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:scale-110">
                <span class="material-symbols-outlined text-white text-lg">arrow_forward</span>
            </div>
        </a>
    </div>
</section>

<!-- ASESORÍA PERSONALIZADA (CON DATOS DE JAVIER) -->
<section class="section-dark py-12 sm:py-16 md:py-20 px-4 sm:px-6 md:px-8" id="asesoria">
    <div class="max-w-6xl mx-auto relative">
        <div class="text-center mb-8 sm:mb-12">
            <span class="section-label" style="background:rgba(74,190,217,0.15);color:#7DD3E8;border-color:rgba(74,190,217,0.35);">Tu Viaje, Nuestra Pasión</span>
            <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-2">Asesoría <span style="color:#4ABED9;">Personalizada</span></h2>
            <p class="max-w-xl mx-auto mt-3 text-sm sm:text-base" style="color:rgba(255,255,255,0.6);">Cada viaje es único. Contáctanos y diseñaremos la experiencia perfecta para ti y tu familia.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 lg:gap-10 items-center">
            <!-- Card de Javier -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-turquesa/20">
                <div class="bg-gradient-to-r from-petroleo-dark via-petroleo to-petroleo-light p-4 sm:p-6 md:p-8 flex flex-col sm:flex-row items-center gap-4 sm:gap-6 border-b-4 border-turquesa">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-2xl overflow-hidden border-4 border-turquesa/30 shadow-xl shrink-0 ring-2 ring-turquesa/20">
                        <img src="<?= Router::url('/img/javier.jpg') ?>" alt="Javier Edgar Sandy Da Cruz" class="w-full h-full object-cover">
                    </div>
                    <div class="text-white text-center sm:text-left">
                        <h3 class="text-xl sm:text-2xl font-black drop-shadow-lg">Javier Edgar Sandy Da Cruz</h3>
                        <p class="text-turquesa-light text-sm font-bold uppercase tracking-widest mt-1 drop-shadow">CEO & Fundador</p>
                        <p class="text-white/70 text-sm mt-2">Responsable de tus aventuras</p>
                    </div>
                </div>
                <div class="p-4 sm:p-6 md:p-8 space-y-4 sm:space-y-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-turquesa/20 to-turquesa-dark/10 flex items-center justify-center shrink-0 shadow-md">
                            <span class="material-symbols-outlined text-turquesa-dark text-2xl">location_on</span>
                        </div>
                        <div>
                            <p class="text-xs text-petroleo/40 uppercase tracking-widest font-bold">Dirección</p>
                            <p class="text-sm font-semibold text-petroleo">Jirón Zavala 568A, Pucallpa</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-turquesa/20 to-turquesa-dark/10 flex items-center justify-center shrink-0 shadow-md">
                            <span class="material-symbols-outlined text-turquesa-dark text-2xl">call</span>
                        </div>
                        <div>
                            <p class="text-xs text-petroleo/40 uppercase tracking-widest font-bold">Teléfono / WhatsApp</p>
                            <p class="text-sm font-semibold text-petroleo">976 324 716</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-turquesa/20 to-turquesa-dark/10 flex items-center justify-center shrink-0 shadow-md">
                            <span class="material-symbols-outlined text-turquesa-dark text-2xl">mail</span>
                        </div>
                        <div>
                            <p class="text-xs text-petroleo/40 uppercase tracking-widest font-bold">Email</p>
                            <p class="text-sm font-semibold text-petroleo">aventurastravelpucallpa@gmail.com</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-turquesa/20 to-turquesa-dark/10 flex items-center justify-center shrink-0 shadow-md">
                            <span class="material-symbols-outlined text-turquesa-dark text-2xl">badge</span>
                        </div>
                        <div>
                            <p class="text-xs text-petroleo/40 uppercase tracking-widest font-bold">RUC</p>
                            <p class="text-sm font-semibold text-petroleo">10475951587</p>
                        </div>
                    </div>

                    <!-- Botón WhatsApp -->
                    <a href="https://wa.me/51976324716?text=Hola%20Javier%2C%20me%20interesa%20información%20sobre%20un%20viaje" target="_blank"
                        class="flex items-center justify-center gap-3 w-full py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold rounded-xl transition-all active:scale-95 shadow-lg hover:shadow-xl mt-6">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Escríbeme por WhatsApp
                    </a>
                </div>
            </div>

            <!-- Info lateral -->
            <div class="space-y-6 sm:space-y-8">
                <div class="card-glass rounded-2xl p-5 sm:p-6" style="border:2px solid rgba(74,190,217,0.25);">
                    <div class="flex items-center gap-4 mb-5">
                        <img src="<?= Router::url('/img/a_color.png') ?>" alt="Logo Aventuras Travel" class="h-12 drop-shadow">
                        <div>
                            <h3 class="text-xl font-black" style="color:#1B3A4B;">Aventuras Travel Pucallpa</h3>
                            <p class="text-xs font-bold uppercase tracking-widest" style="color:#00687A;">Agencia de viajes certificada</p>
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed" style="color:#2D5468;font-weight:500;">
                        Somos tu puerta local a experiencias globales. Con años de experiencia organizando viajes familiares,
                        escolares y corporativos, garantizamos que cada aventura sea inolvidable. Desde la selva amazónica hasta
                        las playas del Caribe y las ciudades más emblemáticas del mundo.
                    </p>
                </div>

                <!-- Valores -->
                <div class="grid grid-cols-3 gap-3 sm:gap-4">
                    <div class="rounded-2xl p-4 text-center" style="background:linear-gradient(135deg,#00687A,#4ABED9);">
                        <span class="material-symbols-outlined text-3xl text-white mb-2">verified</span>
                        <p class="text-xs font-bold text-white uppercase tracking-widest">Certificada</p>
                    </div>
                    <div class="rounded-2xl p-4 text-center" style="background:linear-gradient(135deg,#0D2432,#1B3A4B);border:1px solid rgba(74,190,217,0.2);">
                        <span class="material-symbols-outlined text-3xl mb-2" style="color:#4ABED9;">support_agent</span>
                        <p class="text-xs font-bold text-white uppercase tracking-widest">Soporte 24/7</p>
                    </div>
                    <div class="rounded-2xl p-4 text-center" style="background:linear-gradient(135deg,#F4A633,#D4860F);">
                        <span class="material-symbols-outlined text-3xl text-white mb-2">public</span>
                        <p class="text-xs font-bold text-white uppercase tracking-widest">Red Global</p>
                    </div>
                </div>

                <!-- Mapa de ubicación -->
                <div class="rounded-2xl overflow-hidden h-[180px]" style="border:2px solid rgba(74,190,217,0.2);">
                    <iframe src="https://maps.google.com/maps?q=Jiron+Zavala+568A+Pucallpa&output=embed&z=16"
                        class="w-full h-full border-0" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- AUTOCOMPLETE SEARCH -->
<style>
.ac-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 15;
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
.ac-dropdown.hidden { display: none; }
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
.ac-item:hover, .ac-item.ac-active {
    background: rgba(74,190,217,0.08);
}
.ac-icon {
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, rgba(74,190,217,0.12), rgba(0,104,122,0.08));
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.25rem;
}
.ac-icon img {
    width: 1.5rem;
    height: 1.125rem;
    object-fit: cover;
    border-radius: 2px;
}
.ac-info { flex: 1; min-width: 0; }
.ac-name {
    font-weight: 700;
    font-size: 0.9375rem;
    color: #1B3A4B;
    line-height: 1.2;
}
.ac-name mark {
    background: rgba(74,190,217,0.25);
    color: #00687A;
    border-radius: 2px;
    padding: 0 1px;
}
.ac-region {
    font-size: 0.75rem;
    color: rgba(27,58,75,0.45);
    margin-top: 0.125rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ac-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.125rem;
    flex-shrink: 0;
}
.ac-temp {
    font-size: 0.8125rem;
    font-weight: 800;
    color: #00687A;
}
.ac-type {
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: rgba(27,58,75,0.3);
}
.ac-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1.25rem;
    color: rgba(27,58,75,0.35);
    font-size: 0.8125rem;
    font-weight: 600;
}
.ac-empty {
    padding: 1.5rem;
    text-align: center;
    color: rgba(27,58,75,0.35);
    font-size: 0.8125rem;
}
</style>
<script>
(function() {
    const input = document.getElementById('heroSearchInput');
    const dropdown = document.getElementById('searchSuggestions');
    const form = document.getElementById('heroSearchForm');
    if (!input || !dropdown) return;

    let debounceTimer = null;
    let activeIdx = -1;
    let currentResults = [];
    const SEARCH_URL = '<?= Router::url("/search/results") ?>';

    // Country code -> flag emoji
    function countryFlag(code) {
        if (!code || code.length !== 2) return '🌍';
        const c = code.toUpperCase();
        return String.fromCodePoint(...[...c].map(l => 0x1F1E6 - 65 + l.charCodeAt(0)));
    }

    // Highlight matching text
    function highlight(text, query) {
        if (!query) return text;
        const r = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
        return text.replace(r, '<mark>$1</mark>');
    }

    // Fetch suggestions from Open-Meteo Geocoding API
    async function fetchSuggestions(query) {
        if (query.length < 2) { hide(); return; }

        show();
        dropdown.innerHTML = '<div class="ac-loading"><span class="material-symbols-outlined text-lg animate-spin">progress_activity</span>Buscando destinos...</div>';

        try {
            const res = await fetch('https://geocoding-api.open-meteo.com/v1/search?name=' + encodeURIComponent(query) + '&count=6&language=es&format=json');
            const data = await res.json();

            if (!data.results || data.results.length === 0) {
                dropdown.innerHTML = '<div class="ac-empty"><span class="material-symbols-outlined text-2xl block mb-1" style="opacity:.3">location_off</span>No se encontraron destinos para "' + query + '"</div>';
                currentResults = [];
                return;
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
            const name = r.name || '';
            const admin = r.admin1 || '';
            const country = r.country || '';
            const cc = r.country_code || '';
            const flag = countryFlag(cc);
            const region = [admin, country].filter(Boolean).join(', ');
            const pop = r.population ? (r.population > 1000000 ? (r.population / 1000000).toFixed(1) + 'M hab.' : (r.population > 1000 ? Math.round(r.population / 1000) + 'K hab.' : r.population + ' hab.')) : '';
            const elevation = r.elevation ? Math.round(r.elevation) + ' m' : '';
            const typeMap = { 'PPLC': 'Capital', 'PPLA': 'Ciudad', 'PPLA2': 'Ciudad', 'PPL': 'Localidad' };
            const fcode = typeMap[r.feature_code] || '';

            return '<a class="ac-item' + (i === activeIdx ? ' ac-active' : '') + '" data-idx="' + i + '" href="' + SEARCH_URL + '?q=' + encodeURIComponent(name + (country ? ', ' + country : '')) + '">'
                + '<div class="ac-icon"><span style="font-size:1.5rem">' + flag + '</span></div>'
                + '<div class="ac-info">'
                + '<div class="ac-name">' + highlight(name, query) + '</div>'
                + '<div class="ac-region">' + region + (pop ? ' · ' + pop : '') + (elevation ? ' · ' + elevation : '') + '</div>'
                + '</div>'
                + '<div class="ac-meta">'
                + (fcode ? '<span class="ac-type">' + fcode + '</span>' : '')
                + '<span class="ac-temp"><span class="material-symbols-outlined" style="font-size:14px;vertical-align:middle">arrow_forward</span></span>'
                + '</div>'
                + '</a>';
        }).join('');
    }

    function show() { dropdown.classList.remove('hidden'); }
    function hide() { dropdown.classList.add('hidden'); activeIdx = -1; currentResults = []; }

    // Debounced input handler
    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 2) { hide(); return; }
        debounceTimer = setTimeout(function() { fetchSuggestions(q); }, 280);
    });

    // Keyboard navigation
    input.addEventListener('keydown', function(e) {
        if (dropdown.classList.contains('hidden') || currentResults.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIdx = Math.min(activeIdx + 1, currentResults.length - 1);
            updateActive();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIdx = Math.max(activeIdx - 1, -1);
            updateActive();
        } else if (e.key === 'Enter' && activeIdx >= 0) {
            e.preventDefault();
            var item = dropdown.querySelector('.ac-item[data-idx="' + activeIdx + '"]');
            if (item) window.location.href = item.href;
        } else if (e.key === 'Escape') {
            hide();
        }
    });

    function updateActive() {
        var items = dropdown.querySelectorAll('.ac-item');
        items.forEach(function(el, i) {
            el.classList.toggle('ac-active', i === activeIdx);
        });
        if (activeIdx >= 0 && currentResults[activeIdx]) {
            input.value = currentResults[activeIdx].name;
        }
    }

    // Click outside to close
    document.addEventListener('click', function(e) {
        if (!document.getElementById('searchWrapper').contains(e.target)) {
            hide();
        }
    });

    // Focus to reopen
    input.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && currentResults.length > 0) {
            show();
            renderResults(this.value.trim());
        }
    });
})();
</script>
