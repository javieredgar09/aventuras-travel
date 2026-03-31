<?php
// Parcial: Itinerario
// Determinar servicios (grupo primero, luego contrato)
$services = [];
if (!empty($grupo['servicios'])) $services = $grupo['servicios'];
elseif (!empty($contrato['servicios'])) $services = $contrato['servicios'];

?>
<section class="card">
  <h2>Itinerario de Actividades</h2>
  <div class="itinerary-list" id="itinerary">
    <?php if (empty($services)): ?>
      <div class="empty">No hay itinerario registrado. Contacta a tu asesor o el representante del grupo.</div>
    <?php else:
      $found = false;
      foreach ($services as $s):
        $tipo = strtolower($s['servicio_tipo'] ?? $s['tipo'] ?? '');
        $det = json_decode($s['detalle_json'] ?? '{}', true);
        if (str_contains($tipo, 'itiner') || str_contains($tipo, 'vuelo') || str_contains($tipo, 'actividad')):
          $found = true;
    ?>
      <article class="it-day">
        <div style="display:flex;align-items:center;gap:12px">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" aria-hidden="true"><use href="#icon-itinerary" fill="#0f6b66"></use></svg>
          <div>
            <h4 style="margin:0"><?= htmlspecialchars($det['titulo'] ?? ($det['itinerario'][0]['titulo'] ?? 'Actividad')) ?></h4>
            <p style="margin:6px 0 0;color:var(--cd-muted)"><?= htmlspecialchars($det['descripcion'] ?? ($det['itinerario'][0]['descripcion'] ?? 'Descripción por definir')) ?></p>
          </div>
        </div>
      </article>
    <?php endif; endforeach;
      if (!$found) echo '<div class="empty">No hay actividades específicas en el itinerario.</div>';
    endif; ?>
</section>
