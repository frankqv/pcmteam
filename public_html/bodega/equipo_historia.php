<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array((int) $_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('Location: ../error404.php');
    exit;
}
require_once '../../config/ctconex.php'; // Debe definir $connect (PDO)

// Obtener ID del inventario (equipo) de la URL
$inventario_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($inventario_id <= 0) {
    echo "<p>ID de equipo inválido.</p>";
    exit;
}

// Consulta unificada (UNION) para recoger todos los eventos relevantes
// Nota: los JSON_OBJECT incluyen campo 'meta' para facilitar extracción de lote/proveedor si existen
$sql = "
(SELECT
  'entrada' AS tipo,
  fecha_entrada AS fecha_evento,
  usuario_id AS actor_id,
  NULL AS tecnico_id,
  observaciones AS descripcion,
  CONCAT('Entrada ID=', id) AS referencia,
  -- Intentamos incluir lote/proveedor si existen en la tabla
  JSON_OBJECT('tabla','bodega_entradas','id',id,
    'lote', IFNULL(lote, ''),
    'proveedor_id', IFNULL(proveedor_id, ''),
    'proveedor', IFNULL(proveedor, '')) AS meta
 FROM bodega_entradas
 WHERE inventario_id = :id)

UNION ALL

(SELECT
  'diagnostico' AS tipo,
  fecha_diagnostico AS fecha_evento,
  NULL AS actor_id,
  tecnico_id AS tecnico_id,
  CONCAT('Estado:', estado_reparacion, ' | Obs: ', IFNULL(observaciones, '')) AS descripcion,
  CONCAT('Diagnóstico ID=', id) AS referencia,
  JSON_OBJECT('tabla','bodega_diagnosticos','id',id,
    'lote', IFNULL(lote, ''),
    'proveedor_id', IFNULL(proveedor_id, ''),
    'proveedor', IFNULL(proveedor, '')) AS meta
 FROM bodega_diagnosticos
 WHERE inventario_id = :id)

UNION ALL

(SELECT
  'electrico' AS tipo,
  fecha_proceso AS fecha_evento,
  NULL AS actor_id,
  tecnico_id AS tecnico_id,
  CONCAT('Fallas: ', IFNULL(fallas_detectadas, ''), ' | Reparaciones: ', IFNULL(reparaciones_realizadas, '')) AS descripcion,
  CONCAT('Eléctrico ID=', id) AS referencia,
  JSON_OBJECT('tabla','bodega_electrico','id',id,
    'lote', IFNULL(lote, ''),
    'proveedor_id', IFNULL(proveedor_id, ''),
    'proveedor', IFNULL(proveedor, '')) AS meta
 FROM bodega_electrico
 WHERE inventario_id = :id)

UNION ALL

(SELECT
  'estetico' AS tipo,
  fecha_proceso AS fecha_evento,
  NULL AS actor_id,
  tecnico_id AS tecnico_id,
  CONCAT('Grado: ', IFNULL(grado_asignado,''), ' | Partes: ', IFNULL(partes_reemplazadas,''), ' | Obs: ', IFNULL(observaciones,'')) AS descripcion,
  CONCAT('Estético ID=', id) AS referencia,
  JSON_OBJECT('tabla','bodega_estetico','id',id,
    'lote', IFNULL(lote, ''),
    'proveedor_id', IFNULL(proveedor_id, ''),
    'proveedor', IFNULL(proveedor, '')) AS meta
 FROM bodega_estetico
 WHERE inventario_id = :id)

UNION ALL

(SELECT
  'control_calidad' AS tipo,
  fecha_control AS fecha_evento,
  NULL AS actor_id,
  tecnico_id AS tecnico_id,
  CONCAT('Estado final: ', IFNULL(estado_final,''), ' | Cat: ', IFNULL(categoria_rec,''), ' | Obs: ', IFNULL(observaciones,'')) AS descripcion,
  CONCAT('ControlQA ID=', id) AS referencia,
  JSON_OBJECT('tabla','bodega_control_calidad','id',id,
    'lote', IFNULL(lote, ''),
    'proveedor_id', IFNULL(proveedor_id, ''),
    'proveedor', IFNULL(proveedor, '')) AS meta
 FROM bodega_control_calidad
 WHERE inventario_id = :id)

UNION ALL

