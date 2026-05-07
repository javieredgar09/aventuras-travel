<!-- ADMIN – CREDENCIALES GENERADAS (VISTA ÚNICA) -->
<?php
$creds = $creds ?? [];
$user  = $user  ?? [];
$id    = $id    ?? 0;
$whatsappResult = $whatsappResult ?? null;

$codigo   = $creds['codigo']   ?? '';
$password = $creds['password'] ?? '';
$nombre   = $creds['nombre']   ?? '';
$telefono = $creds['telefono'] ?? '';
$email    = $creds['email']    ?? '';
$rol      = $creds['rol']      ?? '';
$esReset  = !empty($creds['reset']);

$rolLabels = [
    'cliente_familiar' => 'Cliente Familiar',
    'cliente_colegio'  => 'Cliente Escolar',
    'representante'    => 'Representante de Grupo',
    'cliente_grupal'   => 'Cliente Grupal',
];

// ─── Configuración de marca ────────────────────────────────────────────────
$MARCA_NOMBRE   = 'AVENTURAS TRAVEL';
$MARCA_CIUDAD   = 'P U C A L L P A';
$MARCA_ESLOGAN  = 'The Art of Discovery';
$MARCA_TELEFONO = '+51 976 324 716';
$MARCA_WEB      = 'www.aventurastravel.pe';
// Versículo dinámico — puedes añadir más
$versiculos = [
    [
        'texto' => '"No te he mandado que te esfuerces y seas valiente? No temas ni desmayes, porque el SEÑOR tu Dios estará contigo dondequiera que vayas."',
        'ref'   => '— Josué 1:9',
    ],
    [
        'texto' => '"Porque yo sé los planes que tengo para vosotros, planes de bienestar y no de calamidad, para daros un futuro y una esperanza."',
        'ref'   => '— Jeremías 29:11',
    ],
    [
        'texto' => '"El SEÑOR protegerá tu salida y tu entrada, desde ahora y para siempre."',
        'ref'   => '— Salmos 121:8',
    ],
    [
        'texto' => '"Todo lo puedo en Cristo que me fortalece."',
        'ref'   => '— Filipenses 4:13',
    ],
];
// Elegir versículo basado en el día para que varíe
$versiculo = $versiculos[date('N') % count($versiculos)];

