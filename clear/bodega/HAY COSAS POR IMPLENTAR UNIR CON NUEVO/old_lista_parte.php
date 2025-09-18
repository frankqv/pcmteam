<?php
// lista_parte.php - Lista de Inventario de Partes
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
$partes = [];
$marcas = [];
$productos = [];
$filtros = [
  'marca' => $_GET['marca'] ?? '',
  'producto' => $_GET['producto'] ?? '',
  'condicion' => $_GET['condicion'] ?? '',
  'caja' => $_GET['caja'] ?? ''
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

// Cargar datos iniciales
try {
  // Construir consulta con filtros
  $whereConditions = ["cantidad > 0"];
  $params = [];
  
  if (!empty($filtros['marca'])) {
    $whereConditions[] = "marca = ?";
    $params[] = $filtros['marca'];
  }
  
  if (!empty($filtros['producto'])) {
    $whereConditions[] = "producto = ?";
    $params[] = $filtros['producto'];
  }
  
  if (!empty($filtros['condicion'])) {
    $whereConditions[] = "condicion = ?";
    $params[] = $filtros['condicion'];
  }
  
  if (!empty($filtros['caja'])) {
    $whereConditions[] = "caja = ?";
    $params[] = $filtros['caja'];
  }
  
  $whereClause = implode(" AND ", $whereConditions);
  
  // Obtener partes con filtros
  $sql = "SELECT * FROM bodega_partes WHERE $whereClause ORDER BY marca, referencia, fecha_registro DESC";
  $stmt = $connect->prepare($sql);
  $stmt->execute($params);
  $partes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Obtener marcas únicas para filtros
  $stmt = $connect->query("SELECT DISTINCT marca FROM bodega_partes WHERE cantidad > 0 ORDER BY marca");
  $marcas = $stmt->fetchAll(PDO::FETCH_COLUMN);
  
  // Obtener productos únicos para filtros
  $stmt = $connect->query("SELECT DISTINCT producto FROM bodega_partes WHERE cantidad > 0 AND producto IS NOT NULL ORDER BY producto");
  $productos = $stmt->fetchAll(PDO::FETCH_COLUMN);
  
  // Obtener cajas únicas para filtros
  $stmt = $connect->query("SELECT DISTINCT caja FROM bodega_partes WHERE cantidad > 0 ORDER BY caja");
  $cajas = $stmt->fetchAll(PDO::FETCH_COLUMN);
  
} catch (Exception $e) {
  error_log("Error carga inicial: " . $e->getMessage());
  $mensaje .= "<div class='alert alert-warning'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $connect->beginTransaction();
    
    if (isset($_POST['actualizar_stock'])) {
      $parte_id = (int) $_POST['parte_id'];
      $nueva_cantidad = (int) $_POST['nueva_cantidad'];
      
      if ($nueva_cantidad < 0) {
        throw new Exception("La cantidad no puede ser negativa");
      }
      
      // Obtener cantidad actual
      $stmt = $connect->prepare("SELECT cantidad, caja, marca, referencia FROM bodega_partes WHERE id = ? LIMIT 1");
      $stmt->execute([$parte_id]);
      $parte_actual = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if (!$parte_actual) {
        throw new Exception("Parte no encontrada");
      }
      
      // Actualizar stock
      $stmt = $connect->prepare("UPDATE bodega_partes SET cantidad = ? WHERE id = ?");
      $stmt->execute([$nueva_cantidad, $parte_id]);
      
      // Registrar en log de cambios
      $stmt = $connect->prepare("
        INSERT INTO bodega_log_cambios 
        (inventario_id, usuario_id, campo_modificado, valor_anterior, valor_nuevo, tipo_cambio)
        VALUES (?, ?, ?, ?, ?, 'edicion_manual')
      ");
      $stmt->execute([
        $parte_id,
        $_SESSION['id'],
        'cantidad',
        $parte_actual['cantidad'],
        $nueva_cantidad
      ]);
      
      $mensaje .= "<div class='alert alert-success'>✅ Stock actualizado correctamente</div>";
      
      // Recargar datos
      $stmt = $connect->prepare("SELECT * FROM bodega_partes WHERE $whereClause ORDER BY marca, referencia, fecha_registro DESC");
      $stmt->execute($params);
      $partes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    $connect->commit();
    
  } catch (Exception $e) {
    $connect->rollBack();
    $mensaje .= "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
  }
}

// Helper function for condition badges
function condicionBadgeClass(string $condicion): string {
  $condicion = strtoupper(trim($condicion ?? ''));
  switch ($condicion) {
    case 'NUEVO': return 'badge-success';
    case 'USADO': return 'badge-warning';
    default: return 'badge-secondary';
  }
}

// Helper function for stock status
function stockStatus(int $cantidad): array {
  if ($cantidad <= 0) {
    return ['Sin stock', 'badge-danger'];
  } elseif ($cantidad <= 5) {
    return ['Stock bajo', 'badge-warning'];
  } elseif ($cantidad <= 20) {
    return ['Stock medio', 'badge-info'];
  } else {
    return ['Stock alto', 'badge-success'];
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventario de Partes - Sistema de Bodega</title>
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
      color: #6c757d;
    }
    .parte-card {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }
    .parte-card:hover {
      background: #e9ecef;
      border-color: #6c757d;
      transform: translateY(-2px);
    }
    .alert {
      padding: 12px 15px;
      margin-bottom: 20px;
      border-radius: 8px;
    }
    .stats-card {
      background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
      color: white;
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 4px 15px rgba(108,117,125,0.3);
    }
    .stats-number {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 10px;
    }
    .filter-section {
      background: #e9ecef;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
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
            <i class="material-icons" style="color: #6c757d;">inventory_2</i>
            Inventario de Partes
          </h1>
          <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
              <a href="inventario.php" class="btn btn-sm btn-outline-secondary">
                <i class="material-icons">inventory</i> Inventario
              </a>
              <a href="solicitar_parte.php" class="btn btn-sm btn-outline-primary">
                <i class="material-icons">add_shopping_cart</i> Solicitar Parte
              </a>
            </div>
          </div>
        </div>

        <?php echo $mensaje; ?>

        <!-- Estadísticas -->
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="stats-card">
              <div class="stats-number"><?php echo count($partes); ?></div>
              <div>Total Partes</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
              <div class="stats-number">
                <?php echo array_sum(array_column($partes, 'cantidad')); ?>
              </div>
              <div>Total Stock</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
              <div class="stats-number">
                <?php echo count(array_filter($partes, function($p) { return $p['cantidad'] <= 5; })); ?>
              </div>
              <div>Stock Bajo</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
              <div class="stats-number">
                <?php echo count(array_unique(array_column($partes, 'marca'))); ?>
              </div>
              <div>Marcas</div>
            </div>
          </div>
        </div>

        <!-- Filtros -->
        <div class="filter-section">
          <h5><i class="material-icons">filter_list</i> Filtros de Búsqueda</h5>
          <form method="GET" class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label for="marca">Marca</label>
                <select class="form-control" id="marca" name="marca">
                  <option value="">Todas</option>
                  <?php foreach ($marcas as $marca): ?>
                    <option value="<?php echo htmlspecialchars($marca); ?>" 
                            <?php echo ($filtros['marca'] === $marca) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($marca); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="producto">Producto</label>
                <select class="form-control" id="producto" name="producto">
                  <option value="">Todos</option>
                  <?php foreach ($productos as $producto): ?>
                    <option value="<?php echo htmlspecialchars($producto); ?>" 
                            <?php echo ($filtros['producto'] === $producto) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($producto); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="condicion">Condición</label>
                <select class="form-control" id="condicion" name="condicion">
                  <option value="">Todas</option>
                  <option value="Nuevo" <?php echo ($filtros['condicion'] === 'Nuevo') ? 'selected' : ''; ?>>Nuevo</option>
                  <option value="Usado" <?php echo ($filtros['condicion'] === 'Usado') ? 'selected' : ''; ?>>Usado</option>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="caja">Caja</label>
                <select class="form-control" id="caja" name="caja">
                  <option value="">Todas</option>
                  <?php foreach ($cajas as $caja): ?>
                    <option value="<?php echo htmlspecialchars($caja); ?>" 
                            <?php echo ($filtros['caja'] === $caja) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($caja); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">
                  <i class="material-icons">search</i> Filtrar
                </button>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>&nbsp;</label>
                <a href="lista_parte.php" class="btn btn-secondary btn-block">
                  <i class="material-icons">refresh</i> Limpiar
                </a>
              </div>
            </div>
          </form>
        </div>

        <!-- Lista de partes -->
        <div class="form-section">
          <div class="section-title">
            <i class="material-icons card-icon">list</i>
            <h5 class="mb-0">Partes Disponibles (<?php echo count($partes); ?> resultados)</h5>
          </div>
          
          <?php if (empty($partes)): ?>
            <div class="text-center text-muted py-5">
              <i class="material-icons" style="font-size: 64px; color: #ccc;">inventory_2</i>
              <h4>No se encontraron partes</h4>
              <p>Intente ajustar los filtros de búsqueda</p>
            </div>
          <?php else: ?>
            <div class="row">
              <?php foreach ($partes as $parte): ?>
                <div class="col-md-6 col-lg-4">
                  <div class="parte-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div>
                        <strong><?php echo htmlspecialchars($parte['referencia']); ?></strong>
                        <?php if (!empty($parte['numero_parte'])): ?>
                          <br><small class="text-muted">#<?php echo htmlspecialchars($parte['numero_parte']); ?></small>
                        <?php endif; ?>
                      </div>
                      <span class="badge <?php echo condicionBadgeClass($parte['condicion']); ?>">
                        <?php echo htmlspecialchars($parte['condicion']); ?>
                      </span>
                    </div>
                    
                    <div class="mb-2">
                      <strong>Marca:</strong> <?php echo htmlspecialchars($parte['marca']); ?><br>
                      <strong>Producto:</strong> <?php echo htmlspecialchars($parte['producto'] ?? 'N/A'); ?><br>
                      <strong>Caja:</strong> <?php echo htmlspecialchars($parte['caja']); ?>
                    </div>
                    
                    <div class="mb-2">
                      <?php 
                      $stockInfo = stockStatus($parte['cantidad']);
                      ?>
                      <strong>Stock:</strong> 
                      <span class="badge <?php echo $stockInfo[1]; ?>">
                        <?php echo $parte['cantidad']; ?> - <?php echo $stockInfo[0]; ?>
                      </span>
                    </div>
                    
                    <div class="mb-2">
                      <strong>Precio:</strong> 
                      <span class="text-success">$<?php echo number_format($parte['precio'], 0, ',', '.'); ?></span>
                    </div>
                    
                    <?php if (!empty($parte['detalles'])): ?>
                      <div class="mb-2">
                        <strong>Detalles:</strong><br>
                        <small class="text-muted"><?php echo htmlspecialchars($parte['detalles']); ?></small>
                      </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between align-items-center">
                      <small class="text-muted">
                        <i class="material-icons" style="font-size: 14px;">schedule</i>
                        <?php echo date('d/m/Y', strtotime($parte['fecha_registro'])); ?>
                      </small>
                      
                      <!-- Botón para actualizar stock -->
                      <button type="button" class="btn btn-sm btn-outline-primary" 
                              onclick="actualizarStock(<?php echo $parte['id']; ?>, '<?php echo htmlspecialchars($parte['referencia']); ?>', <?php echo $parte['cantidad']; ?>)">
                        <i class="material-icons">edit</i> Stock
                      </button>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal para actualizar stock -->
  <div class="modal fade" id="stockModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Actualizar Stock</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="POST">
            <input type="hidden" id="parte_id" name="parte_id">
            
            <div class="form-group">
              <label>Referencia</label>
              <input type="text" class="form-control" id="parte_referencia" readonly>
            </div>
            
            <div class="form-group">
              <label for="nueva_cantidad">Nueva Cantidad</label>
              <input type="number" class="form-control" id="nueva_cantidad" name="nueva_cantidad" 
                     min="0" required>
            </div>
            
            <div class="form-group">
              <button type="submit" name="actualizar_stock" class="btn btn-primary">
                <i class="material-icons">save</i> Actualizar
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
    function actualizarStock(id, referencia, cantidadActual) {
      document.getElementById('parte_id').value = id;
      document.getElementById('parte_referencia').value = referencia;
      document.getElementById('nueva_cantidad').value = cantidadActual;
      $('#stockModal').modal('show');
    }
    
    // Auto-submit del formulario
    document.querySelector('form[method="POST"]').addEventListener('submit', function() {
      const submitBtn = this.querySelector('button[name="actualizar_stock"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="material-icons">hourglass_empty</i> Actualizando...';
      }
    });
  </script>
</body>
</html>