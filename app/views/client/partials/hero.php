<?php
// Parcial: hero
$heroUrl = htmlspecialchars($contrato['hero_image'] ?? ($grupo['hero_image'] ?? Router::url('/assets/img/default-hero.jpg')));
$codigo = htmlspecialchars($contrato['codigo'] ?? ($grupo['codigo'] ?? ''));
$titulo = htmlspecialchars($contrato['destino'] ?? $grupo['destino'] ?? 'Tu viaje');
$sub = htmlspecialchars(($contrato['fecha_salida'] ?? $grupo['fecha_viaje'] ?? '') . ' — ' . ($contrato['fecha_retorno'] ?? $grupo['fecha_retorno'] ?? ''));
?>
<section class="hero-card" style="background-image: url('<?= $heroUrl ?>')">
  <div class="hero-content">
    <small class="badge">Contrato <?= $codigo ?></small>
    <h1 class="hero-title"><?= $titulo ?></h1>
    <p class="hero-sub"><?= $sub ?></p>
    <div class="hero-actions">
      <a href="#itinerary" class="btn">Ver Itinerario</a>
      <a href="#" class="btn secondary">Descargar Vouchers</a>
    </div>
  </div>
</section>