(SELECT
  'cambio' AS tipo,
  fecha_cambio AS fecha_evento,
  usuario_id AS actor_id,
  NULL AS tecnico_id,
  CONCAT('Campo: ', IFNULL(campo_modificado,''), ' | De: ', IFNULL(valor_anterior,''), ' | A: ', IFNULL(valor_nuevo,'')) AS descripcion,
  CONCAT('Log ID=', id) AS referencia,
  JSON_OBJECT('tabla','bodega_log_cambios','id',id,
    'lote', IFNULL(lote, ''),
    'proveedor_id', IFNULL(proveedor_id, ''),
    'proveedor', IFNULL(proveedor, '')) AS meta
 FROM bodega_log_cambios
 WHERE inventario_id = :id)

ORDER BY fecha_evento DESC, tipo DESC
";

try {
    $stmt = $connect->prepare($sql);
    $stmt->bindValue(':id', $inventario_id, PDO::PARAM_INT);
    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Error al obtener historial: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Función mejorada para resolver información del actor/técnico (nombre, email, teléfono, tipo)
function resolverActorFull(PDO $pdo, $id) {
    static $cache = [];
    if (!$id) return null;
    if (isset($cache[$id])) return $cache[$id];

    // Tablas y columnas candidatas para buscar información del usuario/cliente/técnico
    $tables = [
        ['table'=>'clientes','idcols'=>['idclie','id','user_id'],'namecols'=>['nomcli','nombres','nombre'],'lastnamecols'=>['apecli','apellidos','apellido'],'emailcols'=>['email','correo'],'phonecols'=>['celu','telefono','telefono_movil','phone']],
        ['table'=>'usuarios','idcols'=>['id','user_id','usuario_id'],'namecols'=>['nombre','nombres','first_name'],'lastnamecols'=>['apellido','apellidos','last_name'],'emailcols'=>['email','mail'],'phonecols'=>['telefono','celular','phone']],
        ['table'=>'users','idcols'=>['id','user_id'],'namecols'=>['name','username','first_name'],'lastnamecols'=>['last_name'],'emailcols'=>['email'],'phonecols'=>['phone','telefono']],
        ['table'=>'empleados','idcols'=>['id'],'namecols'=>['nombres'],'lastnamecols'=>['apellidos'],'emailcols'=>['email'],'phonecols'=>['telefono']],
        ['table'=>'tecnicos','idcols'=>['id'],'namecols'=>['nombres'],'lastnamecols'=>['apellidos'],'emailcols'=>['email'],'phonecols'=>['telefono']]
    ];

    foreach ($tables as $t) {
        // verificar existencia de tabla
        $q = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table");
        $q->execute([':table' => $t['table']]);
        if ($q->fetchColumn() == 0) continue;

        // intentar por cada columna idcandidate
        foreach ($t['idcols'] as $idcol) {
            // verificar columna
            $q2 = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :col");
            $q2->execute([':table'=>$t['table'], ':col'=>$idcol]);
            if ($q2->fetchColumn() == 0) continue;

            // construir select dinámico con columnas disponibles
            $colsToTry = array_merge($t['namecols'], $t['lastnamecols'], $t['emailcols'], $t['phonecols']);
            $foundCols = [];
            foreach ($colsToTry as $c) {
                $q3 = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :col");
                $q3->execute([':table'=>$t['table'], ':col'=>$c]);
                if ($q3->fetchColumn() > 0) $foundCols[] = $c;
            }
            if (empty($foundCols)) continue;

            $selectParts = [];
            foreach ($foundCols as $c) {
                $selectParts[] = "IFNULL(`$c`,'') AS `$c`";
            }
            $selectSQL = implode(',', $selectParts);
            try {
                $s = $pdo->prepare("SELECT $selectSQL FROM `{$t['table']}` WHERE `$idcol` = :id LIMIT 1");
                $s->execute([':id'=>$id]);
                $row = $s->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    // armar nombre completo
                    $nameParts = [];
                    foreach ($t['namecols'] as $nc) if (!empty($row[$nc])) $nameParts[] = $row[$nc];
                    foreach ($t['lastnamecols'] as $lc) if (!empty($row[$lc])) $nameParts[] = $row[$lc];
                    $fullname = trim(implode(' ', $nameParts));
                    $result = [
                        'id'=>$id,
                        'tabla'=>$t['table'],
                        'nombre'=> $fullname ?: ('Usuario #'.$id),
                        'email'=> (isset($row[$t['emailcols'][0]])? $row[$t['emailcols'][0]] : null),
                        'telefono'=> (isset($row[$t['phonecols'][0]])? $row[$t['phonecols'][0]] : null),
                    ];
                    $cache[$id] = $result;
                    return $result;
                }
            } catch (Exception $e) {
                // ignorar y continuar
            }
        }
    }

    // fallback: devolver solo id
    $cache[$id] = ['id'=>$id,'tabla'=>null,'nombre'=>'Usuario #'.$id,'email'=>null,'telefono'=>null];
    return $cache[$id];
}

