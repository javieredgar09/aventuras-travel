<?php
// Modal para editar itinerario (visible a representantes)
$grupoId = $grupo['id'] ?? null;
$services = $grupo['servicios'] ?? [];
$itDetalle = null;
foreach ($services as $s) {
  $tipo = strtolower($s['servicio_tipo'] ?? $s['tipo'] ?? '');
  if (str_contains($tipo, 'itiner')) { $itDetalle = $s['detalle_json']; break; }
}
if (!$itDetalle) $itDetalle = json_encode(['itinerario' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
<div id="modal-itinerary" class="modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);align-items:center;justify-content:center;z-index:1000;padding:1rem">
  <div class="modal-card" style="width:100%;max-width:860px;background:#fff;border-radius:12px;padding:14px 16px">
    <h3>Editar Itinerario</h3>
    <form id="form-itinerary">
      <input type="hidden" name="grupo_id" value="<?= htmlspecialchars($grupoId) ?>">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '') ?>">
      <textarea name="detalle_json" id="detalle_json" style="width:100%;height:50vh;max-height:320px;padding:12px;border:1px solid #e6f2f1;border-radius:8px;font-family:monospace;font-size:13px"><?= htmlspecialchars($itDetalle) ?></textarea>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:12px">
        <button type="button" id="cancel-it" class="btn secondary">Cancelar</button>
        <button type="submit" class="btn">Guardar</button>
      </div>
    </form>
  </div>
</div>
