<?php
// Vista cliente: Detalle de contrato (servicios y cronograma de pagos)
// Variables esperadas: $contrato, $user, $csrf_token
?>
<?php
    // Fragmento: Detalle de contrato (se incluye dentro del layout `client.php`)
    $heroImage = $contrato['hero_image'] ?? ($contrato['grupo']['imagen'] ?? ($contrato['imagen'] ?? 'https://images.unsplash.com/photo-1508193638397-1c77f8c9b3d8?q=80&w=1400&auto=format&fit=crop'));
    $total = (float) ($contrato['valor_total'] ?? 0);
    $pagado = 0.0;
    if (!empty($contrato['pagos'])) { foreach ($contrato['pagos'] as $p) { if (!empty($p['estado']) && $p['estado']==='aprobado') $pagado += (float)$p['monto']; }}
    $pct = $total > 0 ? round(($pagado/$total)*100) : 0;
?>

<?php
    $heroUrl = $heroImage;
    if (strpos($heroUrl, 'storage.php') !== false) {
        if (strpos($heroUrl, '/') !== 0) $heroUrl = '/' . ltrim($heroUrl, '/');
        $heroUrl = Router::url($heroUrl);
    }
?>
<section class="hero">
    <div class="hero-bg" style="background-image:url('<?= htmlspecialchars($heroUrl) ?>')"></div>
    <!-- hero-image: <?= htmlspecialchars($heroUrl) ?> -->
    <div class="hero-content">
        <div class="hero-left">
            <h1 class="hero-title"><?= htmlspecialchars($contrato['destino'] ?? 'Viaje') ?></h1>
            <div class="hero-sub">Grupo: <strong><?= htmlspecialchars($contrato['grupo']['nombre'] ?? '—') ?></strong> · <?= htmlspecialchars($contrato['codigo'] ?? '—') ?> · <?= htmlspecialchars(($contrato['fecha_salida'] ?? '') ? ($contrato['fecha_salida'] . ' – ' . ($contrato['fecha_retorno'] ?? '')) : '') ?></div>
            <div style="margin-top:12px;color:#fff;font-weight:700">Pagado: $<?= number_format($pagado,2) ?> · Total: $<?= number_format($total,2) ?></div>
        </div>
        <div class="hero-right">
            <div class="card balance">
                <div class="label">Saldo pendiente</div>
                <div class="amount">$<?= number_format($contrato['saldo'] ?? ($total - $pagado),2) ?></div>
                <div class="sub">Total: $<?= number_format($total,2) ?></div>
            </div>
            <div class="actions">
                <a class="btn primary" href="<?= Router::url('/client/payments?contrato_id=' . (int)($contrato['id']??0)) ?>">Pagar ahora</a>
                <a class="btn outline" href="<?= Router::url('/client/contract/' . (int)($contrato['id']??0)) ?>?download=1">Descargar contrato</a>
                <button class="btn outline" id="btnToggleUpload">Subir comprobante</button>
            </div>
        </div>
    </div>
</section>