// Resolver proveedor (similar a resolverActorFull)
function resolverProveedorFull(PDO $pdo, $idOrName) {
    static $cache = [];
    if (!$idOrName) return null;
    // Si es numérico, tratar como id, si no, como nombre
    $cacheKey = is_numeric($idOrName) ? 'id_'.$idOrName : 'name_'.md5($idOrName);
    if (isset($cache[$cacheKey])) return $cache[$cacheKey];

    $candidates = [
        ['table'=>'proveedores','idcols'=>['id','proveedor_id'],'namecols'=>['nombre','razon_social','nombre_proveedor'],'emailcols'=>['email','correo'],'phonecols'=>['telefono','celular','telefono']],
        ['table'=>'proveedor','idcols'=>['id'],'namecols'=>['nombre','razon_social'],'emailcols'=>['email'],'phonecols'=>['telefono']],
        ['table'=>'suppliers','idcols'=>['id','supplier_id'],'namecols'=>['name','company'],'emailcols'=>['email'],'phonecols'=>['phone']]
    ];

    foreach ($candidates as $t) {
        // verificar existencia de tabla
        $q = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table");
        $q->execute([':table' => $t['table']]);
        if ($q->fetchColumn() == 0) continue;

        // si es id numeric probar columnas idcols
        if (is_numeric($idOrName)) {
            foreach ($t['idcols'] as $idcol) {
                $q2 = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :col");
                $q2->execute([':table'=>$t['table'], ':col'=>$idcol]);
                if ($q2->fetchColumn() == 0) continue;

                $colsToTry = array_merge($t['namecols'], $t['emailcols'], $t['phonecols']);
                $foundCols = [];
                foreach ($colsToTry as $c) {
                    $q3 = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :col");
                    $q3->execute([':table'=>$t['table'], ':col'=>$c]);
                    if ($q3->fetchColumn() > 0) $foundCols[] = $c;
                }
                if (empty($foundCols)) continue;

                $selectParts = [];
                foreach ($foundCols as $c) $selectParts[] = "IFNULL(`$c`,'') AS `$c`";
                $selectSQL = implode(',', $selectParts);
                try {
                    $s = $pdo->prepare("SELECT $selectSQL FROM `{$t['table']}` WHERE `$idcol` = :id LIMIT 1");
                    $s->execute([':id'=>$idOrName]);
                    $row = $s->fetch(PDO::FETCH_ASSOC);
                    if ($row) {
                        $name = null;
                        foreach ($t['namecols'] as $nc) if (!empty($row[$nc])) { $name = $row[$nc]; break; }
                        $result = ['id'=>$idOrName,'tabla'=>$t['table'],'nombre'=>$name ?: ('Proveedor #'.$idOrName),'email'=>($row[$t['emailcols'][0]] ?? null),'telefono'=>($row[$t['phonecols'][0]] ?? null)];
                        $cache[$cacheKey] = $result; return $result;
                    }
                } catch (Exception $e) {}
            }
        } else {
            // buscar por nombre parcial en las columnas namecols
            foreach ($t['namecols'] as $nc) {
                $q4 = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :col");
                $q4->execute([':table'=>$t['table'], ':col'=>$nc]);
                if ($q4->fetchColumn() == 0) continue;

                try {
                    $s = $pdo->prepare("SELECT * FROM `{$t['table']}` WHERE `$nc` LIKE :name LIMIT 1");
                    $s->execute([':name'=>'%'.substr($idOrName,0,200).'%']);
                    $row = $s->fetch(PDO::FETCH_ASSOC);
                    if ($row) {
                        $name = $row[$nc];
                        $result = ['id'=>($row['id'] ?? null),'tabla'=>$t['table'],'nombre'=>$name,'email'=>($row[$t['emailcols'][0]] ?? null),'telefono'=>($row[$t['phonecols'][0]] ?? null)];
                        $cache[$cacheKey] = $result; return $result;
                    }
                } catch (Exception $e) {}
            }
        }
    }

    $cache[$cacheKey] = ['id'=>$idOrName,'tabla'=>null,'nombre'=>is_numeric($idOrName)? 'Proveedor #'.$idOrName : $idOrName,'email'=>null,'telefono'=>null];
    return $cache[$cacheKey];
}

