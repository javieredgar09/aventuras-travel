<?php
// Vista: Portal del Cliente - Dashboard principal
// Variables esperadas: $contrato (array), $user, $csrf_token
?>
<?php
// Fragmento: Dashboard del cliente (se renderiza dentro del layout `client.php`)
    $heroImage = $contrato['hero_image'] ?? ($contrato['grupo']['imagen'] ?? ($contrato['imagen'] ?? 'https://images.unsplash.com/photo-1508193638397-1c77f8c9b3d8?q=80&w=1400&auto=format&fit=crop'));
?>

<?php
    $heroUrl = $heroImage;
    if (strpos($heroUrl, 'storage.php') !== false) {
        if (strpos($heroUrl, '/') !== 0) $heroUrl = '/' . ltrim($heroUrl, '/');
        $heroUrl = Router::url($heroUrl);
    }
?>
<section class="hero">
    <div class="hero-bg" style="background-image: url('<?= htmlspecialchars($heroUrl) ?>');"></div>
    <!-- hero-image: <?= htmlspecialchars($heroUrl) ?> -->
    <div class="hero-content">
        <div class="hero-left">
            <h1 class="hero-title"><?= htmlspecialchars($contrato['destino'] ?? 'Viaje') ?></h1>
            <div class="hero-sub"><?= htmlspecialchars($contrato['codigo'] ?? '—') ?> · <?= htmlspecialchars(($contrato['fecha_salida'] ?? '') ? ($contrato['fecha_salida'] . ' – ' . ($contrato['fecha_retorno'] ?? '')) : '') ?></div>
        </div>
        <div class="hero-right">
            <div class="card balance">
                <div class="label">Saldo pendiente</div>
                <div class="amount">$<?= number_format($contrato['saldo'] ?? 0,2) ?></div>
                <div class="sub">Total: $<?= number_format($contrato['valor_total'] ?? 0,2) ?></div>
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
                        <ul class="services">
                            <?php if (!empty($contrato['servicios'])): foreach($contrato['servicios'] as $s): ?>
                                <li><?= htmlspecialchars($s['nombre'] ?? $s['tipo'] ?? 'Servicio') ?></li>
                            <?php endforeach; else: ?>
                                <li>No hay servicios detallados. Revisa la sección de servicios.</li>
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
                        <div class="form-row">
                            <label>Monto</label>
                            <input type="number" name="monto" step="0.01" required>
                        </div>
                        <div class="form-row">
                            <label>Comprobante</label>
                            <input type="file" name="comprobante" accept="application/pdf,image/*" required>
                        </div>
                        <div class="form-actions">
                            <button class="btn primary" type="submit">Enviar</button>
                            <button class="btn outline" type="button" id="btnCancelUpload">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

        </main>

        <footer class="footer">&copy; <?= date('Y') ?> Aventuras Travel · Soporte: soporte@aventuras.travel</footer>
    </div>

    <script src="<?= Router::url('/assets/js/client_portal.js') ?>"></script>
</body>
</html>
