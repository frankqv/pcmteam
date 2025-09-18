<?php
// ver_triage_2.php - Visualización de Diagnósticos del Segundo Triage
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
$equipos_diagnosticados = [];
$equipo_seleccionado = null;
$diagnostico_detalle = null;
$entrada_info = null;

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

// Cargar equipos con diagnósticos de triage 2 realizados
try {
  $stmt = $connect->prepare("
    SELECT i.*, 
           d.fecha_diagnostico,
           d.tecnico_id as diagnostico_tecnico_id,
           d.camara, d.teclado, d.parlantes, d.bateria, d.microfono, 
           d.pantalla, d.puertos, d.disco,
           d.falla_electrica, d.detalle_falla_electrica,
           d.falla_estetica, d.detalle_falla_estetica,
           d.estado_reparacion, d.observaciones as observaciones_diagnostico,
           e.observaciones as observaciones_entrada,
           e.fecha_entrada, e.proveedor_id,
           p.nombre as nombre_proveedor,
           u.nombre as nombre_tecnico
    FROM bodega_inventario i
    INNER JOIN bodega_diagnosticos d ON i.id = d.inventario_id
    LEFT JOIN bodega_entradas e ON i.id = e.inventario_id
    LEFT JOIN proveedores p ON e.proveedor_id = p.id
    LEFT JOIN usuarios u ON d.tecnico_id = u.id
    WHERE i.estado = 'activo'
    ORDER BY d.fecha_diagnostico DESC
  ");
  $stmt->execute();
  $equipos_diagnosticados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  error_log("Error carga equipos: " . $e->getMessage());
  $mensaje .= "<div class='alert alert-warning'>Error al cargar equipos: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Cargar datos detallados del equipo seleccionado
if (isset($_GET['id']) && (int) $_GET['id'] > 0) {
  $equipo_id = (int) $_GET['id'];
  try {
    // Obtener datos del equipo con diagnóstico
    $stmt = $connect->prepare("
      SELECT i.*, 
             d.fecha_diagnostico, d.tecnico_id as diagnostico_tecnico_id,
             d.camara, d.teclado, d.parlantes, d.bateria, d.microfono, 
             d.pantalla, d.puertos, d.disco,
             d.falla_electrica, d.detalle_falla_electrica,
             d.falla_estetica, d.detalle_falla_estetica,
             d.estado_reparacion, d.observaciones as observaciones_diagnostico,
             u.nombre as nombre_tecnico
      FROM bodega_inventario i
      LEFT JOIN bodega_diagnosticos d ON i.id = d.inventario_id
      LEFT JOIN usuarios u ON d.tecnico_id = u.id
      WHERE i.id = ?
      ORDER BY d.fecha_diagnostico DESC
      LIMIT 1
    ");
    $stmt->execute([$equipo_id]);
    $equipo_seleccionado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($equipo_seleccionado) {
      // Obtener información de entrada
      $stmt = $connect->prepare("
        SELECT e.*, p.nombre as nombre_proveedor, u.nombre as usuario_registro
        FROM bodega_entradas e
        LEFT JOIN proveedores p ON e.proveedor_id = p.id
        LEFT JOIN usuarios u ON e.usuario_id = u.id
        WHERE e.inventario_id = ?
        ORDER BY e.fecha_entrada DESC
        LIMIT 1
      ");
      $stmt->execute([$equipo_id]);
      $entrada_info = $stmt->fetch(PDO::FETCH_ASSOC);
    }
  } catch (Exception $e) {
    error_log("Error carga equipo: " . $e->getMessage());
    $mensaje .= "<div class='alert alert-warning'>Error al cargar equipo: " . htmlspecialchars($e->getMessage()) . "</div>";
  }
}

// Helper functions para badges de estado
function getBadgeClass(string $estado): string {
  $estado = strtoupper(trim($estado ?? ''));
  switch ($estado) {
    case 'BUENO': return 'bg-success text-white';
    case 'MALO': return 'bg-danger text-white';
    case 'REGULAR': return 'bg-warning text-dark';
    case 'N/D': return 'bg-secondary text-white';
    default: return 'bg-light text-dark';
  }
}

function getDispositionBadge(string $disposicion): string {
  $disposicion = strtolower(trim($disposicion ?? ''));
  switch ($disposicion) {
    case 'pendiente_estetico': return 'bg-info text-white';
    case 'en_mantenimiento': return 'bg-warning text-dark';
    case 'en_proceso': return 'bg-primary text-white';
    case 'aprobado': return 'bg-success text-white';
    case 'rechazado': return 'bg-danger text-white';
    default: return 'bg-secondary text-white';
  }
}

function getFaultBadge(string $falla): string {
  $falla = strtolower(trim($falla ?? ''));
  if ($falla === 'si') return 'bg-danger text-white';
  if ($falla === 'no') return 'bg-success text-white';
  return 'bg-secondary text-white';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ver Diagnósticos Triage 2 - PCMarket SAS</title>
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/custom.css">
  <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet" />
  <style>
    .main-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 20px;
    }
    .top-navbar {
      background: linear-gradient(135deg, #6c63ff 0%, #4834d4 100%);
      padding: 15px 20px;
      margin-bottom: 20px;
      border-radius: 8px;
      color: white;
    }
    .navbar-brand {
      color: white !important;
      font-weight: bold;
      text-decoration: none;
    }
    .equipment-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    .equipment-card {
      background: #fff;
      border: 1px solid #dee2e6;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .equipment-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    .equipment-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 2px solid #f8f9fa;
    }
    .equipment-code {
      font-size: 20px;
      font-weight: bold;
      color: #2c3e50;
    }
    .equipment-status {
      font-size: 12px;
      padding: 4px 8px;
      border-radius: 12px;
      font-weight: 500;
    }
    .equipment-info {
      margin-bottom: 15px;
    }
    .info-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      font-size: 14px;
    }
    .info-label {
      font-weight: 500;
      color: #495057;
    }
    .info-value {
      color: #6c757d;
      text-align: right;
      max-width: 60%;
      word-wrap: break-word;
    }
    .diagnostic-section {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .diagnostic-title {
      font-weight: bold;
      color: #495057;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
    }
    .diagnostic-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 8px;
    }
    .diagnostic-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }
    .diagnostic-label {
      font-size: 11px;
      color: #6c757d;
      margin-bottom: 4px;
    }
    .diagnostic-value {
      font-size: 12px;
      padding: 2px 6px;
      border-radius: 4px;
      font-weight: 500;
    }
    .observations-section {
      background: #fff3cd;
      border: 1px solid #ffeaa7;
      border-radius: 8px;
      padding: 12px;
      margin-top: 10px;
    }
    .observations-title {
      font-weight: bold;
      color: #856404;
      margin-bottom: 8px;
      font-size: 14px;
    }
    .observations-text {
      color: #856404;
      font-size: 13px;
      line-height: 1.4;
      margin-bottom: 8px;
    }
    .fault-indicators {
      display: flex;
      gap: 8px;
      margin-top: 10px;
      flex-wrap: wrap;
    }
    .fault-badge {
      padding: 4px 8px;
      border-radius: 6px;
      font-size: 11px;
      font-weight: 500;
    }
    .detail-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 1000;
    }
    .modal-content {
      background: white;
      margin: 5% auto;
      padding: 20px;
      width: 90%;
      max-width: 800px;
      border-radius: 12px;
      max-height: 80%;
      overflow-y: auto;
    }
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 2px solid #f8f9fa;
    }
    .close-modal {
      background: none;
      border: none;
      font-size: 24px;
      cursor: pointer;
      color: #6c757d;
    }
    .detail-section {
      margin-bottom: 20px;
      padding: 15px;
      background: #f8f9fa;
      border-radius: 8px;
    }
    .detail-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-top: 15px;
    }
    .detail-item {
      background: white;
      padding: 12px;
      border-radius: 6px;
      border-left: 4px solid #007bff;
    }
    .filter-section {
      background: #fff;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .filter-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      align-items: end;
    }
    .stats-section {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 30px;
    }
    .stat-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px;
      border-radius: 12px;
      text-align: center;
    }
    .stat-number {
      font-size: 32px;
      font-weight: bold;
      display: block;
    }
    .stat-label {
      font-size: 14px;
      opacity: 0.9;
    }
  </style>
