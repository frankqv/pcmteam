<?php
// lista_parte.php - Inventario de Partes Disponibles
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
$partes = [];
$estadisticas = [
    'total_partes' => 0,
    'total_stock' => 0,
    'stock_bajo' => 0,
    'marcas_unicas' => 0
];// Obtener información del usuario para navbar
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
}// Procesamiento del formulario de actualización de stock
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $connect->beginTransaction();
        $parte_id = (int) ($_POST['parte_id'] ?? 0);
        $nueva_cantidad = (int) ($_POST['nueva_cantidad'] ?? 0);
        $usuario_id = (int) ($_SESSION['id']);
        if ($parte_id > 0 && $nueva_cantidad >= 0) {
            // Obtener cantidad anterior
            $stmt = $connect->prepare("SELECT cantidad, referencia, marca FROM bodega_partes WHERE id = ? LIMIT 1");
            $stmt->execute([$parte_id]);
            $parte_actual = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($parte_actual) {
                $cantidad_anterior = (int) $parte_actual['cantidad'];
                // Actualizar stock
                $stmt = $connect->prepare("
          UPDATE bodega_partes 
          SET cantidad = ?, fecha_modificacion = NOW()
          WHERE id = ?
        ");
                $stmt->execute([$nueva_cantidad, $parte_id]);
                // Registrar cambio en log
                $stmt = $connect->prepare("
          INSERT INTO bodega_log_cambios 
          (inventario_id, usuario_id, cambio_realizado, valor_anterior, valor_nuevo, fecha_cambio)
          VALUES (?, ?, ?, ?, ?, NOW())
        ");
                $stmt->execute([
                    $parte_id,
                    $usuario_id,
                    'actualizacion_stock_parte',
                    $cantidad_anterior,
                    $nueva_cantidad
                ]);
                $mensaje .= "<div class='alert alert-success'>✅ Stock actualizado: {$parte_actual['marca']} {$parte_actual['referencia']} - {$cantidad_anterior} → {$nueva_cantidad}</div>";
            }
        }
        $connect->commit();
    } catch (Exception $e) {
        $connect->rollBack();
        $mensaje .= "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}// Cargar estadísticas y partes
try {
    // Estadísticas generales
    $stmt = $connect->prepare("
    SELECT 
      COUNT(*) as total_partes,
      SUM(cantidad) as total_stock,
      SUM(CASE WHEN cantidad <= 5 THEN 1 ELSE 0 END) as stock_bajo,
      COUNT(DISTINCT marca) as marcas_unicas
    FROM bodega_partes 
    WHERE cantidad > 0
  ");
    $stmt->execute();
    $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
    // Partes disponibles
    $stmt = $connect->prepare("
    SELECT id, caja, cantidad, marca, referencia, producto, condicion, precio, detalles, codigo, serial, fecha_modificacion
    FROM bodega_partes 
    WHERE cantidad > 0 
    ORDER BY marca, referencia
  ");
    $stmt->execute();
    $partes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error carga datos: " . $e->getMessage());
    $mensaje .= "<div class='alert alert-warning'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</div>";
}// Helper functions
function condicionBadgeClass(string $condicion): string
{
    $condicion = strtoupper(trim($condicion ?? ''));
    switch ($condicion) {
        case 'NUEVO':
            return 'condicion-nuevo';
        case 'USADO':
            return 'condicion-usado';
        case 'REFURBISHED':
            return 'condicion-refurbished';
        default:
            return 'condicion-nd';
    }
}function stockStatus(int $cantidad): string
{
    if ($cantidad <= 2)
        return 'stock-critico';
    if ($cantidad <= 5)
        return 'stock-bajo';
    if ($cantidad <= 10)
        return 'stock-medio';
    return 'stock-alto';
}
?><!DOCTYPE html>
<html lang="es"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Partes - PCMarket SAS</title>
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
        } .filtros-container {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        } .filtros-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        } .filtro-grupo label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
            color: #495057;
            font-size: 0.9em;
        } .partes-table {
            max-height: 600px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
        } .table-sm th,
        .table-sm td {
            padding: 8px;
            font-size: 0.875em;
        } .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.875em;
            font-weight: 500;
        } .condicion-nuevo {
            background-color: #d4edda;
            color: #155724;
        } .condicion-usado {
            background-color: #fff3cd;
            color: #856404;
        } .condicion-refurbished {
            background-color: #d1ecf1;
            color: #0c5460;
        } .condicion-nd {
            background-color: #e2e3e5;
            color: #495057;
        } .stock-critico {
            background-color: #f8d7da;
            color: #721c24;
        } .stock-bajo {
            background-color: #fff3cd;
            color: #856404;
        } .stock-medio {
            background-color: #d1ecf1;
            color: #0c5460;
        } .stock-alto {
            background-color: #d4edda;
            color: #155724;
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
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        } .navbar-brand {
            color: white !important;
            font-weight: bold;
            text-decoration: none;
        } .modal-header {
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            color: white;
        } .modal-header .close {
            color: white;
        }
    </style>
