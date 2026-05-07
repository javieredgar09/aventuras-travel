<!-- ASESORÍAS – PÁGINA DEDICADA – AVENTURAS TRAVEL PUCALLPA -->

<!-- Hero mini -->
<section class="relative h-[240px] sm:h-[280px] md:h-[340px] overflow-hidden">
    <img src="<?= Router::url('/img/machu.jpg') ?>" alt="Asesorías" class="absolute inset-0 w-full h-full object-cover"
        onerror="this.src='https://images.unsplash.com/photo-1526392060635-9d6019884377?w=1600&q=80'">
    <div class="absolute inset-0 bg-gradient-to-t from-petroleo via-petroleo/70 to-petroleo/30"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 md:px-8 h-full flex flex-col justify-center items-center text-center">
        <span class="bg-turquesa/20 backdrop-blur-sm text-turquesa px-3 sm:px-4 py-1.5 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-widest mb-3 sm:mb-4">Tu Viaje, Nuestra Pasión</span>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight">Asesoría Personalizada</h1>
        <p class="text-white/60 mt-2 sm:mt-3 max-w-xl text-sm sm:text-base">Cada viaje es único. Te acompañamos en cada paso para diseñar la experiencia perfecta para ti y tu familia.</p>
    </div>
</section>

<!-- Contenido principal -->
<section class="py-10 sm:py-14 md:py-16 px-4 sm:px-6 md:px-8">
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 sm:gap-8 lg:gap-10">

            <!-- Columna izquierda: Card de Javier (3 cols) -->
            <div class="lg:col-span-3 space-y-8">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-petroleo/5">
                    <!-- Header con foto -->
                    <div class="bg-gradient-to-r from-petroleo to-petroleo-light p-4 sm:p-6 md:p-8 flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-2xl overflow-hidden border-4 border-white/20 shadow-lg shrink-0">
                            <img src="<?= Router::url('/img/javier.jpg') ?>" alt="Javier Edgar Sandy Da Cruz" class="w-full h-full object-cover">
                        </div>
                        <div class="text-white text-center sm:text-left">
                            <h2 class="text-xl sm:text-2xl font-black">Javier Edgar Sandy Da Cruz</h2>
                            <p class="text-turquesa text-sm font-bold uppercase tracking-widest mt-1">CEO & Fundador</p>
                            <p class="text-white/60 text-sm mt-2">Responsable de tus aventuras</p>
                        </div>
                    </div>

                    <!-- Datos de contacto -->
                    <div class="p-4 sm:p-6 md:p-8 space-y-4 sm:space-y-5">
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
                                <p class="text-sm font-semibold text-petroleo">aventurastravelpucallpa@gmail.com</p>
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
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-turquesa/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-turquesa-dark text-2xl">schedule</span>
                            </div>
                            <div>
                                <p class="text-xs text-petroleo/40 uppercase tracking-widest font-bold">Horario de Atención</p>
                                <p class="text-sm font-semibold text-petroleo">Lunes a Sábado: 9:00 AM – 7:00 PM</p>
                            </div>
                        </div>

                        <!-- Botón WhatsApp grande -->
                        <a href="https://wa.me/51976324716?text=Hola%20Javier%2C%20me%20interesa%20información%20sobre%20un%20viaje%20con%20Aventuras%20Travel" target="_blank"
                            class="flex items-center justify-center gap-3 w-full py-4 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl transition-all active:scale-95 shadow-lg hover:shadow-xl mt-4">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Escríbeme por WhatsApp
                        </a>

                        <!-- Botón llamar -->
                        <a href="tel:+51976324716"
                            class="flex items-center justify-center gap-3 w-full py-4 bg-turquesa-dark hover:bg-petroleo text-white font-bold rounded-xl transition-all active:scale-95">
                            <span class="material-symbols-outlined text-xl">call</span>
                            Llamar Ahora
                        </a>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Info empresa + Mapa (2 cols) -->
            <div class="lg:col-span-2 space-y-6 sm:space-y-8">
                <!-- Logo y descripción -->
                <div class="bg-superficie rounded-2xl p-5 sm:p-6 md:p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <img src="<?= Router::url('/img/a_color.png') ?>" alt="Logo Aventuras Travel" class="h-14">
                        <div>
                            <h3 class="text-xl font-black text-petroleo">Aventuras Travel Pucallpa</h3>
                            <p class="text-xs text-petroleo/40 uppercase tracking-widest font-bold">Agencia de viajes certificada</p>
                        </div>
                    </div>
                    <p class="text-petroleo/60 leading-relaxed text-sm">
                        Somos tu puerta local a experiencias globales. Con años de experiencia organizando viajes familiares,
                        escolares y corporativos, garantizamos que cada aventura sea inolvidable.
                    </p>
                </div>

                <!-- Valores -->
                <div class="grid grid-cols-3 gap-2 sm:gap-4">
                    <div class="bg-white rounded-2xl p-3 sm:p-5 text-center shadow-sm border border-petroleo/5">
                        <span class="material-symbols-outlined text-3xl text-turquesa mb-2">verified</span>
                        <p class="text-[10px] font-bold text-petroleo/60 uppercase tracking-widest">Certificada</p>
                    </div>
                    <div class="bg-white rounded-2xl p-3 sm:p-5 text-center shadow-sm border border-petroleo/5">
                        <span class="material-symbols-outlined text-3xl text-turquesa mb-2">support_agent</span>
                        <p class="text-[10px] font-bold text-petroleo/60 uppercase tracking-widest">Soporte 24/7</p>
                    </div>
                    <div class="bg-white rounded-2xl p-3 sm:p-5 text-center shadow-sm border border-petroleo/5">
                        <span class="material-symbols-outlined text-3xl text-turquesa mb-2">public</span>
                        <p class="text-[10px] font-bold text-petroleo/60 uppercase tracking-widest">Red Global</p>
                    </div>
                </div>

                <!-- Servicios -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 md:p-8 shadow-sm border border-petroleo/5">
                    <h3 class="font-black text-petroleo mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-turquesa">travel_explore</span>
                        Nuestros Servicios
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 py-2 border-b border-petroleo/5">
                            <span class="material-symbols-outlined text-turquesa text-lg">family_restroom</span>
                            <span class="text-sm text-petroleo/70">Viajes Familiares</span>
                        </div>
                        <div class="flex items-center gap-3 py-2 border-b border-petroleo/5">
                            <span class="material-symbols-outlined text-turquesa text-lg">school</span>
                            <span class="text-sm text-petroleo/70">Viajes Escolares (Promociones)</span>
                        </div>
                        <div class="flex items-center gap-3 py-2 border-b border-petroleo/5">
                            <span class="material-symbols-outlined text-turquesa text-lg">business</span>
                            <span class="text-sm text-petroleo/70">Paquetes Corporativos</span>
                        </div>
                        <div class="flex items-center gap-3 py-2 border-b border-petroleo/5">
                            <span class="material-symbols-outlined text-turquesa text-lg">flight</span>
                            <span class="text-sm text-petroleo/70">Boletos Aéreos</span>
                        </div>
                        <div class="flex items-center gap-3 py-2">
                            <span class="material-symbols-outlined text-turquesa text-lg">hotel</span>
                            <span class="text-sm text-petroleo/70">Reservas de Hoteles</span>
                        </div>
                    </div>
                </div>

                <!-- Mapa -->
                <div class="rounded-2xl overflow-hidden shadow-sm border border-petroleo/5 h-[220px]">
                    <iframe src="https://maps.google.com/maps?q=Jiron+Zavala+568A+Pucallpa&output=embed&z=16"
                        class="w-full h-full border-0" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Final -->