// ─── Limpiar teléfono para WhatsApp ────────────────────────────────────────
$telLimpio = preg_replace('/[^0-9]/', '', $telefono);
$loginUrl  = rtrim((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'], '/') . Router::url('/login');

// ─── Construir mensaje WhatsApp ELEGANTE ───────────────────────────────────
$sep  = "━━━━━━━━━━━━━━━━";
$dash = "─ ─ ─ ─ ─ ─ ─ ─";

$wLines = [];
// ENCABEZADO — logo en texto
$wLines[] = "🌿✈️ *{$MARCA_NOMBRE}* ✈️🌿";
$wLines[] = "      *{$MARCA_CIUDAD}*";
$wLines[] = $sep;
$wLines[] = "";
// Saludo personalizado
$wLines[] = "¡Hola, *{$nombre}*! 👋";
$wLines[] = "";
if ($esReset) {
    $wLines[] = "Tu contraseña ha sido *restablecida* con éxito. 🔄";
} else {
    $wLines[] = "Nos alegra darte la bienvenida. Aquí están tus datos de acceso al portal de viajes: 🌎";
}
$wLines[] = "";
// Credenciales
$wLines[] = "🔐 *TUS CREDENCIALES*";
$wLines[] = $dash;
$wLines[] = "👤 *Usuario:*  {$codigo}";
$wLines[] = "🔑 *Contraseña:*  {$password}";
$wLines[] = $dash;
$wLines[] = "🌐 *Portal de acceso:*";
$wLines[] = "   {$loginUrl}";
$wLines[] = "";
// Versículo bíblico
$wLines[] = $sep;
$wLines[] = "📖 _{$versiculo['texto']}_";
$wLines[] = "         *{$versiculo['ref']}*";
$wLines[] = $sep;
$wLines[] = "";
// Cierre elegante
$wLines[] = "🌿 _{$MARCA_ESLOGAN}_";
$wLines[] = "*{$MARCA_NOMBRE}*  ·  {$MARCA_CIUDAD}";
$wLines[] = "📞 {$MARCA_TELEFONO}";
$wLines[] = "🌐 {$MARCA_WEB}";
$wLines[] = "";
$wLines[] = "⚠️ _Guarda estas credenciales de forma segura y no las compartas._";

$whatsMsg  = implode("\n", $wLines);
$whatsLink = $telLimpio ? "https://wa.me/{$telLimpio}?text=" . rawurlencode($whatsMsg) : null;
?>

<div class="max-w-2xl mx-auto">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <a href="<?= Router::url('/admin/users') ?>" class="text-turquesa font-semibold text-sm hover:underline flex items-center gap-1">
            <span class="material-symbols-outlined text-lg">arrow_back</span> Volver a Usuarios
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        <!-- ── Columna izq: credenciales + acciones ── -->
        <div class="space-y-4">
            <!-- Header de éxito -->
            <div class="bg-gradient-to-br from-petroleo to-petroleo/80 rounded-2xl px-6 py-7 text-center relative overflow-hidden shadow-xl">
                <!-- Patrón decorativo -->
                <div class="absolute inset-0 opacity-5">
                    <div class="absolute -top-4 -right-4 w-32 h-32 rounded-full bg-turquesa"></div>
                    <div class="absolute -bottom-8 -left-4 w-40 h-40 rounded-full bg-turquesa"></div>
                </div>
                <!-- Logo en texto -->
                <div class="relative z-10">
                    <p class="text-turquesa text-[10px] font-bold tracking-[6px] uppercase mb-0.5">✈️ Aventuras Travel ✈️</p>
                    <p class="text-white/30 text-[9px] tracking-[8px] uppercase mb-4">P U C A L L P A</p>
                    <div class="w-14 h-14 bg-emerald-400/20 border-2 border-emerald-400/40 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="material-symbols-outlined text-2xl text-emerald-300"><?= $esReset ? 'lock_reset' : 'how_to_reg' ?></span>
                    </div>
                    <h1 class="text-xl font-black text-white"><?= $esReset ? '¡Contraseña Restablecida!' : '¡Usuario Creado!' ?></h1>
                    <p class="text-white/50 text-xs mt-1"><?= htmlspecialchars($nombre) ?> · <?= $rolLabels[$rol] ?? $rol ?></p>
                    <p class="text-turquesa/60 text-[10px] italic mt-3 tracking-wide"><?= $MARCA_ESLOGAN ?></p>
                </div>
            </div>

            <!-- Aviso una sola vez -->
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-center gap-2 text-amber-700 text-xs font-bold">
                <span class="material-symbols-outlined text-base shrink-0">warning</span>
                Estas credenciales se muestran <strong>una sola vez</strong>. Cópialas o envíalas antes de salir.
            </div>

            <!-- Credenciales -->
            <div class="bg-white rounded-2xl border border-petroleo/5 shadow-sm p-5 space-y-4">
                <!-- Usuario -->
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-petroleo/40 mb-1">👤 Usuario (para ingresar)</p>
                    <div class="flex items-center gap-2">
                        <code id="txt-codigo" class="flex-1 bg-superficie rounded-lg px-4 py-3 font-mono text-lg font-black text-petroleo tracking-widest text-center border border-petroleo/10">
                            <?= htmlspecialchars($codigo) ?>
                        </code>
                        <button onclick="copyText('txt-codigo', this)" class="w-10 h-10 bg-superficie border border-petroleo/10 rounded-lg flex items-center justify-center transition-colors hover:bg-petroleo/10 shrink-0" title="Copiar usuario">
                            <span class="material-symbols-outlined text-petroleo text-base">content_copy</span>
                        </button>
                    </div>
                </div>
                <!-- Contraseña -->
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-petroleo/40 mb-1">🔑 Contraseña</p>
                    <div class="flex items-center gap-2">
                        <code id="txt-password" class="flex-1 bg-superficie rounded-lg px-4 py-3 font-mono text-lg font-black text-turquesa-dark tracking-widest text-center border border-petroleo/10">
                            <?= htmlspecialchars($password) ?>
                        </code>
                        <button onclick="copyText('txt-password', this)" class="w-10 h-10 bg-superficie border border-petroleo/10 rounded-lg flex items-center justify-center transition-colors hover:bg-petroleo/10 shrink-0" title="Copiar contraseña">
                            <span class="material-symbols-outlined text-petroleo text-base">content_copy</span>
                        </button>
                    </div>
                </div>
                <!-- URL -->
                <div class="bg-petroleo/5 rounded-lg px-4 py-2.5 text-center">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-petroleo/30 mb-0.5">Portal</p>
                    <a href="<?= Router::url('/login') ?>" target="_blank" class="text-turquesa-dark underline font-mono text-xs"><?= $loginUrl ?></a>
                </div>
            </div>

            <!-- Acciones -->
            <div class="flex gap-3">
                <a href="<?= Router::url('/admin/users') ?>"
                   class="flex-1 text-center px-4 py-3 rounded-xl text-sm font-bold text-petroleo bg-white border border-petroleo/10 hover:bg-humo transition-colors">
                    Ir a Usuarios
                </a>
                <a href="<?= Router::url('/admin/users/create') ?>"
                   class="flex-1 text-center px-4 py-3 rounded-xl text-sm font-bold text-white bg-turquesa hover:bg-turquesa-dark transition-colors flex items-center justify-center gap-2 shadow-lg shadow-turquesa/20">
                    <span class="material-symbols-outlined text-base">person_add</span> Crear Otro
                </a>
            </div>
        </div>

        <!-- ── Columna der: envío WhatsApp ── -->
        <div class="space-y-4">
            <!-- Preview del mensaje -->
            <div class="bg-white rounded-2xl border border-petroleo/5 shadow-sm overflow-hidden">
                <!-- Header preview -->
                <div class="bg-[#075e54] px-4 py-3 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-300 flex items-center justify-center font-bold text-[#075e54] text-sm shrink-0">
                        AT
                    </div>
                    <div>
                        <p class="text-white text-sm font-bold">Aventuras Travel</p>
                        <p class="text-green-300 text-[10px]">Vista previa del mensaje</p>
                    </div>
                    <svg class="w-5 h-5 text-white/50 ml-auto" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" /></svg>
                </div>
                <!-- Burbuja de chat -->
                <div class="bg-[#e5ddd5] p-4 min-h-[200px]">
                    <div class="bg-white rounded-xl rounded-tl-none shadow-sm px-4 py-3 max-w-full">
                        <pre id="wa-preview" class="text-[11px] leading-relaxed text-gray-800 font-sans whitespace-pre-wrap break-words"><?= htmlspecialchars($whatsMsg) ?></pre>
                        <p class="text-right text-[9px] text-gray-400 mt-1">ahora · ✓✓</p>
                    </div>
                </div>
            </div>

            <!-- Botón enviar al número registrado -->
            <?php if ($whatsLink): ?>
            <a href="<?= $whatsLink ?>" target="_blank" rel="noopener"
               class="w-full flex items-center justify-center gap-3 px-5 py-4 bg-[#25d366] hover:bg-[#1db954] text-white font-bold rounded-2xl transition-all active:scale-95 shadow-xl shadow-emerald-500/30">
                <svg class="w-7 h-7 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <div class="text-left">
                    <div class="text-base leading-tight">Enviar por WhatsApp</div>
                    <div class="text-xs font-normal opacity-80 leading-tight"><?= htmlspecialchars($telefono) ?></div>
                </div>
            </a>
            <?php else: ?>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs text-amber-700 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm shrink-0">info</span>
                No se registró número de teléfono. Usa el campo de abajo o copia el mensaje manualmente.
            </div>
            <?php endif; ?>

            <!-- Estado del envío automático de WhatsApp (API) -->
            <?php if ($whatsappResult !== null): ?>
            <div class="rounded-xl p-4 border <?= $whatsappResult['success'] ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' ?>">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-lg shrink-0 <?= $whatsappResult['success'] ? 'text-emerald-600' : 'text-red-600' ?>">
                        <?= $whatsappResult['success'] ? 'check_circle' : 'error' ?>
                    </span>
                    <div class="flex-1 text-xs <?= $whatsappResult['success'] ? 'text-emerald-700' : 'text-red-700' ?>">
                        <?php if ($whatsappResult['success']): ?>
                            <p class="font-bold mb-1">✅ WhatsApp enviado exitosamente</p>
                            <p class="opacity-80">El mensaje con las credenciales ha sido enviado al número registrado.</p>
                            <?php if (!empty($whatsappResult['message_id'])): ?>
                                <p class="text-[10px] opacity-60 mt-1">ID del mensaje: <?= htmlspecialchars($whatsappResult['message_id']) ?></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="font-bold mb-1">❌ Error al enviar WhatsApp</p>
                            <p class="opacity-80 mb-1"><?= htmlspecialchars($whatsappResult['error'] ?? 'Error desconocido') ?></p>
                            <p class="text-[10px] opacity-60">Puedes enviar el mensaje manualmente usando el botón verde de arriba.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Enviar a número personalizado -->
            <div class="bg-white rounded-2xl border border-petroleo/5 shadow-sm p-4">
                <p class="text-[10px] font-bold uppercase tracking-widest text-petroleo/40 mb-3">Enviar a otro número</p>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-emerald-500">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </span>
                        <input type="tel" id="custom-phone" placeholder="+51 999 999 999"
                               class="w-full pl-10 pr-4 py-3 bg-superficie border border-petroleo/10 rounded-xl text-sm outline-none focus:ring-2 focus:ring-emerald-300">
                    </div>
                    <button onclick="sendToCustom()"
                            class="px-4 py-3 bg-[#25d366] hover:bg-[#1db954] text-white rounded-xl text-sm font-bold flex items-center gap-1.5 transition-colors shadow-lg shadow-emerald-400/20">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                        Enviar
                    </button>
                </div>
            </div>

            <!-- Copiar mensaje -->
            <button onclick="copyFullMsg(this)"
                    class="w-full py-3 rounded-xl text-sm font-bold text-petroleo bg-white border border-petroleo/10 hover:bg-superficie transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-base">content_copy</span>
                Copiar mensaje completo
            </button>
        </div>

    </div><!-- /grid -->
</div>

<script>
const whatsMsg = <?= json_encode($whatsMsg, JSON_HEX_TAG | JSON_HEX_AMP) ?>;

function copyText(elementId, btn) {
    const text = document.getElementById(elementId).textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        const icon = btn.querySelector('.material-symbols-outlined');
        const orig = icon.textContent;
        icon.textContent = 'check';
        btn.classList.add('!bg-emerald-100');
        setTimeout(() => { icon.textContent = orig; btn.classList.remove('!bg-emerald-100'); }, 2200);
    });
}

function copyFullMsg(btn) {
    navigator.clipboard.writeText(whatsMsg).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<span class="material-symbols-outlined text-base">check_circle</span> ¡Copiado!';
        btn.classList.add('!bg-emerald-50', '!text-emerald-700', '!border-emerald-200');
        setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('!bg-emerald-50','!text-emerald-700','!border-emerald-200'); }, 2500);
    });
}

function sendToCustom() {
    const phone  = document.getElementById('custom-phone').value.trim();
    const digits = phone.replace(/[^0-9]/g, '');
    if (!digits || digits.length < 7) {
        alert('Ingresa un número de WhatsApp válido (ej: 51976324716).');
        return;
    }
    window.open('https://wa.me/' + digits + '?text=' + encodeURIComponent(whatsMsg), '_blank');
}
</script>
