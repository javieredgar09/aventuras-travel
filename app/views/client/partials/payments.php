<?php
// Parcial: Cronograma / Pagos (vista compacta)
$payments = $contrato['pagos'] ?? [];
?>
<div class="card" style="margin-top:14px">
  <h3>Cronograma de Pagos</h3>
  <?php if (empty($payments)): ?>
    <div class="empty">No hay pagos programados.</div>
  <?php else: ?>
    <ul class="payments-list" style="list-style:none;margin:0;padding:0;">
      <?php foreach ($payments as $p): ?>
        <li style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f2f7f6">
          <div>
            <div style="font-weight:800"><?= htmlspecialchars($p['concepto'] ?? 'Pago') ?></div>
            <div class="muted" style="font-size:13px">Vence: <?= htmlspecialchars($p['fecha_vencimiento'] ?? '') ?></div>
          </div>
          <div style="text-align:right">
            <div style="font-weight:900">$<?= number_format($p['monto'] ?? 0, 2) ?></div>
            <div class="status <?= htmlspecialchars($p['estado'] ?? '') ?>" style="margin-top:6px;font-size:12px"><?= htmlspecialchars($p['estado'] ?? '') ?></div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
