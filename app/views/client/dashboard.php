<?php
// Vista: Cliente - Dashboard modular (usa parciales en app/views/client/partials)
 $user = $data['user'] ?? ($_SESSION['user'] ?? null);
$contrato = $contrato ?? $data['contrato'] ?? null;
$grupo = $grupo ?? $data['grupo'] ?? null;

// Rutas a parciales
$partials = __DIR__ . '/partials';

?>
 <div class="client-dashboard container">
  <style>
    /* Mantener header / sidebar del layout; limitar ancho del contenido */
    .client-dashboard.container {max-width:1200px;margin:0 auto}
  </style>

  <!-- Menú superior replicado desde el diseño enviado -->
  <nav class="fixed top-0 w-full z-50 bg-surface/90 backdrop-blur-xl nav-shadow flex justify-between items-center px-6 md:px-12 py-3">
    <div class="flex items-center gap-8">
      <span class="text-xl md:text-2xl font-black tracking-tighter text-cyan-900">Elevated Explorer</span>
      <div class="hidden lg:flex items-center gap-1 border-l border-slate-200 pl-8">
        <a class="px-4 py-2 text-cyan-700 font-bold border-b-2 border-cyan-600" href="#">Inicio</a>
        <a class="px-4 py-2 text-slate-600 hover:text-cyan-700" href="#">Servicios</a>
        <a class="px-4 py-2 text-slate-600 hover:text-cyan-700" href="#">Mis Viajes</a>
        <a class="px-4 py-2 text-slate-600 hover:text-cyan-700" href="#">Pagos</a>
        <a class="px-4 py-2 text-slate-600 hover:text-cyan-700" href="#">Soporte</a>
      </div>
    </div>
    <div class="flex items-center gap-2 md:gap-4">
      <button class="p-2 hover:bg-cyan-50/50 rounded-lg duration-300 ease-in-out">
        <span class="material-symbols-outlined text-cyan-800">notifications</span>
      </button>
      <button class="flex items-center gap-2 px-3 py-1.5 hover:bg-cyan-50/50 rounded-full transition-colors">
        <span class="material-symbols-outlined text-cyan-800 text-3xl">account_circle</span>
        <div class="hidden sm:block text-left">
          <p class="text-xs font-bold text-cyan-800 leading-none"><?= htmlspecialchars($user['nombre'] ?? '') ?></p>
          <p class="text-[10px] text-slate-500 leading-none mt-0.5">Cliente</p>
        </div>
      </button>
    </div>
  </nav>

  <?php require $partials . '/icons.php'; ?>

  <?php
  // Datos principales
  $userName = trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')) ?: ($user['email'] ?? 'Cliente');
  $startDate = $contrato['fecha_salida'] ?? $grupo['fecha_viaje'] ?? null;
  $daysLeft = '';
  if ($startDate) {
    $d1 = new DateTime($startDate);
    $now = new DateTime();
    $diff = $now->diff($d1);
    $daysLeft = (int)$diff->format('%r%a');
  }

  // Cálculos y formateo de moneda seguros
  $valor_total = (float)($contrato['valor_total'] ?? 0);
  $total_pagado = (float)($contrato['total_pagado'] ?? 0);
  $saldo = isset($contrato['saldo']) ? (float)$contrato['saldo'] : ($valor_total - $total_pagado);

  $default_currency = $contrato['moneda'] ?? 'PEN';
  function format_moneda($amount, $currency = null) {
      global $default_currency;
      $amount = (float)$amount;
      $currency = $currency ?? $default_currency;
      $c = strtoupper($currency);
      if (in_array($c, ['USD', '$'])) {
          $symbol = '$';
      } elseif ($c === 'EUR') {
          $symbol = '€';
      } elseif ($c === 'PEN') {
          $symbol = 'S/.';
      } else {
          $symbol = $c . ' ';
      }
      return $symbol . number_format($amount, 2, ',', '.');
  }
  ?>

  <section class="hero-card" style="background-image: url('<?= htmlspecialchars($contrato['hero_image'] ?? ($grupo['hero_image'] ?? Router::url('/assets/img/default-hero.jpg'))) ?>');">
    <div class="hero-content">
      <h1 class="hero-title"><?= htmlspecialchars($grupo['destino'] ?? $contrato['destino'] ?? 'Destino') ?></h1>
      <div style="margin-top:6px;color:rgba(255,255,255,0.92);font-weight:600">
        Grupo: <?= htmlspecialchars($grupo['nombre'] ?? ($contrato['grupo_nombre'] ?? '—')) ?> · <?= htmlspecialchars($contrato['codigo'] ?? '') ?> · <?= htmlspecialchars($contrato['fecha_salida'] ?? '') ?> · <?= htmlspecialchars($contrato['fecha_retorno'] ?? '') ?>
      </div>
      <div style="margin-top:12px;font-weight:700;">Pagado: <?= htmlspecialchars(format_moneda($total_pagado, $contrato['moneda'] ?? null)) ?> · Total: <?= htmlspecialchars(format_moneda($valor_total, $contrato['moneda'] ?? null)) ?></div>

      <div class="hero-actions">
        <a href="#" class="btn secondary">Descargar Contrato</a>
        <a href="#" class="btn">Subir comprobante</a>
      </div>
    </div>

    <!-- Caja flotante de saldo a la derecha similar al diseño -->
    <div style="position:absolute;right:36px;top:36px;z-index:5">
      <div class="card" style="padding:18px 28px;border-radius:12px;min-width:200px;text-align:center;background:rgba(255,255,255,0.92)">
        <div class="muted" style="font-size:13px">Saldo pendiente</div>
        <div style="font-size:28px;font-weight:900;color:var(--cd-primary)"><?= htmlspecialchars(format_moneda($saldo, $contrato['moneda'] ?? null)) ?></div>
        <div class="muted" style="font-size:12px">Total: <?= htmlspecialchars(format_moneda($valor_total, $contrato['moneda'] ?? null)) ?></div>
      </div>
    </div>
  </section>

  <div class="grid-layout" style="display:grid;grid-template-columns:260px 1fr 300px;gap:20px;margin-top:20px;align-items:start">
    <div>
      <?php if (!empty($contrato) || !empty($cliente)): ?>
      <div class="card" style="border-radius:16px;padding:20px;background:linear-gradient(180deg,#0ea5a3,#0f6b66);color:#fff">
        <h3>¿Listo para más?</h3>
        <p>Planifica tu próxima escapada con tarifas exclusivas para clientes preferenciales.</p>
        <!-- Reserva deshabilitada desde el panel cliente (solo admin puede crear reservas) -->
        <div style="margin-top:12px"><a class="btn" href="#" aria-disabled="true" style="opacity:0.6;pointer-events:none">Nueva Reserva</a></div>
      </div>
      <?php else: ?>
      <div class="card" style="border-radius:16px;padding:20px;">
        <h3>Panel del Cliente</h3>
        <p class="muted">No se encontró un contrato asociado. Contacta a tu asesor para activar tu reserva.</p>
      </div>
      <?php endif; ?>
    </div>

    <div>
      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px">
        <div class="card">
          <h4>Saldo pendiente</h4>
          <div style="font-size:28px;font-weight:900;color:var(--cd-primary)"><?= htmlspecialchars(format_moneda($saldo, $contrato['moneda'] ?? null)) ?></div>
        </div>
        <div class="card">
          <h4>Próximo vuelo</h4>
          <div class="muted"><?= htmlspecialchars($contrato['vuelos'][0]['origen'] ?? '-') ?> → <?= htmlspecialchars($contrato['vuelos'][0]['destino'] ?? '-') ?></div>
        </div>
      </div>

      <div style="margin-top:18px">
        <h3>Pasajeros del Contrato</h3>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:10px">
          <?php foreach ($contrato['pasajeros'] ?? [] as $p): ?>
            <div style="background:#fff;padding:12px;border-radius:10px;box-shadow:0 6px 18px rgba(16,31,31,0.04)">
              <div style="font-weight:800"><?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?></div>
              <div class="muted" style="font-size:13px"><?= htmlspecialchars($p['tipo'] ?? '') ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <aside>
      <div class="card">
        <h4>Estado del Contrato</h4>
        <div class="muted"><?= htmlspecialchars($contrato['estado'] ?? 'Activo') ?></div>
        <div style="margin-top:12px"><a class="btn" href="<?= Router::url('/client/payments') ?>">Pagar Cuota</a></div>
      </div>
    </aside>
  </div>

  <div style="margin-top:24px">
    <h2 style="margin-bottom:12px">Itinerario de Actividades</h2>
    <div class="grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
      <?php
      $services = $grupo['servicios'] ?? ($contrato['servicios'] ?? []);
      $items = [];
      foreach ($services as $s) {
        $det = json_decode($s['detalle_json'] ?? $s['detalles_json'] ?? '{}', true);
        $items[] = [ 'titulo' => $det['titulo'] ?? ($s['nombre'] ?? $s['servicio_tipo'] ?? 'Actividad'), 'descripcion' => $det['descripcion'] ?? ($det['itinerario'][0]['descripcion'] ?? '') ];
      }
      if (empty($items)) echo '<div class="empty">No hay actividades registradas.</div>';
      foreach ($items as $it): ?>
        <div class="card">
          <h4><?= htmlspecialchars($it['titulo']) ?></h4>
          <p class="muted"><?= htmlspecialchars($it['descripcion']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>

