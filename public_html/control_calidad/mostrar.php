<?php
// mostrar.php - Control de Calidad - Acciones y Formularios
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
$estadisticas = [
  'total' => 0,
  'pendientes' => 0,
  'aprobados' => 0,
  'rechazados' => 0
];
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
// Procesamiento del formulario de control de calidad
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $connect->beginTransaction();
    $inventario_id = (int) ($_POST['inventario_id'] ?? 0);
    $tecnico_id = (int) ($_SESSION['id']);
    if ($inventario_id > 0) {
      // Validar que el equipo existe y está pendiente de control de calidad
      $stmt = $connect->prepare("
        SELECT * FROM bodega_inventario 
        WHERE id = ? AND estado = 'activo' AND disposicion = 'pendiente_control_calidad'
        LIMIT 1
      ");
      $stmt->execute([$inventario_id]);
      $inventario = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if (!$inventario) {
        throw new Exception("Equipo no encontrado o no está pendiente de control de calidad");
      }
      
      // Insertar control de calidad
      $stmt = $connect->prepare("
        INSERT INTO bodega_control_calidad 
        (inventario_id, tecnico_id, burning_test, sentinel_test, estado_final, 
         categoria_rec, observaciones, fecha_control)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
      ");
      
      $stmt->execute([
        $inventario_id,
        $tecnico_id,
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
        // Registrar en log de cambios
        $stmt = $connect->prepare("
        INSERT INTO bodega_log_cambios 
        (inventario_id, usuario_id, campo_modificado, valor_anterior, valor_nuevo, fecha_cambio)
        VALUES (?, ?, ?, ?, ?, NOW())
      ");
      $stmt->execute([
        $inventario_id,
        $tecnico_id,
        'disposicion', // Campo modificado
        $inventario['disposicion'], // Valor anterior
        $nueva_disposicion // Valor nuevo
      ]);
      }
      
      $mensaje .= "<div class='alert alert-success'>✅ Control de calidad realizado correctamente. Equipo: " . htmlspecialchars($inventario['codigo_g']) . "</div>";
    }
    $connect->commit();
  } catch (Exception $e) {
    $connect->rollBack();
    $mensaje .= "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
  }
}

// Cargar datos
try {
  // Estadísticas generales
  $stmt = $connect->prepare("
    SELECT 
      COUNT(*) as total,
      SUM(CASE WHEN cc.estado_final IS NULL THEN 1 ELSE 0 END) as pendientes,
      SUM(CASE WHEN cc.estado_final = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
      SUM(CASE WHEN cc.estado_final = 'rechazado' THEN 1 ELSE 0 END) as rechazados
    FROM bodega_inventario i
    LEFT JOIN bodega_control_calidad cc ON i.id = cc.inventario_id
    WHERE i.disposicion = 'pendiente_control_calidad' AND i.estado = 'activo'
  ");
  $stmt->execute();
  $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
  // Equipos pendientes de control de calidad
  $stmt = $connect->prepare("
    SELECT i.*, 
           COALESCE(cc.estado_final, 'pendiente') as estado_qc,
           cc.fecha_control as fecha_qc,
           cc.categoria_rec,
           e.estado_final as estado_electrico,
           est.estado_final as estado_estetico,
           est.grado_asignado
    FROM bodega_inventario i
    LEFT JOIN bodega_control_calidad cc ON i.id = cc.inventario_id
    LEFT JOIN bodega_electrico e ON i.id = e.inventario_id
    LEFT JOIN bodega_estetico est ON i.id = est.inventario_id
    WHERE i.disposicion = 'pendiente_control_calidad' 
      AND i.estado = 'activo'
      AND (cc.estado_final IS NULL OR cc.estado_final = 'requiere_revision')
    ORDER BY i.fecha_ingreso ASC
  ");
  $stmt->execute();
  $equipos_pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // Equipos aprobados recientemente
  $stmt = $connect->prepare("
    SELECT i.*, cc.fecha_control, cc.estado_final, cc.categoria_rec, cc.observaciones
    FROM bodega_inventario i
    INNER JOIN bodega_control_calidad cc ON i.id = cc.inventario_id
    WHERE cc.estado_final = 'aprobado'
      AND i.estado = 'activo'
      AND i.disposicion = 'Para Venta'
    ORDER BY cc.fecha_control DESC
    LIMIT 20
  ");
  $stmt->execute();
  $equipos_aprobados = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // Equipos rechazados recientemente
  $stmt = $connect->prepare("
    SELECT i.*, cc.fecha_control, cc.estado_final, cc.observaciones
    FROM bodega_inventario i
    INNER JOIN bodega_control_calidad cc ON i.id = cc.inventario_id
    WHERE cc.estado_final = 'rechazado'
      AND i.estado = 'activo'
    ORDER BY cc.fecha_control DESC
    LIMIT 20
  ");
  $stmt->execute();
  $equipos_rechazados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  error_log("Error carga datos: " . $e->getMessage());
  $mensaje .= "<div class='alert alert-warning'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Helper functions
function badgeClass(string $v): string
{
  $v = strtoupper(trim($v ?? ''));
  if ($v === 'BUENO' || $v === 'APROBADO')
    return 'status-bueno';
  if ($v === 'MALO' || $v === 'RECHAZADO')
    return 'status-malo';
  return 'status-nd';
}

function gradoBadgeClass(string $grado): string {
  $grado = strtoupper(trim($grado ?? ''));
  switch ($grado) {
    case 'A':
      return 'grado-a';
    case 'B':
      return 'grado-b';
    case 'C':
      return 'grado-c';
    case 'SCRAP':
      return 'grado-scrap';
    default:
      return 'grado-nd';
  }
}

function categoriaBadgeClass(string $categoria): string {
  $categoria = strtoupper(trim($categoria ?? ''));
  switch ($categoria) {
    case 'REC-A':
      return 'categoria-a';
    case 'REC-B':
      return 'categoria-b';
    case 'REC-C':
      return 'categoria-c';
    case 'REC-SCRAP':
      return 'categoria-scrap';
    default:
      return 'categoria-nd';
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Control de Calidad - Acciones - PCMarket SAS</title>
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
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    } .section-title {
      background: #f2f2f2;
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      padding: 10px 15px;
      border-bottom: 2px solid #f0f0f0;
      border-radius: 5px 5px 0 0;
    } .card-icon {
      font-size: 24px;
      margin-right: 10px;
    } .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    } .stat-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    } .stat-card h3 {
      font-size: 2.5rem;
      margin: 0;
      font-weight: bold;
    } .stat-card p {
      margin: 5px 0 0 0;
      opacity: 0.9;
    } .equipment-card {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    } .equipment-card:hover {
      background: #e9ecef;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    } .equipment-code {
      font-size: 18px;
      font-weight: bold;
      color: #495057;
      margin-bottom: 5px;
    } .equipment-details {
      font-size: 14px;
      color: #6c757d;
    } .status-badge {
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.875em;
      font-weight: 500;
    } .status-bueno {
      background-color: #d4edda;
      color: #155724;
    } .status-malo {
      background-color: #f8d7da;
      color: #721c24;
    } .status-nd {
      background-color: #e2e3e5;
      color: #495057;
    } .grado-a {
      background-color: #d4edda;
      color: #155724;
    } .grado-b {
      background-color: #fff3cd;
      color: #856404;
    } .grado-c {
      background-color: #f8d7da;
      color: #721c24;
    } .grado-scrap {
      background-color: #721c24;
      color: #f8d7da;
    } .grado-nd {
      background-color: #e2e3e5;
      color: #495057;
    } .categoria-a {
      background-color: #d4edda;
      color: #155724;
    } .categoria-b {
      background-color: #fff3cd;
      color: #856404;
    } .categoria-c {
      background-color: #f8d7da;
      color: #721c24;
    } .categoria-scrap {
      background-color: #721c24;
      color: #f8d7da;
    } .categoria-nd {
      background-color: #e2e3e5;
      color: #495057;
    } .alert {
      padding: 12px 15px;
      margin-bottom: 15px;
      border-radius: 4px;
      border: 1px solid transparent;
    } .alert-success {
      background-color: #d4edda;
      border-color: #c3e6cb;
      color: #155724;
    } .alert-danger {
      background-color: #f8d7da;
      border-color: #f5c6cb;
      color: #721c24;
    } .alert-warning {
      background-color: #fff3cd;
      border-color: #ffeaa7;
      color: #856404;
    } .btn-container {
      display: flex;
      gap: 10px;
      margin-top: 20px;
      justify-content: center;
    } .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      font-weight: 500;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    } .btn-primary {
      background: #007bff;
      color: white;
    } .btn-secondary {
      background: #6c757d;
      color: white;
    } .btn-success {
      background: #28a745;
      color: white;
    } .btn:hover {
      opacity: 0.9;
      transform: translateY(-1px);
    } .main-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 20px;
    } .top-navbar {
      background: linear-gradient(135deg, #20bf6b 0%, #0fb9b1 100%);
      padding: 15px 20px;
      margin-bottom: 20px;
      border-radius: 8px;
    } .navbar-brand {
      color: white !important;
      font-weight: bold;
      text-decoration: none;
    } .modal-header {
      background: linear-gradient(135deg, #20bf6b 0%, #0fb9b1 100%);
      color: white;
    } .modal-header .close {
      color: white;
    } .qc-form {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
    } .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
    }
  </style>
</head>
<body>
  <!-- Top Navbar -->
  <?php
  include_once '../layouts/nav.php';
  include_once '../layouts/menu_data.php';
  ?>
  <nav id="sidebar">
    <div class="sidebar-header">
      <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
    </div>
    <?php renderMenu($menu); ?>
  </nav>
  <div class="main-container">
    <!-- Top Navbar -->
    <div class="top-navbar">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">
          <i class="material-icons" style="margin-right: 8px;">verified</i>
          ✅ CONTROL DE CALIDAD - ACCIONES | <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'USUARIO'); ?>
        </a>
      </div>
    </div>
    <!-- Mensajes de alerta -->
    <?php if (!empty($mensaje)): ?>
      <?php echo $mensaje; ?>
    <?php endif; ?>
    <!-- Estadísticas -->
    <div class="stats-grid">
      <div class="stat-card">
        <h3><?php echo $estadisticas['total']; ?></h3>
        <p>Total Equipos</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $estadisticas['pendientes']; ?></h3>
        <p>Pendientes QC</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $estadisticas['aprobados']; ?></h3>
        <p>Aprobados</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $estadisticas['rechazados']; ?></h3>
        <p>Rechazados</p>
      </div>
    </div>
    <!-- Formulario de Control de Calidad -->
    <div class="qc-form">
      <div class="section-title">
        <div class="card-icon">🔬</div>
        <h4>Realizar Control de Calidad</h4>
      </div>
      
      <form method="POST">
        <div class="form-grid">
          <div class="form-group">
            <label for="inventario_id">Seleccionar Equipo *</label>
            <select id="inventario_id" name="inventario_id" class="form-control" required>
              <option value="">-- Seleccionar Equipo --</option>
              <?php foreach ($equipos_pendientes as $equipo): ?>
                <option value="<?php echo $equipo['id']; ?>"
                  data-codigo="<?php echo htmlspecialchars($equipo['codigo_g']); ?>"
                  data-marca="<?php echo htmlspecialchars($equipo['marca']); ?>"
                  data-modelo="<?php echo htmlspecialchars($equipo['modelo']); ?>">
                  <?php echo htmlspecialchars($equipo['codigo_g'] . ' - ' . $equipo['marca'] . ' ' . $equipo['modelo']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="burning_test">Burning Test (24h mínimo) *</label>
            <textarea id="burning_test" name="burning_test" rows="3" class="form-control"
              placeholder="Describe el resultado del Burning Test realizado por 24h..." required></textarea>
          </div>
          <div class="form-group">
            <label for="sentinel_test">Sentinel Test *</label>
            <textarea id="sentinel_test" name="sentinel_test" rows="3" class="form-control"
              placeholder="Describe el resultado del Sentinel Test (antivirus)..." required></textarea>
          </div>
          <div class="form-group">
            <label for="categoria_rec">Categorización REC *</label>
            <select id="categoria_rec" name="categoria_rec" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="REC-A">REC-A - Excelente estado, sin problemas</option>
              <option value="REC-B">REC-B - Buen estado, problemas menores</option>
              <option value="REC-C">REC-C - Estado regular, problemas moderados</option>
              <option value="REC-SCRAP">REC-SCRAP - No apto para venta</option>
            </select>
          </div>
          <div class="form-group">
            <label for="estado_final">Estado Final *</label>
            <select id="estado_final" name="estado_final" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="aprobado">APROBADO - Pasa a Business Room</option>
              <option value="rechazado">RECHAZADO - Requiere más trabajo</option>
            </select>
          </div>
          <div class="form-group">
            <label for="observaciones">Observaciones Adicionales</label>
            <textarea id="observaciones" name="observaciones" rows="3" class="form-control"
              placeholder="Observaciones adicionales del control de calidad..."></textarea>
          </div>
        </div>
        <div class="btn-container">
          <button type="submit" class="btn btn-success">
            <i class="material-icons" style="margin-right: 8px;">save</i>
            Ejecutar Control de Calidad
          </button>
          <button type="reset" class="btn btn-secondary">
            <i class="material-icons" style="margin-right: 8px;">clear</i>
            Limpiar Formulario
          </button>
        </div>
      </form>
    </div>
    <!-- Equipos Pendientes de Control de Calidad -->
    <div class="form-section">
      <div class="section-title">
        <div class="card-icon">📋</div>
        <h4>Equipos Pendientes de Control de Calidad</h4>
      </div>
      
      <?php if (empty($equipos_pendientes)): ?>
        <div class="alert alert-info">
          ✅ No hay equipos pendientes de control de calidad en este momento.
        </div>
      <?php else: ?>
        <div class="row">
          <?php foreach ($equipos_pendientes as $equipo): ?>
            <div class="col-md-6 col-lg-4">
              <div class="equipment-card">
                <div class="equipment-code"><?php echo htmlspecialchars($equipo['codigo_g'] ?? 'N/A'); ?></div>
                <div class="equipment-details">
                  <strong><?php echo htmlspecialchars(($equipo['marca'] ?? '') . ' ' . ($equipo['modelo'] ?? '')); ?></strong><br>
                  <small>Serial: <?php echo htmlspecialchars($equipo['serial'] ?? 'N/A'); ?></small><br>
                  <small>Ubicación: <?php echo htmlspecialchars($equipo['ubicacion'] ?? 'N/A'); ?></small><br>
                  <div style="margin-top: 10px;">
                    <span class="status-badge <?php echo badgeClass($equipo['estado_electrico']); ?>">
                      Eléctrico: <?php echo htmlspecialchars(ucfirst($equipo['estado_electrico'] ?? 'N/A')); ?>
                    </span><br>
                    <span class="status-badge <?php echo badgeClass($equipo['estado_estetico']); ?>">
                      Estético: <?php echo htmlspecialchars(ucfirst($equipo['estado_estetico'] ?? 'N/A')); ?>
                    </span><br>
                    <span class="status-badge <?php echo gradoBadgeClass($equipo['grado_asignado']); ?>">
                      Grado: <?php echo htmlspecialchars($equipo['grado_asignado'] ?? 'N/A'); ?>
                    </span>
                  </div>
                  <div style="margin-top: 10px;">
                    <button type="button" class="btn btn-success btn-sm"
                      onclick="seleccionarEquipo(<?php echo $equipo['id']; ?>, '<?php echo htmlspecialchars($equipo['codigo_g']); ?>')">
                      <i class="material-icons" style="font-size: 16px;">verified</i>
                      Seleccionar para QC
                    </button>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <!-- Equipos Aprobados Recientemente -->
    <div class="form-section">
      <div class="section-title">
        <div class="card-icon">✅</div>
        <h4>Equipos Aprobados Recientemente (Listos para Business Room)</h4>
      </div>
      
      <?php if (empty($equipos_aprobados)): ?>
        <div class="alert alert-info">
          ✅ No hay equipos aprobados recientemente.
        </div>
      <?php else: ?>
        <div class="row">
          <?php foreach ($equipos_aprobados as $equipo): ?>
            <div class="col-md-6 col-lg-4">
              <div class="equipment-card">
                <div class="equipment-code"><?php echo htmlspecialchars($equipo['codigo_g'] ?? 'N/A'); ?></div>
                <div class="equipment-details">
                  <strong><?php echo htmlspecialchars(($equipo['marca'] ?? '') . ' ' . ($equipo['modelo'] ?? '')); ?></strong><br>
                  <small>Serial: <?php echo htmlspecialchars($equipo['serial'] ?? 'N/A'); ?></small><br>
                  <small>Ubicación: <?php echo htmlspecialchars($equipo['ubicacion'] ?? 'N/A'); ?></small><br>
                  <div style="margin-top: 10px;">
                    <span class="status-badge status-bueno">
                      ✅ APROBADO
                    </span><br>
                    <span class="status-badge <?php echo categoriaBadgeClass($equipo['categoria_rec']); ?>">
                      <?php echo htmlspecialchars($equipo['categoria_rec'] ?? 'N/A'); ?>
                    </span><br>
                    <small>Fecha:
                      <?php echo htmlspecialchars((new DateTime($equipo['fecha_control']))->format('d/m/Y H:i')); ?></small>
                  </div>
                  <div style="margin-top: 10px;">
                    <span class="badge badge-success">
                      <i class="material-icons" style="font-size: 14px;">shopping_cart</i>
                      Listo para Business Room
                    </span>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <!-- Equipos Rechazados Recientemente -->
    <div class="form-section">
      <div class="section-title">
        <div class="card-icon">❌</div>
        <h4>Equipos Rechazados Recientemente</h4>
      </div>
      
      <?php if (empty($equipos_rechazados)): ?>
        <div class="alert alert-info">
          ✅ No hay equipos rechazados recientemente.
        </div>
      <?php else: ?>
        <div class="row">
          <?php foreach ($equipos_rechazados as $equipo): ?>
            <div class="col-md-6 col-lg-4">
              <div class="equipment-card">
                <div class="equipment-code"><?php echo htmlspecialchars($equipo['codigo_g'] ?? 'N/A'); ?></div>
                <div class="equipment-details">
                  <strong><?php echo htmlspecialchars(($equipo['marca'] ?? '') . ' ' . ($equipo['modelo'] ?? '')); ?></strong><br>
                  <small>Serial: <?php echo htmlspecialchars($equipo['serial'] ?? 'N/A'); ?></small><br>
                  <small>Ubicación: <?php echo htmlspecialchars($equipo['ubicacion'] ?? 'N/A'); ?></small><br>
                  <div style="margin-top: 10px;">
                    <span class="status-badge status-malo">
                      ❌ RECHAZADO
                    </span><br>
                    <small>Fecha:
                      <?php echo htmlspecialchars((new DateTime($equipo['fecha_control']))->format('d/m/Y H:i')); ?></small>
                  </div>
                  <?php if (!empty($equipo['observaciones'])): ?>
                    <div style="margin-top: 10px;">
                      <small><strong>Motivo:</strong>
                        <?php echo htmlspecialchars(substr($equipo['observaciones'], 0, 100)); ?>
                        <?php if (strlen($equipo['observaciones']) > 100): ?>...<?php endif; ?></small>
                    </div>
                  <?php endif; ?>
                  <div style="margin-top: 10px;">
                    <span class="badge badge-warning">
                      <i class="material-icons" style="font-size: 14px;">build</i>
                      Requiere más trabajo
                    </span>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <!-- Botones de navegación -->
    <div class="btn-container">
      <a href="dashboard.php" class="btn btn-primary">
        <i class="material-icons" style="margin-right: 8px;">dashboard</i>
        Dashboard QC
      </a>
      <a href="../bodega/mostrar.php" class="btn btn-secondary">
        <i class="material-icons" style="margin-right: 8px;">inventory</i>
        Volver al Dashboard
      </a>
      <a href="../b_room/mostrar.php" class="btn btn-success">
        <i class="material-icons" style="margin-right: 8px;">shopping_cart</i>
        Business Room
      </a>
    </div>
  </div>
  <!-- Scripts -->
  <script src="../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script>
    function seleccionarEquipo(equipoId, codigoEquipo) {
      document.getElementById('inventario_id').value = equipoId;
      
      // Mostrar confirmación
      if (confirm(`¿Desea seleccionar el equipo ${codigoEquipo} para realizar el control de calidad?`)) {
        // Hacer scroll al formulario
        document.querySelector('.qc-form').scrollIntoView({ behavior: 'smooth' });
        // Resaltar el equipo seleccionado
        document.querySelectorAll('.equipment-card').forEach(card => {
          card.style.border = '1px solid #dee2e6';
        });
        // Encontrar y resaltar la tarjeta del equipo seleccionado
        const targetCard = document.querySelector(`[onclick*="${equipoId}"]`).closest('.equipment-card');
        if (targetCard) {
          targetCard.style.border = '2px solid #28a745';
          targetCard.style.background = '#d4edda';
        }
      }
    }
    // Validar formulario antes de enviar
    document.querySelector('form').addEventListener('submit', function (e) {
      const inventarioId = document.getElementById('inventario_id').value;
      const burningTest = document.getElementById('burning_test').value.trim();
      const sentinelTest = document.getElementById('sentinel_test').value.trim();
      const categoriaRec = document.getElementById('categoria_rec').value;
      const estadoFinal = document.getElementById('estado_final').value;
      
      if (!inventarioId || !burningTest || !sentinelTest || !categoriaRec || !estadoFinal) {
        e.preventDefault();
        alert('Por favor, complete todos los campos obligatorios marcados con *.');
        return false;
      }
      
      // Confirmar antes de enviar
      if (!confirm('¿Está seguro de que desea ejecutar el control de calidad para este equipo?')) {
        e.preventDefault();
        return false;
      }
    });
    // Actualizar información del equipo seleccionado
    document.getElementById('inventario_id').addEventListener('change', function () {
      const selectedOption = this.options[this.selectedIndex];
      if (selectedOption.value) {
        const codigo = selectedOption.getAttribute('data-codigo');
        const marca = selectedOption.getAttribute('data-marca');
        const modelo = selectedOption.getAttribute('data-modelo');
        // Mostrar información del equipo seleccionado
        alert(`Equipo seleccionado: ${codigo}\nMarca: ${marca}\nModelo: ${modelo}\n\nComplete los campos del formulario para realizar el control de calidad.`);
      }
    });
  </script>
</body>
</html>