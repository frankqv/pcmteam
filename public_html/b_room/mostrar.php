<?php
/* b_room/mostrar.php */
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('location: ../error404.php');
    exit();
}
require_once '../../config/ctconex.php';
// Obtener técnicos para filtros
$tecnicos = [];
$resultTec = $conn->query("SELECT id, nombre FROM usuarios WHERE rol IN ('5','6','7')");
while ($rowTec = $resultTec->fetch_assoc()) {
    $tecnicos[] = $rowTec;
}
// Verificar que el usuario existe y obtener su información
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
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Inventario - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/font.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <style>
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-disponible {
            background-color: #d4edda;
            color: #155724;
        }

        .status-en_diagnostico {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-en_reparacion {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-en_control {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-pendiente {
            background-color: #f5c6cb;
            color: #721c24;
        }

        .status-business_room {
            background-color: #d4edda;
            color: #155724;
        }

        .btn-precio {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <?php include_once '../layouts/nav.php';
        include_once '../layouts/menu_data.php'; ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        <div id="content">
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg" style="background: #27ae60;">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <?php
                        $titulo = "";
                        switch ($_SESSION['rol']) {
                            case 1:
                                $titulo = "ADMINISTRADOR";
                                break;
                            case 2:
                                $titulo = "DEFAULT";
                                break;
                            case 3:
                                $titulo = "CONTABLE";
                                break;
                            case 4:
                                $titulo = "COMERCIAL";
                                break;
                            case 5:
                                $titulo = "JEFE TÉCNICO";
                                break;
                            case 6:
                                $titulo = "TÉCNICO";
                                break;
                            case 7:
                                $titulo = "BODEGA";
                                break;
                            default:
                                $titulo = $userInfo['nombre'];
                                break;
                        }
                        ?>
                        <a class="navbar-brand" href="#"> <B>BUSINESS ROOM </B> <?php echo htmlspecialchars($titulo); ?></a>
                        <a class="navbar-brand" href="#"> Inventario </a>
                    </div>
                    <ul class="nav navbar-nav ml-auto">
                        <li class="dropdown nav-item active">
                            <a href="#" class="nav-link" data-toggle="dropdown">
                                <img src="../assets/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.webp'); ?>"
                                    alt="Foto de perfil" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                            </a>
                            <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
                                <li><strong><?php echo htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?></strong></li>
                                <li><?php echo htmlspecialchars($userInfo['usuario'] ?? 'usuario'); ?></li>
                                <li><?php echo htmlspecialchars($userInfo['correo'] ?? 'correo@ejemplo.com'); ?></li>
                                <li><?php echo htmlspecialchars(trim($userInfo['idsede'] ?? '') !== '' ? $userInfo['idsede'] : 'Sede sin definir'); ?></li>
                                <li class="mt-2"><a href="../cuenta/perfil.php" class="btn btn-sm btn-primary btn-block">Mi perfil</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="main-content">
                <!-- Resumen de Inventario -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <?php
                                $whereClause = "i.estado = 'activo'";
                                if (in_array($_SESSION['rol'], [5, 6, 7])) {
                                    $whereClause .= " AND i.tecnico_id = " . $_SESSION['id'];
                                }
                                $sql = "SELECT COUNT(*) as total FROM bodega_inventario i WHERE " . $whereClause;
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                ?>
                                <h4 class="mb-0"><?php echo $row['total']; ?></h4>
                                <div class="text-white-50">Total Equipos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <?php
                                $sql = "SELECT COUNT(*) as disponibles FROM bodega_inventario i WHERE " . $whereClause . " AND i.disposicion IN ('disponible', 'Business Room')";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                ?>
                                <h4 class="mb-0"><?php echo $row['disponibles']; ?></h4>
                                <div class="text-white-50">Disponibles</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <?php
                                $sql = "SELECT COUNT(*) as en_proceso FROM bodega_inventario i WHERE " . $whereClause . " AND i.disposicion IN ('en_diagnostico', 'en_reparacion', 'en_control', 'En revisión', 'En Alistamiento', 'En_Alistamiento')";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                ?>
                                <h4 class="mb-0"><?php echo $row['en_proceso']; ?></h4>
                                <div class="text-white-50">En Proceso</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white mb-4">
                            <div class="card-body">
                                <?php
                                $sql = "SELECT COUNT(*) as pendientes FROM bodega_inventario i WHERE " . $whereClause . " AND i.disposicion IN ('pendiente', 'En reparación')";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                ?>
                                <h4 class="mb-0"><?php echo $row['pendientes']; ?></h4>
                                <div class="text-white-50">Pendientes</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Filtros de Búsqueda</h4>
                            </div>
                            <div class="card-body">
                                <form id="filterForm" class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Estado</label>
                                            <select class="form-control" id="filterEstado">
                                                <option value="">Todos</option>
                                                <option value="disponible">Disponible</option>
                                                <option value="Business Room">Business Room</option>
                                                <option value="en_diagnostico">En Diagnóstico</option>
                                                <option value="en_reparacion">En Reparación</option>
                                                <option value="En revisión">En Revisión</option>
                                                <option value="en_control">En Control de Calidad</option>
                                                <option value="pendiente">Pendiente</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Ubicación</label>
                                            <select class="form-control" id="filterUbicacion">
                                                <option value="">Todas</option>
                                                <option value="Principal">Principal</option>
                                                <option value="Bodega">Bodega</option>
                                                <option value="Laboratorio">Laboratorio</option>
                                                <option value="Exhibición">Exhibición</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Grado</label>
                                            <select class="form-control" id="filterGrado">
                                                <option value="">Todos</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-block" id="applyFilters">Aplicar Filtros</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tabla de Inventario -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Inventario Detallado</h4>
                                <button type="button" id="enviarVentasBtn" class="btn btn-success" disabled>
                                    <i class="material-icons">shopping_cart</i> Enviar Seleccionados a Ventas
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="inventarioTable" class="table table-striped table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll" title="Seleccionar todos"></th>
                                                <th>Código</th>
                                                <th>Producto</th>
                                                <th>Marca</th>
                                                <th>Modelo</th>
                                                <th>Serial</th>
                                                <th>Ubicación</th>
                                                <th>Grado</th>
                                                <th>Disposicion</th>
                                                <th>Precio</th>
                                                <th>Técnico</th>
                                                <th>Última Modificación</th>
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
                                                WHERE i.estado = 'activo' 
                                                AND i.disposicion IN ('Para Venta', 'Business', 'para_venta', 'aprobado', 'Business Room', 'en_control')";
                                            if (in_array($_SESSION['rol'], [5, 6, 7])) {
                                                $sql .= " AND i.tecnico_id = " . $_SESSION['id'];
                                            }
                                            $sql .= " ORDER BY i.fecha_modificacion DESC";
                                            $result = $conn->query($sql);
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $statusClass = 'status-' . strtolower(str_replace(' ', '_', $row['estado_actual']));
                                                    echo "<tr>";
                                                    echo "<td><input type='checkbox' class='equipo-checkbox' value='" . $row['id'] . "'></td>";
                                                    echo "<td>" . htmlspecialchars($row['codigo_g']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['producto']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['marca']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['serial']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['ubicacion']) . "</td>";
                                                    echo "<td><span class='badge badge-info'>" . htmlspecialchars($row['grado']) . "</span></td>";
                                                    echo "<td><span class='status-badge " . $statusClass . "'>" . htmlspecialchars($row['estado_actual']) . "</span></td>";
                                                    // Precio con botón siempre visible
                                                    $precioTxt = '';
                                                    $precioActual = isset($row['precio']) && $row['precio'] !== '' && $row['precio'] !== '0' ? $row['precio'] : '';
                                                    if ($precioActual) {
                                                        $precioTxt = '<div>$' . number_format((float)$precioActual, 0, ',', '.') . '</div>';
                                                    } else {
                                                        $precioTxt = '<div class="text-danger">Sin precio</div>';
                                                    }
                                                    $precioTxt .= '<button type="button" class="btn btn-success btn-sm btn-precio edit-price-btn mt-1" data-id="' . $row['id'] . '" data-precio="' . $precioActual . '" title="Editar precio">
                                                        <i class="material-icons" style="font-size:14px;">edit</i> Precio
                                                    </button>';
                                                    echo "<td>" . $precioTxt . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['tecnico_nombre'] ?? 'Sin asignar') . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['fecha_modificacion']) . "</td>";
                                                    echo "<td class='text-center'>
                                                        <a href='javascript:void(0)' class='btn btn-info btn-sm view-btn' data-id='" . $row['id'] . "' title='Ver detalles'><i class='material-icons'>visibility</i></a>
                                                        <a href='javascript:void(0)' class='btn btn-primary btn-sm edit-btn' data-id='" . $row['id'] . "' title='Editar'><i class='material-icons'>edit</i></a>";
                                                    if ($_SESSION['rol'] == 1) {
                                                        echo "<a href='javascript:void(0)' class='btn btn-danger btn-sm delete-btn' data-id='" . $row['id'] . "' title='Eliminar'><i class='material-icons'>delete</i></a>";
                                                    }
                                                    echo "</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='13' class='text-center'>No hay equipos listos para enviar a ventas</td></tr>";
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
    <!-- Modal Ver Detalles -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Equipo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="viewModalBody">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Editar Precio -->
    <div class="modal fade" id="priceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="priceForm" method="post" action="../controllers/update_price.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Precio y Foto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- begin:: Precio -->
                    <?php
                    // Supongamos que te pasan por GET o POST el inventario_id
                    $inventario_id = $_GET['inventario_id'] ?? null;
                    $precioActual = null;
                    if ($inventario_id) {
                        require_once '../../config/ctconex.php';
                        $stmt = $pdo->prepare("SELECT precio FROM inventarios WHERE id = :id");
                        $stmt->execute(['id' => $inventario_id]);
                        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($fila) {
                            $precioActual = $fila['precio'];
                        }
                    }
                    ?>
                    <div class="modal-body">
                        <input type="hidden" name="inventario_id" id="priceInventarioId">
                        <div class="form-group">
                            <label>Precio <span class="text-danger">*</span></label>
                            <input type="text" id="precio" name="precio"
                                class="form-control" placeholder="$0" required>
                            <small class="form-text text-muted">Precio actual: <span id="precioActualText">N/A</span></small>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const priceInput = document.getElementById('precio');
                                if (!priceInput) return;
                                priceInput.addEventListener('input', function() {
                                    let raw = this.value.replace(/\D/g, '');
                                    if (raw === '') {
                                        this.value = '';
                                        return;
                                    }
                                    let withDots = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                    this.value = '$' + withDots;
                                });
                                priceInput.addEventListener('focus', function() {
                                    setTimeout(() => {
                                        this.setSelectionRange(this.value.length, this.value.length);
                                    }, 0);
                                });
                                const form = document.getElementById('priceForm');
                                if (form) {
                                    form.addEventListener('submit', function(e) {
                                        const cleanValue = priceInput.value.replace(/[$\.]/g, '');
                                        const hiddenInput = document.createElement('input');
                                        hiddenInput.type = 'hidden';
                                        hiddenInput.name = 'precio_clean';
                                        hiddenInput.value = cleanValue;
                                        this.appendChild(hiddenInput);
                                    });
                                }
                            });
                        </script>
                        <!-- End:: finlaizacion popUp de Cambiar precio  -->
                        <div class="form-group">
                            <label>Foto (opcional)</label>
                            <input type="file" class="form-control-file" name="foto" accept="image/*">
                            <small class="form-text text-muted">Deja vacío si no deseas cambiar la foto</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
    <script src="../assets/js/loader.js"></script>
    <script type="text/javascript" src="../assets/js/datatable.js"></script>
    <script type="text/javascript" src="../assets/js/datatablebuttons.js"></script>
    <script type="text/javascript" src="../assets/js/jszip.js"></script>
    <script type="text/javascript" src="../assets/js/pdfmake.js"></script>
    <script type="text/javascript" src="../assets/js/vfs_fonts.js"></script>
    <script type="text/javascript" src="../assets/js/buttonshtml5.js"></script>
    <script type="text/javascript" src="../assets/js/buttonsprint.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#inventarioTable').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                language: {
                    url: '../assets/js/spanish.json'
                },
                pageLength: 25,
                responsive: true,
                order: [
                    [11, 'desc']
                ]
            });
            $('#applyFilters').click(function() {
                var estado = $('#filterEstado').val();
                var ubicacion = $('#filterUbicacion').val();
                var grado = $('#filterGrado').val();
                table.columns(8).search(estado);
                table.columns(6).search(ubicacion);
                table.columns(7).search(grado);
                table.draw();
            });
            $('#filterForm').append('<div class="col-md-12 mt-2"><button type="button" class="btn btn-secondary" id="clearFilters">Limpiar Filtros</button></div>');
            $('#clearFilters').click(function() {
                $('#filterEstado, #filterUbicacion, #filterGrado').val('');
                table.search('').columns().search('').draw();
            });
            // Ver detalles
            $(document).on('click', '.view-btn', function() {
                var id = $(this).data('id');
                $('#viewModalBody').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Cargando...</span></div></div>');
                $('#viewModal').modal('show');
                $.ajax({
                    url: '../controllers/get_inventario_details.php',
                    type: 'GET',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        $('#viewModalBody').html(response);
                    },
                    error: function() {
                        $('#viewModalBody').html('<div class="alert alert-danger">Error al cargar los detalles del equipo.</div>');
                    }
                });
            });
            // Editar equipo
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                window.location.href = 'editar_inventario.php?id=' + id;
            });
            // NUEVO: Editar precio (funciona siempre, con o sin precio previo)
            $(document).on('click', '.edit-price-btn', function() {
                var id = $(this).data('id');
                var precioActual = $(this).data('precio');
                $('#priceInventarioId').val(id);
                $('#pricePrecioInput').val(precioActual || '');
                $('#precioActualText').text(precioActual ? '$' + parseFloat(precioActual).toLocaleString('es-CO') : 'Sin precio');
                $('#priceModal').modal('show');
            });
            // Eliminar equipo
            $(document).on('click', '.delete-btn', function() {
                if (confirm('¿Está seguro de que desea eliminar este equipo del inventario?')) {
                    var id = $(this).data('id');
                    var button = $(this);
                    button.prop('disabled', true);
                    $.ajax({
                        url: '../../backend/php/delete_inventario.php',
                        type: 'POST',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            try {
                                var result = JSON.parse(response);
                                if (result.success) {
                                    alert('Equipo eliminado exitosamente');
                                    location.reload();
                                } else {
                                    alert('Error: ' + result.message);
                                    button.prop('disabled', false);
                                }
                            } catch (e) {
                                alert('Equipo eliminado exitosamente');
                                location.reload();
                            }
                        },
                        error: function() {
                            alert('Error al eliminar el equipo');
                            button.prop('disabled', false);
                        }
                    });
                }
            });
            // Selección múltiple
            $('#selectAll').change(function() {
                $('.equipo-checkbox').prop('checked', this.checked);
                updateEnviarButton();
            });
            $('.equipo-checkbox').change(function() {
                updateEnviarButton();
                if (!this.checked) {
                    $('#selectAll').prop('checked', false);
                }
                var totalCheckboxes = $('.equipo-checkbox').length;
                var checkedCheckboxes = $('.equipo-checkbox:checked').length;
                if (checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0) {
                    $('#selectAll').prop('checked', true);
                }
            });

            function updateEnviarButton() {
                var selectedCount = $('.equipo-checkbox:checked').length;
                if (selectedCount > 0) {
                    $('#enviarVentasBtn').prop('disabled', false).text('Enviar ' + selectedCount + ' Equipos a Ventas');
                } else {
                    $('#enviarVentasBtn').prop('disabled', true).html('<i class="material-icons">shopping_cart</i> Enviar Seleccionados a Ventas');
                }
            }
            // Enviar equipos a ventas
            $('#enviarVentasBtn').click(function() {
                var selectedIds = [];
                $('.equipo-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });
                if (selectedIds.length === 0) {
                    alert('Por favor seleccione al menos un equipo');
                    return;
                }
                if (!confirm('¿Está seguro de enviar ' + selectedIds.length + ' equipos a ventas?')) {
                    return;
                }
                var button = $(this);
                button.prop('disabled', true).html('<i class="material-icons">hourglass_empty</i> Procesando...');
                $.ajax({
                    url: '../../backend/php/procesar_envio_ventas.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        equipos_ids: selectedIds
                    }),
                    success: function(response) {
                        try {
                            var result = JSON.parse(response);
                            if (result.status === 'success') {
                                var message = result.message;
                                if (result.warnings) message += '\n\nAdvertencias: ' + result.warnings;
                                if (result.info) message += '\n\nInformación: ' + result.info;
                                alert(message);
                                location.reload();
                            } else {
                                alert('Error: ' + result.message);
                                button.prop('disabled', false).html('<i class="material-icons">shopping_cart</i> Enviar Seleccionados a Ventas');
                            }
                        } catch (e) {
                            alert('Error al procesar la respuesta del servidor');
                            button.prop('disabled', false).html('<i class="material-icons">shopping_cart</i> Enviar Seleccionados a Ventas');
                        }
                    },
                    error: function() {
                        alert('Error de conexión. Intente nuevamente.');
                        button.prop('disabled', false).html('<i class="material-icons">shopping_cart</i> Enviar Seleccionados a Ventas');
                    }
                });
            });
        });
    </script>
</body>

</html>
<?php ob_end_flush(); ?>