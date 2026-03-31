<!-- BUSCADOR DE DESTINOS - PÁGINA INICIAL – TAILWIND -->

<section class="px-8 py-16 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="text-center mb-10">
        <span class="text-xs font-bold uppercase tracking-widest text-turquesa-dark">Exploración Global</span>
        <h1 class="text-4xl font-black text-petroleo mt-2 mb-3">¿A dónde quieres viajar?</h1>
        <p class="text-petroleo/50 max-w-xl mx-auto">Escribe el nombre de cualquier ciudad o país para ver clima en tiempo real, moneda, mapa interactivo, hoteles y experiencias imperdibles.</p>
    </div>

    <!-- Search Bar -->
    <form action="<?= Router::url('/search/results') ?>" method="GET" class="bg-white rounded-2xl shadow-xl p-2 flex items-center max-w-3xl mx-auto mb-16">
        <span class="material-symbols-outlined text-petroleo/30 ml-4 text-2xl">search</span>
        <input type="text" name="q" placeholder="Cusco, Cancún, Punta Cana, París..." autofocus
            class="flex-1 border-none bg-transparent px-4 py-4 text-lg font-medium text-petroleo focus:ring-0 placeholder:text-petroleo/30">
        <button type="submit" class="px-8 py-4 bg-gradient-to-r from-turquesa-dark to-turquesa text-white font-bold rounded-xl hover:shadow-lg transition-all active:scale-95">
            Explorar
        </button>
    </form>

    <!-- Popular Destinations -->
    <div class="mb-8">
        <h2 class="text-xl font-black text-petroleo mb-6">Destinos Populares</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php
            $populares = [
                ['Cusco', 'Perú', 'castle'],
                ['Machu Picchu', 'Perú', 'landscape'],
                ['Cancún', 'México', 'beach_access'],
                ['Punta Cana', 'Rep. Dominicana', 'pool'],
                ['París', 'Francia', 'flag'],
                ['Iquitos', 'Perú', 'forest'],
                ['Bali', 'Indonesia', 'temple_buddhist'],
                ['Roma', 'Italia', 'church'],
            ];
            foreach ($populares as $dest):
            ?>
            <a href="<?= Router::url('/search/results') ?>?q=<?= urlencode($dest[0]) ?>" 
               class="group bg-white rounded-xl p-5 border border-petroleo/5 hover:shadow-lg hover:border-turquesa/30 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-turquesa/10 text-turquesa-dark rounded-xl flex items-center justify-center group-hover:bg-turquesa group-hover:text-white transition-all">
                        <span class="material-symbols-outlined"><?= $dest[2] ?></span>
                    </div>
                    <div>
                        <p class="font-bold text-petroleo text-sm"><?= $dest[0] ?></p>
                        <p class="text-xs text-petroleo/40"><?= $dest[1] ?></p>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Features -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
        <div class="text-center p-6">
            <span class="material-symbols-outlined text-4xl text-turquesa mb-3">thermostat</span>
            <h3 class="font-bold text-petroleo mb-1">Clima en Tiempo Real</h3>
            <p class="text-xs text-petroleo/40">Temperatura, humedad y condiciones actuales vía Open-Meteo API.</p>
        </div>
        <div class="text-center p-6">
            <span class="material-symbols-outlined text-4xl text-turquesa mb-3">currency_exchange</span>
            <h3 class="font-bold text-petroleo mb-1">Tipo de Cambio</h3>
            <p class="text-xs text-petroleo/40">Moneda local y conversión actualizada vía MoneyConvert API.</p>
        </div>
        <div class="text-center p-6">
            <span class="material-symbols-outlined text-4xl text-turquesa mb-3">hotel</span>
            <h3 class="font-bold text-petroleo mb-1">Hoteles y Experiencias</h3>
            <p class="text-xs text-petroleo/40">Hospedajes recomendados y lugares turísticos imperdibles.</p>
        </div>
    </div>
</section>
