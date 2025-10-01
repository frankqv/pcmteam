<?php
// bodega/lista_estetico.php
// estetico.php - Diagnóstico Estético de Equipos
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
$equipo_seleccionado = null;
$diagnostico_ultimo = null;
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
// Cargar equipos pendientes de diagnóstico estético
// Permite que cualquier usuario vea todos los equipos sin restricciones
try {
  $stmt = $connect->prepare("
    SELECT i.*, 
           COALESCE(est.estado_final, 'pendiente') as estado_estetico,
           est.fecha_proceso as fecha_estetico,
           e.estado_final as estado_electrico,
           d.falla_electrica,
           d.falla_estetica,
           u_tecnico.nombre as tecnico_asignado
    FROM bodega_inventario i
    LEFT JOIN bodega_estetico est ON i.id = est.inventario_id
    LEFT JOIN bodega_electrico e ON i.id = e.inventario_id  
    LEFT JOIN bodega_diagnosticos d ON i.id = d.inventario_id
    LEFT JOIN usuarios u_tecnico ON i.tecnico_id = u_tecnico.id
    WHERE i.estado = 'activo'
      AND (
        -- Equipos pendientes de diagnóstico estético
        i.disposicion = 'pendiente_estetico' 
        OR 
        -- Equipos que ya pasaron área eléctrica
        e.estado_final = 'aprobado'
        OR
        -- Equipos que necesitan revisión estética
        est.estado_final = 'requiere_revision'
        OR
        -- Equipos en cualquier etapa que puedan necesitar trabajo estético
        i.disposicion IN ('en_diagnostico', 'en_mantenimiento', 'en_proceso')
      )
    ORDER BY 
      CASE 
        WHEN i.disposicion = 'pendiente_estetico' THEN 1
        WHEN est.estado_final = 'requiere_revision' THEN 2
        WHEN e.estado_final = 'aprobado' THEN 3
        ELSE 4
      END,
      i.fecha_ingreso ASC
  ");
  $stmt->execute();
  $equipos_pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  error_log("Error carga equipos: " . $e->getMessage());
  $mensaje .= "<div class='alert alert-warning'>Error al cargar equipos: " . htmlspecialchars($e->getMessage()) . "</div>";
}
// Sección CORREGIDA para cargar TODOS los equipos disponibles para diagnóstico estético
// Permite que cualquier usuario vea todos los equipos sin restricciones
try {
  $stmt = $connect->prepare("
    SELECT i.*, 
           COALESCE(est.estado_final, 'pendiente') as estado_estetico,
           est.fecha_proceso as fecha_estetico,
           e.estado_final as estado_electrico,
           d.falla_electrica,
           d.falla_estetica,
           u_tecnico.nombre as tecnico_asignado
    FROM bodega_inventario i
    LEFT JOIN bodega_estetico est ON i.id = est.inventario_id
    LEFT JOIN bodega_electrico e ON i.id = e.inventario_id  
    LEFT JOIN bodega_diagnosticos d ON i.id = d.inventario_id
    LEFT JOIN usuarios u_tecnico ON i.tecnico_id = u_tecnico.id
    WHERE i.estado = 'activo'
      AND (
        -- Equipos pendientes de diagnóstico estético
        i.disposicion = 'pendiente_estetico' 
        OR 
        -- Equipos que ya pasaron área eléctrica
        e.estado_final = 'aprobado'
        OR
        -- Equipos que necesitan revisión estética
        est.estado_final = 'requiere_revision'
        OR
        -- Equipos en cualquier etapa que puedan necesitar trabajo estético
        i.disposicion IN ('en_diagnostico', 'en_mantenimiento', 'en_proceso')
      )
    ORDER BY 
      CASE 
        WHEN i.disposicion = 'pendiente_estetico' THEN 1
        WHEN est.estado_final = 'requiere_revision' THEN 2
        WHEN e.estado_final = 'aprobado' THEN 3
        ELSE 4
      END,
      i.fecha_ingreso ASC
  ");
  $stmt->execute();
  $equipos_pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  error_log("Error carga equipos: " . $e->getMessage());
  $mensaje .= "<div class='alert alert-warning'>Error al cargar equipos: " . htmlspecialchars($e->getMessage()) . "</div>";
}
// Código CORREGIDO para insertar diagnóstico estético
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $connect->beginTransaction();
    $inventario_id = (int) ($_POST['inventario_id'] ?? 0);
    $tecnico_id = (int) ($_SESSION['id']);
    if ($inventario_id > 0) {
      // Insertar diagnóstico estético
      $stmt = $connect->prepare("
        INSERT INTO bodega_estetico 
        (inventario_id, tecnico_id, estado_carcasa, estado_pantalla_fisica, estado_teclado_fisico, 
         rayones_golpes, limpieza_realizada, partes_reemplazadas, grado_asignado, 
         estado_final, observaciones, fecha_proceso)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
      ");
      $stmt->execute([
        $inventario_id,
        $tecnico_id,
        $_POST['estado_carcasa'] ?? 'N/D',
        $_POST['estado_pantalla_fisica'] ?? 'N/D',
        $_POST['estado_teclado_fisico'] ?? 'N/D',
        $_POST['rayones_golpes'] ?? '',
        $_POST['limpieza_realizada'] ?? 'no',
        $_POST['partes_reemplazadas'] ?? '',
        $_POST['grado_asignado'] ?? 'C',
        $_POST['estado_final'] ?? 'pendiente',
        $_POST['observaciones'] ?? ''
      ]);
      // Actualizar disposición del inventario según el estado final
      $nueva_disposicion = 'en_revision';
      if ($_POST['estado_final'] === 'aprobado') {
        $nueva_disposicion = 'pendiente_control_calidad';
      } elseif ($_POST['estado_final'] === 'requiere_revision') {
        $nueva_disposicion = 'pendiente_estetico';
      }
      $stmt = $connect->prepare("
        UPDATE bodega_inventario 
        SET disposicion = ?, fecha_modificacion = NOW()
        WHERE id = ?
      ");
      $stmt->execute([$nueva_disposicion, $inventario_id]);
      // Registrar cambio en log
      $stmt = $connect->prepare("
        INSERT INTO bodega_log_cambios 
        (inventario_id, usuario_id, campo_modificado, valor_anterior, valor_nuevo, fecha_cambio)
        VALUES (?, ?, ?, ?, ?, NOW())
      ");
      $stmt->execute([
        $inventario_id,
        $tecnico_id,
        'disposicion',
        'pendiente_estetico',
        $nueva_disposicion
      ]);
      $mensaje .= "<div class='alert alert-success'>✅ Diagnóstico estético guardado correctamente</div>";
      // Recargar TODOS los equipos disponibles (sin restricciones)
      $stmt = $connect->prepare("
        SELECT i.*, 
               COALESCE(est.estado_final, 'pendiente') as estado_estetico,
               est.fecha_proceso as fecha_estetico,
               e.estado_final as estado_electrico,
               d.falla_electrica,
               d.falla_estetica,
               u_tecnico.nombre as tecnico_asignado
        FROM bodega_inventario i
        LEFT JOIN bodega_estetico est ON i.id = est.inventario_id
        LEFT JOIN bodega_electrico e ON i.id = e.inventario_id  
        LEFT JOIN bodega_diagnosticos d ON i.id = d.inventario_id
        LEFT JOIN usuarios u_tecnico ON i.tecnico_id = u_tecnico.id
        WHERE i.estado = 'activo'
          AND (
            i.disposicion = 'pendiente_estetico' 
            OR e.estado_final = 'aprobado'
            OR est.estado_final = 'requiere_revision'
            OR i.disposicion IN ('en_diagnostico', 'en_mantenimiento', 'en_proceso')
          )
        ORDER BY 
          CASE 
            WHEN i.disposicion = 'pendiente_estetico' THEN 1
            WHEN est.estado_final = 'requiere_revision' THEN 2
            WHEN e.estado_final = 'aprobado' THEN 3
            ELSE 4
          END,
          i.fecha_ingreso ASC
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
// Código CORREGIDO para cargar datos del equipo seleccionado
// Reemplaza la sección de carga de equipo seleccionado (líneas 130-150 aproximadamente)
if (isset($_GET['id']) && (int) $_GET['id'] > 0) {
  $equipo_id = (int) $_GET['id'];
  try {
    $stmt = $connect->prepare("SELECT * FROM bodega_inventario WHERE id = ? LIMIT 1");
    $stmt->execute([$equipo_id]);
    $equipo_seleccionado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($equipo_seleccionado) {
      $stmt = $connect->prepare("
        SELECT * FROM bodega_estetico 
        WHERE inventario_id = ? 
        ORDER BY fecha_proceso DESC LIMIT 1
      ");
      $stmt->execute([$equipo_id]);
      $diagnostico_ultimo = $stmt->fetch(PDO::FETCH_ASSOC);
    }
  } catch (Exception $e) {
    error_log("Error carga equipo: " . $e->getMessage());
    $mensaje .= "<div class='alert alert-warning'>Error al cargar equipo: " . htmlspecialchars($e->getMessage()) . "</div>";
  }
}
// Helper function for status badges
function badgeClass(string $v): string
{
  $v = strtoupper(trim($v ?? ''));
  if ($v === 'BUENO' || $v === 'APROBADO') return 'status-bueno';
  if ($v === 'MALO' || $v === 'RECHAZADO') return 'status-malo';
  return 'status-nd';
}
// Helper function for grade badges
function gradoBadgeClass(string $grado): string
{
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Diagnóstico Estético - PCMarket SAS</title>
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
    }
    .section-title {
      background: #f2f2f2;
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      padding: 10px 15px;
      border-bottom: 2px solid #f0f0f0;
      border-radius: 5px 5px 0 0;
    }
    .card-icon {
      font-size: 24px;
      margin-right: 10px;
    }
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
    }
    .equipment-card {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .equipment-card:hover {
      background: #e9ecef;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .equipment-card.selected {
      background: #d4edda;
      border-color: #28a745;
    }
    .equipment-code {
      font-size: 18px;
      font-weight: bold;
      color: #495057;
      margin-bottom: 5px;
    }
    .equipment-details {
      font-size: 14px;
      color: #6c757d;
    }
    .status-badge {
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.875em;
      font-weight: 500;
    }
    .status-bueno {
      background-color: #d4edda;
      color: #155724;
    }
    .status-malo {
      background-color: #f8d7da;
      color: #721c24;
    }
    .status-nd {
      background-color: #e2e3e5;
      color: #495057;
    }
    .grado-a {
      background-color: #d4edda;
      color: #155724;
    }
    .grado-b {
      background-color: #fff3cd;
      color: #856404;
    }
    .grado-c {
      background-color: #f8d7da;
      color: #721c24;
    }
    .grado-scrap {
      background-color: #721c24;
      color: #f8d7da;
    }
    .grado-nd {
      background-color: #e2e3e5;
      color: #495057;
    }
    .alert {
      padding: 12px 15px;
      margin-bottom: 15px;
      border-radius: 4px;
      border: 1px solid transparent;
    }
    .alert-success {
      background-color: #d4edda;
      border-color: #c3e6cb;
      color: #155724;
    }
    .alert-danger {
      background-color: #f8d7da;
      border-color: #f5c6cb;
      color: #721c24;
    }
    .alert-warning {
      background-color: #fff3cd;
      border-color: #ffeaa7;
      color: #856404;
    }
    .btn-container {
      display: flex;
      gap: 10px;
      margin-top: 20px;
      justify-content: center;
    }
    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      font-weight: 500;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }
    .btn-primary {
      background: #007bff;
      color: white;
    }
    .btn-secondary {
      background: #6c757d;
      color: white;
    }
    .btn-success {
      background: #28a745;
      color: white;
    }
    .btn:hover {
      opacity: 0.9;
      transform: translateY(-1px);
    }
    .main-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    .top-navbar {
      background: linear-gradient(135deg, #542965 0%, #9b59b6 100%);
      padding: 15px 20px;
      margin-bottom: 20px;
      border-radius: 8px;
    }
    .navbar-brand {
      color: white !important;
      font-weight: bold;
      text-decoration: none;
    }
    .diagnosis-panel {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
    }
    .diagnosis-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      border-bottom: 1px solid #eee;
    }
    .diagnosis-label {
      font-weight: 500;
      color: #495057;
    }
    .guia-grados {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
    }
    .guia-grados h5 {
      color: #495057;
      margin-bottom: 10px;
    }
    .guia-grados ul {
      margin-bottom: 0;
      padding-left: 20px;
    }
    .guia-grados li {
      margin-bottom: 5px;
      color: #6c757d;
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
          <i class="material-icons" style="margin-right: 8px;">palette</i>
          🎨 DIAGNÓSTICO ESTÉTICO | <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'USUARIO'); ?>
        </a>
      </div>
    </div>
    <!-- Mensajes de alerta -->
    <?php if (!empty($mensaje)): ?>
      <?php echo $mensaje; ?>
    <?php endif; ?>
    <!-- Guía de Grados Estéticos -->
    <div class="guia-grados">
      <h5><i class="material-icons" style="margin-right: 8px;">info</i>Guía de Grados Estéticos</h5>
      <ul>
        <li><strong>Grado A:</strong> Excelente estado, sin rayones visibles, carcasa impecable</li>
        <li><strong>Grado B:</strong> Buen estado, rayones menores, carcasa en buen estado</li>
        <li><strong>Grado C:</strong> Estado regular, rayones visibles, carcasa aceptable</li>
        <li><strong>SCRAP:</strong> Estado muy deteriorado, no apto para venta</li>
      </ul>
    </div>
    <!-- Lista de Equipos Pendientes -->
    <!-- Reemplaza la sección de "Lista de Equipos Pendientes" en tu HTML -->
    <div class="form-section">
      <div class="section-title">
        <div class="card-icon">📋</div>
        <h4>Todos los Equipos Disponibles para Diagnóstico Estético</h4>
      </div>
      <?php if (empty($equipos_pendientes)): ?>
        <div class="alert alert-info">
          ℹ️ No hay equipos disponibles para diagnóstico estético en este momento.
        </div>
      <?php else: ?>
        <div class="alert alert-info">
          📌 <strong>Mostrando <?php echo count($equipos_pendientes); ?> equipos disponibles</strong> - Cualquier técnico puede trabajar con estos equipos.
        </div>
        <div class="row">
          <?php foreach ($equipos_pendientes as $equipo): ?>
            <div class="col-md-6 col-lg-4">
              <div class="equipment-card" onclick="seleccionarEquipo(<?php echo $equipo['id']; ?>)">
                <div class="equipment-code"><?php echo htmlspecialchars($equipo['codigo_g'] ?? 'N/A'); ?></div>
                <div class="equipment-details">
                  <strong><?php echo htmlspecialchars(($equipo['marca'] ?? '') . ' ' . ($equipo['modelo'] ?? '')); ?></strong><br>
                  <small>Serial: <?php echo htmlspecialchars($equipo['serial'] ?? 'N/A'); ?></small><br>
                  <small>Ubicación: <?php echo htmlspecialchars($equipo['ubicacion'] ?? 'N/A'); ?></small><br>
                  <!-- Información del técnico asignado (si existe) -->
                  <?php if (!empty($equipo['tecnico_asignado'])): ?>
                    <small>Técnico: <?php echo htmlspecialchars($equipo['tecnico_asignado']); ?></small><br>
                  <?php endif; ?>
                  <!-- Estado actual del equipo -->
                  <span class="status-badge status-nd">
                    <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $equipo['disposicion'] ?? 'pendiente'))); ?>
                  </span>
                  <!-- Estado estético actual -->
                  <span class="status-badge <?php echo badgeClass($equipo['estado_estetico']); ?>">
                    Estético: <?php echo htmlspecialchars(ucfirst($equipo['estado_estetico'] ?? 'pendiente')); ?>
                  </span>
                  <!-- Indicadores de fallas si existen -->
                  <?php if (!empty($equipo['falla_electrica']) && $equipo['falla_electrica'] === 'si'): ?>
                    <br><small class="text-warning">⚡ Tiene falla eléctrica</small>
                  <?php endif; ?>
                  <?php if (!empty($equipo['falla_estetica']) && $equipo['falla_estetica'] === 'si'): ?>
                    <br><small class="text-danger">🎨 Tiene falla estética</small>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <!-- Formulario de Diagnóstico Estético -->
    <?php if ($equipo_seleccionado): ?>
      <!-- Panel de Diagnóstico Actual -->
      <div class="diagnosis-panel">
        <div class="section-title">
          <div class="card-icon">📋</div>
          <h3>Equipo Seleccionado: <?php echo htmlspecialchars($equipo_seleccionado['codigo_g']); ?></h3>
        </div>
        <div class="diagnosis-item">
          <span class="diagnosis-label">Marca/Modelo</span>
          <span><strong><?php echo htmlspecialchars(($equipo_seleccionado['marca'] ?? '') . ' ' . ($equipo_seleccionado['modelo'] ?? '')); ?></strong></span>
        </div>
        <div class="diagnosis-item">
          <span class="diagnosis-label">Serial</span>
          <span><?php echo htmlspecialchars($equipo_seleccionado['serial'] ?? 'N/A'); ?></span>
        </div>
        <div class="diagnosis-item">
          <span class="diagnosis-label">Ubicación</span>
          <span><?php echo htmlspecialchars($equipo_seleccionado['ubicacion'] ?? 'N/A'); ?></span>
        </div>
        <div class="diagnosis-item">
          <span class="diagnosis-label">Disposición Actual</span>
          <span class="status-badge status-nd"><?php echo htmlspecialchars(ucfirst($equipo_seleccionado['disposicion'] ?? 'N/A')); ?></span>
        </div>
        <?php if ($diagnostico_ultimo): ?>
          <div class="diagnosis-item">
            <span class="diagnosis-label">Último Diagnóstico Estético</span>
            <span><?php echo htmlspecialchars((new DateTime($diagnostico_ultimo['fecha_registro']))->format('d/m/Y H:i')); ?></span>
          </div>
          <div class="diagnosis-item">
            <span class="diagnosis-label">Estado Anterior</span>
            <span class="status-badge <?php echo badgeClass($diagnostico_ultimo['estado_final']); ?>">
              <?php echo htmlspecialchars(ucfirst($diagnostico_ultimo['estado_final'])); ?>
            </span>
          </div>
          <div class="diagnosis-item">
            <span class="diagnosis-label">Grado Anterior</span>
            <span class="status-badge <?php echo gradoBadgeClass($diagnostico_ultimo['grado_asignado']); ?>">
              <?php echo htmlspecialchars($diagnostico_ultimo['grado_asignado']); ?>
            </span>
          </div>
        <?php endif; ?>
      </div>
      <!-- Formulario -->
      <form method="POST" class="form-section">
        <div class="section-title">
          <div class="card-icon">🎨</div>
          <h4>Nuevo Diagnóstico Estético</h4>
        </div>
        <input type="hidden" name="inventario_id" value="<?php echo $equipo_seleccionado['id']; ?>">
        <div class="form-grid">
          <div class="form-group">
            <label for="estado_carcasa">Estado de la Carcasa</label>
            <select id="estado_carcasa" name="estado_carcasa" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="EXCELENTE">EXCELENTE - Sin rayones</option>
              <option value="BUENO">BUENO - Rayones menores</option>
              <option value="REGULAR">REGULAR - Rayones visibles</option>
              <option value="MALO">MALO - Muchos rayones</option>
              <option value="N/D">N/D</option>
            </select>
          </div>
          <div class="form-group">
            <label for="estado_pantalla_fisica">Estado Físico de la Pantalla</label>
            <select id="estado_pantalla_fisica" name="estado_pantalla_fisica" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="EXCELENTE">EXCELENTE - Sin rayones</option>
              <option value="BUENO">BUENO - Rayones menores</option>
              <option value="REGULAR">REGULAR - Rayones visibles</option>
              <option value="MALO">MALO - Muchos rayones</option>
              <option value="N/D">N/D</option>
            </select>
          </div>
          <div class="form-group">
            <label for="estado_teclado_fisico">Estado Físico del Teclado</label>
            <select id="estado_teclado_fisico" name="estado_teclado_fisico" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="EXCELENTE">EXCELENTE - Sin desgaste</option>
              <option value="BUENO">BUENO - Desgaste menor</option>
              <option value="REGULAR">REGULAR - Desgaste visible</option>
              <option value="MALO">MALO - Mucho desgaste</option>
              <option value="N/D">N/D</option>
            </select>
          </div>
          <div class="form-group">
            <label for="grado_asignado">Grado Estético Asignado</label>
            <select id="grado_asignado" name="grado_asignado" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="A">A - Excelente</option>
              <option value="B">B - Bueno</option>
              <option value="C">C - Regular</option>
              <option value="SCRAP">SCRAP - No apto</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="rayones_golpes">Descripción de Rayones y Golpes</label>
          <textarea id="rayones_golpes" name="rayones_golpes" rows="3" class="form-control"
            placeholder="Describe detalladamente los rayones, golpes o daños físicos encontrados..."></textarea>
        </div>
        <div class="form-group">
          <label for="limpieza_realizada">¿Se Realizó Limpieza?</label>
          <select id="limpieza_realizada" name="limpieza_realizada" class="form-control" required>
            <option value="">-- Seleccionar --</option>
            <option value="si">SÍ - Limpieza completa realizada</option>
            <option value="parcial">PARCIAL - Limpieza básica</option>
            <option value="no">NO - No se requirió limpieza</option>
          </select>
        </div>
        <div class="form-group">
          <label for="partes_reemplazadas">Partes Reemplazadas</label>
          <textarea id="partes_reemplazadas" name="partes_reemplazadas" rows="3" class="form-control"
            placeholder="Lista las partes que fueron reemplazadas por daños estéticos..."></textarea>
        </div>
        <div class="form-group">
          <label for="estado_final">Estado Final</label>
          <select id="estado_final" name="estado_final" class="form-control" required>
            <option value="">-- Seleccionar --</option>
            <option value="aprobado">APROBADO - Pasa a Control de Calidad</option>
            <option value="rechazado">RECHAZADO - Requiere Más Trabajo</option>
            <option value="requiere_revision">REQUIERE REVISIÓN - Necesita más trabajo estético</option>
          </select>
        </div>
        <div class="form-group">
          <label for="observaciones">Observaciones Adicionales</label>
          <textarea id="observaciones" name="observaciones" rows="3" class="form-control"
            placeholder="Observaciones adicionales del diagnóstico estético..."></textarea>
        </div>
        <div class="btn-container">
          <button type="submit" class="btn btn-success">
            <i class="material-icons" style="margin-right: 8px;">save</i>
            Guardar Diagnóstico Estético
          </button>
        </div>
      </form>
      <!-- Botones de navegación -->
      <div class="btn-container">
        <a href="?" class="btn btn-secondary">
          <i class="material-icons" style="margin-right: 8px;">list</i>
          Ver Todos los Equipos
        </a>
        <a href="../bodega/mostrar.php" class="btn btn-primary">
          <i class="material-icons" style="margin-right: 8px;">dashboard</i>
          Volver al Dashboard
        </a>
      </div>
    <?php else: ?>
      <div class="alert alert-info">
        <i class="material-icons" style="margin-right: 8px;">info</i>
        Selecciona un equipo de la lista para realizar el diagnóstico estético.
      </div>
    <?php endif; ?>
  </div>
  <!-- Scripts -->
  <script src="../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script>
    function seleccionarEquipo(equipoId) {
      // Remover selección anterior
      document.querySelectorAll('.equipment-card').forEach(card => {
        card.classList.remove('selected');
      });
      // Seleccionar nueva tarjeta
      event.currentTarget.classList.add('selected');
      // Redirigir al formulario
      window.location.href = '?id=' + equipoId;
    }
    // Pre-llenar formulario si hay diagnóstico anterior
    <?php if ($diagnostico_ultimo): ?>
      document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('estado_carcasa').value = '<?php echo htmlspecialchars($diagnostico_ultimo['estado_carcasa'] ?? ''); ?>';
        document.getElementById('estado_pantalla_fisica').value = '<?php echo htmlspecialchars($diagnostico_ultimo['estado_pantalla_fisica'] ?? ''); ?>';
        document.getElementById('estado_teclado_fisico').value = '<?php echo htmlspecialchars($diagnostico_ultimo['estado_teclado_fisico'] ?? ''); ?>';
        document.getElementById('grado_asignado').value = '<?php echo htmlspecialchars($diagnostico_ultimo['grado_asignado'] ?? ''); ?>';
        document.getElementById('rayones_golpes').value = '<?php echo htmlspecialchars($diagnostico_ultimo['rayones_golpes'] ?? ''); ?>';
        document.getElementById('limpieza_realizada').value = '<?php echo htmlspecialchars($diagnostico_ultimo['limpieza_realizada'] ?? ''); ?>';
        document.getElementById('partes_reemplazadas').value = '<?php echo htmlspecialchars($diagnostico_ultimo['partes_reemplazadas'] ?? ''); ?>';
        document.getElementById('observaciones').value = '<?php echo htmlspecialchars($diagnostico_ultimo['observaciones'] ?? ''); ?>';
      });
    <?php endif; ?>
  </script>
</body>
</html>