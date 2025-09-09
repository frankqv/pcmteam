<?php
/* b_room/mostrar.php - Business Room Inventory Management */
ob_start();
session_start();

// Verificación de roles mejorada
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('location: ../error404.php');
    exit();
}

require_once '../../config/ctconex.php';

// Configuración de la página
$pageTitle = "Business Room - Inventario";
$pageIcon = "business_center";

// Obtener información del usuario
$userInfo = null;
if (isset($_SESSION['id'])) {
    $sql = "SELECT nombre, usuario, correo, rol, foto, idsede FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userInfo = $result->fetch_assoc();
}

if (!$userInfo) {
    header('Location: ../error404.php');
    exit();
}

// Obtener técnicos para filtros
$tecnicos = [];
$resultTec = $conn->query("SELECT id, nombre FROM usuarios WHERE rol IN ('5','6','7') ORDER BY nombre");
while ($rowTec = $resultTec->fetch_assoc()) {
    $tecnicos[] = $rowTec;
}

// Estadísticas del Business Room
$stats = [];
$whereClause = "estado = 'activo' AND disposicion IN ('Para Venta', 'Business', 'para_venta', 'Business Room')";

if (in_array($_SESSION['rol'], [5, 6, 7])) {
    $whereClause .= " AND tecnico_id = " . $_SESSION['id'];
}

// Total equipos
$sql = "SELECT COUNT(*) as total FROM bodega_inventario WHERE " . $whereClause;
$result = $conn->query($sql);
$stats['total'] = $result->fetch_assoc()['total'];

// Equipos con precio
$sql = "SELECT COUNT(*) as con_precio FROM bodega_inventario WHERE " . $whereClause . " AND precio IS NOT NULL AND precio != '' AND precio != '0'";
$result = $conn->query($sql);
$stats['con_precio'] = $result->fetch_assoc()['con_precio'];

// Equipos sin precio
$stats['sin_precio'] = $stats['total'] - $stats['con_precio'];