</head><body>
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
                    <i class="material-icons" style="margin-right: 8px;">inventory</i>
                    📦 INVENTARIO DE PARTES | <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'USUARIO'); ?>
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
                <h3><?php echo $estadisticas['total_partes']; ?></h3>
                <p>Total Partes</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $estadisticas['total_stock']; ?></h3>
                <p>Total Stock</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $estadisticas['stock_bajo']; ?></h3>
                <p>Stock Bajo (≤5)</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $estadisticas['marcas_unicas']; ?></h3>
                <p>Marcas Únicas</p>
            </div>
        </div>
        <!-- Filtros -->
        <div class="filtros-container">
            <div class="section-title">
                <div class="card-icon">🔍</div>
                <h5>Filtros de Búsqueda</h5>
            </div>
            <div class="filtros-row">
                <div class="filtro-grupo">
                    <label for="filtro_marca">Filtrar por Marca:</label>
                    <select id="filtro_marca" class="form-control">
                        <option value="">Todas las marcas</option>
                        <?php
                        $marcas_unicas = array_unique(array_column($partes, 'marca'));
                        sort($marcas_unicas);
                        foreach ($marcas_unicas as $marca):
                            if (!empty($marca)):
                                ?>
                                <option value="<?php echo htmlspecialchars($marca); ?>">
                                    <?php echo htmlspecialchars($marca); ?>
                                </option>
                                <?php
                            endif;
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="filtro-grupo">
                    <label for="filtro_producto">Filtrar por Producto:</label>
                    <select id="filtro_producto" class="form-control">
                        <option value="">Todos los productos</option>
                        <?php
                        $productos_unicos = array_unique(array_column($partes, 'producto'));
                        sort($productos_unicos);
                        foreach ($productos_unicos as $producto):
                            if (!empty($producto)):
                                ?>
                                <option value="<?php echo htmlspecialchars($producto); ?>">
                                    <?php echo htmlspecialchars($producto); ?>
                                </option>
                                <?php
                            endif;
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="filtro-grupo">
                    <label for="filtro_condicion">Filtrar por Condición:</label>
                    <select id="filtro_condicion" class="form-control">
                        <option value="">Todas las condiciones</option>
                        <option value="Nuevo">Nuevo</option>
                        <option value="Usado">Usado</option>
                        <option value="Refurbished">Refurbished</option>
                    </select>
                </div>
                <div class="filtro-grupo">
                    <label for="filtro_caja">Filtrar por Caja:</label>
                    <select id="filtro_caja" class="form-control">
                        <option value="">Todas las cajas</option>
                        <?php
                        $cajas_unicas = array_unique(array_column($partes, 'caja'));
                        sort($cajas_unicas);
                        foreach ($cajas_unicas as $caja):
                            if (!empty($caja)):
                                ?>
                                <option value="<?php echo htmlspecialchars($caja); ?>">
                                    <?php echo htmlspecialchars($caja); ?>
                                </option>
                                <?php
                            endif;
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="filtro-grupo">
                    <label for="filtro_busqueda">Buscar por Referencia:</label>
                    <input type="text" id="filtro_busqueda" class="form-control" placeholder="Escriba referencia...">
                </div>
                <div class="filtro-grupo">
                    <button type="button" id="btn_limpiar_filtros" class="btn btn-secondary">
                        <i class="material-icons" style="margin-right: 8px;">clear</i>
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>
        <!-- Tabla de Partes -->
        <div class="form-section">
            <div class="section-title">
                <div class="card-icon">📋</div>
                <h4>Lista de Partes Disponibles</h4>
            </div>
            <?php if (empty($partes)): ?>
                <div class="alert alert-info">
                    ✅ No hay partes disponibles en el inventario en este momento.
                </div>
            <?php else: ?>
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
                                <th>Última Modificación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_partes_body">
                            <?php foreach ($partes as $parte): ?>
                                <tr>
                                    <td data-caja="<?php echo htmlspecialchars($parte['caja']); ?>">
                                        <?php echo htmlspecialchars($parte['caja']); ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo stockStatus($parte['cantidad']); ?>">
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
                                        <span class="status-badge <?php echo condicionBadgeClass($parte['condicion']); ?>">
                                            <?php echo htmlspecialchars($parte['condicion']); ?>
                                        </span>
                                    </td>
                                    <td>$<?php echo number_format($parte['precio'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars((new DateTime($parte['fecha_modificacion']))->format('d/m/Y H:i')); ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="abrirModalStock(<?php echo $parte['id']; ?>, '<?php echo htmlspecialchars($parte['referencia']); ?>', '<?php echo htmlspecialchars($parte['marca']); ?>', <?php echo $parte['cantidad']; ?>)">
                                            <i class="material-icons" style="font-size: 16px;">edit</i>
                                            Stock
                                        </button>
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
            <a href="solicitar_parte.php" class="btn btn-success">
                <i class="material-icons" style="margin-right: 8px;">add_shopping_cart</i>
                Solicitar Parte
            </a>
        </div>
    </div>
    <!-- Modal de Actualización de Stock -->
    <div class="modal fade" id="modalStock" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="material-icons" style="margin-right: 8px;">edit</i>
                        Actualizar Stock - <span id="parteReferencia"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" id="parte_id" name="parte_id" value="">
                        <div class="form-group">
                            <label for="parte_info">Información de la Parte:</label>
                            <div id="parte_info" class="form-control-plaintext"></div>
                        </div>
                        <div class="form-group">
                            <label for="stock_actual">Stock Actual:</label>
                            <div id="stock_actual" class="form-control-plaintext"></div>
                        </div>
                        <div class="form-group">
                            <label for="nueva_cantidad">Nueva Cantidad:</label>
                            <input type="number" id="nueva_cantidad" name="nueva_cantidad" class="form-control" min="0"
                                required placeholder="Ingrese la nueva cantidad">
                        </div>
                        <div class="btn-container">
                            <button type="submit" class="btn btn-success">
                                <i class="material-icons" style="margin-right: 8px;">save</i>
                                Actualizar Stock
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
    <!-- Scripts -->
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        function abrirModalStock(parteId, referencia, marca, cantidad) {
            document.getElementById('parte_id').value = parteId;
            document.getElementById('parteReferencia').textContent = referencia;
            document.getElementById('parte_info').textContent = marca + ' - ' + referencia;
            document.getElementById('stock_actual').textContent = cantidad;
            document.getElementById('nueva_cantidad').value = cantidad;
            document.getElementById('nueva_cantidad').focus();
            $('#modalStock').modal('show');
        }
        // Filtros de partes
        $('#filtro_marca, #filtro_producto, #filtro_condicion, #filtro_caja').on('change', function () {
            filtrarPartes();
        });
        $('#filtro_busqueda').on('keyup', function () {
            filtrarPartes();
        });
        $('#btn_limpiar_filtros').on('click', function () {
            $('#filtro_marca').val('');
            $('#filtro_producto').val('');
            $('#filtro_condicion').val('');
            $('#filtro_caja').val('');
            $('#filtro_busqueda').val('');
            filtrarPartes();
        });
        function filtrarPartes() {
            const filtroMarca = $('#filtro_marca').val().toLowerCase();
            const filtroProducto = $('#filtro_producto').val().toLowerCase();
            const filtroCondicion = $('#filtro_condicion').val().toLowerCase();
            const filtroCaja = $('#filtro_caja').val().toLowerCase();
            const filtroBusqueda = $('#filtro_busqueda').val().toLowerCase();
            $('#tabla_partes_body tr').each(function () {
                const fila = $(this);
                const marca = fila.find('[data-marca]').data('marca').toLowerCase();
                const producto = fila.find('[data-producto]').data('producto').toLowerCase();
                const caja = fila.find('[data-caja]').data('caja').toLowerCase();
                const referencia = fila.find('[data-referencia]').data('referencia').toLowerCase();
                const condicion = fila.find('td:eq(5) .status-badge').text().toLowerCase();
                let mostrar = true;
                if (filtroMarca && marca.indexOf(filtroMarca) === -1) {
                    mostrar = false;
                }
                if (filtroProducto && producto.indexOf(filtroProducto) === -1) {
                    mostrar = false;
                }
                if (filtroCondicion && condicion.indexOf(filtroCondicion) === -1) {
                    mostrar = false;
                }
                if (filtroCaja && caja.indexOf(filtroCaja) === -1) {
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
    </script>
</body></html>