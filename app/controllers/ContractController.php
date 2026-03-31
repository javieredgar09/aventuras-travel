<?php
class ContractController extends Controller {
    public function index(): void {
        $model = new Contrato();
        $contratos = $model->getRecentWithClient(50);
        $data = [
            'title'     => 'Contracts - Aventuras Travel',
            'contratos' => $contratos,
        ];
        $this->render('admin/contracts/index', $data, 'admin');
    }

    public function show(string $id): void {
        $model = new Contrato();
        $contrato = $model->getFullDetails((int) $id);
        if (!$contrato) {
            $this->redirect('/admin/contracts');
            return;
        }

        // Enrich cuotas with real payment totals
        $pagoModel = new Pago();
        $cuotaModel = new Cuota();
        $totalPagadoReal = $pagoModel->getTotalApprovedByContrato((int) $id);
        $contrato['total_pagado_real'] = $totalPagadoReal;
        $contrato['saldo_real'] = max(0, (float)($contrato['valor_total'] ?? 0) - $totalPagadoReal);
        $contrato['cuotas'] = $cuotaModel->getByEntidad('contrato', (int) $id);
        $contrato['resumen_cuotas'] = $cuotaModel->getSummary('contrato', (int) $id);

        $data = [
            'title'      => $contrato['codigo'] . ' - Aventuras Travel',
            'contrato'   => $contrato,
            'csrf_token' => $this->generateCsrfToken(),
            'flash'      => $this->getFlash(),
        ];
        $this->render('admin/contracts/show', $data, 'admin');
    }

    public function delete(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/contracts/' . $id);
            return;
        }

        $db = Database::getInstance();
        $contrato = (new Contrato())->find((int) $id);
        
