<?php
// dashboard.php - Dashboard de Control de Calidad
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
$mensaje = '';
$equipos_pendientes = [];
$equipos_aprobados = [];
$equipos_rechazados = [];

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
  // Equipos pendientes de control de calidad
  $stmt = $connect->prepare("
    SELECT bi.*, be.grado_asignado, be.estado_final as estado_estetico
    FROM bodega_inventario bi
    LEFT JOIN bodega_estetico be ON bi.id = be.inventario_id
    WHERE bi.disposicion = 'pendiente_control_calidad'
    AND bi.estado = 'activo'
    AND bi.id NOT IN (
      SELECT DISTINCT inventario_id 
      FROM bodega_control_calidad 
      WHERE estado_final IN ('aprobado', 'rechazado')
    )
    ORDER BY bi.fecha_ingreso ASC
    LIMIT 100
  ");
  $stmt->execute();
  $equipos_pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Equipos aprobados recientemente
  $stmt = $connect->prepare("
    SELECT bi.*, bcc.fecha_control, bcc.estado_final, bcc.categoria_rec
    FROM bodega_inventario bi
    INNER JOIN bodega_control_calidad bcc ON bi.id = bcc.inventario_id
    WHERE bcc.estado_final = 'aprobado'
    AND bi.estado = 'activo'
    ORDER BY bcc.fecha_control DESC
    LIMIT 20
  ");
  $stmt->execute();
  $equipos_aprobados = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Equipos rechazados recientemente
  $stmt = $connect->prepare("
    SELECT bi.*, bcc.fecha_control, bcc.estado_final, bcc.observaciones
    FROM bodega_inventario bi
    INNER JOIN bodega_control_calidad bcc ON bi.id = bcc.inventario_id
    WHERE bcc.estado_final = 'rechazado'
    AND bi.estado = 'activo'
    ORDER BY bcc.fecha_control DESC
    LIMIT 20
  ");
  $stmt->execute();
  $equipos_rechazados = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
} catch (Exception $e) {
  error_log("Error carga inicial: " . $e->getMessage());
  $mensaje .= "<div class='alert alert-warning'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $connect->beginTransaction();
    
    if (isset($_POST['guardar_control_calidad'])) {
      $inventario_id = (int) $_POST['inventario_id'];
      
      // Validar que el equipo existe
      $stmt = $connect->prepare("SELECT * FROM bodega_inventario WHERE id = ? AND estado = 'activo' LIMIT 1");
      $stmt->execute([$inventario_id]);
      $inventario = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if (!$inventario) {
        throw new Exception("Equipo no encontrado");
      }
      
      // Insertar control de calidad
      $stmt = $connect->prepare("
        INSERT INTO bodega_control_calidad 
        (inventario_id, tecnico_id, burning_test, sentinel_test, estado_final, 
         categoria_rec, observaciones)
        VALUES (?, ?, ?, ?, ?, ?, ?)
      ");
      
      $stmt->execute([
        $inventario_id,
        $_SESSION['id'],
        $_POST['burning_test'] ?? '',
        $_POST['sentinel_test'] ?? '',
        $_POST['estado_final'] ?? 'rechazado',
        $_POST['categoria_rec'] ?? 'REC-C',
        $_POST['observaciones'] ?? ''
      ]);
      
      // Actualizar disposición del inventario según resultado
      $nueva_disposicion = '';
      switch ($_POST['estado_final']) {
        case 'aprobado':
          $nueva_disposicion = 'Para Venta';
          break;
        case 'rechazado':
          $nueva_disposicion = 'en_revision';
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
      
      $mensaje .= "<div class='alert alert-success'>✅ Control de calidad guardado correctamente</div>";
      
      // Recargar datos
      $stmt = $connect->prepare("
        SELECT bi.*, be.grado_asignado, be.estado_final as estado_estetico
        FROM bodega_inventario bi
        LEFT JOIN bodega_estetico be ON bi.id = be.inventario_id
        WHERE bi.disposicion = 'pendiente_control_calidad'
        AND bi.estado = 'activo'
        AND bi.id NOT IN (
          SELECT DISTINCT inventario_id 
          FROM bodega_control_calidad 
          WHERE estado_final IN ('aprobado', 'rechazado')
        )
        ORDER BY bi.fecha_ingreso ASC
        LIMIT 100
      ");
      $stmt->execute();
      $equipos_pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <title>Control de Calidad - Sistema de Bodega</title>
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
      color: #b6b059;
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
      border-color: #b6b059;
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
    .stats-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 4px 15px rgba(102,126,234,0.3);
    }
    .stats-number {
      font-size: 2.5rem;
      font-weight: bold;
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
            <i class="material-icons" style="color: #b6b059;">verified</i>
            Control de Calidad
          </h1>
          <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
              <a href="../bodega/inventario.php" class="btn btn-sm btn-outline-secondary">
                <i class="material-icons">inventory</i> Inventario
              </a>
            </div>
          </div>
        </div>

        <?php echo $mensaje; ?>

        <!-- Estadísticas -->
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="stats-card">
              <div class="stats-number"><?php echo count($equipos_pendientes); ?></div>
              <div>Pendientes de Control</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
              <div class="stats-number"><?php echo count($equipos_aprobados); ?></div>
              <div>Aprobados Hoy</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);">
              <div class="stats-number"><?php echo count($equipos_rechazados); ?></div>
              <div>Rechazados Hoy</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
              <div class="stats-number"><?php echo count($equipos_pendientes) + count($equipos_aprobados) + count($equipos_rechazados); ?></div>
              <div>Total Procesados</div>
            </div>
          </div>
        </div>

        <div class="row">
          <!-- Lista de equipos pendientes -->
          <div class="col-md-6">
            <div class="form-section">
              <div class="section-title">
                <i class="material-icons card-icon">pending_actions</i>
                <h5 class="mb-0">Equipos Pendientes de Control</h5>
              </div>
              
              <?php if (empty($equipos_pendientes)): ?>
                <div class="text-center text-muted py-4">
                  <i class="material-icons" style="font-size: 48px; color: #ccc;">check_circle</i>
                  <p>No hay equipos pendientes de control de calidad</p>
                </div>
              <?php else: ?>
                <?php foreach ($equipos_pendientes as $equipo): ?>
                  <div class="equipo-card" onclick="seleccionarEquipo(<?php echo $equipo['id']; ?>)">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <strong><?php echo htmlspecialchars($equipo['codigo_g']); ?></strong>
                        <br>
                        <small class="text-muted">
                          <?php echo htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']); ?>
                        </small>
                      </div>
                      <span class="badge <?php echo gradoBadgeClass($equipo['grado_asignado']); ?>">
                        Grado <?php echo htmlspecialchars($equipo['grado_asignado']); ?>
                      </span>
                    </div>
                    <div class="mt-2">
                      <small class="text-info">
                        <i class="material-icons" style="font-size: 14px;">check_circle</i>
                        Estético aprobado
                      </small>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

          <!-- Formulario de control de calidad -->
          <div class="col-md-6">
            <div class="form-section">
              <div class="section-title">
                <i class="material-icons card-icon">build</i>
                <h5 class="mb-0">Control de Calidad</h5>
              </div>
              
              <div class="text-center text-muted py-5">
                <i class="material-icons" style="font-size: 64px; color: #ccc;">verified</i>
                <h4>Seleccione un equipo</h4>
                <p>Haga clic en un equipo de la lista para comenzar el control de calidad</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Equipos aprobados y rechazados -->
        <div class="row">
          <div class="col-md-6">
            <div class="form-section">
              <div class="section-title">
                <i class="material-icons card-icon" style="color: #28a745;">check_circle</i>
                <h5 class="mb-0">Equipos Aprobados Recientemente</h5>
              </div>
              
              <?php if (empty($equipos_aprobados)): ?>
                <div class="text-center text-muted py-4">
                  <p>No hay equipos aprobados recientemente</p>
                </div>
              <?php else: ?>
                <?php foreach ($equipos_aprobados as $equipo): ?>
                  <div class="equipo-card">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <strong><?php echo htmlspecialchars($equipo['codigo_g']); ?></strong>
                        <br>
                        <small class="text-muted">
                          <?php echo htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']); ?>
                        </small>
                      </div>
                      <span class="badge badge-success">
                        <?php echo htmlspecialchars($equipo['categoria_rec']); ?>
                      </span>
                    </div>
                    <div class="mt-2">
                      <small class="text-success">
                        <i class="material-icons" style="font-size: 14px;">schedule</i>
                        <?php echo date('d/m/Y H:i', strtotime($equipo['fecha_control'])); ?>
                      </small>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-section">
              <div class="section-title">
                <i class="material-icons card-icon" style="color: #dc3545;">cancel</i>
                <h5 class="mb-0">Equipos Rechazados Recientemente</h5>
              </div>
              
              <?php if (empty($equipos_rechazados)): ?>
                <div class="text-center text-muted py-4">
                  <p>No hay equipos rechazados recientemente</p>
                </div>
              <?php else: ?>
                <?php foreach ($equipos_rechazados as $equipo): ?>
                  <div class="equipo-card">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <strong><?php echo htmlspecialchars($equipo['codigo_g']); ?></strong>
                        <br>
                        <small class="text-muted">
                          <?php echo htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']); ?>
                        </small>
                      </div>
                      <span class="badge badge-danger">
                        Rechazado
                      </span>
                    </div>
                    <div class="mt-2">
                      <small class="text-danger">
                        <i class="material-icons" style="font-size: 14px;">schedule</i>
                        <?php echo date('d/m/Y H:i', strtotime($equipo['fecha_control'])); ?>
                      </small>
                    </div>
                    <?php if (!empty($equipo['observaciones'])): ?>
                      <div class="mt-2">
                        <small class="text-muted">
                          <strong>Motivo:</strong> <?php echo htmlspecialchars(substr($equipo['observaciones'], 0, 100)) . (strlen($equipo['observaciones']) > 100 ? '...' : ''); ?>
                        </small>
                      </div>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal para control de calidad -->
  <div class="modal fade" id="controlModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Control de Calidad</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="controlForm" method="POST">
            <input type="hidden" id="inventario_id" name="inventario_id">
            
            <div class="form-group">
              <label for="burning_test">Burning Test</label>
              <textarea class="form-control" id="burning_test" name="burning_test" rows="3" 
                        placeholder="Resultado de la prueba de estrés térmico..." required></textarea>
            </div>

            <div class="form-group">
              <label for="sentinel_test">Sentinel Test</label>
              <textarea class="form-control" id="sentinel_test" name="sentinel_test" rows="3" 
                        placeholder="Resultado de la prueba de seguridad..." required></textarea>
            </div>

            <div class="form-group">
              <label for="categoria_rec">Categoría REC</label>
              <select class="form-control" id="categoria_rec" name="categoria_rec" required>
                <option value="REC-A">REC-A - Excelente</option>
                <option value="REC-B">REC-B - Bueno</option>
                <option value="REC-C">REC-C - Regular</option>
                <option value="REC-SCRAP">REC-SCRAP - No reparable</option>
              </select>
            </div>

            <div class="form-group">
              <label for="estado_final">Estado Final</label>
              <select class="form-control" id="estado_final" name="estado_final" required>
                <option value="aprobado">Aprobado</option>
                <option value="rechazado">Rechazado</option>
              </select>
            </div>

            <div class="form-group">
              <label for="observaciones">Observaciones</label>
              <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                        placeholder="Observaciones adicionales..."></textarea>
            </div>

            <div class="form-group">
              <button type="submit" name="guardar_control_calidad" class="btn btn-primary">
                <i class="material-icons">save</i> Guardar Control
              </button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">
                Cancelar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <script>
    function seleccionarEquipo(id) {
      // Aquí se cargarían los datos del equipo en el modal
      document.getElementById('inventario_id').value = id;
      $('#controlModal').modal('show');
    }
    
    // Auto-submit del formulario
    document.getElementById('controlForm').addEventListener('submit', function() {
      document.querySelector('button[name="guardar_control_calidad"]').disabled = true;
      document.querySelector('button[name="guardar_control_calidad"]').innerHTML = '<i class="material-icons">hourglass_empty</i> Guardando...';
    });
  </script>
</body>
</html>