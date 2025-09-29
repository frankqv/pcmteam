<?php
// electrico.php - Diagnóstico Eléctrico de Equipos
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
$diagnostico_general = null;

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

// Cargar equipos pendientes de diagnóstico eléctrico
try {
  $stmt = $connect->prepare("
    SELECT i.*, 
           d.falla_electrica,
           d.observaciones as observaciones_diagnostico,
           COALESCE(e.estado_final, 'pendiente') as estado_electrico,
           e.fecha_proceso as fecha_electrico
    FROM bodega_inventario i
    LEFT JOIN bodega_diagnosticos d ON i.id = d.inventario_id
    LEFT JOIN bodega_electrico e ON i.id = e.inventario_id
    WHERE (i.disposicion = 'en_mantenimiento' OR d.falla_electrica = 'si')
      AND (e.estado_final IS NULL OR e.estado_final = 'requiere_revision')
      AND i.estado = 'activo'
    ORDER BY i.fecha_ingreso ASC
  ");
  $stmt->execute();
  $equipos_pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  error_log("Error carga equipos: " . $e->getMessage());
  $mensaje .= "<div class='alert alert-warning'>Error al cargar equipos: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $connect->beginTransaction();
    
    $inventario_id = (int) ($_POST['inventario_id'] ?? 0);
    $tecnico_id = (int) ($_SESSION['id']);
    
    if ($inventario_id > 0) {
      // Insertar diagnóstico eléctrico
      $stmt = $connect->prepare("
        INSERT INTO bodega_electrico 
        (inventario_id, tecnico_id, estado_bateria, estado_fuente, estado_puertos, 
         estado_pantalla, estado_teclado, estado_audio, fallas_detectadas, 
         reparaciones_realizadas, estado_final, observaciones, fecha_proceso)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
      ");
      
      $stmt->execute([
        $inventario_id,
        $tecnico_id,
        $_POST['estado_bateria'] ?? 'N/D',
        $_POST['estado_fuente'] ?? 'N/D',
        $_POST['estado_puertos'] ?? 'N/D',
        $_POST['estado_pantalla'] ?? 'N/D',
        $_POST['estado_teclado'] ?? 'N/D',
        $_POST['estado_audio'] ?? 'N/D',
        $_POST['fallas_detectadas'] ?? '',
        $_POST['reparaciones_realizadas'] ?? '',
        $_POST['estado_final'] ?? 'pendiente',
        $_POST['observaciones'] ?? ''
      ]);
      
      // Actualizar disposición del inventario según el estado final
      $nueva_disposicion = 'en_revision'; // por defecto
      if ($_POST['estado_final'] === 'aprobado') {
        $nueva_disposicion = 'pendiente_estetico';
      } elseif ($_POST['estado_final'] === 'requiere_revision') {
        $nueva_disposicion = 'en_mantenimiento';
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
        'disposicion_electrico',
        'en_mantenimiento',
        $nueva_disposicion
      ]);
      
      $mensaje .= "<div class='alert alert-success'>✅ Diagnóstico eléctrico guardado correctamente</div>";
      
      // Recargar equipos pendientes
      $stmt = $connect->prepare("
        SELECT i.*, 
               d.falla_electrica,
               d.observaciones as observaciones_diagnostico,
               COALESCE(e.estado_final, 'pendiente') as estado_electrico,
               e.fecha_proceso as fecha_electrico
        FROM bodega_inventario i
        LEFT JOIN bodega_diagnosticos d ON i.id = d.inventario_id
        LEFT JOIN bodega_electrico e ON i.id = e.inventario_id
        WHERE (i.disposicion = 'en_mantenimiento' OR d.falla_electrica = 'si')
          AND (e.estado_final IS NULL OR e.estado_final = 'requiere_revision')
          AND i.estado = 'activo'
        ORDER BY i.fecha_ingreso ASC
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

// Cargar datos del equipo seleccionado
if (isset($_GET['id']) && (int) $_GET['id'] > 0) {
  $equipo_id = (int) $_GET['id'];
  try {
    $stmt = $connect->prepare("SELECT * FROM bodega_inventario WHERE id = ? LIMIT 1");
    $stmt->execute([$equipo_id]);
    $equipo_seleccionado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($equipo_seleccionado) {
      // Obtener último diagnóstico eléctrico
      $stmt = $connect->prepare("
        SELECT * FROM bodega_electrico 
        WHERE inventario_id = ? 
        ORDER BY fecha_proceso DESC LIMIT 1
      ");
      $stmt->execute([$equipo_id]);
      $diagnostico_ultimo = $stmt->fetch(PDO::FETCH_ASSOC);
      
      // Obtener diagnóstico general (bodega_diagnosticos)
      $stmt = $connect->prepare("
        SELECT * FROM bodega_diagnosticos 
        WHERE inventario_id = ? 
        ORDER BY fecha_diagnostico DESC LIMIT 1
      ");
      $stmt->execute([$equipo_id]);
      $diagnostico_general = $stmt->fetch(PDO::FETCH_ASSOC);
    }
  } catch (Exception $e) {
    error_log("Error carga equipo: " . $e->getMessage());
    $mensaje .= "<div class='alert alert-warning'>Error al cargar equipo: " . htmlspecialchars($e->getMessage()) . "</div>";
  }
}

// Helper function for status badges
function badgeClass(string $v): string {
  $v = strtoupper(trim($v ?? ''));
  if ($v === 'BUENO' || $v === 'APROBADO') return 'status-bueno';
  if ($v === 'MALO' || $v === 'RECHAZADO') return 'status-malo';
  return 'status-nd';
}

// Helper function for electrical fault badge
function faultBadgeClass(string $v): string {
  $v = strtolower(trim($v ?? ''));
  if ($v === 'si') return 'status-malo';
  if ($v === 'no') return 'status-bueno';
  return 'status-nd';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Diagnóstico Eléctrico - PCMarket SAS</title>
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
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
      margin: 2px;
      display: inline-block;
    }
    .status-bueno { background-color: #d4edda; color: #155724; }
    .status-malo { background-color: #f8d7da; color: #721c24; }
    .status-nd { background-color: #e2e3e5; color: #495057; }
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
    .alert-info {
      background-color: #d1ecf1;
      border-color: #bee5eb;
      color: #0c5460;
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
      background: #3498db;
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
    .fault-indicator {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 5px;
    }
    .observations-text {
      background: #f8f9fa;
      padding: 10px;
      border-radius: 4px;
      border-left: 4px solid #007bff;
      margin-top: 5px;
      font-style: italic;
      color: #495057;
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
          <i class="material-icons" style="margin-right: 8px;">electrical_services</i>
          ⚡ DIAGNÓSTICO ELÉCTRICO | <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'USUARIO'); ?>
        </a>
      </div>
    </div>
    
    <!-- Mensajes de alerta -->
    <?php if (!empty($mensaje)): ?>
      <?php echo $mensaje; ?>
    <?php endif; ?>
    
    <!-- Lista de Equipos Pendientes -->
    <div class="form-section">
      <div class="section-title">
        <div class="card-icon">📋</div>
        <h4>Equipos Pendientes de Diagnóstico Eléctrico</h4>
      </div>
      
      <?php if (empty($equipos_pendientes)): ?>
        <div class="alert alert-info">
          ✅ No hay equipos pendientes de diagnóstico eléctrico en este momento.
        </div>
      <?php else: ?>
        <div class="row">
          <?php foreach ($equipos_pendientes as $equipo): ?>
            <div class="col-md-6 col-lg-4">
              <div class="equipment-card" onclick="seleccionarEquipo(<?php echo $equipo['id']; ?>)">
                <div class="equipment-code"><?php echo htmlspecialchars($equipo['codigo_g'] ?? 'N/A'); ?></div>
                <div class="equipment-details">
                  <strong><?php echo htmlspecialchars(($equipo['marca'] ?? '') . ' ' . ($equipo['modelo'] ?? '')); ?></strong><br>
                  <small>Serial: <?php echo htmlspecialchars($equipo['serial'] ?? 'N/A'); ?></small><br>
                  <small>Ubicación: <?php echo htmlspecialchars($equipo['ubicacion'] ?? 'N/A'); ?></small><br>
                  
                  <div class="fault-indicator">
                    <span class="status-badge <?php echo faultBadgeClass($equipo['falla_electrica'] ?? 'no'); ?>">
                      Falla Eléctrica: <?php echo htmlspecialchars(strtoupper($equipo['falla_electrica'] ?? 'N/D')); ?>
                    </span>
                    <span class="status-badge status-nd">
                      Estado: <?php echo htmlspecialchars(ucfirst($equipo['estado_electrico'] ?? 'pendiente')); ?>
                    </span>
                  </div>
                  
                  <?php if (!empty($equipo['observaciones_diagnostico'])): ?>
                    <div class="observations-text">
                      <small><strong>Observaciones:</strong> <?php echo htmlspecialchars($equipo['observaciones_diagnostico']); ?></small>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    
    <!-- Formulario de Diagnóstico Eléctrico -->
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
        
        <?php if ($diagnostico_general): ?>
          <div class="diagnosis-item">
            <span class="diagnosis-label">Falla Eléctrica Detectada</span>
            <span class="status-badge <?php echo faultBadgeClass($diagnostico_general['falla_electrica']); ?>">
              <?php echo htmlspecialchars(strtoupper($diagnostico_general['falla_electrica'])); ?>
            </span>
          </div>
          
          <?php if (!empty($diagnostico_general['detalle_falla_electrica'])): ?>
            <div class="diagnosis-item">
              <span class="diagnosis-label">Detalle Falla Eléctrica</span>
              <span><?php echo htmlspecialchars($diagnostico_general['detalle_falla_electrica']); ?></span>
            </div>
          <?php endif; ?>
          
          <?php if (!empty($diagnostico_general['observaciones'])): ?>
            <div class="diagnosis-item">
              <span class="diagnosis-label">Observaciones del Diagnóstico</span>
              <div class="observations-text">
                <?php echo htmlspecialchars($diagnostico_general['observaciones']); ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($diagnostico_ultimo): ?>
          <div class="diagnosis-item">
            <span class="diagnosis-label">Último Diagnóstico Eléctrico</span>
            <span><?php echo htmlspecialchars((new DateTime($diagnostico_ultimo['fecha_proceso']))->format('d/m/Y H:i')); ?></span>
          </div>
          <div class="diagnosis-item">
            <span class="diagnosis-label">Estado Anterior</span>
            <span class="status-badge <?php echo badgeClass($diagnostico_ultimo['estado_final']); ?>">
              <?php echo htmlspecialchars(ucfirst($diagnostico_ultimo['estado_final'])); ?>
            </span>
          </div>
        <?php endif; ?>
      </div>
      
      <!-- Formulario -->
      <form method="POST" class="form-section">
        <div class="section-title">
          <div class="card-icon">⚡</div>
          <h4>Nuevo Diagnóstico Eléctrico</h4>
        </div>
        
        <input type="hidden" name="inventario_id" value="<?php echo $equipo_seleccionado['id']; ?>">
        
        <div class="form-grid">
          <div class="form-group">
            <label for="estado_bateria">Estado de la Batería</label>
            <select id="estado_bateria" name="estado_bateria" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="BUENO">BUENO</option>
              <option value="REGULAR">REGULAR</option>
              <option value="MALO">MALO</option>
              <option value="N/D">N/D</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="estado_fuente">Estado de la Fuente de Poder</label>
            <select id="estado_fuente" name="estado_fuente" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="BUENO">BUENO</option>
              <option value="REGULAR">REGULAR</option>
              <option value="MALO">MALO</option>
              <option value="N/D">N/D</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="estado_puertos">Estado de los Puertos</label>
            <select id="estado_puertos" name="estado_puertos" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="BUENO">BUENO</option>
              <option value="REGULAR">REGULAR</option>
              <option value="MALO">MALO</option>
              <option value="N/D">N/D</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="estado_pantalla">Estado de la Pantalla</label>
            <select id="estado_pantalla" name="estado_pantalla" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="BUENO">BUENO</option>
              <option value="REGULAR">REGULAR</option>
              <option value="MALO">MALO</option>
              <option value="N/D">N/D</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="estado_teclado">Estado del Teclado</label>
            <select id="estado_teclado" name="estado_teclado" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="BUENO">BUENO</option>
              <option value="REGULAR">REGULAR</option>
              <option value="MALO">MALO</option>
              <option value="N/D">N/D</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="estado_audio">Estado del Audio</label>
            <select id="estado_audio" name="estado_audio" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="BUENO">BUENO</option>
              <option value="REGULAR">REGULAR</option>
              <option value="MALO">MALO</option>
              <option value="N/D">N/D</option>
            </select>
          </div>
        </div>
        
        <div class="form-group">
          <label for="fallas_detectadas">Fallas Detectadas</label>
          <textarea id="fallas_detectadas" name="fallas_detectadas" rows="3" class="form-control" 
                    placeholder="Describe las fallas eléctricas encontradas..."></textarea>
        </div>
        
        <div class="form-group">
          <label for="reparaciones_realizadas">Reparaciones Realizadas</label>
          <textarea id="reparaciones_realizadas" name="reparaciones_realizadas" rows="3" class="form-control" 
                    placeholder="Describe las reparaciones realizadas..."></textarea>
        </div>
        
        <div class="form-group">
          <label for="estado_final">Estado Final</label>
          <select id="estado_final" name="estado_final" class="form-control" required>
            <option value="">-- Seleccionar --</option>
            <option value="aprobado">APROBADO - Pasa a Revisión Estética</option>
            <option value="rechazado">RECHAZADO - Requiere Más Trabajo</option>
            <option value="requiere_revision">REQUIERE REVISIÓN - Necesita más diagnóstico</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="observaciones">Observaciones Adicionales</label>
          <textarea id="observaciones" name="observaciones" rows="3" class="form-control" 
                    placeholder="Observaciones adicionales del diagnóstico eléctrico..."></textarea>
        </div>
        
        <div class="btn-container">
          <button type="submit" class="btn btn-success">
            <i class="material-icons" style="margin-right: 8px;">save</i>
            Guardar Diagnóstico Eléctrico
          </button>
        </div>
      </form>
      
      <!-- Botones de navegación -->
      <div class="btn-container">
        <a href="?" class="btn btn-secondary">
          <i class="material-icons" style="margin-right: 8px;">list</i>
          Ver Todos los Equipos
        </a>
        <a href="../bodega/historial_electrico.php" class="btn btn-primary">
          <i class="material-icons" style="margin-right: 8px;">dashboard</i>
          Volver al Dashboard
        </a>
      </div>
    <?php else: ?>
      <div class="alert alert-info">
        <i class="material-icons" style="margin-right: 8px;">info</i>
        Selecciona un equipo de la lista para realizar el diagnóstico eléctrico.
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
      document.getElementById('estado_bateria').value = '<?php echo htmlspecialchars($diagnostico_ultimo['estado_bateria'] ?? ''); ?>';
      document.getElementById('estado_fuente').value = '<?php echo htmlspecialchars($diagnostico_ultimo['estado_fuente'] ?? ''); ?>';
      document.getElementById('estado_puertos').value = '<?php echo htmlspecialchars($diagnostico_ultimo['estado_puertos'] ?? ''); ?>';
      document.getElementById('estado_pantalla').value = '<?php echo htmlspecialchars($diagnostico_ultimo['estado_pantalla'] ?? ''); ?>';
      document.getElementById('estado_teclado').value = '<?php echo htmlspecialchars($diagnostico_ultimo['estado_teclado'] ?? ''); ?>';
      document.getElementById('estado_audio').value = '<?php echo htmlspecialchars($diagnostico_ultimo['estado_audio'] ?? ''); ?>';
      document.getElementById('fallas_detectadas').value = '<?php echo htmlspecialchars($diagnostico_ultimo['fallas_detectadas'] ?? ''); ?>';
      document.getElementById('reparaciones_realizadas').value = '<?php echo htmlspecialchars($diagnostico_ultimo['reparaciones_realizadas'] ?? ''); ?>';
      document.getElementById('observaciones').value = '<?php echo htmlspecialchars($diagnostico_ultimo['observaciones'] ?? ''); ?>';
    });
    <?php endif; ?>
  </script>
</body>
</html>