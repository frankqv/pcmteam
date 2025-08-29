<?php
// ingresar_m.php - Formulario Integrado de Limpieza y Mantenimiento
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
$diagnostico_ultimo = null;
$mantenimiento_ultimo = null;
$tecnicos = [];
$partesDisponibles = [];
$marcasUnicas = [];
$productosUnicos = [];

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
    // Último diagnóstico
    $stmt = $connect->prepare("SELECT * FROM bodega_diagnosticos WHERE inventario_id = ? ORDER BY fecha_diagnostico DESC LIMIT 1");
    $stmt->execute([$inventario_id]);
    $diagnostico_ultimo = $stmt->fetch(PDO::FETCH_ASSOC);
    // Último mantenimiento
    $stmt = $connect->prepare("SELECT * FROM bodega_mantenimiento WHERE inventario_id = ? ORDER BY fecha_registro DESC LIMIT 1");
    $stmt->execute([$inventario_id]);
    $mantenimiento_ultimo = $stmt->fetch(PDO::FETCH_ASSOC);
  }
// Técnicos
  $stmt = $connect->query("SELECT id, nombre FROM usuarios WHERE rol IN (5,6) ORDER BY nombre");
  $tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Partes disponibles
  $stmt = $connect->prepare("SELECT id, caja, cantidad, marca, referencia, producto, condicion, precio, detalles, codigo, serial FROM bodega_partes WHERE cantidad > 0 ORDER BY marca, referencia");
  $stmt->execute();
  $partesDisponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($partesDisponibles as $p) {
    if (!empty($p['marca']) && !in_array($p['marca'], $marcasUnicas))
      $marcasUnicas[] = $p['marca'];
    if (!empty($p['producto']) && !in_array(strtolower($p['producto']), array_map('strtolower', $productosUnicos)))
      $productosUnicos[] = $p['producto'];
  }
  sort($marcasUnicas);
  sort($productosUnicos);
} catch (Exception $e) {
  error_log("Error carga inicial: " . $e->getMessage());
  $mensaje .= "<div class='alert alert-warning'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $connect->beginTransaction();
    // 1. Actualizar datos del equipo si se enviaron
    if (isset($_POST['actualizar_equipo']) && $inventario_id > 0) {
      $stmt = $connect->prepare("
        UPDATE bodega_inventario 
        SET modelo = ?, procesador = ?, ram = ?, disco = ?, pulgadas = ?, grado = ?, fecha_modificacion = NOW()
        WHERE id = ?
      ");
      $stmt->execute([
        $_POST['edit_modelo'] ?? '',
        $_POST['edit_procesador'] ?? '',
        $_POST['edit_ram'] ?? '',
        $_POST['edit_disco'] ?? '',
        $_POST['edit_pulgadas'] ?? '',
        $_POST['edit_grado'] ?? '',
        $inventario_id
      ]);
      $mensaje .= "<div class='alert alert-success'>✅ Datos del equipo actualizados</div>";
    }
    // 2. Guardar nuevo diagnóstico técnico
    if (isset($_POST['guardar_diagnostico']) && $inventario_id > 0) {
      $compPortatil = $_POST['componentes_portatil'] ?? [];
      $compComputador = $_POST['componentes_computador'] ?? [];
      $vidaUtilDisco = trim($_POST['vida_util_disco'] ?? '');
      $observacionesDiag = trim($_POST['observaciones_diagnostico'] ?? '');
      $estadoRep = trim($_POST['estado_reparacion'] ?? 'aprobado');
        $camara = $compPortatil['Camara'] ?? 'N/D';
      $teclado = $compPortatil['Teclado'] ?? 'N/D';
      $parlantes = $compPortatil['Parlantes'] ?? 'N/D';
      $bateria = $compPortatil['Bateria'] ?? 'N/D';
      $microfono = $compPortatil['Microfono'] ?? 'N/D';
      $pantalla = $compPortatil['Pantalla'] ?? 'N/D';
        $seleccionadorDisco = $_POST['selecionador_Disco'] ?? [];
      $discoEst = $seleccionadorDisco['Disco'] ?? 'N/D';
      $puertosJSON = json_encode($compComputador, JSON_UNESCAPED_UNICODE);
      $discoTexto = "Estado: $discoEst; Vida útil: $vidaUtilDisco%";
        $stmt = $connect->prepare("
        INSERT INTO bodega_diagnosticos 
        (inventario_id, tecnico_id, camara, teclado, parlantes, bateria, microfono, pantalla, puertos, disco, estado_reparacion, observaciones)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      ");
      $stmt->execute([
        $inventario_id, $_SESSION['id'], $camara, $teclado, $parlantes, $bateria, 
        $microfono, $pantalla, $puertosJSON, $discoTexto, $estadoRep, $observacionesDiag
      ]);
      $mensaje .= "<div class='alert alert-success'>✅ Nuevo diagnóstico técnico guardado</div>";
    }
    // 3. Guardar mantenimiento y limpieza
    if (isset($_POST['guardar_mantenimiento']) && $inventario_id > 0) {
      // Datos de limpieza y mantenimiento
      $limpieza_electronico = $_POST['limpieza_electronico'] ?? 'pendiente';
      $obs_limpieza = $_POST['obs_limpieza'] ?? '';
      $mantenimiento_crema = $_POST['mantenimiento_crema'] ?? 'pendiente';
      $obs_crema = $_POST['obs_crema'] ?? '';
        // Cambio de piezas
      $cambio_piezas = $_POST['cambio_piezas'] ?? 'no';
      $piezas_solicitadas = '';
      if ($cambio_piezas === 'si') {
        $piezas_data = [
          'detalle' => $_POST['detalle_solicitud'] ?? '',
          'cantidad' => $_POST['cantidad_solicitada'] ?? '',
          'codigo_equipo' => $_POST['codigo_equipo'] ?? '',
          'serial_parte' => $_POST['serial_parte'] ?? '',
          'marca_parte' => $_POST['marca_parte'] ?? '',
          'nivel_urgencia' => $_POST['nivel_urgencia'] ?? '',
          'referencia_parte' => $_POST['referencia_parte'] ?? '',
          'ubicacion_pieza' => $_POST['ubicacion_pieza'] ?? ''
        ];
        $piezas_solicitadas = json_encode($piezas_data, JSON_UNESCAPED_UNICODE);
      }
        // Proceso de reconstrucción
      $proceso_reconstruccion = $_POST['proceso_reconstruccion'] ?? 'no';
      $parte_reconstruida = $_POST['parte_reconstruida'] ?? '';
        // Remisión a otra área
      $remite_otra_area = $_POST['remite_otra_area'] ?? 'no';
      $area_remite = $_POST['area_remite'] ?? '';
        // Fallas
      $falla_electrica = $_POST['falla_electrica'] ?? 'no';
      $detalle_falla_electrica = $_POST['detalle_falla_electrica'] ?? '';
      $falla_estetica = $_POST['falla_estetica'] ?? 'no';
      $detalle_falla_estetica = $_POST['detalle_falla_estetica'] ?? '';
        // Observaciones
      $proceso_electronico = $_POST['proceso_electronico'] ?? '';
      $observaciones_globales = $_POST['observaciones_globales'] ?? '';
        $stmt = $connect->prepare("
        INSERT INTO bodega_mantenimiento 
        (inventario_id, tecnico_id, usuario_registro, limpieza_electronico, observaciones_limpieza_electronico,
        mantenimiento_crema_disciplinaria, observaciones_mantenimiento_crema, cambio_piezas, 
        piezas_solicitadas_cambiadas, proceso_reconstruccion, parte_reconstruida, remite_otra_area, 
        area_remite, falla_electrica, detalle_falla_electrica, falla_estetica, detalle_falla_estetica,
        proceso_electronico, observaciones_globales, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'realizado')
      ");
      $stmt->execute([
        $inventario_id, $_SESSION['id'], $_SESSION['id'], $limpieza_electronico, $obs_limpieza,
        $mantenimiento_crema, $obs_crema, $cambio_piezas, $piezas_solicitadas, $proceso_reconstruccion,
        $parte_reconstruida, $remite_otra_area, $area_remite, $falla_electrica, $detalle_falla_electrica,
        $falla_estetica, $detalle_falla_estetica, $proceso_electronico, $observaciones_globales
      ]);
        // Si hay solicitud de pieza, guardar en tabla separada
      if ($cambio_piezas === 'si' && !empty($_POST['detalle_solicitud'])) {
        $stmt = $connect->prepare("
          INSERT INTO bodega_solicitud_parte 
          (inventario_id, detalle_solicitud, cantidad_solicitada, codigo_equipo, serial_parte, 
          marca_parte, nivel_urgencia, referencia_parte, ubicacion_pieza, id_tecnico, usuario_solicitante)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
          $inventario_id, $_POST['detalle_solicitud'], $_POST['cantidad_solicitada'],
          $_POST['codigo_equipo'], $_POST['serial_parte'], $_POST['marca_parte'],
          $_POST['nivel_urgencia'], $_POST['referencia_parte'], $_POST['ubicacion_pieza'],
          $_SESSION['id'], $_SESSION['id']
        ]);
      }
        $mensaje .= "<div class='alert alert-success'>✅ Proceso de limpieza y mantenimiento guardado correctamente</div>";
    }
    $connect->commit();
    // Recargar datos después de guardar
    if ($inventario_id > 0) {
      $stmt = $connect->prepare("SELECT * FROM bodega_inventario WHERE id = ? LIMIT 1");
      $stmt->execute([$inventario_id]);
      $inventario = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $connect->prepare("SELECT * FROM bodega_diagnosticos WHERE inventario_id = ? ORDER BY fecha_diagnostico DESC LIMIT 1");
      $stmt->execute([$inventario_id]);
      $diagnostico_ultimo = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $connect->prepare("SELECT * FROM bodega_mantenimiento WHERE inventario_id = ? ORDER BY fecha_registro DESC LIMIT 1");
      $stmt->execute([$inventario_id]);
      $mantenimiento_ultimo = $stmt->fetch(PDO::FETCH_ASSOC);
    }
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

// Obtener técnico responsable del último diagnóstico
$tecnicoResponsable = 'No asignado';
if (!empty($diagnostico_ultimo['tecnico_id'])) {
  $stmtTecnico = $connect->prepare("SELECT nombre FROM usuarios WHERE id = ? LIMIT 1");
  $stmtTecnico->execute([$diagnostico_ultimo['tecnico_id']]);
  $rowTecnico = $stmtTecnico->fetch(PDO::FETCH_ASSOC);
  if ($rowTecnico) {
    $tecnicoResponsable = $rowTecnico['nombre'];
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Limpieza y Mantenimiento - Sistema Integrado</title>
  <link rel="stylesheet" href="../../backend/css/ingesar.css" />
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
      padding-bottom: 10px;
      border-bottom: 2px solid #f0f0f0;
    }
    .card-icon {
      font-size: 24px;
      margin-right: 10px;
    }
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 15px;
    }
    .hidden {
  display: none;
  max-height: 0;
  opacity: 0;
  overflow: hidden;
  transition: max-height .32s ease, opacity .32s ease, transform .32s ease;
}

.activo123 {
  display: block;
  max-height: 2000px;
  opacity: 1;
  transform: translateY(0);
  margin-top: 15px;
  padding: 15px;
  background-color: #f8f9fa;
  border-left: 4px solid #667eea;
  border-radius: 8px;
  box-shadow: 0 6px 18px rgba(102,126,234,0.06);
  transition: max-height .32s ease, opacity .32s ease, transform .32s ease;
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
    .status-badge {
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.875em;
      font-weight: 500;
    }
    .status-bueno { background-color: #d4edda; color: #155724; }
    .status-malo { background-color: #f8d7da; color: #721c24; }
    .status-nd { background-color: #e2e3e5; color: #495057; }
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
    .equipment-info {
        
      background: linear-gradient(135deg, #e0ffcd 0%,rgb(197, 228, 177) 100%);
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .equipment-code {
      font-size: 24px;
      font-weight: bold;
      color: #495057;
      margin-bottom: 5px;
    }
    .equipment-description {
      font-size: 16px;
      color: #6c757d;
      margin-bottom: 15px;
    }
    .equipment-details {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 10px;
    }
    .detail-item {
      display: flex;
      flex-direction: column;
    }
    .detail-label {
      font-size: 12px;
      font-weight: 500;
      color: #6c757d;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .detail-value {
      font-size: 14px;
      color: #495057;
      font-weight: 500;
    }
    .main-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    .top-navbar {
    background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
    padding: 10px 20px;
    margin-bottom: 20px;
    border-radius: 8px;
    }
    .navbar-brand {
      color: white !important;
      font-weight: bold;
      text-decoration: none;
    }
    .partes-table {
      max-height: 400px;
      overflow-y: auto;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    .table-sm th, .table-sm td {
      padding: 8px;
      font-size: 0.875em;
    }
    .filtros-container {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      border: 1px solid #dee2e6;
    }
    .filtros-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      align-items: end;
    }
    .filtro-grupo label {
      display: block;
      font-weight: 500;
      margin-bottom: 5px;
      color: #495057;
      font-size: 0.9em;
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
    <!--top-navbar-->  <div class="top-navbar">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <i class="material-icons" style="margin-right: 8px;">build</i>
        🔧 LIMPIEZA Y MANTENIMIENTO | <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'USUARIO'); ?>
      </a>
    </div>
  </div>  
    
    <!-- Mensajes de alerta -->
    <?php if (!empty($mensaje)): ?>
      <?php echo $mensaje; ?>
    <?php endif; ?>     <?php if (!$inventario && $inventario_id > 0): ?>
      <div class="alert alert-danger">
        ❌ No se encontró el equipo con ID: <?php echo $inventario_id; ?>
        <br><a href="?" class="btn btn-secondary">Seleccionar otro equipo</a>
      </div>
    <?php elseif (!$inventario_id): ?>
      <div class="alert alert-warning">
        ⚠️ Selecciona un equipo para continuar con el proceso de limpieza y mantenimiento.
        <br><small>Agrega <code>?id=NUMERO</code> a la URL para seleccionar un equipo específico.</small>
      </div>
    <?php else: ?>
        <!-- Panel de Diagnóstico Actual -->
      <div class="diagnosis-panel">
        <div class="section-title">
          <div class="card-icon">📋</div>
          <h3>Resultados del TRIAGE Actual</h3>
        </div>
        <div class="diagnosis-item">
          <span class="diagnosis-label">Equipo</span>
          <span><strong><?= htmlspecialchars($inventario['codigo_g'] . ' — ' . ($inventario['marca'] ?? '') . ' ' . ($inventario['modelo'] ?? '')) ?></strong></span>
        </div>
        <div class="diagnosis-item">
          <span class="diagnosis-label">Fecha Último Diagnóstico</span>
          <span><?= htmlspecialchars(isset($diagnostico_ultimo['fecha_diagnostico']) ? (new DateTime($diagnostico_ultimo['fecha_diagnostico']))->format('d/m/Y H:i') : 'N/D') ?></span>
        </div>
        <?php
        $fields = [
          'Cámara' => $diagnostico_ultimo['camara'] ?? 'N/D',
          'Teclado' => $diagnostico_ultimo['teclado'] ?? 'N/D',
          'Parlantes' => $diagnostico_ultimo['parlantes'] ?? 'N/D',
          'Batería' => $diagnostico_ultimo['bateria'] ?? 'N/D',
          'Micrófono' => $diagnostico_ultimo['microfono'] ?? 'N/D',
          'Pantalla' => $diagnostico_ultimo['pantalla'] ?? 'N/D'
        ];
        foreach ($fields as $label => $val) {
          $cls = badgeClass((string) $val);
          echo "<div class='diagnosis-item'><span class='diagnosis-label'>{$label}</span><span class='status-badge {$cls}'>" . htmlspecialchars(strtoupper($val)) . "</span></div>";
        }
        $estado = $diagnostico_ultimo['estado_reparacion'] ?? 'N/D';
        ?>
        <div class="diagnosis-item">
          <span class="diagnosis-label">Estado</span>
          <span class="status-badge <?= badgeClass($estado) ?>"><?= htmlspecialchars(strtoupper($estado)) ?></span>
        </div>
        <div class="diagnosis-item">
          <span class="diagnosis-label">Responsable Último TRIAGE</span>
          <span class="status-badge status-nd"><?= htmlspecialchars($tecnicoResponsable) ?></span>
        </div>
      </div>       <!-- Información del Equipo -->
      <div class="equipment-info">
        <div class="equipment-code"><?php echo htmlspecialchars($inventario['codigo_g'] ?? 'N/A'); ?></div>
        <div class="equipment-description"><?php echo htmlspecialchars(($inventario['marca'] ?? '') . ' ' . ($inventario['modelo'] ?? '') . ' - ' . ($inventario['procesador'] ?? '')); ?></div>
        <div class="equipment-details">
          <div class="detail-item">
            <span class="detail-label">Serial</span>
            <span class="detail-value"><?php echo htmlspecialchars($inventario['serial'] ?? 'N/A'); ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Ubicación</span>
            <span class="detail-value"><?php echo htmlspecialchars($inventario['ubicacion'] ?? 'N/A'); ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Posición</span>
            <span class="detail-value"><?php echo htmlspecialchars($inventario['posicion'] ?? 'N/A'); ?></span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Lote</span>
            <span class="detail-value"><?php echo htmlspecialchars($inventario['lote'] ?? 'N/A'); ?></span>
          </div>
        </div>
      </div>       <!-- Formulario Principal -->
      <form method="POST" id="mainForm">
        <input type="hidden" name="inventario_id" value="<?php echo $inventario_id; ?>">
            <!-- 1. Edición de Datos del Equipo -->
        <div class="form-section">
          <div class="section-title">
            <div class="card-icon">✏️</div>
            <h4>Editar Datos del Equipo</h4>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label for="edit_modelo">Modelo</label>
              <input type="text" id="edit_modelo" name="edit_modelo" class="form-control" 
                   value="<?php echo htmlspecialchars($inventario['modelo'] ?? ''); ?>" 
                   placeholder="Ej: Dell Latitude 3420">
            </div>
            <div class="form-group">
              <label for="edit_procesador">Procesador</label>
              <input type="text" id="edit_procesador" name="edit_procesador" class="form-control" 
                   value="<?php echo htmlspecialchars($inventario['procesador'] ?? ''); ?>" 
                   placeholder="Ej: Intel i5 11th Gen">
            </div>
            <div class="form-group">
              <label for="edit_ram">RAM</label>
              <input type="text" id="edit_ram" name="edit_ram" class="form-control" 
                   value="<?php echo htmlspecialchars($inventario['ram'] ?? ''); ?>" 
                   placeholder="Ej: 8GB, 16GB">
            </div>
            <div class="form-group">
              <label for="edit_disco">Disco</label>
              <input type="text" id="edit_disco" name="edit_disco" class="form-control" 
                   value="<?php echo htmlspecialchars($inventario['disco'] ?? ''); ?>" 
                   placeholder="Ej: 256GB SSD">
            </div>
            <div class="form-group">
              <label for="edit_pulgadas">Pulgadas</label>
              <input type="text" id="edit_pulgadas" name="edit_pulgadas" class="form-control" 
                   value="<?php echo htmlspecialchars($inventario['pulgadas'] ?? ''); ?>" 
                   placeholder="Ej: 14, 15.6">
            </div>
            <div class="form-group">
              <label for="edit_grado">Grado</label>
              <select id="edit_grado" name="edit_grado" class="form-control">
                <option value="">-- Seleccionar --</option>
                <option value="A" <?php echo ($inventario['grado'] ?? '') === 'A' ? 'selected' : ''; ?>>A - Excelente</option>
                <option value="B" <?php echo ($inventario['grado'] ?? '') === 'B' ? 'selected' : ''; ?>>B - Bueno</option>
                <option value="C" <?php echo ($inventario['grado'] ?? '') === 'C' ? 'selected' : ''; ?>>C - Regular</option>
                <option value="SCRAP" <?php echo ($inventario['grado'] ?? '') === 'SCRAP' ? 'selected' : ''; ?>>SCRAP</option>
              </select>
            </div>
          </div>
          <div class="btn-container">
            <button type="submit" name="actualizar_equipo" class="btn btn-secondary">
              Actualizar Datos del Equipo
            </button>
          </div>
        </div>         <!-- 2. Nuevo Diagnóstico Técnico -->
        <div class="form-section">
          <div class="section-title">
            <div class="card-icon">🔍</div>
            <h4>Nuevo Diagnóstico Técnico (TRIAGE 2)</h4>
          </div>
          <div class="form-group">
            <label for="tecnico_diagnostico">Responsable</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['nombre']); ?>" disabled>
            <input type="hidden" name="tecnico_diagnostico" value="<?php echo $_SESSION['id']; ?>">
          </div>
                <h5>Componentes (Portátil)</h5>
          <div class="form-grid">
            <?php 
            $componentes_portatil = ['Camara', 'Teclado', 'Parlantes', 'Bateria', 'Microfono', 'Pantalla'];
            foreach ($componentes_portatil as $comp): ?>
              <div class="form-group">
                <label><?= htmlspecialchars($comp) ?></label>
                <select name="componentes_portatil[<?= htmlspecialchars($comp) ?>]" class="form-control">
                  <option value="BUENO" selected>BUENO</option>
                  <option value="MALO">MALO</option>
                  <option value="N/D">N/D</option>
                </select>
              </div>
            <?php endforeach; ?>
          </div>           <div class="form-group">
            <div class="form-grid">
              <div class="form-group">
                <label for="vida_util_disco">Vida útil disco (%)</label>
                <div class="input-group">
                  <input type="number" id="vida_util_disco" name="vida_util_disco" class="form-control" 
                      min="0" max="100" placeholder="95" >
                  <div class="input-group-append">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="select-Disco">Estado del Disco</label>
                <select name="selecionador_Disco[Disco]" id="select-Disco" class="form-control" disabled>
                  <option value="BUENO">BUENO</option>
                  <option value="REGULAR">REGULAR</option>
                  <option value="MALO">MALO</option>
                  <option value="N/D">N/D</option>
                </select>
                <small class="text-muted">
                   <span id="diskIndicator" style="display:inline-block;width:12px;height:12px;border-radius:50%;margin-right:5px;background:#6c757d;"></span>
                  Bueno (70-100%), Regular (50-69%), Malo (0-49%)
                </small>
              </div>
            </div>
          </div>           <h5>Puertos (Computador de Mesa)</h5>
          <div class="form-grid">
            <?php 
            $componentes_computador = ['VGA', 'DVI', 'HDMI', 'USB', 'Red'];
            foreach ($componentes_computador as $comp): ?>
              <div class="form-group">
                <label><?= htmlspecialchars($comp) ?></label>
                <select name="componentes_computador[<?= htmlspecialchars($comp) ?>]" class="form-control">
                  <option value="BUENO" selected>BUENO</option>
                  <option value="MALO">MALO</option>
                  <option value="N/D">N/D</option>
                </select>
              </div>
            <?php endforeach; ?>
          </div>           <div class="form-group">
            <label>Estado reparación</label>
            <select name="estado_reparacion" class="form-control">
              <option value="aprobado" selected>APROBADO</option>
              <option value="falla_mecanica">FALLA MECÁNICA</option>
              <option value="falla_electrica">FALLA ELÉCTRICA</option>
              <option value="reparacion_cosmetica">REPARACIÓN COSMÉTICA</option>
            </select>
          </div>           <div class="form-group">
            <label>Observaciones del Diagnóstico</label>
            <textarea name="observaciones_diagnostico" rows="3" class="form-control"></textarea>
          </div>           <div class="btn-container">
            <button type="submit" name="guardar_diagnostico" class="btn btn-primary">
              Guardar Nuevo Diagnóstico
            </button>
          </div>
        </div>         <!-- 3. Limpieza y Mantenimiento -->
        <div class="form-section">
          <div class="section-title">
            <div class="card-icon">🧽</div>
            <h4>Limpieza y Mantenimiento</h4>
          </div>
                <div class="form-grid">
            <div class="form-group">
              <label for="limpieza_electronico">Limpieza Electrónica</label>
              <select id="limpieza_electronico" name="limpieza_electronico" class="form-control">
                <option value="pendiente" selected>Pendiente</option>
                <option value="realizada">Realizada</option>
                <option value="no_aplica">No Aplica</option>
              </select>
              <div id="obs_limpieza_block" class="hidden" style="margin-top: 10px;">
                <label for="obs_limpieza">Observaciones Limpieza</label>
                <textarea id="obs_limpieza" name="obs_limpieza" rows="2" class="form-control"></textarea>
              </div>
            </div>             <div class="form-group">
              <label for="mantenimiento_crema">Mantenimiento (Crema Térmica)</label>
              <select id="mantenimiento_crema" name="mantenimiento_crema" class="form-control">
                <option value="pendiente" selected>Pendiente</option>
                <option value="realizada">Realizada</option>
                <option value="no_aplica">No Aplica</option>
              </select>
              <div id="obs_crema_block" class="hidden" style="margin-top: 10px;">
                <label for="obs_crema">Observaciones Crema Térmica</label>
                <textarea id="obs_crema" name="obs_crema" rows="2" class="form-control"></textarea>
              </div>
            </div>
          </div>           <!-- Cambio de Piezas -->
          <div class="form-group">
            <label for="cambio_piezas">¿Requiere Cambio de Piezas?</label>
            <select id="cambio_piezas" name="cambio_piezas" class="form-control">
              <option value="no" selected>No</option>
              <option value="si">Sí</option>
            </select>
                    <div id="piezas_block" class="hidden" style="margin-top: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
              <h6>Solicitud de Pieza/Parte</h6>
              <div class="form-grid">
                <div class="form-group">
                  <label>Detalle de la Solicitud</label>
                  <input type="text" name="detalle_solicitud" class="form-control" placeholder="Descripción de la pieza necesaria">
                </div>
                <div class="form-group">
                  <label>Cantidad Solicitada</label>
                  <input type="number" name="cantidad_solicitada" class="form-control" min="1" placeholder="1">
                </div>
                <div class="form-group">
                  <label>Código del Equipo</label>
                  <input type="text" name="codigo_equipo" class="form-control" 
                    value="<?php echo htmlspecialchars($inventario['codigo_g'] ?? ''); ?>">
                </div>
                <div class="form-group">
                  <label>Serial de la Pieza</label>
                  <input type="text" name="serial_parte" class="form-control" placeholder="Serial de la pieza">
                </div>
                <div class="form-group">
                  <label>Marca de la Pieza</label>
                  <input type="text" name="marca_parte" class="form-control" placeholder="Marca">
                </div>
                <div class="form-group">
                  <label>Nivel de Urgencia</label>
                  <select name="nivel_urgencia" class="form-control">
                    <option value="Baja">Baja (7 días)</option>
                    <option value="Media">Media (2-3 días)</option>
                    <option value="Urgente">Urgente (24h)</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Referencia de la Pieza</label>
                  <input type="text" name="referencia_parte" class="form-control" placeholder="Referencia">
                </div>
                <div class="form-group">
                  <label>Ubicación de la Pieza</label>
                  <input type="text" name="ubicacion_pieza" class="form-control" placeholder="Ubicación en bodega">
                </div>
              </div>
            </div>
          </div>           <!-- Ver Partes Disponibles -->
          <div class="form-group">
            <label for="lista_partes_bodega">¿Ver listado de partes disponibles en bodega?</label>
            <select id="lista_partes_bodega" name="lista_partes_bodega" class="form-control">
              <option value="no" selected>No</option>
              <option value="si">Sí</option>
            </select>
                    <div id="partes_disponibles_container" class="hidden" style="margin-top: 15px;">
              <!-- Filtros -->
              <div class="filtros-container">
                <div class="filtros-row">
                  <div class="filtro-grupo">
                    <label for="filtro_marca">Filtrar por Marca:</label>
                    <select id="filtro_marca" class="form-control">
                      <option value="">Todas las marcas</option>
                      <?php foreach ($marcasUnicas as $marca): ?>
                        <option value="<?php echo htmlspecialchars($marca); ?>">
                          <?php echo htmlspecialchars($marca); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="filtro-grupo">
                    <label for="filtro_producto">Filtrar por Producto:</label>
                    <select id="filtro_producto" class="form-control">
                      <option value="">Todos los productos</option>
                      <?php foreach ($productosUnicos as $producto): ?>
                        <option value="<?php echo htmlspecialchars($producto); ?>">
                          <?php echo htmlspecialchars($producto); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="filtro-grupo">
                    <label for="filtro_busqueda">Buscar por Referencia:</label>
                    <input type="text" id="filtro_busqueda" class="form-control" placeholder="Escriba referencia...">
                  </div>
                  <div class="filtro-grupo">
                    <button type="button" id="btn_limpiar_filtros" class="btn btn-secondary">
                      Limpiar Filtros
                    </button>
                  </div>
                </div>
              </div>               <!-- Tabla de partes -->
              <div class="partes-table">
                <table class="table table-sm table-striped">
                  <thead>
                    <tr>
                      <th>Caja</th>
                      <th>Cantidad</th>
                      <th>Marca</th>
                      <th>Referencia</th>
                      <th>Producto</th>
                      <th>Condición</th>
                      <th>Precio</th>
                      <th>Acción</th>
                    </tr>
                  </thead>
                  <tbody id="tabla_partes_body">
                    <?php if (!empty($partesDisponibles)): ?>
                      <?php foreach ($partesDisponibles as $parte): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($parte['caja']); ?></td>
                          <td>
                            <span class="badge badge-<?php echo $parte['cantidad'] > 5 ? 'success' : ($parte['cantidad'] > 1 ? 'warning' : 'danger'); ?>">
                              <?php echo $parte['cantidad']; ?>
                            </span>
                          </td>
                          <td data-marca="<?php echo htmlspecialchars($parte['marca']); ?>">
                            <?php echo htmlspecialchars($parte['marca']); ?>
                          </td>
                          <td data-referencia="<?php echo htmlspecialchars($parte['referencia']); ?>">
                            <?php echo htmlspecialchars($parte['referencia']); ?>
                          </td>
                          <td data-producto="<?php echo htmlspecialchars($parte['producto']); ?>">
                            <?php echo htmlspecialchars($parte['producto']); ?>
                          </td>
                          <td>
                            <span class="badge badge-<?php echo $parte['condicion'] === 'Nuevo' ? 'primary' : 'secondary'; ?>">
                              <?php echo htmlspecialchars($parte['condicion']); ?>
                            </span>
                          </td>
                          <td>$<?php echo number_format($parte['precio'], 0, ',', '.'); ?></td>
                          <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="seleccionarParte(
                              <?php echo $parte['id']; ?>, 
                              '<?php echo htmlspecialchars($parte['referencia']); ?>',
                              '<?php echo htmlspecialchars($parte['marca']); ?>',
                              '<?php echo htmlspecialchars($parte['producto']); ?>'
                            )">
                              Seleccionar
                            </button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="8" class="text-center">No hay partes disponibles en bodega</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>           <!-- Proceso Reconstrucción -->
          <div class="form-group">
            <label for="proceso_reconstruccion">¿Requiere Proceso de Reconstrucción?</label>
            <select id="proceso_reconstruccion" name="proceso_reconstruccion" class="form-control">
              <option value="no" selected>No</option>
              <option value="si">Sí</option>
            </select>
            <div id="parte_block" class="hidden" style="margin-top: 10px;">
              <label for="parte_reconstruida">Parte Reconstruida</label>
              <input type="text" id="parte_reconstruida" name="parte_reconstruida" class="form-control" placeholder="Descripción de la parte reconstruida">
            </div>
          </div>           <!-- Fallas -->
          <div class="form-grid">
            <div class="form-group">
              <label for="falla_electrica">¿Falla Eléctrica?</label>
              <select id="falla_electrica" name="falla_electrica" class="form-control">
                <option value="no" selected>No</option>
                <option value="si">Sí</option>
              </select>
              <div id="detalle_falla_electrica_block" class="hidden" style="margin-top: 10px;">
                <label for="detalle_falla_electrica">Detalle de la Falla Eléctrica</label>
                <input type="text" id="detalle_falla_electrica" name="detalle_falla_electrica" class="form-control">
              </div>
            </div>             <div class="form-group">
              <label for="falla_estetica">¿Falla Estética?</label>
              <select id="falla_estetica" name="falla_estetica" class="form-control">
                <option value="no" selected>No</option>
                <option value="si">Sí</option>
              </select>
              <div id="falla_estetica_block" class="hidden" style="margin-top: 10px;">
                <label for="detalle_falla_estetica">Detalle de la Falla Estética</label>
                <input type="text" id="detalle_falla_estetica" name="detalle_falla_estetica" class="form-control">
              </div>
            </div>
          </div>           <div class="form-group">
            <!--<label for="proceso_electronico">Proceso Electrónico (Detalle)</label>
            <textarea id="proceso_electronico" name="proceso_electronico" rows="3" class="form-control" placeholder="Describe el proceso electrónico realizado"></textarea> -->
          </div>          <div class="form-group">
            <label for="observaciones_globales">Observaciones Globales</label>
            <textarea id="observaciones_globales" name="observaciones_globales" rows="4" class="form-control" placeholder="Observaciones generales del proceso de limpieza y mantenimiento"></textarea>
          </div>  
          
                    <!-- Remisión a otra área -->
                    <div class="form-group">
            <label for="remite_otra_area">¿Remite a Otra Área?</label>
            <select id="remite_otra_area" name="remite_otra_area" class="form-control">
              <option value="no" selected>No</option>
              <option value="si">Sí</option>
            </select>
            <div id="area_block" class="hidden" style="margin-top: 10px;">
              <label for="area_remite">Área a la que Remite</label>
              <select id="area_remite" name="area_remite" class="form-control">
                <option value="">-- Seleccionar --</option>
                <option value="bodega">Bodega</option>
                <option value="laboratorio">Laboratorio</option>
                <option value="control_calidad">QR | Control de Calidad</option>
                <option value="business room">business room</option>
              </select>
            </div>
          </div> 
          
          <div class="btn-container">
            <button type="submit" name="guardar_mantenimiento" class="btn btn-success">
              Guardar Limpieza y Mantenimiento
            </button>
          </div>
        </div>
      </form>       <!-- Botones de navegación -->
      <div class="btn-container">
      <a href="../../bodega/tr" class="btn btn-secondary">Seleccionar Otro Equipo</a>
        <!-- <a href="?" class="btn btn-secondary">Seleccionar Otro Equipo</a> -->
        <a href="../laboratorio/mostrar.php" class="btn btn-primary">Volver al Dashboard</a>
      </div>     <?php endif; ?>
  </div>   <!-- Scripts -->
  <script src="../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
<script>
    
    $(document).ready(function() {
      // Manejo del disco y vida útil
      const input = $('#vida_util_disco');
      const select = $('#select-Disco');
      const indicator = $('#diskIndicator');
        function setIndicator(state) {
        indicator.css('background', '#6c757d'); // default N/D (gris)
        if (state === 'good') indicator.css('background', '#28a745');
        else if (state === 'regular') indicator.css('background', '#ffc107');
        else if (state === 'bad') indicator.css('background', '#dc3545');
      }
        function updateFromValue(val) {
        if (val === '' || isNaN(val)) {
          select.val('N/D');
          setIndicator('nd');
          return;
        }
        let n = Math.max(0, Math.min(100, parseInt(val, 10)));
        if (n >= 70) { select.val('BUENO'); setIndicator('good'); }
        else if (n >= 50) { select.val('REGULAR'); setIndicator('regular'); }
        else { select.val('MALO'); setIndicator('bad'); }
      }
        // Inicializar
      updateFromValue(input.val());
        input.on('input', function() {
        this.value = this.value.toString().replace(/\D/g, '').slice(0, 3);
        if (this.value !== '' && parseInt(this.value, 10) > 100) this.value = '100';
        updateFromValue(this.value);
      });
        // Función genérica para toggle de elementos
function toggleField(element, targetId, showClass = 'activo123', hideClass = 'hidden') {
  const target = $('#' + targetId);
  target.removeClass(showClass + ' ' + hideClass)
        .addClass(element.value === 'realizada' || element.value === 'si' ? showClass : hideClass);
}

// Mostrar/ocultar campos condicionales
$('#limpieza_electronico').on('change', function() {
  toggleField(this, 'obs_limpieza_block');
});

$('#mantenimiento_crema').on('change', function() {
  toggleField(this, 'obs_crema_block');
});

$('#cambio_piezas').on('change', function() {
  toggleField(this, 'piezas_block');
});

$('#proceso_reconstruccion').on('change', function() {
  toggleField(this, 'parte_block');
});

$('#remite_otra_area').on('change', function() {
  toggleField(this, 'area_block');
});

$('#falla_electrica').on('change', function() {
  toggleField(this, 'detalle_falla_electrica_block');
});

$('#falla_estetica').on('change', function() {
  toggleField(this, 'falla_estetica_block');
});

// Listado de partes disponibles
$('#lista_partes_bodega').on('change', function() {
  toggleField(this, 'partes_disponibles_container');
});
        // Filtros de partes
      $('#filtro_marca, #filtro_producto, #filtro_busqueda').on('change keyup', function() {
        filtrarPartes();
      });
        $('#btn_limpiar_filtros').on('click', function() {
        $('#filtro_marca').val('');
        $('#filtro_producto').val('');
        $('#filtro_busqueda').val('');
        filtrarPartes();
      });
        function filtrarPartes() {
        const filtroMarca = $('#filtro_marca').val().toLowerCase();
        const filtroProducto = $('#filtro_producto').val().toLowerCase();
        const filtroBusqueda = $('#filtro_busqueda').val().toLowerCase();
            $('#tabla_partes_body tr').each(function() {
          const fila = $(this);
          const marca = fila.find('[data-marca]').data('marca').toLowerCase();
          const producto = fila.find('[data-producto]').data('producto').toLowerCase();
          const referencia = fila.find('[data-referencia]').data('referencia').toLowerCase();
                let mostrar = true;
                if (filtroMarca && marca.indexOf(filtroMarca) === -1) {
            mostrar = false;
          }
                if (filtroProducto && producto.indexOf(filtroProducto) === -1) {
            mostrar = false;
          }
                if (filtroBusqueda && referencia.indexOf(filtroBusqueda) === -1) {
            mostrar = false;
          }
                if (mostrar) {
            fila.show();
          } else {
            fila.hide();
          }
        });
      }
    });
    function seleccionarParte(parteId, referencia, marca, producto) {
      // Llenar campos del formulario
      const referenciaField = document.querySelector('input[name="referencia_parte"]');
      const marcaField = document.querySelector('input[name="marca_parte"]');
        if (referenciaField) referenciaField.value = referencia;
      if (marcaField) marcaField.value = marca;
        alert('Parte seleccionada: ' + marca + ' - ' + referencia);
        // Cerrar el listado
      document.getElementById('lista_partes_bodega').value = 'no';
      document.getElementById('partes_disponibles_container').classList.add('activo123');
    }
  </script>
</body>
</html>