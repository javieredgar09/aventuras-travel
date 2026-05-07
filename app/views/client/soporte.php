<?php
/**
 * Vista: Soporte al Cliente – Aventuras Travel Pucallpa
 * Datos de contacto, WhatsApp, ubicación y formulario de consulta rápida.
 */
require_once __DIR__ . '/../../helpers/DestinationHelper.php';

$user    = $user ?? ($_SESSION['user'] ?? []);
$nombre  = htmlspecialchars(trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')));
$codigo  = htmlspecialchars($user['codigo'] ?? '');
$csrf    = $csrf_token ?? '';

// Número WhatsApp (formato internacional sin +)
$waNumber = '51976324716';
$waLink   = "https://wa.me/{$waNumber}";

// Imagen dinámica de aventura
$heroImg = DestinationHelper::getHeroImage('Aventura');
?>

<div class="max-w-5xl mx-auto">

    <!-- Hero Header - Mejorado con imagen de aventura -->
    <div class="relative h-[240px] sm:h-[280px] rounded-3xl overflow-hidden mb-10 shadow-2xl group">
        <img src="<?= htmlspecialchars($heroImg) ?>" alt="Soporte Aventuras Travel"
             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
        <div class="absolute inset-0 bg-gradient-to-r from-petroleo-dark/95 via-petroleo/85 to-turquesa-dark/40"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-petroleo-dark/90 via-transparent to-transparent"></div>
        <div class="absolute top-0 right-0 w-80 h-80 rounded-full blur-3xl -translate-y-1/3 translate-x-1/4 pointer-events-none bg-turquesa/15"></div>
        
        <div class="relative z-10 px-6 sm:px-8 h-full flex flex-col items-center justify-center text-center">
            <div class="flex-shrink-0 w-20 h-20 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center shadow-xl mb-6">
                <span class="material-symbols-outlined text-5xl text-white">headset_mic</span>
            </div>
            <p class="text-white/70 text-sm font-black uppercase tracking-widest mb-2">Centro de Ayuda</p>
            <h1 class="text-3xl md:text-4xl font-black text-white leading-tight mb-3">¿En qué te ayudamos, <span class="text-turquesa-light"><?= $nombre ?: 'viajero' ?></span>?</h1>
            <p class="text-white/80 text-sm md:text-base max-w-2xl">Estamos listos para atenderte. Contacta con nuestro equipo por el canal que prefieras.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ====== COLUMNA IZQUIERDA: Datos de contacto ====== -->
        <div class="lg:col-span-1 flex flex-col gap-5">

            <!-- WhatsApp (principal) -->
            <a href="<?= $waLink ?>?text=Hola%2C+soy+<?= urlencode($nombre) ?>+%28<?= urlencode($codigo) ?>%29+y+necesito+ayuda+con+mi+reserva." 
               target="_blank"
               class="group flex items-center gap-4 bg-[#25D366] hover:bg-[#1ebe58] rounded-2xl p-5 shadow-lg shadow-[#25D366]/30 transition-all duration-300 hover:scale-[1.02]">
                <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-8 h-8 fill-white" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zm-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>
                <div>
                    <p class="text-white font-black text-lg leading-tight">WhatsApp</p>
                    <p class="text-white/80 text-sm">+51 976 324 716</p>
                    <p class="text-white/60 text-xs mt-0.5">Respuesta inmediata</p>
                </div>
                <span class="material-symbols-outlined text-white/60 ml-auto group-hover:text-white transition-colors">arrow_forward</span>
            </a>

            <!-- Teléfono -->
            <a href="tel:+51976324716"
               class="group flex items-center gap-4 bg-white rounded-2xl p-5 shadow-md border border-petroleo/8 hover:border-turquesa/40 transition-all hover:shadow-lg">
                <div class="w-14 h-14 rounded-xl bg-turquesa/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-turquesa-dark text-3xl">call</span>
                </div>
                <div>
                    <p class="font-bold text-petroleo">Llamada Directa</p>
                    <p class="text-turquesa-dark font-bold text-lg">+51 976 324 716</p>
                    <p class="text-slate-400 text-xs">Lun–Sáb · 8am – 7pm</p>
                </div>
            </a>

            <!-- Email -->
            <a href="mailto:aventurastravelpucallpa@gmail.com?subject=Consulta%20de%20<?= urlencode($codigo) ?>"
               class="group flex items-center gap-4 bg-white rounded-2xl p-5 shadow-md border border-petroleo/8 hover:border-turquesa/40 transition-all hover:shadow-lg">
                <div class="w-14 h-14 rounded-xl bg-turquesa/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-turquesa-dark text-3xl">mail</span>
                </div>
                <div>
                    <p class="font-bold text-petroleo">Correo Electrónico</p>
                    <p class="text-turquesa-dark text-sm font-semibold break-all">aventurastravelpucallpa@gmail.com</p>
                    <p class="text-slate-400 text-xs">Respuesta en 24h hábiles</p>
                </div>
            </a>

            <!-- Facebook -->
            <a href="https://www.facebook.com/profile.php?id=61557817259488" target="_blank"
               class="group flex items-center gap-4 bg-white rounded-2xl p-5 shadow-md border border-petroleo/8 hover:border-[#1877F2]/40 transition-all hover:shadow-lg">
                <div class="w-14 h-14 rounded-xl bg-[#1877F2]/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-8 h-8 fill-[#1877F2]" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-petroleo">Facebook</p>
                    <p class="text-[#1877F2] text-sm font-semibold">Aventuras Travel Pucallpa - Oficial</p>
                    <p class="text-slate-400 text-xs">Mensajes directos</p>
                </div>
            </a>

            <!-- Dirección -->
            <div class="bg-white rounded-2xl p-5 shadow-md border border-petroleo/8">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-xl bg-turquesa/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-turquesa-dark text-3xl">location_on</span>
                    </div>
                    <div>
                        <p class="font-bold text-petroleo mb-1">Oficina Central</p>
                        <p class="text-slate-600 text-sm leading-relaxed">Pucallpa, Ucayali<br>Perú 🇵🇪</p>
                        <p class="text-slate-400 text-xs mt-1">Lun–Sáb · 8:00am – 7:00pm</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====== COLUMNA DERECHA: Formulario + FAQ ====== -->
        <div class="lg:col-span-2 flex flex-col gap-6">

            <!-- Formulario de Consulta Rápida -->
            <div class="bg-white rounded-2xl shadow-md border border-petroleo/8 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-turquesa/5 to-transparent">
                    <h2 class="font-black text-petroleo text-lg flex items-center gap-2">
                        <span class="material-symbols-outlined text-turquesa-dark">edit_note</span>
                        Enviar una Consulta
                    </h2>
                    <p class="text-slate-500 text-sm mt-1">Nuestro equipo responderá en menos de 24 horas hábiles.</p>
                </div>
                <form id="soporteForm" class="p-6 space-y-4"
                      onsubmit="handleSupportForm(event)">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tu nombre</label>
                            <input type="text" value="<?= $nombre ?>" readonly
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-petroleo font-medium cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Código de Contrato</label>
                            <input type="text" value="<?= $codigo ?>" readonly
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-petroleo font-medium cursor-not-allowed">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tipo de Consulta</label>
                        <select name="tipo" required
                                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-petroleo focus:ring-2 focus:ring-turquesa/30 focus:border-turquesa-dark outline-none transition-all">
                            <option value="">— Selecciona un tema —</option>
                            <option value="pago">💳 Pagos y Comprobantes</option>
                            <option value="itinerario">✈️  Itinerario y Vuelos</option>
                            <option value="pasajeros">👥 Pasajeros y Documentos</option>
                            <option value="cambio">🔄 Cambios o Cancelaciones</option>
                            <option value="otro">💬 Otra Consulta</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mensaje</label>
                        <textarea name="mensaje" rows="4" required
                                  placeholder="Cuéntanos en detalle tu consulta o inconveniente..."
                                  class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-petroleo focus:ring-2 focus:ring-turquesa/30 focus:border-turquesa-dark outline-none transition-all resize-none"></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 bg-turquesa-dark hover:bg-petroleo text-white font-bold py-3.5 px-6 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                            <span class="material-symbols-outlined text-lg">send</span>
                            Enviar Consulta
                        </button>
                        <!-- WhatsApp rápido -->
                        <a id="waFormBtn" href="#"
                           class="flex-1 inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe58] text-white font-bold py-3.5 px-6 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 fill-white flex-shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zm-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Enviar por WhatsApp
                        </a>
                    </div>

                    <!-- Success message -->
                    <div id="soporteSuccess" class="hidden bg-green-50 border border-green-200 rounded-xl p-4 text-green-800 text-sm font-medium flex items-center gap-2">
                        <span class="material-symbols-outlined text-green-600">check_circle</span>
                        ¡Consulta enviada! Te contactaremos pronto.
                    </div>
                </form>
            </div>

            <!-- Preguntas Frecuentes -->
            <div class="bg-white rounded-2xl shadow-md border border-petroleo/8 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h2 class="font-black text-petroleo text-lg flex items-center gap-2">
                        <span class="material-symbols-outlined text-turquesa-dark">quiz</span>
                        Preguntas Frecuentes
                    </h2>
                </div>
                <div class="divide-y divide-slate-100" id="faqAccordion">
                    <?php
                    $faqs = [
                        ['¿Cómo subo mi comprobante de pago?', 'Ve a la sección "Pagos" en tu panel, selecciona la cuota correspondiente, haz clic en "Registrar Pago", adjunta tu comprobante (PDF, JPG o PNG, máx 5MB) y envíalo. El equipo lo revisará en 24 horas hábiles.'],
                        ['¿En cuánto tiempo se aprueba mi pago?', 'Revisamos y aprobamos comprobantes de lunes a sábado entre 8am y 7pm. El tiempo promedio de validación es de 2 a 4 horas dentro de ese horario.'],
                        ['¿Puedo cambiar mis datos de pasajero?', 'Sí. Contacta con nosotros por WhatsApp indicando tu código de contrato y los datos que deseas modificar. Hay cambios permitidos hasta 30 días antes del viaje.'],
                        ['¿Qué métodos de pago aceptan?', 'Aceptamos transferencia bancaria (BCP, BBVA, Interbank, Scotiabank), depósito en efectivo, Yape y Plin. Siempre debes subir el comprobante en el portal.'],
                        ['¿Cómo descargo mis vouchers y documentos?', 'En la sección "Mis Viajes" → "Documentos" encontrarás todos los archivos disponibles. El equipo los irá cargando a medida que se confirmen.'],
                        ['¿Puedo cancelar mi reserva?', 'Las políticas de cancelación dependen del destino y fecha. Contáctanos con anticipación para evaluar tu caso y minimizar penalidades.'],
                    ];
                    foreach ($faqs as $i => $faq): ?>
                    <div class="faq-item" id="faq-<?= $i ?>">
                        <button onclick="toggleFaq(<?= $i ?>)"
                                class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-slate-50/80 transition-colors">
                            <span class="font-semibold text-petroleo text-sm pr-4"><?= htmlspecialchars($faq[0]) ?></span>
                            <span class="faq-icon material-symbols-outlined text-turquesa-dark flex-shrink-0 transition-transform" id="faq-icon-<?= $i ?>">expand_more</span>
                        </button>
                        <div class="faq-answer hidden px-6 pb-4 text-sm text-slate-600 leading-relaxed" id="faq-answer-<?= $i ?>">
                            <?= htmlspecialchars($faq[1]) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Horario y emergencias -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gradient-to-br from-petroleo to-turquesa-dark rounded-2xl p-5 text-white shadow-lg">
                    <span class="material-symbols-outlined text-turquesa text-2xl mb-3 block">schedule</span>
                    <p class="font-black text-lg mb-3">Horario de Atención</p>
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-white/70">Lunes – Viernes</span>
                            <span class="font-semibold">8:00am – 7:00pm</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white/70">Sábado</span>
                            <span class="font-semibold">8:00am – 2:00pm</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white/70">Domingo</span>
                            <span class="font-semibold text-white/50">Cerrado</span>
                        </div>
                    </div>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 shadow-md">
                    <span class="material-symbols-outlined text-amber-600 text-2xl mb-3 block">emergency</span>
                    <p class="font-black text-petroleo text-lg mb-2">Urgencias</p>
                    <p class="text-slate-600 text-sm leading-relaxed mb-3">Para emergencias durante el viaje (accidentes, vuelos perdidos), contáctanos directamente:</p>
                    <a href="<?= $waLink ?>?text=URGENCIA+<?= urlencode($codigo) ?>+necesito+asistencia+inmediata" target="_blank"
                       class="inline-flex items-center gap-2 bg-[#25D366] text-white font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-[#1ebe58] transition-colors">
                        <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zm-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp Urgencias
                    </a>
                </div>
            </div>

        </div><!-- /col-right -->
    </div><!-- /grid -->

</div>

<script>
// ===== FAQ Accordion =====
function toggleFaq(i) {
    const answer = document.getElementById('faq-answer-' + i);
    const icon   = document.getElementById('faq-icon-' + i);
    const isOpen = !answer.classList.contains('hidden');
    // Close all
    document.querySelectorAll('.faq-answer').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.faq-icon').forEach(el => {
        el.style.transform = 'rotate(0deg)';
    });
    if (!isOpen) {
        answer.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    }
}