// Cargar datos del equipo (seleccionamos todo para detectar columnas como configuracion/lote/proveedor)
$equip = [];
try {
    $q = $connect->prepare('SELECT * FROM bodega_inventario WHERE id = :id');
    $q->execute([':id' => $inventario_id]);
    $equip = $q->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // continuar sin datos
}

// Extraer datos útiles de equip si existen
function pickEquipField(array $equip, array $candidates) {
    foreach ($candidates as $c) if (isset($equip[$c]) && $equip[$c] !== '') return $equip[$c];
    return null;
}

$configuracion = pickEquipField($equip, ['configuracion','config','specs','especificaciones','config_json']);
$lote_equipo = pickEquipField($equip, ['lote','batch','lote_num','batch_no','lot']);
$proveedor_ref = pickEquipField($equip, ['proveedor_id','id_proveedor','proveedor','supplier_id','supplier']);

$proveedorInfoEquip = null;
if ($proveedor_ref) {
    $proveedorInfoEquip = resolverProveedorFull($connect, $proveedor_ref);
}

// Resumen estadístico sencillo
$totales = ['total'=>count($eventos)];
$porTipo = [];
foreach ($eventos as $ev) {
    $porTipo[$ev['tipo']] = ($porTipo[$ev['tipo']] ?? 0) + 1;
}

// Pre-resolver actores y proveedores encontrados en eventos (para no hacer consultas repetidas en modal)
$preResolved = [];
$preSuppliers = [];
foreach ($eventos as $ev) {
    if (!empty($ev['actor_id'])) $preResolved[$ev['actor_id']] = resolverActorFull($connect, $ev['actor_id']);
    if (!empty($ev['tecnico_id'])) $preResolved[$ev['tecnico_id']] = resolverActorFull($connect, $ev['tecnico_id']);
    // intentar parsear meta JSON para buscar proveedor_id o proveedor
    $meta = null;
    if (!empty($ev['meta'])) {
        $decoded = json_decode($ev['meta'], true);
        if (is_array($decoded)) {
            if (!empty($decoded['proveedor_id'])) $preSuppliers[$decoded['proveedor_id']] = resolverProveedorFull($connect, $decoded['proveedor_id']);
            if (!empty($decoded['proveedor'])) $preSuppliers['name_'.md5($decoded['proveedor'])] = resolverProveedorFull($connect, $decoded['proveedor']);
            if (!empty($decoded['lote']) && empty($lote_equipo)) $lote_equipo = $decoded['lote'];
        }
    }
}
if ($proveedorInfoEquip) $preSuppliers[$proveedorInfoEquip['id'] ?? 'equip_ref'] = $proveedorInfoEquip;