<main class="content">
            <div class="grid three">
                <div class="card user-card">
                    <div class="card-header">Titular del Contrato</div>
                    <div class="card-body">
                        <div class="user-name"><?= htmlspecialchars($contrato['cliente_nombre'] ?? $contrato['titular_nombre'] ?? '—') ?></div>
                        <div class="user-meta"><?= htmlspecialchars($contrato['cliente_email'] ?? $contrato['titular_correo'] ?? '') ?></div>
                        <div class="user-meta"><?= htmlspecialchars($contrato['cliente_telefono'] ?? $contrato['titular_telefono'] ?? '') ?></div>
                    </div>
                </div>

                <div class="card services-card">
                    <div class="card-header">Servicios Contratados</div>
                    <div class="card-body">
                            <ul class="svc-list">
                                <?php if (!empty($contrato['servicios'])): foreach($contrato['servicios'] as $s): $detalles = json_decode($s['detalles_json'] ?? '{}', true) ?: []; ?>
                                    <li class="svc-item">
                                        <div class="svc-icon">S</div>
                                        <div class="svc-body">
                                            <div class="svc-name"><?= htmlspecialchars($s['nombre'] ?? ucfirst($s['tipo'] ?? 'Servicio')) ?></div>
                                            <div class="svc-meta"><?= htmlspecialchars($s['descripcion'] ?? '') ?></div>
                                            <?php if (!empty($detalles)): ?>
                                                <div class="svc-meta">Itinerario: 
                                                    <?php if (!empty($detalles['hoteles'])): foreach($detalles['hoteles'] as $h): ?>
                                                        <div>- <?= htmlspecialchars($h['nombre'] ?? '') ?> (<?= htmlspecialchars($h['checkin'] ?? '') ?> → <?= htmlspecialchars($h['checkout'] ?? '') ?>)</div>
                                                    <?php endforeach; endif; ?>
                                                    <?php if (!empty($detalles['tours'])): foreach($detalles['tours'] as $t): ?>
                                                        <div>- <?= htmlspecialchars($t['titulo'] ?? $t['nombre'] ?? '') ?>: <?= htmlspecialchars($t['fecha'] ?? '') ?></div>
                                                    <?php endforeach; endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="svc-price"><?= !empty($s['precio']) ? '$' . number_format($s['precio'],2) : '' ?></div>
                                    </li>
                                <?php endforeach; elseif (!empty($contrato['servicios_grupo'])): foreach($contrato['servicios_grupo'] as $g): $det = json_decode($g['detalle_json'] ?? '{}', true) ?: []; $name = $g['titulo'] ?? ($det['hoteles'][0]['nombre'] ?? ($det['vuelos'][0]['ruta'] ?? ucfirst($g['servicio_tipo'] ?? 'Servicio'))); ?>
                                    <li class="svc-item">
                                        <div class="svc-icon">G</div>
                                        <div class="svc-body">
                                            <div class="svc-name"><?= htmlspecialchars($name) ?></div>
                                            <div class="svc-meta">Grupo: <?= htmlspecialchars($g['titulo'] ?? '') ?></div>
                                            <?php if (!empty($det)): ?>
                                                <div class="svc-meta">Itinerario:
                                                    <?php if (!empty($det['vuelos'])): foreach($det['vuelos'] as $vf): ?>
                                                        <div>- Vuelo: <?= htmlspecialchars($vf['ruta'] ?? '') ?> (<?= htmlspecialchars($vf['salida'] ?? $vf['salida_iso'] ?? '') ?>)</div>
                                                    <?php endforeach; endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="svc-price"></div>
                                    </li>
                                <?php endforeach; else: ?>
                                    <li class="empty">No hay servicios detallados.</li>
                                <?php endif; ?>
                            </ul>
                    </div>
                </div>

                <div class="card pax-card">
                    <div class="card-header">Pasajeros (<?= count($contrato['pasajeros'] ?? []) ?>)</div>
                    <div class="card-body">
                        <ul class="pax-list">
                            <?php foreach($contrato['pasajeros'] ?? [] as $p): ?>
                                <li><?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card">
                <h2>Itinerario de Vuelos</h2>
                <div class="card-body">
                    <?php if (!empty($contrato['vuelos'])): foreach($contrato['vuelos'] as $v): ?>
                        <div class="svc-item flight-item">
                            <div class="svc-icon">✈️</div>
                            <div class="svc-body">
                                <div class="svc-name"><?= htmlspecialchars($v['aerolinea'] ?? 'Aerolínea') ?> - <?= htmlspecialchars($v['numero_vuelo'] ?? $v['numero'] ?? '') ?></div>
                                <div class="svc-meta"><?= htmlspecialchars(($v['origen'] ?? '') . ' → ' . ($v['destino'] ?? '')) ?></div>
                                <div class="svc-meta">Salida: <?= htmlspecialchars($v['fecha_salida'] ?? $v['salida_iso'] ?? '') ?> · Llegada: <?= htmlspecialchars($v['fecha_llegada'] ?? $v['llegada_iso'] ?? '') ?></div>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="empty">No hay vuelos registrados en este contrato.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid two">
                <div class="card payments-card">
                    <div class="card-header">Cronograma de Pagos</div>
                    <div class="card-body">
                        <?php if (!empty($contrato['plan_cuotas'])): ?>
                            <table class="payments">
                                <thead><tr><th>Concepto</th><th>Vencimiento</th><th>Monto</th><th>Estado</th></tr></thead>
                                <tbody>
                                <?php foreach($contrato['plan_cuotas'] as $c): ?>
                                    <tr>
                                        <td>Cuota <?= htmlspecialchars($c['numero_cuota'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($c['fecha_vencimiento'] ?? '') ?></td>
                                        <td>$<?= number_format($c['monto'] ?? 0,2) ?></td>
                                        <td><span class="pill <?= htmlspecialchars($c['estado'] ?? '') ?>"><?= htmlspecialchars(ucfirst($c['estado'] ?? 'pendiente')) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php elseif (!empty($contrato['pagos'])): ?>
                            <table class="payments">
                                <thead><tr><th>Concepto</th><th>Vencimiento</th><th>Monto</th><th>Estado</th></tr></thead>
                                <tbody>
                                <?php foreach($contrato['pagos'] as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['concepto'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($p['fecha_vencimiento'] ?? '-') ?></td>
                                        <td>$<?= number_format($p['monto'] ?? 0,2) ?></td>
                                        <td><span class="pill <?= htmlspecialchars($p['estado'] ?? '') ?>"><?= htmlspecialchars(ucfirst($p['estado'] ?? 'pendiente')) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty">No hay pagos programados.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card policies-card">
                    <div class="card-header">Políticas</div>
                    <div class="card-body">
                        <ul class="policies">
                            <li><strong>Penalidad:</strong> 3% por cuota atrasada (gracia: 5 días).</li>
                            <li><strong>Cancelación:</strong> Hasta 30 días antes sin penalidad.</li>
                            <li><strong>Documentos:</strong> Pasaporte vigente requerido.</li>
                            <li><strong>Modificaciones:</strong> Sujetas a disponibilidad y cargos.</li>
                        </ul>
                    </div>
                    <div class="card-foot">
                        <button class="btn outline contact" id="btnContact">Contactar</button>
                    </div>
                </div>
            </div>

            <div id="uploadPanel" class="card upload-card" style="display:none;">
                <div class="card-header">Subir comprobante</div>
                <div class="card-body">
                    <form action="<?= Router::url('/client/registerPayment') ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <input type="hidden" name="contrato_id" value="<?= (int)($contrato['id']??0) ?>">
                        <div class="form-row"><label>Monto</label><input type="number" name="monto" step="0.01" required></div>
                        <div class="form-row"><label>Comprobante</label><input type="file" name="comprobante" accept="application/pdf,image/*" required></div>
                        <div class="form-actions"><button class="btn primary" type="submit">Enviar</button><button class="btn outline" type="button" id="btnCancelUpload">Cancelar</button></div>
                    </form>
                </div>
            </div>

        </main>

        <footer class="footer">&copy; <?= date('Y') ?> Aventuras Travel · soporte@aventuras.travel</footer>
    </div>

    <!-- scripts del cliente se cargan desde el layout client.php -->
</body>
</html>
