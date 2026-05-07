<?php
/* ===============================================================
   PROMOCIONES PÚBLICAS – Aventuras Travel
   =============================================================== */
$promociones   = $promociones   ?? [];
$activas       = array_filter($promociones, fn($p) => ($p['status_label'] ?? '') === 'active');
$expiradas     = array_filter($promociones, fn($p) => ($p['status_label'] ?? '') !== 'active');
$whatsappBase  = 'https://wa.me/51976324716';
?>

<style>
/* ──────────────────────────────────────────────────────────────
   PROMOTIONS – SCOPED STYLES
   ────────────────────────────────────────────────────────────── */

/* ── Hero blobs ── */
.ph-blob {
    position:absolute; border-radius:50%;
    filter:blur(90px); opacity:.14; pointer-events:none;
    animation:phBlob 14s ease-in-out infinite alternate;
}
.ph-blob-1 { width:420px;height:420px;background:#4ABED9;top:-120px;right:-80px;animation-delay:0s; }
.ph-blob-2 { width:300px;height:300px;background:#10B981;bottom:-80px;left:8%;animation-delay:-5s; }
.ph-blob-3 { width:180px;height:180px;background:#4ABED9;top:45%;left:48%;animation-delay:-10s; }
@keyframes phBlob {
    0%   { transform:translate(0,0) scale(1); }
    50%  { transform:translate(28px,-18px) scale(1.1); }
    100% { transform:translate(-18px,14px) scale(.93); }
}

/* ── Filter bar ── */
.pf-btn {
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.55rem 1rem; border-radius:.75rem;
    font-size:.8rem; font-weight:700;
    color:#1B3A4B; background:transparent; border:none;
    cursor:pointer; transition:all .2s ease; white-space:nowrap;
}
.pf-btn:hover  { background:rgba(74,190,217,.1); color:#00687A; }
.pf-btn.active { background:linear-gradient(135deg,#00687A,#4ABED9); color:#fff; box-shadow:0 4px 16px rgba(0,104,122,.28); }
.pf-count {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:1.15rem; height:1.15rem; padding:0 .3rem;
    border-radius:999px; font-size:.62rem; font-weight:800;
    background:rgba(27,58,75,.08); color:rgba(27,58,75,.5);
}
.pf-btn.active .pf-count { background:rgba(255,255,255,.22); color:#fff; }

/* ── PROMO CARD ── */
.pc-wrap {
    background:#fff;
    border-radius:1.25rem;
    overflow:hidden;
    border:1.5px solid rgba(27,58,75,.07);
    box-shadow:0 4px 20px rgba(27,58,75,.07);
    display:flex; flex-direction:column;
    transition:transform .35s cubic-bezier(.22,1,.36,1), box-shadow .35s ease, border-color .25s ease;
    opacity:0; transform:translateY(1.25rem);
    animation:pcIn .55s ease forwards;
}
.pc-wrap:hover {
    transform:translateY(-7px);
    box-shadow:0 22px 52px rgba(27,58,75,.14);
    border-color:rgba(74,190,217,.3);
}
.pc-wrap.pc-expired { opacity:.65 !important; }
.pc-wrap.pc-expired:hover { opacity:.85 !important; }
.pc-wrap.pc-hidden  { display:none !important; }
@keyframes pcIn { to { opacity:1; transform:translateY(0); } }

/* ── Image area ── */
.pc-img-shell {
    position:relative;
    background:#0D2432;
    overflow:hidden;
    flex-shrink:0;
}
.pc-img {
    display:block;
    width:100%;
    height:auto;
    min-height:220px;
    max-height:460px;
    object-fit:contain;
    object-position:center;
    background:#0D2432;
    transition:transform .55s ease;
    cursor:zoom-in;
}
.pc-wrap:hover .pc-img { transform:scale(1.03); }

/* Zoom hover overlay */
.pc-zoom-veil {
    position:absolute; inset:0;
    background:transparent;
    display:flex; align-items:center; justify-content:center;
    cursor:zoom-in;
    transition:background .28s ease;
    z-index:4;
}
.pc-wrap:hover .pc-zoom-veil { background:rgba(13,36,50,.38); }
.pc-zoom-pill {
    display:flex; align-items:center; gap:.4rem;
    background:rgba(74,190,217,.9);
    backdrop-filter:blur(8px);
    color:#fff;
    padding:.6rem 1.1rem;
    border-radius:999px;
    font-size:.72rem; font-weight:800;
    letter-spacing:.08em; text-transform:uppercase;
    box-shadow:0 8px 24px rgba(0,0,0,.28);
    transform:scale(.65) translateY(6px);
    opacity:0;
    transition:all .3s cubic-bezier(.34,1.56,.64,1);
}
.pc-zoom-pill .material-symbols-outlined { font-size:1.2rem; }
.pc-wrap:hover .pc-zoom-pill { transform:scale(1) translateY(0); opacity:1; }

/* Fallback no-image */
.pc-img-fallback {
    min-height:220px;
    display:flex; align-items:center; justify-content:center;
    background:linear-gradient(135deg,rgba(74,190,217,.12),rgba(27,58,75,.12));
}

/* Status & discount badges (on image) */
.pc-badge-live {
    display:inline-flex; align-items:center; gap:.35rem;
    background:#10B981; color:#fff;
    padding:.25rem .7rem; border-radius:999px;
    font-size:.62rem; font-weight:800; text-transform:uppercase;
    box-shadow:0 4px 12px rgba(16,185,129,.35);
}
.pc-badge-ended {
    display:inline-flex; align-items:center; gap:.35rem;
    background:rgba(27,58,75,.65); backdrop-filter:blur(6px);
    color:rgba(255,255,255,.85);
    padding:.25rem .7rem; border-radius:999px;
    font-size:.62rem; font-weight:800; text-transform:uppercase;
}
.pc-badge-discount {
    background:linear-gradient(135deg,#00687A,#4ABED9);
    color:#fff;
    padding:.55rem .8rem; border-radius:.85rem;
    font-size:1rem; font-weight:900; line-height:1;
    box-shadow:0 6px 20px rgba(0,104,122,.38);
    animation:discPulse 3.2s ease-in-out infinite;
}
@keyframes discPulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.06)} }

.pc-days-pill {
    display:inline-flex; align-items:center; gap:.2rem;
    background:rgba(0,0,0,.52); backdrop-filter:blur(10px);
    color:#fff; padding:.3rem .6rem; border-radius:999px;
    border:1px solid rgba(255,255,255,.14);
    font-size:.7rem; font-weight:800; line-height:1;
}

/* ── Card body ── */
.pc-body { padding:1.1rem 1.25rem 1.25rem; display:flex; flex-direction:column; flex:1; gap:.75rem; }
.pc-title { font-size:1rem; font-weight:900; color:#1B3A4B; line-height:1.25; transition:color .2s; }
.pc-wrap:hover .pc-title { color:#00687A; }
.pc-desc { font-size:.82rem; color:rgba(27,58,75,.55); line-height:1.6; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; flex:1; }
.pc-meta { display:flex; flex-wrap:wrap; gap:.4rem .9rem; font-size:.72rem; color:rgba(27,58,75,.4); font-weight:600; }
.pc-meta-item { display:inline-flex; align-items:center; gap:.25rem; }
.pc-meta-item .material-symbols-outlined { font-size:.9rem; color:#00687A; }

/* WhatsApp button */
.pc-wa-btn {
    display:flex; align-items:center; justify-content:center; gap:.5rem;
    width:100%; padding:.8rem 1rem;
    background:linear-gradient(135deg,#25D366,#128C7E);
    color:#fff; font-weight:800; font-size:.82rem;
    border-radius:.875rem; text-decoration:none;
    transition:all .25s ease;
    box-shadow:0 4px 14px rgba(37,211,102,.22);
    border:none; cursor:pointer;
}
.pc-wa-btn:hover { background:linear-gradient(135deg,#1db954,#0e7a6c); box-shadow:0 8px 22px rgba(37,211,102,.32); transform:translateY(-1px); }
.pc-wa-btn .material-symbols-outlined { font-size:1.1rem; }

.pc-ended-badge {
    display:flex; align-items:center; justify-content:center; gap:.4rem;
    padding:.75rem; background:rgba(27,58,75,.04);
    border-radius:.875rem; font-size:.78rem; font-weight:700;
    color:rgba(27,58,75,.35);
}

/* ══════════════════════════════════════════════════════════════
   LIGHTBOX
   ══════════════════════════════════════════════════════════════ */
#lb-overlay {
    display:none;
    position:fixed; inset:0; z-index:9998;
    background:rgba(6,16,26,.92);
    backdrop-filter:blur(14px);
    -webkit-backdrop-filter:blur(14px);
    align-items:center; justify-content:center;
    padding:1rem;
    cursor:zoom-out;
    animation:lbFadeIn .3s ease forwards;
}
#lb-overlay.lb-active { display:flex; }
@keyframes lbFadeIn { from{opacity:0} to{opacity:1} }

#lb-frame {
    position:relative;
    max-width:min(92vw, 860px);
    max-height:90vh;
    border-radius:1.25rem;
    overflow:hidden;
    cursor:default;
    box-shadow:0 40px 120px rgba(0,0,0,.75);
    animation:lbScale .38s cubic-bezier(.22,1,.36,1) forwards;
    transform:scale(.78); opacity:0;
}
@keyframes lbScale { to{transform:scale(1);opacity:1} }

#lb-img {
    display:block;
    width:auto; height:auto;
    max-width:100%; max-height:85vh;
    object-fit:contain;
    border-radius:1.25rem;
    background:#0D2432;
}
#lb-caption {
    position:absolute; bottom:0; left:0; right:0;
    padding:1.25rem 1.5rem;
    background:linear-gradient(to top,rgba(0,0,0,.88) 0%,transparent 100%);
    color:#fff; font-weight:800; font-size:.95rem;
    border-radius:0 0 1.25rem 1.25rem;
    text-shadow:0 2px 8px rgba(0,0,0,.5);
    letter-spacing:-.01em;
}
#lb-close-btn {
    position:fixed; top:1rem; right:1rem; z-index:9999;
    width:2.75rem; height:2.75rem;
    background:rgba(255,255,255,.14);
    backdrop-filter:blur(8px);
    border:1.5px solid rgba(255,255,255,.22);
    border-radius:50%;
    color:#fff; font-size:1.3rem; font-weight:900; line-height:1;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer;
    transition:background .2s, transform .2s;
}
#lb-close-btn:hover { background:rgba(255,255,255,.28); transform:rotate(90deg) scale(1.12); }
#lb-hint {
    position:fixed; bottom:1.25rem; left:50%; transform:translateX(-50%);
    z-index:9999;
    display:flex; align-items:center; gap:.6rem;
    background:rgba(13,36,50,.75);
    backdrop-filter:blur(10px);
    border:1px solid rgba(74,190,217,.2);
    padding:.5rem 1.1rem; border-radius:999px;
    color:rgba(255,255,255,.7); font-size:.72rem; font-weight:700;
    white-space:nowrap;
}
#lb-hint kbd {
    background:rgba(255,255,255,.1);
    padding:1px 7px; border-radius:5px;
    font-family:inherit; font-size:.7rem;
}

/* Floating WA */
#floatWA {
    position:fixed; bottom:1.5rem; right:1.5rem; z-index:997;
    display:flex; align-items:center; gap:.65rem;
    padding:.8rem 1.3rem;
    background:linear-gradient(135deg,#25D366,#128C7E);
    color:#fff; font-weight:800; font-size:.82rem;
    border-radius:999px; text-decoration:none;
    box-shadow:0 8px 28px rgba(37,211,102,.35);
    transition:transform .25s ease, box-shadow .25s ease, opacity .3s ease;
    animation:waFloat 3.2s ease-in-out infinite;
}
#floatWA:hover { box-shadow:0 12px 36px rgba(37,211,102,.5); transform:translateY(-2px) scale(1.03); }
@keyframes waFloat {
    0%,100%{box-shadow:0 8px 28px rgba(37,211,102,.35)}
    50%     {box-shadow:0 8px 40px rgba(37,211,102,.55)}
}
</style>

<!-- ══════════════════════════════════════════════════════════════
     HERO
     ══════════════════════════════════════════════════════════════ -->
<section class="relative overflow-hidden" style="background:linear-gradient(160deg,#0D2432 0%,#1B3A4B 55%,#2D5468 100%);min-height:320px;">
    <div class="ph-blob ph-blob-1" aria-hidden="true"></div>
    <div class="ph-blob ph-blob-2" aria-hidden="true"></div>
    <div class="ph-blob ph-blob-3" aria-hidden="true"></div>

    <!-- wave bottom -->
    <svg class="absolute bottom-0 left-0 w-full pointer-events-none" viewBox="0 0 1440 80" preserveAspectRatio="none" style="height:50px">
        <path d="M0,40 C360,80 1080,0 1440,60 L1440,80 L0,80 Z" fill="#F8FBFD"/>
    </svg>

    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 pt-12 pb-20 text-center">
        <!-- Label -->
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4"
              style="background:rgba(74,190,217,.15);color:#7DD3E8;border:1px solid rgba(74,190,217,.3);">
            <span class="material-symbols-outlined" style="font-size:.9rem;">local_offer</span>
            Ofertas Exclusivas
        </span>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight tracking-tight mb-3">
            Cada viaje es una<br><span style="color:#4ABED9;">aventura</span>, ¡Disfrútalo!
        </h1>
        <p style="color:rgba(255,255,255,.55);" class="max-w-lg mx-auto text-sm sm:text-base mb-8">
            Ofertas curadas por nuestros asesores en Pucallpa. ¡Reserva antes de que se agoten!
        </p>

        <!-- Stats -->
        <div class="flex justify-center items-center gap-8 sm:gap-12">
            <div class="text-center">
                <div class="text-3xl font-black" style="color:#4ABED9;"><?= count($activas) ?></div>
                <div class="text-xs uppercase tracking-widest font-bold mt-1" style="color:rgba(255,255,255,.38);">Activas</div>
            </div>
            <div style="width:1px;height:2.5rem;background:rgba(255,255,255,.1);"></div>
            <div class="text-center">
                <div class="text-3xl font-black text-white"><?= count($promociones) ?></div>
                <div class="text-xs uppercase tracking-widest font-bold mt-1" style="color:rgba(255,255,255,.38);">Total</div>
            </div>
            <div style="width:1px;height:2.5rem;background:rgba(255,255,255,.1);"></div>
            <div class="text-center">
                <span class="material-symbols-outlined text-3xl" style="color:#10B981;">verified</span>
                <div class="text-xs uppercase tracking-widest font-bold mt-1" style="color:rgba(255,255,255,.38);">Certificada</div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     FILTER BAR
     ══════════════════════════════════════════════════════════════ -->
<section class="max-w-6xl mx-auto px-4 sm:px-6 -mt-5 relative z-20 mb-8">
    <div class="bg-white rounded-2xl shadow-xl px-3 py-2 flex flex-wrap items-center gap-2"
         style="border:1px solid rgba(27,58,75,.06);box-shadow:0 8px 32px rgba(27,58,75,.08);">
        <button class="pf-btn active" data-pf="all">
            <span class="material-symbols-outlined" style="font-size:1.05rem;">apps</span>
            Todas <span class="pf-count"><?= count($promociones) ?></span>
        </button>
        <button class="pf-btn" data-pf="active">
            <span class="material-symbols-outlined" style="font-size:1.05rem;">local_fire_department</span>
            Activas <span class="pf-count"><?= count($activas) ?></span>
        </button>
        <button class="pf-btn" data-pf="expired">
            <span class="material-symbols-outlined" style="font-size:1.05rem;">history</span>
            Finalizadas <span class="pf-count"><?= count($expiradas) ?></span>
        </button>

        <a href="<?= $whatsappBase ?>?text=<?= rawurlencode('¡Hola Aventuras Travel! 🌴✈️ Quiero información sobre sus promociones actuales.') ?>"
           target="_blank" rel="noopener"
           class="ml-auto flex items-center gap-2 px-4 py-2 rounded-xl text-white text-sm font-bold transition-all active:scale-95"
           style="background:linear-gradient(135deg,#25D366,#128C7E);box-shadow:0 4px 14px rgba(37,211,102,.25);">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Consultar
        </a>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     PROMOTIONS GRID
     ══════════════════════════════════════════════════════════════ -->
<section class="max-w-6xl mx-auto px-4 sm:px-6 pb-16">
<?php if (!empty($promociones)): ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="promoGrid">
    <?php foreach ($promociones as $idx => $p):
        $isActive  = ($p['status_label'] ?? '') === 'active';
        $daysLeft  = max(0, (int)($p['days_remaining'] ?? 0));
        $fechaFin  = $p['fecha_fin'] ?? date('Y-m-d');
        $titulo    = htmlspecialchars($p['titulo']     ?? '');
        $descr     = htmlspecialchars($p['descripcion'] ?? '');
        $destino   = htmlspecialchars($p['destino']    ?? 'Varios destinos');
        $descuento = htmlspecialchars($p['descuento']  ?? '');
        $imgSrc    = !empty($p['imagen'])
                     ? Router::url('/storage/promociones/' . htmlspecialchars($p['imagen']))
                     : '';
        $waMsg = rawurlencode("¡Hola Aventuras Travel! 🌴✈️\n\nMe interesa la promoción:\n📌 *{$p['titulo']}*\n📍 Destino: {$destino}\n💰 Descuento: {$descuento}\n\n¿Pueden brindarme más información? ¡Gracias!");
        $promoSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $p['titulo'] ?? 'promo'), '-'));
    ?>
    <div class="pc-wrap <?= $isActive ? '' : 'pc-expired' ?>"
         id="<?= htmlspecialchars($promoSlug) ?>"
         data-status="<?= $isActive ? 'active' : 'expired' ?>"
         style="animation-delay:<?= $idx * 0.07 ?>s; scroll-margin-top: 100px;">

        <!-- ── Image shell ── -->
        <div class="pc-img-shell">
            <?php if ($imgSrc): ?>
            <img class="pc-img"
                 src="<?= $imgSrc ?>"
                 alt="<?= $titulo ?>"
                 loading="lazy"
                 onclick="lbOpen(this)"
                 data-lb-title="<?= htmlspecialchars($titulo, ENT_QUOTES) ?>">

            <!-- zoom veil -->
            <div class="pc-zoom-veil" onclick="lbOpen(document.querySelector('[data-lb-title=\'<?= htmlspecialchars($titulo, ENT_QUOTES) ?>\']'))">
                <div class="pc-zoom-pill">
                    <span class="material-symbols-outlined">zoom_in</span>
                    Ver completa
                </div>
            </div>

            <?php else: ?>
            <div class="pc-img-fallback">
                <span class="material-symbols-outlined" style="font-size:4rem;color:rgba(74,190,217,.3);">travel_explore</span>
            </div>
            <?php endif; ?>

            <!-- Top-left status -->
            <div style="position:absolute;top:.75rem;left:.75rem;z-index:5;">
                <?php if ($isActive): ?>
                <span class="pc-badge-live">
                    <span style="width:.45rem;height:.45rem;background:#fff;border-radius:50%;display:inline-block;animation:pulse 1.4s infinite;"></span>
                    En Vivo
                </span>
                <?php else: ?>
                <span class="pc-badge-ended">
                    <span class="material-symbols-outlined" style="font-size:.75rem;">schedule</span>
                    Finalizado
                </span>
                <?php endif; ?>
            </div>

            <!-- Top-right discount -->
            <?php if ($descuento): ?>
            <div style="position:absolute;top:.75rem;right:.75rem;z-index:5;">
                <div class="pc-badge-discount"><?= $descuento ?></div>
            </div>
            <?php endif; ?>

            <!-- Bottom-right days -->
            <?php if ($isActive && $daysLeft > 0): ?>
            <div style="position:absolute;bottom:.75rem;right:.75rem;z-index:5;">
                <div class="pc-days-pill">
                    <span class="material-symbols-outlined" style="font-size:.85rem;">timer</span>
                    <strong><?= $daysLeft ?></strong> días
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- ── Card body ── -->
        <div class="pc-body">
            <h3 class="pc-title"><?= $titulo ?></h3>
            <?php if ($descr): ?>
            <p class="pc-desc"><?= $descr ?></p>
            <?php endif; ?>

            <div class="pc-meta">
                <span class="pc-meta-item">
                    <span class="material-symbols-outlined">location_on</span>
                    <?= $destino ?>
                </span>
                <span class="pc-meta-item">
                    <span class="material-symbols-outlined">event</span>
                    <?php if ($isActive): ?>
                        <?= $daysLeft ?> día<?= $daysLeft !== 1 ? 's' : '' ?> restante<?= $daysLeft !== 1 ? 's' : '' ?>
                    <?php else: ?>
                        Finalizó el <?= date('d M Y', strtotime($fechaFin)) ?>
                    <?php endif; ?>
                </span>
            </div>

            <?php if ($isActive): ?>
            <a href="<?= $whatsappBase ?>?text=<?= $waMsg ?>"
               target="_blank" rel="noopener"
               class="pc-wa-btn">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span>¡Me interesa! Consultar ahora</span>
                <span class="material-symbols-outlined" style="font-size:1rem;">arrow_forward</span>
            </a>
            <?php else: ?>
            <div class="pc-ended-badge">
                <span class="material-symbols-outlined" style="font-size:1.1rem;">event_busy</span>
                Promoción Finalizada
            </div>
            <?php endif; ?>
        </div>

    </div>
    <?php endforeach; ?>
    </div>

    <!-- Empty (filter) -->
    <div id="promoEmpty" class="hidden text-center py-16">
        <span class="material-symbols-outlined" style="font-size:3.5rem;color:rgba(74,190,217,.25);display:block;margin-bottom:1rem;">filter_alt_off</span>
        <p style="color:rgba(27,58,75,.4);font-size:1rem;font-weight:700;">No hay promociones en esta categoría</p>
        <p style="color:rgba(27,58,75,.3);font-size:.82rem;margin-top:.25rem;">Prueba con otro filtro</p>
    </div>

<?php else: ?>
    <!-- Empty state – no promos at all -->
    <div class="text-center py-20">
        <div style="width:6rem;height:6rem;background:rgba(74,190,217,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
            <span class="material-symbols-outlined" style="font-size:3rem;color:rgba(74,190,217,.4);">campaign</span>
        </div>
        <h3 style="font-size:1.1rem;font-weight:900;color:#1B3A4B;margin-bottom:.5rem;">No hay promociones disponibles</h3>
        <p style="color:rgba(27,58,75,.4);font-size:.85rem;max-width:26rem;margin:0 auto 2rem;">Estamos preparando increíbles ofertas. ¡Contáctanos para ofertas personalizadas!</p>
        <a href="<?= $whatsappBase ?>?text=<?= rawurlencode('¡Hola! Me gustaría conocer ofertas personalizadas 🌴✈️') ?>"
           target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 px-6 py-3 text-white font-bold rounded-xl transition-all active:scale-95"
           style="background:linear-gradient(135deg,#25D366,#128C7E);box-shadow:0 6px 20px rgba(37,211,102,.28);">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Consultar por WhatsApp
        </a>
    </div>
<?php endif; ?>
</section>

<!-- ══════════════════════════════════════════════════════════════
     CTA BANNER
     ══════════════════════════════════════════════════════════════ -->
<section class="max-w-6xl mx-auto px-4 sm:px-6 pb-16">
    <div class="relative overflow-hidden rounded-3xl text-center px-6 py-12 sm:py-16"
         style="background:linear-gradient(135deg,#0D2432 0%,#1B3A4B 50%,#2D5468 100%);">
        <div style="position:absolute;top:-30%;right:-8%;width:22rem;height:22rem;background:rgba(74,190,217,.08);border-radius:50%;filter:blur(60px);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-25%;left:-6%;width:18rem;height:18rem;background:rgba(74,190,217,.06);border-radius:50%;filter:blur(60px);pointer-events:none;"></div>

        <div class="relative z-10">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4"
                  style="background:rgba(74,190,217,.15);color:#7DD3E8;border:1px solid rgba(74,190,217,.25);">
                <span class="material-symbols-outlined" style="font-size:.85rem;">support_agent</span>
                Asesoría Personalizada
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white mb-3">¿No encuentras lo que buscas?</h2>
            <p style="color:rgba(255,255,255,.5);" class="max-w-md mx-auto text-sm mb-8">
                Nuestro equipo diseña viajes a la medida. Escríbenos y creamos tu experiencia perfecta.
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="<?= $whatsappBase ?>?text=<?= rawurlencode('¡Hola Aventuras Travel! 🌴 Necesito asesoría personalizada. ¿Pueden ayudarme?') ?>"
                   target="_blank" rel="noopener"
                   class="flex items-center gap-3 px-7 py-3.5 text-white font-bold rounded-2xl transition-all active:scale-95"
                   style="background:linear-gradient(135deg,#25D366,#128C7E);box-shadow:0 8px 24px rgba(37,211,102,.3);">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Escribir por WhatsApp
                </a>
                <a href="<?= Router::url('/asesoria') ?>"
                   class="flex items-center gap-2 px-7 py-3.5 text-white font-bold rounded-2xl transition-all"
                   style="background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.18);backdrop-filter:blur(8px);">
                    <span class="material-symbols-outlined">calendar_month</span>
                    Agendar Asesoría
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════════
     LIGHTBOX
     ══════════════════════════════════════════════════════════════ -->
<div id="lb-overlay" onclick="lbBgClick(event)">
    <button id="lb-close-btn" onclick="lbClose()" title="Cerrar">&times;</button>
    <div id="lb-frame">
        <img id="lb-img" src="" alt="">
        <div id="lb-caption"></div>
    </div>
    <div id="lb-hint">
        <span class="material-symbols-outlined" style="font-size:.9rem;">zoom_out</span>
        Clic fuera · <kbd>Esc</kbd> para cerrar
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     FLOATING WA
     ══════════════════════════════════════════════════════════════ -->
<a id="floatWA"
   href="<?= $whatsappBase ?>?text=<?= rawurlencode('¡Hola Aventuras Travel! 🌴✈️ Vi sus promociones y quiero más información.') ?>"
   target="_blank" rel="noopener">
    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    <span class="hidden sm:inline">¿Necesitas ayuda?</span>
</a>

<script>
/* ──────────────────────────────────────────────────────────────
   FILTER TABS
   ────────────────────────────────────────────────────────────── */
(function() {
    var btns  = document.querySelectorAll('.pf-btn');
    var cards = document.querySelectorAll('.pc-wrap');
    var empty = document.getElementById('promoEmpty');

    btns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            btns.forEach(function(b) { b.classList.remove('active'); });
            btn.classList.add('active');
            var f   = btn.dataset.pf;
            var cnt = 0;
            cards.forEach(function(c) {
                var match = f === 'all' || c.dataset.status === f;
                c.classList.toggle('pc-hidden', !match);
                if (match) cnt++;
            });
            if (empty) empty.classList.toggle('hidden', cnt > 0);
        });
    });
})();

