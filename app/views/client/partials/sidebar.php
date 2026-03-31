<?php
// Parcial: Sidebar resumen
$total = $contrato['valor_total'] ?? $grupo['valor_total'] ?? 0;
$estado = htmlspecialchars($contrato['estado'] ?? $grupo['estado'] ?? 'Activo');
?>
<div class="card">
  <h3>Resumen financiero</h3>
  <div style="margin-top:12px;display:flex;flex-direction:column;gap:12px">
    <div class="info-item" style="display:flex;justify-content:space-between;align-items:center"><div>
      <div class="label">Valor total</div>
      <div class="value">$<?= number_format($total,2) ?></div>
    </div>
    <svg width="40" height="40" viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-money" fill="#0f6b66"/></svg>
    </div>
    <div><a class="btn" href="<?= Router::url('/client/payments') ?>">Pagar Ahora</a></div>
  </div>
</div>

<div class="card" style="margin-top:14px">
  <h3>Estado del viaje</h3>
  <p class="muted"><?= $estado ?></p>
</div>