// ===== Support form – redirect to WhatsApp with message =====
function handleSupportForm(e) {
    e.preventDefault();
    const form  = e.target;
    const tipo  = form.querySelector('[name="tipo"]').value;
    const msg   = form.querySelector('[name="mensaje"]').value.trim();
    const code  = '<?= addslashes($codigo) ?>';
    const name  = '<?= addslashes($nombre) ?>';

    if (!tipo || !msg) {
        alert('Por favor selecciona un tipo de consulta y escribe tu mensaje.');
        return;
    }

    const tipos = {
        pago: '💳 Pago/Comprobante', itinerario: '✈️ Itinerario', 
        pasajeros: '👥 Pasajeros', cambio: '🔄 Cambio/Cancelación', otro: '💬 Otra consulta'
    };
    const text = `Hola Aventuras Travel,\nSoy ${name} – Contrato: ${code}\nTema: ${tipos[tipo] || tipo}\n\n${msg}`;
    const waUrl = 'https://wa.me/51976324716?text=' + encodeURIComponent(text);
    
    document.getElementById('soporteSuccess').classList.remove('hidden');
    form.querySelector('[name="tipo"]').value = '';
    form.querySelector('[name="mensaje"]').value = '';
    
    setTimeout(() => {
        window.open(waUrl, '_blank');
        document.getElementById('soporteSuccess').classList.add('hidden');
    }, 800);
}

// ===== Dynamic WA link button from form textarea =====
const waBtn = document.getElementById('waFormBtn');
const msgTA = document.querySelector('[name="mensaje"]');
if (waBtn && msgTA) {
    msgTA.addEventListener('input', function() {
        const msg  = this.value.trim();
        const code = '<?= addslashes($codigo) ?>';
        const name = '<?= addslashes($nombre) ?>';
        const text = msg ? `Hola, soy ${name} (${code}) – ${msg}` : `Hola, soy ${name} (${code}) y necesito ayuda con mi reserva.`;
        waBtn.href = 'https://wa.me/51976324716?text=' + encodeURIComponent(text);
    });
    waBtn.href = 'https://wa.me/51976324716?text=' + encodeURIComponent(
        `Hola, soy <?= addslashes($nombre) ?> (<?= addslashes($codigo) ?>) y necesito ayuda con mi reserva.`
    );
}
</script>