?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Historial de Trazabilidad - Equipo <?= htmlspecialchars($equip['codigo_g'] ?? $inventario_id) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.timeline { position: relative; padding: 1rem 0; }
.timeline::before { content: ''; position: absolute; left: 20px; width: 4px; top: 0; bottom: 0; background: #e9ecef; }
.timeline-item { position: relative; margin-left: 60px; margin-bottom: 1.25rem; }
.timeline-item .time { font-size: .9rem; color: #6c757d; }
.timeline-item .badge-type { font-size:.7rem; }
.card-small { box-shadow: 0 1px 2px rgba(0,0,0,.05); }
.event-meta { font-size: .85rem; color: #495057; }
@media (max-width:576px){ .timeline::before{ left:10px } .timeline-item{ margin-left:48px} }
</style>
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div>
      <h3>Historial de Trazabilidad</h3>
      <p class="mb-0">Equipo: <strong><?= htmlspecialchars($equip['codigo_g'] ?? 'ID '.$inventario_id) ?></strong> — <?= htmlspecialchars(trim(($equip['producto'] ?? '') . ' ' . ($equip['marca'] ?? ''))) ?></p>
      <small class="text-muted">Serial: <?= htmlspecialchars($equip['serial'] ?? '') ?> · Modelo: <?= htmlspecialchars($equip['modelo'] ?? '') ?></small>
      <div class="mt-2 small">
        <strong>Total eventos:</strong> <?= $totales['total'] ?> &nbsp; • &nbsp; <strong>Última actualización:</strong> <?= $eventos ? htmlspecialchars(date('Y-m-d H:i:s', strtotime($eventos[0]['fecha_evento']))) : 'N/A' ?>
      </div>
      <div class="mt-2">
        <?php foreach ($porTipo as $t => $c): ?>
          <span class="badge bg-secondary me-1"><?= htmlspecialchars($t) ?>: <?= $c ?></span>
        <?php endforeach; ?>
      </div>

      <div class="mt-2">
        <?php if ($configuracion): ?>
          <div><strong>Configuración:</strong> <?= htmlspecialchars($configuracion) ?></div>
        <?php endif; ?>
        <?php if ($lote_equipo): ?>
          <div><strong>Lote:</strong> <?= htmlspecialchars($lote_equipo) ?></div>
        <?php endif; ?>
        <?php if ($proveedorInfoEquip): ?>
          <div><strong>Proveedor:</strong> <?= htmlspecialchars($proveedorInfoEquip['nombre']) ?> <?= $proveedorInfoEquip['email'] ? '· ' . htmlspecialchars($proveedorInfoEquip['email']) : '' ?> <?= $proveedorInfoEquip['telefono'] ? '· ' . htmlspecialchars($proveedorInfoEquip['telefono']) : '' ?></div>
        <?php endif; ?>
      </div>

    </div>
    <div class="text-end">
      <a href="lista_triage_2.php" class="btn btn-outline-secondary btn-sm">← Volver al listado</a>
      <button onclick="window.print()" class="btn btn-primary btn-sm ms-2">Imprimir historial</button>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="timeline">
        <?php if (empty($eventos)): ?>
          <div class="alert alert-info">No se encontraron eventos para este equipo.</div>
        <?php else: ?>
          <?php foreach ($eventos as $idx => $ev):
              // resolver nombre de actor/técnico cuando aplique
              $actorInfo = null;
              if (!empty($ev['actor_id'])) $actorInfo = resolverActorFull($connect, $ev['actor_id']);
              $tecnicoInfo = null;
              if (!empty($ev['tecnico_id'])) $tecnicoInfo = resolverActorFull($connect, $ev['tecnico_id']);
              $fecha = date('Y-m-d H:i:s', strtotime($ev['fecha_evento']));
              $shortDesc = mb_strimwidth($ev['descripcion'], 0, 220, '...');

              // intentar extraer lote/proveedor desde meta JSON
              $meta = json_decode($ev['meta'], true);
              $ev_lote = $meta['lote'] ?? null;
              $ev_prov_ref = $meta['proveedor_id'] ?? ($meta['proveedor'] ?? null);
              $ev_prov = null;
              if ($ev_prov_ref) $ev_prov = resolverProveedorFull($connect, $ev_prov_ref);
          ?>
            <div class="timeline-item">
              <div class="card card-small">
                <div class="card-body p-2">
                  <div class="d-flex justify-content-between align-items-start">
                    <div style="flex:1">
                      <h6 class="mb-1">
                        <?= ucfirst(htmlspecialchars(str_replace('_',' ',$ev['tipo']))) ?>
                        <span class="badge bg-secondary badge-type"><?= htmlspecialchars($ev['referencia']) ?></span>
                      </h6>

                      <p class="mb-1 small text-muted time"><?= htmlspecialchars($fecha) ?>
                        <?php if($tecnicoInfo): ?>
                          · Técnico: <strong><?= htmlspecialchars($tecnicoInfo['nombre']) ?></strong>
                        <?php elseif($actorInfo): ?>
                          · Usuario: <strong><?= htmlspecialchars($actorInfo['nombre']) ?></strong>
                        <?php endif; ?>
                      </p>

                      <p class="mb-1 event-meta">
                        <?= nl2br(htmlspecialchars($shortDesc)) ?>
                      </p>

                      <div>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEvent" data-idx="<?= $idx ?>">Ver detalles</button>
                        <?php if($actorInfo && ($actorInfo['email'] || $actorInfo['telefono'])): ?>
                          <small class="text-muted ms-2">Contacto: <?= htmlspecialchars($actorInfo['email'] ?? $actorInfo['telefono']) ?></small>
                        <?php endif; ?>
                        <?php if($ev_lote): ?>
                          <small class="text-muted ms-2">Lote: <?= htmlspecialchars($ev_lote) ?></small>
                        <?php endif; ?>
                        <?php if($ev_prov): ?>
                          <small class="text-muted ms-2">Proveedor: <?= htmlspecialchars($ev_prov['nombre']) ?></small>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <hr>
  <p class="small text-muted">Generado: <?= date('Y-m-d H:i:s') ?> — Trazabilidad combinada desde tablas: entradas, diagnósticos, eléctrico, estético, control de calidad y log de cambios.</p>
</div>

<!-- Modal detalles -->
<div class="modal fade" id="modalEvent" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle del evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="modalBodyContent">Cargando...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Pasar datos de PHP a JS de forma segura (solo lo necesario)
  const EVENTOS = <?= json_encode(array_values($eventos), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
  const ACTORES = {};
  const PROVEEDORES = {};
  <?php
  echo 'const PRE_ACTORES = ' . json_encode(array_values($preResolved), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . ';';
  echo 'const PRE_PROVEEDORES = ' . json_encode(array_values($preSuppliers), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . ';';
  ?>
  PRE_ACTORES.forEach(a => { if(a && a.id) ACTORES[a.id] = a; });
  PRE_PROVEEDORES.forEach(p => { if(p && (p.id || p.nombre)) PROVEEDORES[p.id ?? p.nombre] = p; });

  const modal = document.getElementById('modalEvent');
  modal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const idx = button.getAttribute('data-idx');
    const ev = EVENTOS[idx];
    if (!ev) {
      document.getElementById('modalBodyContent').innerHTML = '<p class="text-danger">Evento no encontrado.</p>';
      return;
    }

    const actor = ev.actor_id ? (ACTORES[ev.actor_id] || {id:ev.actor_id,nombre:'Usuario #'+ev.actor_id}) : null;
    const tecnico = ev.tecnico_id ? (ACTORES[ev.tecnico_id] || {id:ev.tecnico_id,nombre:'Técnico #'+ev.tecnico_id}) : null;

    let meta = {};
    try { meta = ev.meta ? JSON.parse(ev.meta) : {}; } catch(e) { meta = {}; }
    let provRef = meta.proveedor_id || meta.proveedor || null;
    let prov = null;
    if (provRef) prov = PROVEEDORES[provRef] || PROVEEDORES['name_' + md5(provRef)] || {id:provRef,nombre:provRef};

    let html = '';
    html += '<dl class="row">';
    html += '<dt class="col-sm-3">Tipo</dt><dd class="col-sm-9">' + htmlspecialchars(ev.tipo) + '</dd>';
    html += '<dt class="col-sm-3">Referencia</dt><dd class="col-sm-9">' + htmlspecialchars(ev.referencia) + '</dd>';
    html += '<dt class="col-sm-3">Fecha</dt><dd class="col-sm-9">' + htmlspecialchars(ev.fecha_evento) + '</dd>';
    if(actor) html += '<dt class="col-sm-3">Usuario</dt><dd class="col-sm-9">' + htmlspecialchars(actor.nombre || ('Usuario #'+actor.id)) + (actor.email? ' · ' + htmlspecialchars(actor.email) : '') + (actor.telefono? ' · ' + htmlspecialchars(actor.telefono) : '') + '</dd>';
    if(tecnico) html += '<dt class="col-sm-3">Técnico</dt><dd class="col-sm-9">' + htmlspecialchars(tecnico.nombre || ('Técnico #'+tecnico.id)) + '</dd>';
    if(meta.lote) html += '<dt class="col-sm-3">Lote</dt><dd class="col-sm-9">' + htmlspecialchars(meta.lote) + '</dd>';
    if(prov) html += '<dt class="col-sm-3">Proveedor</dt><dd class="col-sm-9">' + htmlspecialchars(prov.nombre || prov.id) + (prov.email? ' · ' + htmlspecialchars(prov.email) : '') + (prov.telefono? ' · ' + htmlspecialchars(prov.telefono) : '') + '</dd>';
    html += '<dt class="col-sm-3">Descripción</dt><dd class="col-sm-9"><pre style="white-space:pre-wrap;">' + htmlspecialchars(ev.descripcion) + '</pre></dd>';
    html += '<dt class="col-sm-3">Meta raw</dt><dd class="col-sm-9"><code>' + htmlspecialchars(ev.meta) + '</code></dd>';
    html += '</dl>';

    document.getElementById('modalBodyContent').innerHTML = html;
  });

  // función de escape simple para HTML
  function htmlspecialchars(str) {
    if (!str && str !== 0) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  // función md5 simple (para claves de proveedores por nombre en el map) - usa un hash sencillo
  function md5(s){
    // fallback hashing simple si no se dispone de una librería. No es criptográfico, solo para claves.
    var h=0; for(var i=0;i<s.length;i++){ h = ((h<<5)-h)+s.charCodeAt(i); h |= 0; } return String(h);
  }
</script>
</body>
</html>
