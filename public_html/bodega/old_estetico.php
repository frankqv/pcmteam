<?php
// estetico.php - Formulario de Diagnóstico Estético
ob_start();
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once dirname(__DIR__, 2) . '/config/ctconex.php';

// Validación de roles
$allowedRoles = [1, 2, 5, 6, 7];
if (!isset($_SESSION['rol']) || !in_array((int) $_SESSION['rol'], $allowedRoles, true)) {
  header('Location: ../error404.php');
  exit;
}

// Variables globales
$inventario_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$mensaje = '';
$inventario = null;
$estetico_ultimo = null;
$equipos_pendientes = [];

// Obtener información del usuario para navbar
$userInfo = null;
try {
  if (isset($_SESSION['id'])) {
    $stmt = $connect->prepare("SELECT id, nombre, usuario, correo, foto, idsede FROM usuarios WHERE id = ? LIMIT 1");
    $stmt->execute([$_SESSION['id']]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
  }
} catch (Exception $e) {
  error_log("Error usuario: " . $e->getMessage());
  $userInfo = [
    'nombre' => 'Usuario',
    'usuario' => 'usuario',
    'correo' => 'correo@ejemplo.com',
    'foto' => 'reere.webp',
    'idsede' => 'Sede sin definir'
  ];
}

// Cargar datos iniciales
try {
  if ($inventario_id > 0) {
    // Inventario
    $stmt = $connect->prepare("SELECT * FROM bodega_inventario WHERE id = ? LIMIT 1");
    $stmt->execute([$inventario_id]);
    $inventario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Último diagnóstico estético
    $stmt = $connect->prepare("SELECT * FROM bodega_estetico WHERE inventario_id = ? ORDER BY fecha_proceso DESC LIMIT 1");
    $stmt->execute([$inventario_id]);
    $estetico_ultimo = $stmt->fetch(PDO::FETCH_ASSOC);
  }
  
  // Equipos pendientes de diagnóstico estético
  $stmt = $connect->prepare("
    SELECT bi.*, be.estado_final as estado_electrico
    FROM bodega_inventario bi
    LEFT JOIN bodega_electrico be ON bi.id = be.inventario_id
    WHERE (bi.disposicion = 'pendiente_estetico' OR be.estado_final = 'aprobado')
    AND bi.estado = 'activo'
    AND bi.id NOT IN (
      SELECT DISTINCT inventario_id 
      FROM bodega_estetico 
      WHERE estado_final IN ('aprobado', 'rechazado')
    )
    ORDER BY bi.fecha_ingreso ASC
    LIMIT 50
  ");
  $stmt->execute();
  $equipos_pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
} catch (Exception $e) {
  error_log("Error carga inicial: " . $e->getMessage());
  $mensaje .= "<div class='alert alert-warning'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $connect->beginTransaction();
    
    if (isset($_POST['guardar_estetico']) && $inventario_id > 0) {
      // Validar que el equipo existe
      if (!$inventario) {
        throw new Exception("Equipo no encontrado");
      }
      
      // Insertar diagnóstico estético
      $stmt = $connect->prepare("
        INSERT INTO bodega_estetico 
        (inventario_id, tecnico_id, estado_carcasa, estado_pantalla_fisica, estado_teclado_fisico,
         rayones_golpes, limpieza_realizada, partes_reemplazadas, grado_asignado, 
         estado_final, observaciones)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      ");
      
      $stmt->execute([
        $inventario_id,
        $_SESSION['id'],
        $_POST['estado_carcasa'] ?? '',
        $_POST['estado_pantalla_fisica'] ?? '',
        $_POST['estado_teclado_fisico'] ?? '',
        $_POST['rayones_golpes'] ?? '',
        $_POST['limpieza_realizada'] ?? 'no',
        $_POST['partes_reemplazadas'] ?? '',
        $_POST['grado_asignado'] ?? 'C',
        $_POST['estado_final'] ?? 'requiere_revision',
        $_POST['observaciones'] ?? ''
      ]);
      
      // Actualizar disposición del inventario según resultado
      $nueva_disposicion = '';
      switch ($_POST['estado_final']) {
        case 'aprobado':
          $nueva_disposicion = 'pendiente_control_calidad';
          break;
        case 'rechazado':
          $nueva_disposicion = 'en_revision';
          break;
        case 'requiere_revision':
          $nueva_disposicion = 'pendiente_estetico';
          break;
      }
      
      if ($nueva_disposicion) {
        $stmt = $connect->prepare("
          UPDATE bodega_inventario 
          SET disposicion = ?, fecha_modificacion = NOW()
          WHERE id = ?
        ");
        $stmt->execute([$nueva_disposicion, $inventario_id]);
      }
      
      // Registrar en log de cambios
      $stmt = $connect->prepare("
        INSERT INTO bodega_log_cambios 
        (inventario_id, usuario_id, campo_modificado, valor_anterior, valor_nuevo, tipo_cambio)
        VALUES (?, ?, ?, ?, ?, 'sistema')
      ");
      $stmt->execute([
        $inventario_id,
        $_SESSION['id'],
        'disposicion',
        $inventario['disposicion'] ?? '',
        $nueva_disposicion
      ]);
      
      $mensaje .= "<div class='alert alert-success'>✅ Diagnóstico estético guardado correctamente</div>";
      
      // Recargar datos
      $stmt = $connect->prepare("SELECT * FROM bodega_inventario WHERE id = ? LIMIT 1");
      $stmt->execute([$inventario_id]);
      $inventario = $stmt->fetch(PDO::FETCH_ASSOC);
      
      $stmt = $connect->prepare("SELECT * FROM bodega_estetico WHERE inventario_id = ? ORDER BY fecha_proceso DESC LIMIT 1");
      $stmt->execute([$inventario_id]);
      $estetico_ultimo = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    $connect->commit();
    
  } catch (Exception $e) {
    $connect->rollBack();
    $mensaje .= "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
  }
}

// Helper function for status badges
function badgeClass(string $v): string {
  $v = strtoupper(trim($v ?? ''));
  if ($v === 'BUENO' || $v === 'APROBADO') return 'status-bueno';
  if ($v === 'MALO' || $v === 'RECHAZADO') return 'status-malo';
  return 'status-nd';
}

// Helper function for grado badges
function gradoBadgeClass(string $grado): string {
  $grado = strtoupper(trim($grado ?? ''));
  switch ($grado) {
    case 'A': return 'badge-success';
    case 'B': return 'badge-info';
    case 'C': return 'badge-warning';
    case 'SCRAP': return 'badge-danger';
    default: return 'badge-secondary';
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Diagnóstico Estético - Sistema de Bodega</title>
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/custom.css">
  <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet" />
  <style>
    .form-section {
      background: #fff;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .section-title {
      background: #f2f2f2;
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      padding: 10px 15px;
      border-bottom: 2px solid #f0f0f0;
      border-radius: 8px 8px 0 0;
    }
    .card-icon {
      font-size: 24px;
      margin-right: 10px;
      color: #2c3e50;
    }
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 15px;
    }
    .equipo-card {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .equipo-card:hover {
      background: #e9ecef;
      border-color: #2c3e50;
      transform: translateY(-2px);
    }
    .equipo-card.selected {
      background: #d4edda;
      border-color: #28a745;
    }
    .status-bueno { background: #d4edda; color: #155724; }
    .status-malo { background: #f8d7da; color: #721c24; }
    .status-nd { background: #fff3cd; color: #856404; }
    .alert {
      padding: 12px 15px;
      margin-bottom: 20px;
      border-radius: 8px;
    }
    .grado-info {
      background: #e3f2fd;
      border: 1px solid #2196f3;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
    }
    .grado-info h6 {
      color: #1976d2;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-md-2 d-none d-md-block bg-dark sidebar">
        <?php include '../layouts/nav.php'; ?>
        <?php include '../layouts/menu_data.php'; ?>
      </nav>

      <!-- Main content -->
      <main role="main" class="col-md-10 ml-sm-auto px-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="material-icons" style="color: #2c3e50;">palette</i>
            Diagnóstico Estético
          </h1>
          <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
              <a href="inventario.php" class="btn btn-sm btn-outline-secondary">
                <i class="material-icons">inventory</i> Inventario
              </a>
            </div>
          </div>
        </div>

        <?php echo $mensaje; ?>

        <!-- Información sobre grados -->
        <div class="grado-info">
          <h6><i class="material-icons">info</i> Guía de Grados Estéticos</h6>
          <div class="row">
            <div class="col-md-3">
              <strong>Grado A:</strong> Excelente estado, sin rayones visibles
            </div>
            <div class="col-md-3">
              <strong>Grado B:</strong> Buen estado, rayones menores aceptables
            </div>
            <div class="col-md-3">
              <strong>Grado C:</strong> Estado regular, rayones moderados
            </div>
            <div class="col-md-3">
              <strong>SCRAP:</strong> Daños severos, no reparable
            </div>
          </div>
        </div>

        <div class="row">
          <!-- Lista de equipos pendientes -->
          <div class="col-md-4">
            <div class="form-section">
              <div class="section-title">
                <i class="material-icons card-icon">pending_actions</i>
                <h5 class="mb-0">Equipos Pendientes</h5>
              </div>
              
              <?php if (empty($equipos_pendientes)): ?>
                <div class="text-center text-muted py-4">
                  <i class="material-icons" style="font-size: 48px; color: #ccc;">check_circle</i>
                  <p>No hay equipos pendientes de diagnóstico estético</p>
                </div>
              <?php else: ?>
                <?php foreach ($equipos_pendientes as $equipo): ?>
                  <div class="equipo-card <?php echo ($inventario_id == $equipo['id']) ? 'selected' : ''; ?>" 
                       onclick="seleccionarEquipo(<?php echo $equipo['id']; ?>)">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <strong><?php echo htmlspecialchars($equipo['codigo_g']); ?></strong>
                        <br>
                        <small class="text-muted">
                          <?php echo htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']); ?>
                        </small>
                      </div>
                      <span class="badge badge-success">
                        Listo para Estético
                      </span>
                    </div>
                    <div class="mt-2">
                      <small class="text-info">
                        <i class="material-icons" style="font-size: 14px;">check_circle</i>
                        Eléctrico aprobado
                      </small>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

          <!-- Formulario de diagnóstico -->
          <div class="col-md-8">
            <?php if ($inventario): ?>
              <div class="form-section">
                <div class="section-title">
                  <i class="material-icons card-icon">build</i>
                  <h5 class="mb-0">Diagnóstico Estético - <?php echo htmlspecialchars($inventario['codigo_g']); ?></h5>
                </div>
                
                <div class="row mb-3">
                  <div class="col-md-6">
                    <strong>Marca:</strong> <?php echo htmlspecialchars($inventario['marca']); ?><br>
                    <strong>Modelo:</strong> <?php echo htmlspecialchars($inventario['modelo']); ?><br>
                    <strong>Serial:</strong> <?php echo htmlspecialchars($inventario['serial']); ?>
                  </div>
                  <div class="col-md-6">
                    <strong>Ubicación:</strong> <?php echo htmlspecialchars($inventario['ubicacion']); ?><br>
                    <strong>Posición:</strong> <?php echo htmlspecialchars($inventario['posicion']); ?><br>
                    <strong>Estado:</strong> 
                    <span class="badge badge-<?php echo ($inventario['disposicion'] == 'pendiente_estetico') ? 'warning' : 'info'; ?>">
                      <?php echo htmlspecialchars($inventario['disposicion']); ?>
                    </span>
                  </div>
                </div>

                <form method="POST" action="">
                  <div class="form-grid">
                    <div class="form-group">
                      <label for="estado_carcasa">Estado de Carcasa</label>
                      <select class="form-control" id="estado_carcasa" name="estado_carcasa" required>
                        <option value="">Seleccionar...</option>
                        <option value="Excelente">Excelente - Sin rayones</option>
                        <option value="Bueno">Bueno - Rayones menores</option>
                        <option value="Regular">Regular - Rayones moderados</option>
                        <option value="Malo">Malo - Rayones mayores</option>
                        <option value="Requiere cambio">Requiere cambio</option>
                        <option value="N/A">N/A</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="estado_pantalla_fisica">Estado Físico de Pantalla</label>
                      <select class="form-control" id="estado_pantalla_fisica" name="estado_pantalla_fisica" required>
                        <option value="">Seleccionar...</option>
                        <option value="Perfecta">Perfecta - Sin rayones</option>
                        <option value="Bueno">Bueno - Rayones menores</option>
                        <option value="Rayones menores">Rayones menores aceptables</option>
                        <option value="Rayones mayores">Rayones mayores</option>
                        <option value="Requiere cambio">Requiere cambio</option>
                        <option value="N/A">N/A</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="estado_teclado_fisico">Estado Físico de Teclado</label>
                      <select class="form-control" id="estado_teclado_fisico" name="estado_teclado_fisico" required>
                        <option value="">Seleccionar...</option>
                        <option value="Perfecto">Perfecto - Sin desgaste</option>
                        <option value="Bueno">Bueno - Desgaste menor</option>
                        <option value="Regular">Regular - Desgaste moderado</option>
                        <option value="Malo">Malo - Mucho desgaste</option>
                        <option value="Requiere cambio">Requiere cambio</option>
                        <option value="N/A">N/A</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="grado_asignado">Grado Asignado</label>
                      <select class="form-control" id="grado_asignado" name="grado_asignado" required>
                        <option value="A">Grado A - Excelente</option>
                        <option value="B">Grado B - Bueno</option>
                        <option value="C">Grado C - Regular</option>
                        <option value="SCRAP">SCRAP - No reparable</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="limpieza_realizada">Limpieza Realizada</label>
                      <select class="form-control" id="limpieza_realizada" name="limpieza_realizada" required>
                        <option value="no">No</option>
                        <option value="si">Sí</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="rayones_golpes">Rayones y Golpes</label>
                    <textarea class="form-control" id="rayones_golpes" name="rayones_golpes" rows="3" 
                              placeholder="Describa los rayones, golpes o daños físicos encontrados..."></textarea>
                  </div>

                  <div class="form-group">
                    <label for="partes_reemplazadas">Partes Reemplazadas</label>
                    <textarea class="form-control" id="partes_reemplazadas" name="partes_reemplazadas" rows="3" 
                              placeholder="Describa las partes que fueron reemplazadas..."></textarea>
                  </div>

                  <div class="form-group">
                    <label for="estado_final">Estado Final</label>
                    <select class="form-control" id="estado_final" name="estado_final" required>
                      <option value="requiere_revision">Requiere Revisión</option>
                      <option value="aprobado">Aprobado</option>
                      <option value="rechazado">Rechazado</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                              placeholder="Observaciones adicionales sobre el estado estético..."></textarea>
                  </div>

                  <div class="form-group">
                    <button type="submit" name="guardar_estetico" class="btn btn-primary">
                      <i class="material-icons">save</i> Guardar Diagnóstico
                    </button>
                    <a href="estetico.php" class="btn btn-secondary">
                      <i class="material-icons">refresh</i> Limpiar
                    </a>
                  </div>
                </form>
              </div>

              <!-- Historial de diagnósticos -->
              <?php if ($estetico_ultimo): ?>
                <div class="form-section">
                  <div class="section-title">
                    <i class="material-icons card-icon">history</i>
                    <h5 class="mb-0">Último Diagnóstico Estético</h5>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($estetico_ultimo['fecha_proceso'])); ?><br>
                      <strong>Estado Final:</strong> 
                      <span class="badge badge-<?php echo ($estetico_ultimo['estado_final'] == 'aprobado') ? 'success' : (($estetico_ultimo['estado_final'] == 'rechazado') ? 'danger' : 'warning'); ?>">
                        <?php echo ucfirst($estetico_ultimo['estado_final']); ?>
                      </span>
                    </div>
                    <div class="col-md-6">
                      <strong>Grado Asignado:</strong> 
                      <span class="badge <?php echo gradoBadgeClass($estetico_ultimo['grado_asignado']); ?>">
                        <?php echo htmlspecialchars($estetico_ultimo['grado_asignado']); ?>
                      </span><br>
                      <strong>Limpieza:</strong> 
                      <span class="badge badge-<?php echo ($estetico_ultimo['limpieza_realizada'] == 'si') ? 'success' : 'secondary'; ?>">
                        <?php echo ($estetico_ultimo['limpieza_realizada'] == 'si') ? 'Realizada' : 'No realizada'; ?>
                      </span>
                    </div>
                  </div>
                  
                  <?php if (!empty($estetico_ultimo['observaciones'])): ?>
                    <div class="mt-3">
                      <strong>Observaciones:</strong><br>
                      <em><?php echo htmlspecialchars($estetico_ultimo['observaciones']); ?></em>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

            <?php else: ?>
              <div class="form-section">
                <div class="text-center text-muted py-5">
                  <i class="material-icons" style="font-size: 64px; color: #ccc;">palette</i>
                  <h4>Seleccione un equipo</h4>
                  <p>Haga clic en un equipo de la lista para comenzar el diagnóstico estético</p>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <script>
    function seleccionarEquipo(id) {
      window.location.href = 'estetico.php?id=' + id;
    }
    
    // Pre-llenar formulario si hay diagnóstico previo
    <?php if ($estetico_ultimo): ?>
    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('estado_carcasa').value = '<?php echo $estetico_ultimo['estado_carcasa'] ?? ''; ?>';
      document.getElementById('estado_pantalla_fisica').value = '<?php echo $estetico_ultimo['estado_pantalla_fisica'] ?? ''; ?>';
      document.getElementById('estado_teclado_fisico').value = '<?php echo $estetico_ultimo['estado_teclado_fisico'] ?? ''; ?>';
      document.getElementById('rayones_golpes').value = '<?php echo $estetico_ultimo['rayones_golpes'] ?? ''; ?>';
      document.getElementById('limpieza_realizada').value = '<?php echo $estetico_ultimo['limpieza_realizada'] ?? 'no'; ?>';
      document.getElementById('partes_reemplazadas').value = '<?php echo $estetico_ultimo['partes_reemplazadas'] ?? ''; ?>';
      document.getElementById('grado_asignado').value = '<?php echo $estetico_ultimo['grado_asignado'] ?? 'C'; ?>';
      document.getElementById('estado_final').value = '<?php echo $estetico_ultimo['estado_final'] ?? 'requiere_revision'; ?>';
      document.getElementById('observaciones').value = '<?php echo $estetico_ultimo['observaciones'] ?? ''; ?>';
    });
    <?php endif; ?>
  </script>
</body>
</html>