</head>
<body>
  <!-- Sidebar y navegación -->
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
          <i class="material-icons" style="margin-right: 8px;">search</i>
          📊 VISUALIZAR TRIAGE 2 | <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'USUARIO'); ?>
        </a>
      </div>
    </div>
    
    <!-- Mensajes de alerta -->
    <?php if (!empty($mensaje)): ?>
      <?php echo $mensaje; ?>
    <?php endif; ?>
    
    <!-- Estadísticas rápidas -->
    <div class="stats-section">
      <div class="stat-card">
        <span class="stat-number"><?php echo count($equipos_diagnosticados); ?></span>
        <span class="stat-label">Total Diagnosticados</span>
      </div>
      <div class="stat-card">
        <span class="stat-number">
          <?php echo count(array_filter($equipos_diagnosticados, fn($e) => $e['falla_electrica'] === 'si')); ?>
        </span>
        <span class="stat-label">Con Falla Eléctrica</span>
      </div>
      <div class="stat-card">
        <span class="stat-number">
          <?php echo count(array_filter($equipos_diagnosticados, fn($e) => $e['falla_estetica'] === 'si')); ?>
        </span>
        <span class="stat-label">Con Falla Estética</span>
      </div>
      <div class="stat-card">
        <span class="stat-number">
          <?php echo count(array_filter($equipos_diagnosticados, fn($e) => $e['estado_reparacion'] === 'aprobado')); ?>
        </span>
        <span class="stat-label">Aprobados</span>
      </div>
    </div>
    
    <!-- Filtros -->
    <div class="filter-section">
      <div class="section-title">
        <div class="card-icon">🔍</div>
        <h4>Filtros de Búsqueda</h4>
      </div>
      <div class="filter-grid">
        <div class="form-group">
          <label for="filter_codigo">Código del Equipo</label>
          <input type="text" id="filter_codigo" class="form-control" placeholder="Buscar por código...">
        </div>
        <div class="form-group">
          <label for="filter_marca">Marca</label>
          <select id="filter_marca" class="form-control">
            <option value="">Todas las marcas</option>
            <?php 
            $marcas = array_unique(array_column($equipos_diagnosticados, 'marca'));
            foreach ($marcas as $marca): ?>
              <option value="<?php echo htmlspecialchars($marca); ?>"><?php echo htmlspecialchars($marca); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="filter_falla_electrica">Falla Eléctrica</label>
          <select id="filter_falla_electrica" class="form-control">
            <option value="">Todos</option>
            <option value="si">Con Falla</option>
            <option value="no">Sin Falla</option>
          </select>
        </div>
        <div class="form-group">
          <label for="filter_estado">Estado de Reparación</label>
          <select id="filter_estado" class="form-control">
            <option value="">Todos los estados</option>
            <option value="aprobado">Aprobado</option>
            <option value="falla_electrica">Falla Eléctrica</option>
            <option value="falla_mecanica">Falla Mecánica</option>
            <option value="reparacion_cosmetica">Reparación Cosmética</option>
          </select>
        </div>
        <div class="form-group">
          <button type="button" class="btn btn-primary" onclick="aplicarFiltros()">
            <i class="material-icons" style="margin-right: 5px;">filter_list</i>
            Aplicar Filtros
          </button>
        </div>
      </div>
    </div>
    
    <!-- Lista de Equipos Diagnosticados -->
    <div class="form-section">
      <div class="section-title">
        <div class="card-icon">📋</div>
        <h4>Equipos con Diagnósticos de Triage 2</h4>
      </div>
      
      <?php if (empty($equipos_diagnosticados)): ?>
        <div class="alert alert-info">
          📝 No se encontraron equipos con diagnósticos de triage 2.
        </div>
      <?php else: ?>
        <div class="equipment-grid" id="equipos-grid">
          <?php foreach ($equipos_diagnosticados as $equipo): ?>
            <div class="equipment-card" 
                 data-codigo="<?php echo htmlspecialchars($equipo['codigo_g']); ?>"
                 data-marca="<?php echo htmlspecialchars($equipo['marca']); ?>"
                 data-falla-electrica="<?php echo htmlspecialchars($equipo['falla_electrica']); ?>"
                 data-estado="<?php echo htmlspecialchars($equipo['estado_reparacion']); ?>"
                 onclick="verDetalle(<?php echo $equipo['id']; ?>)">
              
              <div class="equipment-header">
                <div class="equipment-code"><?php echo htmlspecialchars($equipo['codigo_g'] ?? 'N/A'); ?></div>
                <span class="equipment-status <?php echo getDispositionBadge($equipo['disposicion']); ?>">
                  <?php echo htmlspecialchars(ucfirst($equipo['disposicion'] ?? 'N/A')); ?>
                </span>
              </div>
              
              <div class="equipment-info">
                <div class="info-row">
                  <span class="info-label">Marca/Modelo:</span>
                  <span class="info-value"><?php echo htmlspecialchars(($equipo['marca'] ?? '') . ' ' . ($equipo['modelo'] ?? '')); ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Serial:</span>
                  <span class="info-value"><?php echo htmlspecialchars($equipo['serial'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Técnico:</span>
                  <span class="info-value"><?php echo htmlspecialchars($equipo['nombre_tecnico'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-row">
                  <span class="info-label">Fecha Diagnóstico:</span>
                  <span class="info-value">
                    <?php 
                    if ($equipo['fecha_diagnostico']) {
                      echo htmlspecialchars((new DateTime($equipo['fecha_diagnostico']))->format('d/m/Y H:i'));
                    } else {
                      echo 'N/A';
                    }
                    ?>
                  </span>
                </div>
              </div>
              
              <!-- Indicadores de fallas -->
              <div class="fault-indicators">
                <span class="fault-badge <?php echo getFaultBadge($equipo['falla_electrica']); ?>">
                  Falla Eléctrica: <?php echo htmlspecialchars(strtoupper($equipo['falla_electrica'] ?? 'N/D')); ?>
                </span>
                <span class="fault-badge <?php echo getFaultBadge($equipo['falla_estetica']); ?>">
                  Falla Estética: <?php echo htmlspecialchars(strtoupper($equipo['falla_estetica'] ?? 'N/D')); ?>
                </span>
              </div>
              
              <!-- Observaciones -->
              <?php if (!empty($equipo['observaciones_entrada']) || !empty($equipo['observaciones_diagnostico'])): ?>
                <div class="observations-section">
                  <?php if (!empty($equipo['observaciones_entrada'])): ?>
                    <div class="observations-title">📝 Observaciones de Entrada:</div>
                    <div class="observations-text"><?php echo htmlspecialchars($equipo['observaciones_entrada']); ?></div>
                  <?php endif; ?>
                  
                  <?php if (!empty($equipo['observaciones_diagnostico'])): ?>
                    <div class="observations-title">🔧 Observaciones del Diagnóstico:</div>
                    <div class="observations-text"><?php echo htmlspecialchars($equipo['observaciones_diagnostico']); ?></div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    
    <!-- Botones de navegación -->
    <div class="btn-container">
      <a href="../bodega/mostrar.php" class="btn btn-primary">
        <i class="material-icons" style="margin-right: 8px;">dashboard</i>
        Volver al Dashboard
      </a>
      <a href="triage2.php" class="btn btn-success">
        <i class="material-icons" style="margin-right: 8px;">add_task</i>
        Realizar Nuevo Triage 2
      </a>
    </div>
  </div>
  
  <!-- Modal de Detalle -->
  <div id="detailModal" class="detail-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Detalle del Diagnóstico</h3>
        <button class="close-modal" onclick="cerrarModal()">&times;</button>
      </div>
      <div id="modal-body">
        <!-- El contenido se carga dinámicamente -->
      </div>
    </div>
  </div>
  
  <!-- Scripts -->
  <script src="../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script>
    function verDetalle(equipoId) {
      window.location.href = '?id=' + equipoId + '#detalle';
    }
    
    function aplicarFiltros() {
      const codigo = document.getElementById('filter_codigo').value.toLowerCase();
      const marca = document.getElementById('filter_marca').value.toLowerCase();
      const fallaElectrica = document.getElementById('filter_falla_electrica').value;
      const estado = document.getElementById('filter_estado').value;
      
      const cards = document.querySelectorAll('.equipment-card');
      
      cards.forEach(card => {
        const cardCodigo = card.dataset.codigo.toLowerCase();
        const cardMarca = card.dataset.marca.toLowerCase();
        const cardFallaElectrica = card.dataset.fallaElectrica;
        const cardEstado = card.dataset.estado;
        
        let mostrar = true;
        
        if (codigo && !cardCodigo.includes(codigo)) mostrar = false;
        if (marca && !cardMarca.includes(marca)) mostrar = false;
        if (fallaElectrica && cardFallaElectrica !== fallaElectrica) mostrar = false;
        if (estado && cardEstado !== estado) mostrar = false;
        
        card.style.display = mostrar ? 'block' : 'none';
      });
    }
    
    function limpiarFiltros() {
      document.getElementById('filter_codigo').value = '';
      document.getElementById('filter_marca').value = '';
      document.getElementById('filter_falla_electrica').value = '';
      document.getElementById('filter_estado').value = '';
      aplicarFiltros();
    }
    
    // Si hay un equipo seleccionado, mostrar su detalle
    <?php if ($equipo_seleccionado): ?>
    document.addEventListener('DOMContentLoaded', function() {
      // Scroll hasta el detalle si viene de un enlace
      if (window.location.hash === '#detalle') {
        document.querySelector('.main-container').scrollIntoView({ behavior: 'smooth' });
      }
    });
    <?php endif; ?>
  </script>
  
  <!-- Detalle del equipo seleccionado -->
  <?php if ($equipo_seleccionado): ?>
    <div class="form-section" id="detalle">
      <div class="section-title">
        <div class="card-icon">🔍</div>
        <h4>Detalle Completo - <?php echo htmlspecialchars($equipo_seleccionado['codigo_g']); ?></h4>
      </div>
      
      <!-- Información básica del equipo -->
      <div class="detail-section">
        <h5>📦 Información del Equipo</h5>
        <div class="detail-grid">
          <div class="detail-item">
            <strong>Código:</strong><br>
            <?php echo htmlspecialchars($equipo_seleccionado['codigo_g']); ?>
          </div>
          <div class="detail-item">
            <strong>Marca/Modelo:</strong><br>
            <?php echo htmlspecialchars(($equipo_seleccionado['marca'] ?? '') . ' ' . ($equipo_seleccionado['modelo'] ?? '')); ?>
          </div>
          <div class="detail-item">
            <strong>Serial:</strong><br>
            <?php echo htmlspecialchars($equipo_seleccionado['serial'] ?? 'N/A'); ?>
          </div>
          <div class="detail-item">
            <strong>Procesador:</strong><br>
            <?php echo htmlspecialchars($equipo_seleccionado['procesador'] ?? 'N/A'); ?>
          </div>
          <div class="detail-item">
            <strong>RAM:</strong><br>
            <?php echo htmlspecialchars($equipo_seleccionado['ram'] ?? 'N/A'); ?>
          </div>
          <div class="detail-item">
            <strong>Disco:</strong><br>
            <?php echo htmlspecialchars($equipo_seleccionado['disco'] ?? 'N/A'); ?>
          </div>
        </div>
      </div>
      
      <!-- Información de entrada -->
      <?php if ($entrada_info): ?>
        <div class="detail-section">
          <h5>📥 Información de Entrada</h5>
          <div class="detail-grid">
            <div class="detail-item">
              <strong>Fecha de Entrada:</strong><br>
              <?php echo htmlspecialchars((new DateTime($entrada_info['fecha_entrada']))->format('d/m/Y H:i')); ?>
            </div>
            <div class="detail-item">
              <strong>Proveedor:</strong><br>
              <?php echo htmlspecialchars($entrada_info['nombre_proveedor'] ?? 'N/A'); ?>
            </div>
            <div class="detail-item">
              <strong>Usuario Registro:</strong><br>
              <?php echo htmlspecialchars($entrada_info['usuario_registro'] ?? 'N/A'); ?>
            </div>
            <div class="detail-item">
              <strong>Cantidad:</strong><br>
              <?php echo htmlspecialchars($entrada_info['cantidad'] ?? '1'); ?>
            </div>
          </div>
          
          <?php if (!empty($entrada_info['observaciones'])): ?>
            <div class="observations-section">
              <div class="observations-title">📝 Observaciones de Entrada:</div>
              <div class="observations-text"><?php echo htmlspecialchars($entrada_info['observaciones']); ?></div>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      
      <!-- Diagnóstico del Triage 2 -->
      <div class="detail-section">
        <h5>🔧 Resultados del Diagnóstico Triage 2</h5>
        
        <div class="diagnostic-section">
          <div class="diagnostic-title">
            <i class="material-icons" style="margin-right: 8px;">assessment</i>
            Pruebas de Componentes
          </div>
          <div class="diagnostic-grid">
            <div class="diagnostic-item">
              <div class="diagnostic-label">Cámara</div>
              <span class="diagnostic-value <?php echo getBadgeClass($equipo_seleccionado['camara']); ?>">
                <?php echo htmlspecialchars($equipo_seleccionado['camara'] ?? 'N/D'); ?>
              </span>
            </div>
            <div class="diagnostic-item">
              <div class="diagnostic-label">Teclado</div>
              <span class="diagnostic-value <?php echo getBadgeClass($equipo_seleccionado['teclado']); ?>">
                <?php echo htmlspecialchars($equipo_seleccionado['teclado'] ?? 'N/D'); ?>
              </span>
            </div>
            <div class="diagnostic-item">
              <div class="diagnostic-label">Parlantes</div>
              <span class="diagnostic-value <?php echo getBadgeClass($equipo_seleccionado['parlantes']); ?>">
                <?php echo htmlspecialchars($equipo_seleccionado['parlantes'] ?? 'N/D'); ?>
              </span>
            </div>
            <div class="diagnostic-item">
              <div class="diagnostic-label">Batería</div>
              <span class="diagnostic-value <?php echo getBadgeClass($equipo_seleccionado['bateria']); ?>">
                <?php echo htmlspecialchars($equipo_seleccionado['bateria'] ?? 'N/D'); ?>
              </span>
            </div>
            <div class="diagnostic-item">
              <div class="diagnostic-label">Micrófono</div>
              <span class="diagnostic-value <?php echo getBadgeClass($equipo_seleccionado['microfono']); ?>">
                <?php echo htmlspecialchars($equipo_seleccionado['microfono'] ?? 'N/D'); ?>
              </span>
            </div>
            <div class="diagnostic-item">
              <div class="diagnostic-label">Pantalla</div>
              <span class="diagnostic-value <?php echo getBadgeClass($equipo_seleccionado['pantalla']); ?>">
                <?php echo htmlspecialchars($equipo_seleccionado['pantalla'] ?? 'N/D'); ?>
              </span>
            </div>
          </div>
        </div>
        
        <!-- Prueba de Puertos -->
        <?php if (!empty($equipo_seleccionado['puertos'])): ?>
          <div class="diagnostic-section">
            <div class="diagnostic-title">
              <i class="material-icons" style="margin-right: 8px;">usb</i>
              Estado de Puertos
            </div>
            <div class="diagnostic-grid">
              <?php 
              $puertos = json_decode($equipo_seleccionado['puertos'], true);
              if ($puertos && is_array($puertos)) {
                foreach ($puertos as $puerto => $estado) {
                  echo '<div class="diagnostic-item">';
                  echo '<div class="diagnostic-label">' . htmlspecialchars($puerto) . '</div>';
                  echo '<span class="diagnostic-value ' . getBadgeClass($estado) . '">';
                  echo htmlspecialchars($estado);
                  echo '</span></div>';
                }
              }
              ?>
            </div>
          </div>
        <?php endif; ?>
        
        <!-- Estado del Disco -->
        <?php if (!empty($equipo_seleccionado['disco'])): ?>
          <div class="diagnostic-section">
            <div class="diagnostic-title">
              <i class="material-icons" style="margin-right: 8px;">storage</i>
              Estado del Disco
            </div>
            <div class="observations-text">
              <?php echo htmlspecialchars($equipo_seleccionado['disco']); ?>
            </div>
          </div>
        <?php endif; ?>
        
        <!-- Detalles de Fallas -->
        <div class="detail-grid">
          <div class="detail-item">
            <strong>Falla Eléctrica:</strong><br>
            <span class="fault-badge <?php echo getFaultBadge($equipo_seleccionado['falla_electrica']); ?>">
              <?php echo htmlspecialchars(strtoupper($equipo_seleccionado['falla_electrica'] ?? 'N/D')); ?>
            </span>
            <?php if (!empty($equipo_seleccionado['detalle_falla_electrica'])): ?>
              <div class="observations-text">
                <strong>Detalle:</strong> <?php echo htmlspecialchars($equipo_seleccionado['detalle_falla_electrica']); ?>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="detail-item">
            <strong>Falla Estética:</strong><br>
            <span class="fault-badge <?php echo getFaultBadge($equipo_seleccionado['falla_estetica']); ?>">
              <?php echo htmlspecialchars(strtoupper($equipo_seleccionado['falla_estetica'] ?? 'N/D')); ?>
            </span>
            <?php if (!empty($equipo_seleccionado['detalle_falla_estetica'])): ?>
              <div class="observations-text">
                <strong>Detalle:</strong> <?php echo htmlspecialchars($equipo_seleccionado['detalle_falla_estetica']); ?>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="detail-item">
            <strong>Estado de Reparación:</strong><br>
            <span class="equipment-status <?php echo getDispositionBadge($equipo_seleccionado['estado_reparacion']); ?>">
              <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $equipo_seleccionado['estado_reparacion'] ?? 'N/A'))); ?>
            </span>
          </div>
          
          <div class="detail-item">
            <strong>Técnico Responsable:</strong><br>
            <?php echo htmlspecialchars($equipo_seleccionado['nombre_tecnico'] ?? 'N/A'); ?>
          </div>
          
          <div class="detail-item">
            <strong>Fecha Diagnóstico:</strong><br>
            <?php 
            if ($equipo_seleccionado['fecha_diagnostico']) {
              echo htmlspecialchars((new DateTime($equipo_seleccionado['fecha_diagnostico']))->format('d/m/Y H:i'));
            } else {
              echo 'N/A';
            }
            ?>
          </div>
          
          <div class="detail-item">
            <strong>Disposición Actual:</strong><br>
            <span class="equipment-status <?php echo getDispositionBadge($equipo_seleccionado['disposicion']); ?>">
              <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $equipo_seleccionado['disposicion'] ?? 'N/A'))); ?>
            </span>
          </div>
        </div>
      </div>
      
      <!-- Observaciones Importantes -->
      <?php if (!empty($entrada_info['observaciones']) || !empty($equipo_seleccionado['observaciones_diagnostico'])): ?>
        <div class="detail-section">
          <h5>📝 Observaciones Importantes</h5>
          
          <?php if (!empty($entrada_info['observaciones'])): ?>
            <div class="observations-section">
              <div class="observations-title">📦 Observaciones de Entrada (bodega_entradas):</div>
              <div class="observations-text"><?php echo htmlspecialchars($entrada_info['observaciones']); ?></div>
              <small class="text-muted">
                Registrado el <?php echo htmlspecialchars((new DateTime($entrada_info['fecha_entrada']))->format('d/m/Y H:i')); ?>
                por <?php echo htmlspecialchars($entrada_info['usuario_registro'] ?? 'Usuario desconocido'); ?>
              </small>
            </div>
          <?php endif; ?>
          
          <?php if (!empty($equipo_seleccionado['observaciones_diagnostico'])): ?>
            <div class="observations-section">
              <div class="observations-title">🔧 Observaciones del Diagnóstico Triage 2 (bodega_diagnosticos):</div>
              <div class="observations-text"><?php echo htmlspecialchars($equipo_seleccionado['observaciones_diagnostico']); ?></div>
              <small class="text-muted">
                Diagnóstico realizado el <?php echo htmlspecialchars((new DateTime($equipo_seleccionado['fecha_diagnostico']))->format('d/m/Y H:i')); ?>
                por <?php echo htmlspecialchars($equipo_seleccionado['nombre_tecnico'] ?? 'Técnico desconocido'); ?>
              </small>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      
      <!-- Resumen del Estado -->
      <div class="detail-section">
        <h5>📊 Resumen del Estado</h5>
        <div class="alert alert-info">
          <strong>Próximo Paso:</strong>
          <?php
          switch ($equipo_seleccionado['disposicion']) {
            case 'pendiente_estetico':
              echo 'El equipo está listo para revisión estética.';
              break;
            case 'en_mantenimiento':
              echo 'El equipo requiere más trabajo de mantenimiento.';
              break;
            case 'en_proceso':
              echo 'El equipo está siendo procesado actualmente.';
              break;
            default:
              echo 'Estado: ' . htmlspecialchars(ucwords(str_replace('_', ' ', $equipo_seleccionado['disposicion'] ?? 'Indefinido')));
          }
          ?>
        </div>
        
        <?php if ($equipo_seleccionado['falla_electrica'] === 'si' || $equipo_seleccionado['falla_estetica'] === 'si'): ?>
          <div class="alert alert-warning">
            <strong>⚠️ Atención:</strong> Este equipo requiere atención especial debido a fallas detectadas.
          </div>
        <?php endif; ?>
      </div>
      
      <!-- Botones de acción -->
      <div class="btn-container">
        <a href="?" class="btn btn-secondary">
          <i class="material-icons" style="margin-right: 8px;">arrow_back</i>
          Volver a la Lista
        </a>
        <a href="triage2.php?id=<?php echo $equipo_seleccionado['id']; ?>" class="btn btn-primary">
          <i class="material-icons" style="margin-right: 8px;">edit</i>
          Editar Diagnóstico
        </a>
        <?php if ($equipo_seleccionado['falla_electrica'] === 'si'): ?>
          <a href="electrico.php?id=<?php echo $equipo_seleccionado['id']; ?>" class="btn btn-warning">
            <i class="material-icons" style="margin-right: 8px;">electrical_services</i>
            Ir a Diagnóstico Eléctrico
          </a>
        <?php endif; ?>
        <?php if ($equipo_seleccionado['disposicion'] === 'pendiente_estetico'): ?>
          <a href="estetico.php?id=<?php echo $equipo_seleccionado['id']; ?>" class="btn btn-success">
            <i class="material-icons" style="margin-right: 8px;">palette</i>
            Ir a Revisión Estética
          </a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
  
  <script>
    // Funciones adicionales para interacción
    function exportarDatos() {
      // Función para exportar datos de diagnósticos
      const equipos = <?php echo json_encode($equipos_diagnosticados); ?>;
      const csv = convertirACSV(equipos);
      descargarCSV(csv, 'diagnosticos_triage2.csv');
    }
    
    function convertirACSV(datos) {
      const headers = ['Código', 'Marca', 'Modelo', 'Serial', 'Fecha Diagnóstico', 'Técnico', 'Falla Eléctrica', 'Falla Estética', 'Estado', 'Observaciones'];
      let csv = headers.join(',') + '\n';
      
      datos.forEach(equipo => {
        const fila = [
          equipo.codigo_g || '',
          equipo.marca || '',
          equipo.modelo || '',
          equipo.serial || '',
          equipo.fecha_diagnostico || '',
          equipo.nombre_tecnico || '',
          equipo.falla_electrica || '',
          equipo.falla_estetica || '',
          equipo.estado_reparacion || '',
          (equipo.observaciones_diagnostico || '').replace(/,/g, ';')
        ];
        csv += fila.map(campo => `"${campo}"`).join(',') + '\n';
      });
      
      return csv;
    }
    
    function descargarCSV(contenido, nombreArchivo) {
      const blob = new Blob([contenido], { type: 'text/csv;charset=utf-8;' });
      const enlace = document.createElement('a');
      if (enlace.download !== undefined) {
        const url = URL.createObjectURL(blob);
        enlace.setAttribute('href', url);
        enlace.setAttribute('download', nombreArchivo);
        enlace.style.visibility = 'hidden';
        document.body.appendChild(enlace);
        enlace.click();
        document.body.removeChild(enlace);
      }
    }
    
    // Función de búsqueda en tiempo real
    document.getElementById('filter_codigo').addEventListener('input', function() {
      aplicarFiltros();
    });
    
    // Auto-aplicar filtros cuando cambien los selectores
    ['filter_marca', 'filter_falla_electrica', 'filter_estado'].forEach(id => {
      document.getElementById(id).addEventListener('change', aplicarFiltros);
    });
  </script>
  
  <!-- Botón flotante para exportar -->
  <div style="position: fixed; bottom: 20px; right: 20px; z-index: 100;">
    <button class="btn btn-info" onclick="exportarDatos()" title="Exportar a CSV">
      <i class="material-icons">download</i>
    </button>
  </div>
  
</body>
</html>