        if ($contrato) {
            $grupoId = $contrato['grupo_id'];
            $db->delete('contratos', 'id = ?', [$id]);
            $this->flash('exito', 'Contrato anulado y eliminado.');
            $this->redirect('/admin/sales/' . $grupoId);
        } else {
            $this->redirect('/admin/contracts');
        }
    }

    public function print(int $id): void {
        $model = new Contrato();
        $contrato = $model->getFullDetails($id);
        if (!$contrato) {
            $this->redirect('/admin/contracts');
            return;
        }
        $data = [
            'title'    => 'Contrato ' . $contrato['codigo'] . ' - Aventuras Travel',
            'contrato' => $contrato,
        ];
        $this->render('admin/contracts/print', $data, 'empty');
    }
    public function create(int $id): void {
        $grupoId = $id;
        $db = Database::getInstance();
        $grupo = (new Grupo())->find($grupoId);
        
        if (!$grupo) {
            $this->flash('error', 'Grupo no encontrado.');
            $this->redirect('/admin/sales');
            return;
        }

        $data = [
            'title'      => 'Nuevo Contrato - Aventuras Travel',
            'grupo'      => $grupo,
            'csrf_token' => $this->generateCsrfToken(),
        ];
        $this->render('admin/contracts/create', $data, 'admin');
    }

    public function edit(string $id): void {
        $db = Database::getInstance();
        $contrato = (new Contrato())->find((int)$id);
        if (!$contrato) {
            $this->flash('error', 'Contrato no encontrado.');
            $this->redirect('/admin/contracts');
            return;
        }
        $data = [
            'title' => 'Editar Contrato - ' . $contrato['codigo'],
            'contrato' => $contrato,
            'csrf_token' => $this->generateCsrfToken()
        ];
        $this->render('admin/contracts/edit', $data, 'admin');
    }

    public function update(string $id): void {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/contracts/' . $id);
            return;
        }

        $db = Database::getInstance();
        $contrato = (new Contrato())->find((int)$id);
        if (!$contrato) {
            $this->flash('error', 'Contrato no encontrado.');
            $this->redirect('/admin/contracts');
            return;
        }

        $data = [
            'codigo' => $this->input('codigo') ?: $contrato['codigo'],
            'destino' => $this->input('destino') ?: $contrato['destino'],
            'fecha_salida' => $this->input('fecha_salida') ?: $contrato['fecha_salida'],
            'fecha_retorno' => $this->input('fecha_retorno') ?: $contrato['fecha_retorno'],
            'valor_total' => (float)$this->input('valor_total', $contrato['valor_total'] ?? 0),
            'deposito' => (float)$this->input('deposito', $contrato['deposito'] ?? 0),
            'saldo' => ((float)$this->input('valor_total', $contrato['valor_total'] ?? 0) - (float)$this->input('deposito', $contrato['deposito'] ?? 0)),
            'estado' => $this->input('estado', $contrato['estado'] ?? 'activo')
        ];

        // Filtrar solo columnas existentes
        $cols = $db->fetchAll('SHOW COLUMNS FROM contratos');
        $colNames = array_map(fn($c) => $c['Field'], $cols ?: []);
        $filtered = array_intersect_key($data, array_flip($colNames));

        try {
            $db->update('contratos', $filtered, 'id = ?', [$id]);
            $this->flash('exito', 'Contrato actualizado correctamente.');
        } catch (Exception $e) {
            $this->flash('error', 'Error actualizando contrato: ' . $e->getMessage());
        }
        $this->redirect('/admin/contracts/' . $id);
    }

    public function store(int $id): void {
        $grupoId = $id;
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Token inválido.');
            $this->redirect('/admin/sales/' . $grupoId);
            return;
        }

        $db = Database::getInstance();
        $grupo = (new Grupo())->find($grupoId);

        try {
            $db->beginTransaction();

            // Generar código o usar el ingresado
            $codigo = $this->input('codigo');
            if (empty($codigo)) {
                $year = date('Y');
                $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $grupo['nombre']), 0, 4));
                $lastId = $db->fetchOne("SELECT MAX(id) as max_id FROM contratos");
                $nextId = ($lastId['max_id'] ?? 0) + 1;
                // Generar código garantizando unicidad
                do {
                    $codigo = "{$prefix}-{$year}-" . str_pad($nextId, 3, '0', STR_PAD_LEFT);
                    $exists = $db->fetchOne("SELECT id FROM contratos WHERE codigo = ?", [$codigo]);
                    if ($exists) $nextId++;
                } while ($exists);
            }

            // Preferir valores enviados en el formulario, si faltan usar valores heredados del grupo
            $valorTotal = (float) $this->input('valor_total', $grupo['valor_total'] ?? 0);
            $depositoContrato = (float) $this->input('deposito', $grupo['deposito'] ?? 0);
            
            // Titular Info
            $fechaFirma = $this->input('fecha_firma', date('Y-m-d'));
            $titularNombre = $this->input('titular_nombre');
            $titularDoc = $this->input('titular_documento');
            $titularTel = $this->input('titular_telefono');
            $titularCorreo = $this->input('titular_correo');
            $titularDir = $this->input('titular_direccion');
            $totalCuotas = (int) $this->input('total_cuotas', 0);
            $mesesPago = $this->input('meses_pago', '');

            // Preparar datos para insertar en contratos, pero sólo incluir columnas existentes en la tabla
            $contratoData = [
                'codigo'            => $codigo,
                'grupo_id'          => $grupoId,
                'tipo'              => 'colegio',
                'destino'           => $this->input('destino') ?: $grupo['destino'],
                'destino_code'      => $this->input('destino_code') ?: ($grupo['destino_code'] ?? null),
                'fecha_salida'      => $grupo['fecha_viaje'],
                'fecha_retorno'     => $grupo['fecha_retorno'],
                'valor_total'       => $valorTotal,
                'deposito'          => $depositoContrato,
                'saldo'             => $valorTotal - $depositoContrato,
                'estado'            => 'activo',
                'fecha_firma'       => $fechaFirma,
                'titular_nombre'    => $titularNombre ?: null,
                'titular_documento' => $titularDoc ?: null,
                'titular_telefono'  => $titularTel ?: null,
                'titular_correo'    => $titularCorreo ?: null,
                'titular_direccion' => $titularDir ?: null,
                'total_cuotas'      => $totalCuotas,
                'meses_pago'        => $mesesPago ?: null,
                'tipo_pago'         => $this->input('tipo_pago', 'contado'),
            ];

            // Obtener columnas existentes en la tabla contratos
            $cols = $db->fetchAll("SHOW COLUMNS FROM contratos");
            $colNames = array_map(fn($c) => $c['Field'], $cols ?: []);

            $filtered = array_intersect_key($contratoData, array_flip($colNames));

            // Insertar sólo con las columnas existentes
            $contratoId = $db->insert('contratos', $filtered);

            // Save deposit if any
            if ($depositoContrato > 0) {
                // Pago aprobado si el admin así lo marca? Por defecto pendiente
                $db->insert('pagos', [
                    'contrato_id'       => $contratoId,
                    'grupo_id'          => $grupoId,
                    'entidad_tipo'      => 'contrato',
                    'concepto'          => 'Depósito Inicial - ' . $codigo,
                    'monto'             => $depositoContrato,
                    'cuota_numero'      => 0,
                    'fecha_vencimiento' => date('Y-m-d'),
                    'estado'            => 'pendiente',
                ]);
            }

            // Save Cuotas (Plan Cuotas)
            $cuotas = json_decode($this->input('cuotas_json', '[]'), true);
            foreach ($cuotas as $cuota) {
                $db->insert('plan_cuotas', [
                    'contrato_id'       => $contratoId,
                    'numero_cuota'      => $cuota['numero'],
                    'monto'             => $cuota['monto'],
                    'fecha_vencimiento' => $cuota['fecha'],
                    'estado'            => 'pendiente',
                ]);
            }

            // Save Passengers
            $pasajeros = json_decode($this->input('pasajeros_json', '[]'), true);
            $insertedCount = 0;
            foreach ($pasajeros as $pax) {
                $db->insert('pasajeros', [
                    'nombre'      => $pax['nombre'] ?? '',
                    'apellido'    => $pax['apellido'] ?? '',
                    'tipo'        => $pax['tipo'] ?? 'adulto',
                    'edad'        => $pax['edad'] ?: null,
                    'pasaporte'   => $pax['pasaporte'] ?: null,
                    'grupo_id'    => $grupoId,
                    'contrato_id' => $contratoId,
                ]);
                $insertedCount++;
            }

            // Actualizar contador de pasajeros en el grupo (suma acumulada)
            if ($insertedCount > 0) {
                try {
                    $cur = $db->fetchOne('SELECT cantidad_pasajeros FROM grupos WHERE id = ?', [$grupoId]);
                    $curCount = (int) ($cur['cantidad_pasajeros'] ?? 0);
                    $db->update('grupos', ['cantidad_pasajeros' => $curCount + $insertedCount], 'id = ?', [$grupoId]);
                } catch (Exception $ue) {
                    error_log('ContractController::store - no se pudo actualizar cantidad_pasajeros: ' . $ue->getMessage());
                }
            }

            // Copiar servicios del grupo al contrato (si existen)
            try {
                $svcModel = new ServicioGrupo();
                $groupSvcs = $svcModel->getByGrupo($grupoId);
                if (!empty($groupSvcs)) {
                    foreach ($groupSvcs as $gsvc) {
                        $detalle = json_decode($gsvc['detalle_json'] ?? '{}', true) ?: [];
                        $tipo = $gsvc['servicio_tipo'] ?? 'otro';

                        // Mapear tipo a enum de tabla `servicios`
                        switch ($tipo) {
                            case 'hotel': $mapTipo = 'hotel'; break;
                            case 'excursiones': $mapTipo = 'tour'; break;
                            case 'traslados': $mapTipo = 'transfer'; break;
                            case 'seguro': $mapTipo = 'seguro'; break;
                            case 'vuelos': $mapTipo = 'otro'; break;
                            default: $mapTipo = 'otro'; break;
                        }

                        // Nombre descriptivo
                        $nombre = '';
                        if (isset($detalle['hoteles']) && is_array($detalle['hoteles']) && !empty($detalle['hoteles'][0]['nombre'])) {
                            $nombre = $detalle['hoteles'][0]['nombre'];
                        } elseif (isset($detalle['vuelos']) && is_array($detalle['vuelos']) && !empty($detalle['vuelos'][0]['ruta'])) {
                            $nombre = 'Vuelos - ' . substr($detalle['vuelos'][0]['ruta'], 0, 50);
                        } elseif (!empty($detalle)) {
                            $nombre = ucfirst($tipo);
                        } else {
                            $nombre = ucfirst($tipo);
                        }

                        // Insertar servicio general en `servicios`
                        $db->insert('servicios', [
                            'contrato_id' => $contratoId,
                            'tipo' => $mapTipo,
                            'nombre' => substr($nombre, 0, 200),
                            'descripcion' => null,
                            'fecha_inicio' => null,
                            'fecha_fin' => null,
                            'precio' => 0.00,
                            'estado' => 'pendiente',
                            'detalles_json' => json_encode($detalle, JSON_UNESCAPED_UNICODE),
                        ]);

                        // Si el servicio contiene vuelos, copiar cada tramo a tabla `vuelos`
                        if (isset($detalle['vuelos']) && is_array($detalle['vuelos'])) {
                            foreach ($detalle['vuelos'] as $v) {
                                $aerolinea = $v['aerolinea'] ?? ($v['airline'] ?? null);
                                $numero = $v['numero'] ?? ($v['numero_vuelo'] ?? null);
                                $ruta = $v['ruta'] ?? '';
                                $salida = $v['salida'] ?? ($v['salida_iso'] ?? null);
                                $llegada = $v['llegada'] ?? ($v['llegada_iso'] ?? null);

                                // Normalizar fechas (reemplazar 'T' por espacio, añadir :00 si falta)
                                $formatDate = function($d) {
                                    if (!$d) return null;
                                    $d = str_replace('T', ' ', $d);
                                    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $d)) {
                                        $d .= ':00';
                                    }
                                    return $d;
                                };

                                $fecha_salida = $formatDate($salida);
                                $fecha_llegada = $formatDate($llegada);

                                // Intentar extraer origen/destino desde ruta "ORIG - DEST"
                                $origen = null; $destino = null;
                                if ($ruta && strpos($ruta, '-') !== false) {
                                    $parts = array_map('trim', explode('-', $ruta));
                                    $origen = $parts[0] ?? null;
                                    $destino = $parts[1] ?? null;
                                }

                                $db->insert('vuelos', [
                                    'contrato_id' => $contratoId,
                                    'aerolinea' => $aerolinea ?: 'N/A',
                                    'numero_vuelo' => $numero ?: 'N/A',
                                    'origen' => $origen ?: ($v['origen'] ?? 'N/A'),
                                    'origen_ciudad' => $v['origen_ciudad'] ?? null,
                                    'destino' => $destino ?: ($v['destino'] ?? 'N/A'),
                                    'destino_ciudad' => $v['destino_ciudad'] ?? null,
                                    'fecha_salida' => $fecha_salida ?: date('Y-m-d H:i:s'),
                                    'fecha_llegada' => $fecha_llegada ?: date('Y-m-d H:i:s'),
                                    'tipo' => 'ida',
                                    'clase' => 'economica',
                                    'avion' => $v['avion'] ?? null,
                                    'estado' => 'pendiente',
                                ]);
                            }
                        }
                    }
                }
            } catch (Exception $ee) {
                // No detener la creación del contrato si falla la copia de servicios
                error_log('ContractController::store - error copiando servicios del grupo: ' . $ee->getMessage());
            }

            // AUTO CREATE CLIENT USER & SEND EMAIL
            if (!empty($titularCorreo)) {
                // Comprobar si columna email existe. Proteger contra esquemas faltantes o errores.
                $userExists = null;
                try {
                    $uCols = $db->fetchAll("SHOW COLUMNS FROM usuarios");
                    $uColsNames = array_map(fn($c) => $c['Field'], $uCols ?: []);
                    if (in_array('email', $uColsNames)) {
                        $userExists = $db->fetchOne("SELECT id FROM usuarios WHERE email = ?", [$titularCorreo]);
                    }
                } catch (Exception $ex) {
                    error_log('[ContractController::store] comprobacion email tabla usuarios falló: ' . $ex->getMessage());
                    $userExists = null;
                }

                if (!$userExists) {
                    $randomPass = bin2hex(random_bytes(6)); // 12 chars hex, criptográficamente seguro
                    
                    $partesNombre = explode(' ', $titularNombre ?: 'Cliente', 2);
                    $nombreUsr = $partesNombre[0];
                    $apellidoUsr = $partesNombre[1] ?? '';

                    $userData = [
                        'nombre'   => $nombreUsr,
                        'apellido' => $apellidoUsr,
                        'email'    => $titularCorreo,
                        'password' => password_hash($randomPass, PASSWORD_DEFAULT),
                        'codigo'   => $codigo, // Login code is the contract code
                        'rol'      => 'cliente_colegio',
                        'activo'   => 1
                    ];
                    $userCols = $db->fetchAll("SHOW COLUMNS FROM usuarios");
                    $userColsNames = array_map(fn($c) => $c['Field'], $userCols ?: []);
                    $userFiltered = array_intersect_key($userData, array_flip($userColsNames));
                    $userId = $db->insert('usuarios', $userFiltered);

                    $clienteId = $db->insert('clientes', [
                        'usuario_id' => $userId,
                        'telefono'   => $titularTel ?: null,
                        'direccion'  => $titularDir ?: null
                    ]);

                    // Vincular el contrato al nuevo cliente
                    $db->update('contratos', ['cliente_id' => $clienteId], 'id = ?', [$contratoId]);

                    require_once __DIR__ . '/../services/EmailService.php';
                    $emailSvc = new EmailService();
                    $emailSvc->sendCredentials($titularCorreo, $titularNombre ?: 'Cliente', $codigo, $randomPass);
                } else {
                    // Si el usuario existe, vinculamos este contrato a su cliente_id existente si es posible
                    $clienteRow = $db->fetchOne("SELECT id FROM clientes WHERE usuario_id = ?", [$userExists['id']]);
                    if ($clienteRow) {
                        $db->update('contratos', ['cliente_id' => $clienteRow['id']], 'id = ?', [$contratoId]);
                    }
                }
            }

            $db->commit();
            $this->flash('exito', "Contrato {$codigo} creado detalladamente.");
            $this->redirect('/admin/sales/' . $grupoId);

        } catch (Exception $e) {
            $db->rollBack();
            error_log('Error creando contrato: ' . $e->getMessage());
            $this->flash('error', 'Error creando el contrato: ' . $e->getMessage());
            $this->redirect('/admin/sales/' . $grupoId . '/contract/create');
        }
    }
}
