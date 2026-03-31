<?php
/**
 * Partial: Insurance panel (mobile version)
 * Reused from services.php sidebar insurance card for mobile breakpoint
 */
?>
<div class="bg-secondary rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl">
    <div class="absolute -top-12 -right-12 w-48 h-48 bg-primary-container/20 rounded-full blur-[60px]"></div>
    <div class="relative z-10">
        <div class="w-14 h-14 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-3xl text-primary-fixed" style="font-variation-settings: 'FILL' 1;">verified_user</span>
        </div>
        <h3 class="text-2xl font-black tracking-tight mb-3 leading-tight">Seguro de Viaje<br>Asistencia Global</h3>
        <p class="text-white/60 text-sm mb-8 leading-relaxed font-medium">Tu seguridad es nuestra prioridad. Cuentas con cobertura integral 24/7.</p>
        <div class="space-y-4 mb-8">
            <?php foreach (['Asistencia médica premium (USD 60k)', 'Cobertura equipaje y cancelaciones', 'Repatriación sanitaria incluida'] as $item): ?>
            <div class="flex items-center gap-3">
                <div class="w-5 h-5 rounded-full bg-primary-container/20 flex items-center justify-center border border-primary-container/30">
                    <span class="material-symbols-outlined text-xs text-primary-container">check</span>
                </div>
                <span class="text-sm font-bold"><?= $item ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="w-full py-4 bg-primary text-white font-black rounded-2xl hover:bg-primary-container transition-all shadow-xl shadow-black/20 flex items-center justify-center gap-2 text-sm">
            Ver Certificado de Póliza
            <span class="material-symbols-outlined text-lg">download</span>
        </button>
    </div>
</div>
