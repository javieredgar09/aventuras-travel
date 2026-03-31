<!-- PROMOCIONES PÚBLICAS – TAILWIND -->
<?php $promociones = $promociones ?? []; ?>

<section class="px-8 py-16 max-w-6xl mx-auto">
    <div class="text-center mb-12">
        <span class="text-xs font-bold uppercase tracking-widest text-turquesa-dark">Ofertas Especiales</span>
        <h1 class="text-4xl font-black text-petroleo mt-2 mb-3">Experiencias y Paquetes</h1>
        <p class="text-petroleo/50 max-w-xl mx-auto">Ofertas exclusivas curadas por nuestros asesores de viajes en Pucallpa.</p>
    </div>

    <?php if (!empty($promociones)): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($promociones as $p): ?>
        <div class="bg-white rounded-2xl overflow-hidden border border-petroleo/5 shadow-sm hover:shadow-xl transition-all group">
            <div class="h-48 bg-gradient-to-br from-turquesa/20 to-petroleo/10 relative overflow-hidden">
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="material-symbols-outlined text-6xl text-turquesa/30 group-hover:scale-110 transition-transform">travel_explore</span>
                </div>
                <div class="absolute top-4 left-4">
                    <span class="<?= ($p['activa'] ?? false) ? 'bg-emerald-500' : 'bg-petroleo/30' ?> text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase">
                        <?= ($p['activa'] ?? false) ? 'ACTIVO' : 'FINALIZADO' ?>
                    </span>
                </div>
            </div>
            <div class="p-6">
                <?php if (!empty($p['descuento'])): ?>
                    <span class="text-turquesa-dark font-black text-sm"><?= htmlspecialchars($p['descuento']) ?></span>
                <?php endif; ?>
                <h3 class="text-lg font-black text-petroleo mt-1 mb-2"><?= htmlspecialchars($p['titulo'] ?? '') ?></h3>
                <p class="text-sm text-petroleo/50 mb-4"><?= htmlspecialchars($p['descripcion'] ?? '') ?></p>
                <div class="flex items-center gap-2 text-xs text-petroleo/40">
                    <span class="material-symbols-outlined text-sm">location_on</span>
                    <?= htmlspecialchars($p['destino'] ?? 'Varios destinos') ?>
                    <span class="mx-1">·</span>
                    Válido hasta <?= date('d M, Y', strtotime($p['fecha_fin'] ?? 'now')) ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-12">
        <span class="material-symbols-outlined text-5xl text-turquesa/30 mb-3">campaign</span>
        <p class="text-petroleo/40 text-lg">No hay promociones disponibles en este momento.</p>
    </div>
    <?php endif; ?>
</section>