// Equipos con foto
$sql = "SELECT COUNT(*) as con_foto FROM bodega_inventario WHERE " . $whereClause . " AND foto IS NOT NULL AND foto != ''";
$result = $conn->query($sql);
$stats['con_foto'] = $result->fetch_assoc()['con_foto'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $pageTitle ?> - PCMARKETTEAM</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    
    <style>
        .business-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .business-card:hover {
            transform: translateY(-5px);
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-business_room { background: #28a745; color: white; }
        .status-para_venta { background: #17a2b8; color: white; }
        .status-business { background: #6f42c1; color: white; }
        .price-highlight { font-weight: bold; color: #28a745; }
        .price-missing { color: #dc3545; font-style: italic; }
        .equipo-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .equipo-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .filtros-avanzados {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
        
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        
        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand text-white" href="#">
                            <i class="material-icons">business_center</i>
                            <strong>BUSINESS ROOM</strong> - <?= htmlspecialchars($userInfo['nombre']) ?>
                        </a>
                        <a class="navbar-brand text-white" href="#">Inventario de Venta</a>
                    </div>
                    <!-- Menú usuario -->
                    <ul class="nav navbar-nav ml-auto">
                        <li class="dropdown nav-item active">
                            <a href="#" class="nav-link" data-toggle="dropdown">
                                <img src="../assets/img/<?= htmlspecialchars($userInfo['foto'] ?? 'reere.webp'); ?>"
                                    alt="Foto de perfil" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                            </a>
                            <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
                                <li><strong><?= htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?></strong></li>
                                <li><?= htmlspecialchars($userInfo['usuario'] ?? 'usuario'); ?></li>
                                <li><?= htmlspecialchars($userInfo['correo'] ?? 'correo@ejemplo.com'); ?></li>
                                <li><?= htmlspecialchars(trim($userInfo['idsede'] ?? '') !== '' ? $userInfo['idsede'] : 'Sede sin definir'); ?></li>
                                <li class="mt-2">
                                    <a href="../cuenta/perfil.php" class="btn btn-sm btn-primary btn-block">Mi perfil</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="main-content">
                <!-- Estadísticas del Business Room -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card business-card">
                            <div class="card-body text-center">
                                <i class="material-icons" style="font-size: 3rem; margin-bottom: 10px;">inventory</i>
                                <h2 class="mb-0"><?= $stats['total'] ?></h2>
                                <div class="text-white-50">Total Equipos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="material-icons" style="font-size: 3rem; margin-bottom: 10px;">attach_money</i>
                                <h2 class="mb-0"><?= $stats['con_precio'] ?></h2>
                                <div class="text-white-50">Con Precio</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <i class="material-icons" style="font-size: 3rem; margin-bottom: 10px;">money_off</i>
                                <h2 class="mb-0"><?= $stats['sin_precio'] ?></h2>
                                <div class="text-white-50">Sin Precio</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <i class="material-icons" style="font-size: 3rem; margin-bottom: 10px;">photo_camera</i>
                                <h2 class="mb-0"><?= $stats['con_foto'] ?></h2>
                                <div class="text-white-50">Con Foto</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filtros Avanzados -->
                <div class="filtros-avanzados">
                    <h5><i class="material-icons">filter_list</i> Filtros de Búsqueda</h5>
                    <form id="filterForm" class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><small>Estado</small></label>
                                <select class="form-control form-control-sm" id="filterEstado">
                                    <option value="">Todos</option>
                                    <option value="Business Room">Business Room</option>
                                    <option value="Para Venta">Para Venta</option>
                                    <option value="Business">Business</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><small>Ubicación</small></label>
                                <select class="form-control form-control-sm" id="filterUbicacion">
                                    <option value="">Todas</option>
                                    <option value="Principal">Principal</option>
                                    <option value="Bodega">Bodega</option>
                                    <option value="Laboratorio">Laboratorio</option>
                                    <option value="Exhibición">Exhibición</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><small>Grado</small></label>
                                <select class="form-control form-control-sm" id="filterGrado">
                                    <option value="">Todos</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><small>Precio</small></label>
                                <select class="form-control form-control-sm" id="filterPrecio">
                                    <option value="">Todos</option>
                                    <option value="con_precio">Con Precio</option>
                                    <option value="sin_precio">Sin Precio</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><small>Técnico</small></label>
                                <select class="form-control form-control-sm" id="filterTecnico">
                                    <option value="">Todos</option>
                                    <?php foreach ($tecnicos as $tecnico): ?>
                                        <option value="<?= $tecnico['id'] ?>"><?= htmlspecialchars($tecnico['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><small>&nbsp;</small></label>
                                <div class="btn-group btn-block">
                                    <button type="button" class="btn btn-primary btn-sm" id="applyFilters">
                                        <i class="material-icons">search</i> Filtrar
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" id="clearFilters">
                                        <i class="material-icons">clear</i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Tabla de Inventario -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5><i class="material-icons">inventory_2</i> Inventario Business Room</h5>
                                <div class="btn-group">
                                    <button class="btn btn-success btn-sm" id="addPriceBulk">
                                        <i class="material-icons">attach_money</i> Agregar Precios
                                    </button>
                                    <button class="btn btn-info btn-sm" id="exportData">
                                        <i class="material-icons">file_download</i> Exportar
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="inventarioTable" class="table table-striped table-hover">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th><input type="checkbox" id="selectAll"></th>
                                                <th>Código</th>
                                                <th>Producto</th>
                                                <th>Marca/Modelo</th>
                                                <th>Serial</th>
                                                <th>Ubicación</th>
                                                <th>Grado</th>
                                                <th>Estado</th>
                                                <th>Precio</th>
                                                <th>Técnico</th>
                                                <th>Modificación</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT i.*, 
                                                CASE 
                                                    WHEN d.estado_reparacion IS NOT NULL THEN d.estado_reparacion
                                                    WHEN cc.estado_final IS NOT NULL THEN cc.estado_final
                                                    ELSE i.disposicion 
                                                END as estado_actual,
                                                u.nombre as tecnico_nombre
                                                FROM bodega_inventario i
                                                LEFT JOIN bodega_diagnosticos d ON i.id = d.inventario_id 
                                                    AND d.id = (SELECT MAX(id) FROM bodega_diagnosticos WHERE inventario_id = i.id)
                                                LEFT JOIN bodega_control_calidad cc ON i.id = cc.inventario_id 
                                                    AND cc.id = (SELECT MAX(id) FROM bodega_control_calidad WHERE inventario_id = i.id)
                                                LEFT JOIN usuarios u ON i.tecnico_id = u.id
                                                WHERE " . $whereClause;
                                            $sql .= " ORDER BY i.fecha_modificacion DESC";
                                            
                                            $result = $conn->query($sql);
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $statusClass = 'status-' . strtolower(str_replace(' ', '_', $row['estado_actual']));
                                                    $precioTxt = '';
                                                    $precioClass = '';
                                                    
                                                    if (isset($row['precio']) && $row['precio'] !== '' && $row['precio'] !== '0') {
                                                        $precioTxt = '$' . number_format((float)$row['precio'], 0, ',', '.');
                                                        $precioClass = 'price-highlight';
                                                    } else {
                                                        $precioTxt = "Sin precio";
                                                        $precioClass = 'price-missing';
                                                    }
                                                    
                                                    echo "<tr>";
                                                    echo "<td><input type='checkbox' class='equipo-checkbox' value='" . $row['id'] . "'></td>";
                                                    echo "<td><strong>" . htmlspecialchars($row['codigo_g']) . "</strong></td>";
                                                    echo "<td>" . htmlspecialchars($row['producto']) . "</td>";
                                                    echo "<td><small>" . htmlspecialchars($row['marca'] . ' ' . $row['modelo']) . "</small></td>";
                                                    echo "<td><code>" . htmlspecialchars($row['serial']) . "</code></td>";
                                                    echo "<td>" . htmlspecialchars($row['ubicacion']) . "</td>";
                                                    echo "<td><span class='badge badge-info'>" . htmlspecialchars($row['grado']) . "</span></td>";
                                                    echo "<td><span class='status-badge " . $statusClass . "'>" . htmlspecialchars($row['estado_actual']) . "</span></td>";
                                                    echo "<td class='" . $precioClass . "'>" . $precioTxt . "</td>";
                                                    echo "<td><small>" . htmlspecialchars($row['tecnico_nombre'] ?? 'Sin asignar') . "</small></td>";
                                                    echo "<td><small>" . date('d/m/Y', strtotime($row['fecha_modificacion'])) . "</small></td>";
                                                    echo "<td class='text-center'>";
                                                    echo "<div class='btn-group btn-group-sm'>";
                                                    echo "<button class='btn btn-info btn-sm view-btn' data-id='" . $row['id'] . "' title='Ver detalles'><i class='material-icons'>visibility</i></button>";
                                                    echo "<button class='btn btn-primary btn-sm edit-btn' data-id='" . $row['id'] . "' title='Editar'><i class='material-icons'>edit</i></button>";
                                                    if (!isset($row['precio']) || $row['precio'] === '' || $row['precio'] === '0') {
                                                        echo "<button class='btn btn-warning btn-sm add-price-btn' data-id='" . $row['id'] . "' title='Agregar precio'><i class='material-icons'>attach_money</i></button>";
                                                    }
                                                    if ($_SESSION['rol'] == 1) {
                                                        echo "<button class='btn btn-danger btn-sm delete-btn' data-id='" . $row['id'] . "' title='Eliminar'><i class='material-icons'>delete</i></button>";
                                                    }
                                                    echo "</div></td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='12' class='text-center py-4'><i class='material-icons'>inbox</i><br>No hay equipos en Business Room</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modales y Scripts -->
    <!-- ... (incluir modales para ver detalles, agregar precios, etc.) ... -->
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTable con configuración mejorada
            var table = $('#inventarioTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                },
                pageLength: 25,
                responsive: true,
                order: [[10, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [0, 11] }
                ]
            });
            
            // Funcionalidades mejoradas
            // ... (código JavaScript para filtros, modales, etc.) ...
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>