<section class="py-10 sm:py-14 md:py-16 px-4 sm:px-6 md:px-8 bg-gradient-to-r from-petroleo to-petroleo-light">
    <div class="max-w-3xl mx-auto text-center text-white">
        <h2 class="text-2xl sm:text-3xl font-black mb-3">¿Listo para tu próxima aventura?</h2>
        <p class="text-white/60 mb-6 sm:mb-8 text-sm sm:text-base">Contáctanos hoy y recibe una cotización personalizada sin compromiso.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4 flex-wrap">
            <a href="https://wa.me/51976324716?text=Hola%20Javier%2C%20quiero%20cotizar%20un%20viaje" target="_blank"
                class="px-6 sm:px-8 py-3 sm:py-4 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl transition-all active:scale-95 shadow-lg flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                WhatsApp
            </a>
            <a href="tel:+51976324716"
                class="px-6 sm:px-8 py-3 sm:py-4 bg-white/10 backdrop-blur-md text-white font-bold rounded-xl border border-white/20 hover:bg-white/20 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">call</span>
                Llamar: 976 324 716
            </a>
            <a href="mailto:aventurastravelpucallpa@gmail.com"
                class="px-6 sm:px-8 py-3 sm:py-4 bg-white/10 backdrop-blur-md text-white font-bold rounded-xl border border-white/20 hover:bg-white/20 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">mail</span>
                Email
            </a>
        </div>
    </div>
</section>

<!-- Floating WhatsApp Button -->
<a href="https://wa.me/51976324716?text=<?= rawurlencode('¡Hola Javier! 🌴✈️ Quisiera agendar una asesoría de viaje con Aventuras Travel Pucallpa.') ?>"
   target="_blank" rel="noopener"
   class="fixed bottom-6 right-6 z-50 flex items-center gap-3 pl-4 pr-5 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-full shadow-2xl shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all active:scale-95"
   title="Escríbenos por WhatsApp">
    <svg class="w-6 h-6 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    <span class="hidden sm:inline text-sm">Agendar Asesoría</span>
</a>
