<?php
// solicitar_parte.php - Solicitud de Partes para Equipos
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
$equipos_disponibles = [];
$solicitudes_usuario = [];
$estadisticas = [
  'total' => 0,
  'pendientes' => 0,
  'aprobadas' => 0,
  'entregadas' => 0
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
// Procesamiento del formulario de solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $connect->beginTransaction();
    $inventario_id = (int) ($_POST['inventario_id'] ?? 0);
    $tecnico_id = (int) ($_SESSION['id']);
    if ($inventario_id > 0) {
      // Insertar solicitud de parte
      $stmt = $connect->prepare("
        INSERT INTO bodega_solicitud_parte 
        (inventario_id, detalle_solicitud, cantidad_solicitada, codigo_equipo, serial_parte, 
         marca_parte, nivel_urgencia, referencia_parte, ubicacion_pieza, id_tecnico, 
         usuario_solicitante, estado, fecha_solicitud)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente', NOW())
      ");
      $stmt->execute([
        $inventario_id,
        $_POST['detalle_solicitud'] ?? '',
        $_POST['cantidad_solicitada'] ?? 1,
        $_POST['codigo_equipo'] ?? '',
        $_POST['serial_parte'] ?? '',
        $_POST['marca_parte'] ?? '',
        $_POST['nivel_urgencia'] ?? 'Baja',
        $_POST['referencia_parte'] ?? '',
        $_POST['ubicacion_pieza'] ?? '',
        $tecnico_id,
        $tecnico_id
      ]);
      $mensaje .= "<div class='alert alert-success'>✅ Solicitud de parte enviada correctamente</div>";
    }
    $connect->commit();
  } catch (Exception $e) {
    $connect->rollBack();
    $mensaje .= "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
  }
}
// Cargar datos
try {
  // Equipos disponibles para solicitar partes
  $stmt = $connect->prepare("
    SELECT id, codigo_g, marca, modelo, serial, ubicacion, disposicion
    FROM bodega_inventario 
    WHERE estado = 'activo' 
      AND disposicion IN ('en_mantenimiento', 'pendiente_estetico', 'pendiente_control_calidad')
    ORDER BY codigo_g
  ");
  $stmt->execute();
  $equipos_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // Estadísticas de solicitudes del usuario
  $stmt = $connect->prepare("
    SELECT 
      COUNT(*) as total,
      SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
      SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
      SUM(CASE WHEN estado = 'entregada' THEN 1 ELSE 0 END) as entregadas
    FROM bodega_solicitud_parte 
    WHERE id_tecnico = ?
  ");
  $stmt->execute([$_SESSION['id']]);
  $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
  // Solicitudes del usuario
  $stmt = $connect->prepare("
    SELECT sp.*, i.codigo_g, i.marca, i.modelo
    FROM bodega_solicitud_parte sp
    LEFT JOIN bodega_inventario i ON sp.inventario_id = i.id
    WHERE sp.id_tecnico = ?
    ORDER BY sp.fecha_solicitud DESC
    LIMIT 50
  ");
  $stmt->execute([$_SESSION['id']]);
  $solicitudes_usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  error_log("Error carga datos: " . $e->getMessage());
  $mensaje .= "<div class='alert alert-warning'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</div>";
}
// Helper functions
function urgenciaBadgeClass(string $urgencia): string
{
  $urgencia = strtoupper(trim($urgencia ?? ''));
  switch ($urgencia) {
    case 'BAJA':
      return 'urgencia-baja';
    case 'MEDIA':
      return 'urgencia-media';
    case 'ALTA':
      return 'urgencia-alta';
    default:
      return 'urgencia-nd';
  }
}
function estadoBadgeClass(string $estado): string
{
  $estado = strtoupper(trim($estado ?? ''));
  switch ($estado) {
    case 'PENDIENTE':
      return 'estado-pendiente';
    case 'APROBADA':
      return 'estado-aprobada';
    case 'ENTREGADA':
      return 'estado-entregada';
    case 'RECHAZADA':
      return 'estado-rechazada';
    default:
      return 'estado-nd';
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Solicitud de Partes - PCMarket SAS</title>
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
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    .stat-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .stat-card h3 {
      font-size: 2.5rem;
      margin: 0;
      font-weight: bold;
    }
    .stat-card p {
      margin: 5px 0 0 0;
      opacity: 0.9;
    }
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
    }
    .solicitudes-table {
      max-height: 500px;
      overflow-y: auto;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    .table-sm th,
    .table-sm td {
      padding: 8px;
      font-size: 0.875em;
    }
    .status-badge {
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.875em;
      font-weight: 500;
    }
    .urgencia-baja {
      background-color: #d4edda;
      color: #155724;
    }
    .urgencia-media {
      background-color: #fff3cd;
      color: #856404;
    }
    .urgencia-alta {
      background-color: #f8d7da;
      color: #721c24;
    }
    .urgencia-nd {
      background-color: #e2e3e5;
      color: #495057;
    }
    .estado-pendiente {
      background-color: #fff3cd;
      color: #856404;
    }
    .estado-aprobada {
      background-color: #d1ecf1;
      color: #0c5460;
    }
    .estado-entregada {
      background-color: #d4edda;
      color: #155724;
    }
    .estado-rechazada {
      background-color: #f8d7da;
      color: #721c24;
    }
    .estado-nd {
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
      background: #16a085;
      padding: 15px 20px;
      margin-bottom: 20px;
      border-radius: 8px;
    }
    .navbar-brand {
      color: white !important;
      font-weight: bold;
      text-decoration: none;
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
          <i class="material-icons" style="margin-right: 8px;">add_shopping_cart</i>
          🛒 SOLICITUD DE PARTES | <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'USUARIO'); ?>
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
        <p>Total Solicitudes</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $estadisticas['pendientes']; ?></h3>
        <p>Pendientes</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $estadisticas['aprobadas']; ?></h3>
        <p>Aprobadas</p>
      </div>
      <div class="stat-card">
        <h3><?php echo $estadisticas['entregadas']; ?></h3>
        <p>Entregadas</p>
      </div>
    </div>
    <!-- Formulario de Solicitud -->
    <div class="form-section">
      <div class="section-title">
        <div class="card-icon">📝</div>
        <h4>Nueva Solicitud de Parte</h4>
      </div>
      <form method="POST">
        <div class="form-grid">
          <div class="form-group">
            <label for="codigo_equipo">Código del Equipo *</label>
            <select id="codigo_equipo" name="codigo_equipo" class="form-control" required>
              <option value="">-- Seleccionar Equipo --</option>
              <?php foreach ($equipos_disponibles as $equipo): ?>
                <option value="<?php echo htmlspecialchars($equipo['codigo_g']); ?>"
                  data-id="<?php echo $equipo['id']; ?>"
                  data-marca="<?php echo htmlspecialchars($equipo['marca']); ?>"
                  data-modelo="<?php echo htmlspecialchars($equipo['modelo']); ?>">
                  <?php echo htmlspecialchars($equipo['codigo_g'] . ' - ' . $equipo['marca'] . ' ' . $equipo['modelo']); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <input type="hidden" id="inventario_id" name="inventario_id" value="">
          </div>
          <div class="form-group">
            <label for="detalle_solicitud">Detalle de la Solicitud *</label>
            <textarea id="detalle_solicitud" name="detalle_solicitud" rows="3" class="form-control"
              placeholder="Describe detalladamente la parte que necesitas..." required></textarea>
          </div>
          <div class="form-group">
            <label for="cantidad_solicitada">Cantidad Solicitada *</label>
            <input type="number" id="cantidad_solicitada" name="cantidad_solicitada" class="form-control"
              min="1" value="1" required>
          </div>
          <div class="form-group">
            <label for="nivel_urgencia">Nivel de Urgencia *</label>
            <select id="nivel_urgencia" name="nivel_urgencia" class="form-control" required>
              <option value="">-- Seleccionar --</option>
              <option value="Baja">Baja (7 días)</option>
              <option value="Media">Media (2-3 días)</option>
              <option value="Alta">Alta (24h)</option>
            </select>
          </div>
          <div class="form-group">
            <label for="serial_parte">Serial de la Parte</label>
            <input type="text" id="serial_parte" name="serial_parte" class="form-control"
              placeholder="Serial de la parte (si se conoce)">
          </div>
          <div class="form-group">
            <label for="marca_parte">Marca de la Parte</label>
            <input type="text" id="marca_parte" name="marca_parte" class="form-control"
              placeholder="Marca de la parte">
          </div>
          <div class="form-group">
            <label for="referencia_parte">Referencia de la Parte</label>
            <input type="text" id="referencia_parte" name="referencia_parte" class="form-control"
              placeholder="Referencia o modelo de la parte">
          </div>
          <div class="form-group">
            <label for="ubicacion_pieza">Ubicación de la Pieza</label>
            <input type="text" id="ubicacion_pieza" name="ubicacion_pieza" class="form-control"
              placeholder="Ubicación en bodega (si se conoce)">
          </div>
        </div>
        <div class="btn-container">
          <button type="submit" class="btn btn-success">
            <i class="material-icons" style="margin-right: 8px;">send</i>
            Enviar Solicitud
          </button>
          <button type="reset" class="btn btn-secondary">
            <i class="material-icons" style="margin-right: 8px;">clear</i>
            Limpiar Formulario
          </button>
        </div>
      </form>
    </div>
    <!-- Historial de Solicitudes -->
    <div class="form-section">
      <div class="section-title">
        <div class="card-icon">📋</div>
        <h4>Mis Solicitudes de Partes</h4>
      </div>
      <?php if (empty($solicitudes_usuario)): ?>
        <div class="alert alert-info">
          ✅ No has realizado solicitudes de partes aún.
        </div>
      <?php else: ?>
        <div class="solicitudes-table">
          <table class="table table-sm table-striped">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Equipo</th>
                <th>Detalle</th>
                <th>Cantidad</th>
                <th>Urgencia</th>
                <th>Estado</th>
                <th>Referencia</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($solicitudes_usuario as $solicitud): ?>
                <tr>
                  <td>
                    <?php echo htmlspecialchars((new DateTime($solicitud['fecha_solicitud']))->format('d/m/Y H:i')); ?>
                  </td>
                  <td>
                    <strong><?php echo htmlspecialchars($solicitud['codigo_g'] ?? 'N/A'); ?></strong><br>
                    <small><?php echo htmlspecialchars(($solicitud['marca'] ?? '') . ' ' . ($solicitud['modelo'] ?? '')); ?></small>
                  </td>
                  <td>
                    <?php echo htmlspecialchars(substr($solicitud['detalle_solicitud'], 0, 50)); ?>
                    <?php if (strlen($solicitud['detalle_solicitud']) > 50): ?>...<?php endif; ?>
                  </td>
                  <td>
                    <span class="badge badge-info"><?php echo $solicitud['cantidad_solicitada']; ?></span>
                  </td>
                  <td>
                    <span class="status-badge <?php echo urgenciaBadgeClass($solicitud['nivel_urgencia']); ?>">
                      <?php echo htmlspecialchars($solicitud['nivel_urgencia']); ?>
                    </span>
                  </td>
                  <td>
                    <span class="status-badge <?php echo estadoBadgeClass($solicitud['estado']); ?>">
                      <?php echo htmlspecialchars(ucfirst($solicitud['estado'])); ?>
                    </span>
                  </td>
                  <td>
                    <?php echo htmlspecialchars($solicitud['referencia_parte'] ?? 'N/A'); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
    <!-- Botones de navegación -->
    <div class="btn-container">
      <a href="../bodega/mostrar.php" class="btn btn-primary">
        <i class="material-icons" style="margin-right: 8px;">dashboard</i>
        Volver al Dashboard
      </a>
      <a href="lista_parte.php" class="btn btn-secondary">
        <i class="material-icons" style="margin-right: 8px;">inventory</i>
        Ver Inventario de Partes
      </a>
    </div>
  </div>
  <!-- Scripts -->
  <script src="../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script>
    // Actualizar inventario_id cuando se selecciona un equipo
    document.getElementById('codigo_equipo').addEventListener('change', function() {
      const selectedOption = this.options[this.selectedIndex];
      const inventarioId = selectedOption.getAttribute('data-id');
      document.getElementById('inventario_id').value = inventarioId || '';
      // Pre-llenar marca del equipo si está disponible
      if (inventarioId) {
        const marcaEquipo = selectedOption.getAttribute('data-marca');
        if (marcaEquipo && marcaEquipo !== 'N/A') {
          document.getElementById('marca_parte').value = marcaEquipo;
        }
      }
    });
    // Validar formulario antes de enviar
    document.querySelector('form').addEventListener('submit', function(e) {
      const inventarioId = document.getElementById('inventario_id').value;
      const detalleSolicitud = document.getElementById('detalle_solicitud').value.trim();
      const cantidadSolicitada = document.getElementById('cantidad_solicitada').value;
      const nivelUrgencia = document.getElementById('nivel_urgencia').value;
      if (!inventarioId || !detalleSolicitud || !cantidadSolicitada || !nivelUrgencia) {
        e.preventDefault();
        alert('Por favor, complete todos los campos obligatorios marcados con *.');
        return false;
      }
      if (cantidadSolicitada < 1) {
        e.preventDefault();
        alert('La cantidad solicitada debe ser mayor a 0.');
        return false;
      }
    });
  </script>
</body>
</html>