/* ──────────────────────────────────────────────────────────────
   LIGHTBOX
   ────────────────────────────────────────────────────────────── */
var _lbActive = false;

function lbOpen(imgEl) {
    var src   = imgEl.src;
    var title = imgEl.dataset.lbTitle || imgEl.alt || '';
    var lb    = document.getElementById('lb-overlay');
    var lbImg = document.getElementById('lb-img');
    var lbCap = document.getElementById('lb-caption');
    var frame = document.getElementById('lb-frame');
    if (!lb || !lbImg) return;

    lbImg.src = src;
    lbImg.alt = title;
    if (lbCap) lbCap.textContent = title;

    /* Re-trigger animation */
    frame.style.animation = 'none';
    void frame.offsetWidth;
    frame.style.animation = '';

    lb.classList.add('lb-active');
    document.body.style.overflow = 'hidden';
    _lbActive = true;
}

function lbClose() {
    var lb = document.getElementById('lb-overlay');
    if (!lb) return;
    lb.classList.remove('lb-active');
    document.body.style.overflow = '';
    _lbActive = false;
    setTimeout(function() {
        var i = document.getElementById('lb-img');
        if (i && !_lbActive) i.src = '';
    }, 320);
}

function lbBgClick(e) {
    if (e.target === document.getElementById('lb-overlay')) lbClose();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && _lbActive) lbClose();
});

/* ──────────────────────────────────────────────────────────────
   FLOATING WA – hide on scroll down
   ────────────────────────────────────────────────────────────── */
(function() {
    var btn  = document.getElementById('floatWA');
    var last = 0;
    if (!btn) return;
    window.addEventListener('scroll', function() {
        var st = window.pageYOffset;
        if (st > last && st > 280) {
            btn.style.transform = 'translateY(90px)';
            btn.style.opacity   = '0';
        } else {
            btn.style.transform = '';
            btn.style.opacity   = '1';
        }
        last = st < 0 ? 0 : st;
    }, { passive: true });
})();
</script>
