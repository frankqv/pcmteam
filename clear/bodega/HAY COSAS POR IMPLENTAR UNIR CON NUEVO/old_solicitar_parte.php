<?php
// solicitar_parte.php - Formulario de Solicitud de Partes
ob_start();
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}require_once dirname(__DIR__, 2) . '/config/ctconex.php';// Validación de roles
$allowedRoles = [1, 2, 5, 6, 7];
if (!isset($_SESSION['rol']) || !in_array((int) $_SESSION['rol'], $allowedRoles, true)) {
  header('Location: ../error404.php');
  exit;
}// Variables globales
$mensaje = '';
$solicitudes = [];
$equipos_disponibles = [];
$partes_disponibles = [];// Obtener información del usuario para navbar
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
}// Cargar datos iniciales
try {
  // Equipos disponibles para solicitar partes
  $stmt = $connect->prepare("
    SELECT id, codigo_g, marca, modelo, serial, disposicion
    FROM bodega_inventario 
    WHERE estado = 'activo' 
    AND disposicion IN ('en_mantenimiento', 'en_reparacion', 'en_revision')
    ORDER BY codigo_g
  ");
  $stmt->execute();
  $equipos_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Partes disponibles
  $stmt = $connect->prepare("
    SELECT id, marca, referencia, producto, condicion, cantidad, precio
    FROM bodega_partes 
    WHERE cantidad > 0 
    ORDER BY marca, referencia
  ");
  $stmt->execute();
  $partes_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Solicitudes del usuario actual
  $stmt = $connect->prepare("
    SELECT sp.*, bi.codigo_g, bi.marca as marca_equipo, bi.modelo as modelo_equipo
    FROM bodega_solicitud_parte sp
    LEFT JOIN bodega_inventario bi ON sp.inventario_id = bi.id
    WHERE sp.usuario_solicitante = ?
    ORDER BY sp.fecha_solicitud DESC
    LIMIT 50
  ");
  $stmt->execute([$_SESSION['id']]);
  $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
} catch (Exception $e) {
  error_log("Error carga inicial: " . $e->getMessage());
  $mensaje .= "<div class='alert alert-warning'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</div>";
}// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $connect->beginTransaction();
    
    if (isset($_POST['crear_solicitud'])) {
      // Validar datos requeridos
      if (empty($_POST['detalle_solicitud']) || empty($_POST['codigo_equipo'])) {
        throw new Exception("Todos los campos son obligatorios");
      }
      
      // Insertar solicitud
      $stmt = $connect->prepare("
        INSERT INTO bodega_solicitud_parte 
        (detalle_solicitud, cantidad_solicitada, codigo_equipo, serial_parte, 
         marca_parte, nivel_urgencia, referencia_parte, ubicacion_pieza, 
         id_tecnico, usuario_solicitante, inventario_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      ");
      
      $stmt->execute([
        $_POST['detalle_solicitud'],
        $_POST['cantidad_solicitada'] ?? '1',
        $_POST['codigo_equipo'],
        $_POST['serial_parte'] ?? '',
        $_POST['marca_parte'] ?? '',
        $_POST['nivel_urgencia'] ?? 'Media',
        $_POST['referencia_parte'] ?? '',
        $_POST['ubicacion_pieza'] ?? '',
        $_SESSION['id'],
        $_SESSION['id'],
        $_POST['inventario_id'] ?? null
      ]);
      
      $mensaje .= "<div class='alert alert-success'>✅ Solicitud de parte creada correctamente</div>";
      
      // Recargar solicitudes
      $stmt = $connect->prepare("
        SELECT sp.*, bi.codigo_g, bi.marca as marca_equipo, bi.modelo as modelo_equipo
        FROM bodega_solicitud_parte sp
        LEFT JOIN bodega_inventario bi ON sp.inventario_id = bi.id
        WHERE sp.usuario_solicitante = ?
        ORDER BY sp.fecha_solicitud DESC
        LIMIT 50
      ");
      $stmt->execute([$_SESSION['id']]);
      $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    $connect->commit();
    
  } catch (Exception $e) {
    $connect->rollBack();
    $mensaje .= "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
  }
}// Helper function for urgency badges
function urgenciaBadgeClass(string $urgencia): string {
  $urgencia = strtoupper(trim($urgencia ?? ''));
  switch ($urgencia) {
    case 'ALTA': return 'badge-danger';
    case 'MEDIA': return 'badge-warning';
    case 'BAJA': return 'badge-info';
    default: return 'badge-secondary';
  }
}// Helper function for status badges
function estadoBadgeClass(string $estado): string {
  $estado = strtoupper(trim($estado ?? ''));
  switch ($estado) {
    case 'APROBADA': return 'badge-success';
    case 'PENDIENTE': return 'badge-warning';
    case 'RECHAZADA': return 'badge-danger';
    case 'ENTREGADA': return 'badge-info';
    default: return 'badge-secondary';
  }
}
?><!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Solicitar Parte - Sistema de Bodega</title>
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
      color: #007bff;
    }
    .solicitud-card {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }
    .solicitud-card:hover {
      background: #e9ecef;
      border-color: #007bff;
      transform: translateY(-2px);
    }
    .alert {
      padding: 12px 15px;
      margin-bottom: 20px;
      border-radius: 8px;
    }
    .stats-card {
      background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
      color: white;
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 4px 15px rgba(0,123,255,0.3);
    }
    .stats-number {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 10px;
    }
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 15px;
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
      </nav>      <!-- Main content -->
      <main role="main" class="col-md-10 ml-sm-auto px-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="material-icons" style="color: #007bff;">add_shopping_cart</i>
            Solicitar Parte
          </h1>
          <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
              <a href="lista_parte.php" class="btn btn-sm btn-outline-secondary">
                <i class="material-icons">inventory_2</i> Inventario Partes
              </a>
              <a href="inventario.php" class="btn btn-sm btn-outline-info">
                <i class="material-icons">inventory</i> Inventario
              </a>
            </div>
          </div>
        </div>        <?php echo $mensaje; ?>        <!-- Estadísticas -->
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="stats-card">
              <div class="stats-number"><?php echo count($solicitudes); ?></div>
              <div>Total Solicitudes</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
              <div class="stats-number">
                <?php echo count(array_filter($solicitudes, function($s) { return $s['estado'] === 'pendiente'; })); ?>
              </div>
              <div>Pendientes</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
              <div class="stats-number">
                <?php echo count(array_filter($solicitudes, function($s) { return $s['estado'] === 'aprobada'; })); ?>
              </div>
              <div>Aprobadas</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
              <div class="stats-number">
                <?php echo count(array_filter($solicitudes, function($s) { return $s['estado'] === 'entregada'; })); ?>
              </div>
              <div>Entregadas</div>
            </div>
          </div>
        </div>        <div class="row">
          <!-- Formulario de solicitud -->
          <div class="col-md-6">
            <div class="form-section">
              <div class="section-title">
                <i class="material-icons card-icon">add_circle</i>
                <h5 class="mb-0">Nueva Solicitud de Parte</h5>
              </div>
              
              <form method="POST" action="">
                <div class="form-group">
                  <label for="detalle_solicitud">Detalle de la Solicitud *</label>
                  <textarea class="form-control" id="detalle_solicitud" name="detalle_solicitud" rows="3" 
                            placeholder="Describa la parte que necesita..." required></textarea>
                </div>                <div class="form-grid">
                  <div class="form-group">
                    <label for="cantidad_solicitada">Cantidad</label>
                    <input type="number" class="form-control" id="cantidad_solicitada" name="cantidad_solicitada" 
                           value="1" min="1" required>
                  </div>                  <div class="form-group">
                    <label for="nivel_urgencia">Nivel de Urgencia</label>
                    <select class="form-control" id="nivel_urgencia" name="nivel_urgencia" required>
                      <option value="Baja">Baja</option>
                      <option value="Media" selected>Media</option>
                      <option value="Alta">Alta</option>
                    </select>
                  </div>
                </div>                <div class="form-group">
                  <label for="codigo_equipo">Código del Equipo *</label>
                  <select class="form-control" id="codigo_equipo" name="codigo_equipo" required>
                    <option value="">Seleccionar equipo...</option>
                    <?php foreach ($equipos_disponibles as $equipo): ?>
                      <option value="<?php echo htmlspecialchars($equipo['codigo_g']); ?>">
                        <?php echo htmlspecialchars($equipo['codigo_g'] . ' - ' . $equipo['marca'] . ' ' . $equipo['modelo']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>                <div class="form-grid">
                  <div class="form-group">
                    <label for="serial_parte">Serial de la Parte</label>
                    <input type="text" class="form-control" id="serial_parte" name="serial_parte" 
                           placeholder="Serial de la parte...">
                  </div>                  <div class="form-group">
                    <label for="marca_parte">Marca de la Parte</label>
                    <input type="text" class="form-control" id="marca_parte" name="marca_parte" 
                           placeholder="Marca de la parte...">
                  </div>
                </div>                <div class="form-grid">
                  <div class="form-group">
                    <label for="referencia_parte">Referencia de la Parte</label>
                    <input type="text" class="form-control" id="referencia_parte" name="referencia_parte" 
                           placeholder="Referencia de la parte...">
                  </div>                  <div class="form-group">
                    <label for="ubicacion_pieza">Ubicación de la Pieza</label>
                    <input type="text" class="form-control" id="ubicacion_pieza" name="ubicacion_pieza" 
                           placeholder="Ubicación en bodega...">
                  </div>
                </div>                <div class="form-group">
                  <button type="submit" name="crear_solicitud" class="btn btn-primary">
                    <i class="material-icons">send</i> Crear Solicitud
                  </button>
                  <button type="reset" class="btn btn-secondary">
                    <i class="material-icons">refresh</i> Limpiar
                  </button>
                </div>
              </form>
            </div>
          </div>          <!-- Lista de solicitudes -->
          <div class="col-md-6">
            <div class="form-section">
              <div class="section-title">
                <i class="material-icons card-icon">list</i>
                <h5 class="mb-0">Mis Solicitudes</h5>
              </div>
              
              <?php if (empty($solicitudes)): ?>
                <div class="text-center text-muted py-4">
                  <i class="material-icons" style="font-size: 48px; color: #ccc;">assignment</i>
                  <p>No tienes solicitudes de partes</p>
                </div>
              <?php else: ?>
                <?php foreach ($solicitudes as $solicitud): ?>
                  <div class="solicitud-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div>
                        <strong><?php echo htmlspecialchars($solicitud['detalle_solicitud']); ?></strong>
                        <?php if (!empty($solicitud['codigo_equipo'])): ?>
                          <br><small class="text-muted">Equipo: <?php echo htmlspecialchars($solicitud['codigo_equipo']); ?></small>
                        <?php endif; ?>
                      </div>
                      <div class="text-right">
                        <span class="badge <?php echo urgenciaBadgeClass($solicitud['nivel_urgencia']); ?>">
                          <?php echo htmlspecialchars($solicitud['nivel_urgencia']); ?>
                        </span>
                        <br>
                        <span class="badge <?php echo estadoBadgeClass($solicitud['estado']); ?>">
                          <?php echo ucfirst($solicitud['estado']); ?>
                        </span>
                      </div>
                    </div>
                    
                    <div class="mb-2">
                      <strong>Cantidad:</strong> <?php echo htmlspecialchars($solicitud['cantidad_solicitada']); ?><br>
                      <?php if (!empty($solicitud['marca_parte'])): ?>
                        <strong>Marca:</strong> <?php echo htmlspecialchars($solicitud['marca_parte']); ?><br>
                      <?php endif; ?>
                      <?php if (!empty($solicitud['referencia_parte'])): ?>
                        <strong>Referencia:</strong> <?php echo htmlspecialchars($solicitud['referencia_parte']); ?><br>
                      <?php endif; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                      <small class="text-muted">
                        <i class="material-icons" style="font-size: 14px;">schedule</i>
                        <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])); ?>
                      </small>
                      
                      <?php if ($solicitud['estado'] === 'pendiente'): ?>
                        <span class="badge badge-warning">
                          <i class="material-icons" style="font-size: 12px;">pending</i> En revisión
                        </span>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>        <!-- Información adicional -->
        <div class="row">
          <div class="col-md-12">
            <div class="form-section">
              <div class="section-title">
                <i class="material-icons card-icon">info</i>
                <h5 class="mb-0">Información de Solicitudes</h5>
              </div>
              
              <div class="row">
                <div class="col-md-4">
                  <div class="text-center">
                    <i class="material-icons" style="font-size: 48px; color: #ffc107;">pending</i>
                    <h6>Pendiente</h6>
                    <p class="text-muted">Solicitud enviada, esperando aprobación</p>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="text-center">
                    <i class="material-icons" style="font-size: 48px; color: #28a745;">check_circle</i>
                    <h6>Aprobada</h6>
                    <p class="text-muted">Solicitud aprobada, en proceso de entrega</p>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="text-center">
                    <i class="material-icons" style="font-size: 48px; color: #6f42c1;">local_shipping</i>
                    <h6>Entregada</h6>
                    <p class="text-muted">Parte entregada al solicitante</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <script>
    // Auto-submit del formulario
    document.querySelector('form[method="POST"]').addEventListener('submit', function() {
      const submitBtn = this.querySelector('button[name="crear_solicitud"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="material-icons">hourglass_empty</i> Creando...';
      }
    });
    
    // Actualizar inventario_id cuando se selecciona un equipo
    document.getElementById('codigo_equipo').addEventListener('change', function() {
      const codigo = this.value;
      const equipos = <?php echo json_encode($equipos_disponibles); ?>;
      const equipo = equipos.find(e => e.codigo_g === codigo);
      
      if (equipo) {
        // Crear campo oculto para inventario_id si no existe
        let inventarioField = document.getElementById('inventario_id');
        if (!inventarioField) {
          inventarioField = document.createElement('input');
          inventarioField.type = 'hidden';
          inventarioField.name = 'inventario_id';
          inventarioField.id = 'inventario_id';
          this.parentNode.appendChild(inventarioField);
        }
        inventarioField.value = equipo.id;
      }
    });
  </script>
</body>
</html>