<?php
ob_start();
session_start();
require_once '../../config/ctconex.php';

// Verificar autenticación
if (!isset($_SESSION['rol'])) {
    header('location: ../error404.php');
    exit;
}

$usuario_id = $_SESSION['id'];
$usuario_rol = $_SESSION['rol'];
$usuario_sede = $_SESSION['idsede'] ?? '';

// Construir consulta según el rol
$sql = "SELECT
    av.id,
    av.idventa,
    av.ticket,
    av.fecha_venta,
    av.estado,
    av.total_venta,
    av.valor_abono,
    av.saldo_pendiente,
    av.concepto_salida,
    av.sede,
    c.nomcli,
    c.apecli,
    c.numid,
    u.nombre as vendedor
FROM new_alistamiento_venta av
LEFT JOIN clientes c ON av.idcliente = c.idclie
LEFT JOIN usuarios u ON av.usuario_id = u.id
WHERE 1=1";

// Aplicar filtros según rol
if ($usuario_rol == 1) {
    // Rol 1: Ve todas las ventas
    $sql .= "";
} elseif (in_array($usuario_rol, [4, 5])) {
    // Rol 4-5: Solo ve sus propias ventas
    $sql .= " AND av.usuario_id = $usuario_id";
} else {
    // Otros roles: Solo ven ventas de su misma sede
    $sql .= " AND av.sede = '$usuario_sede'";
}

$sql .= " ORDER BY av.fecha_venta DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Histórico de Ventas - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <link rel="stylesheet" href="../assets/css/datatable.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <style>
        .badge-estado {
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 20px;
        }
        .badge-borrador {
            background: #ffc107;
            color: #000;
        }
        .badge-aprobado {
            background: #28a745;
            color: #fff;
        }
        .badge-cancelado {
            background: #dc3545;
            color: #fff;
        }
        .badge-pendiente {
            background: #17a2b8;
            color: #fff;
        }
        .table-ventas {
            font-size: 14px;
        }
        .table-ventas th {
            background: linear-gradient(135deg, #2B6B5D 0%, #1a4a3f 100%);
            color: white;
            font-weight: 600;
            padding: 15px 10px;
        }
        .table-ventas td {
            padding: 12px 10px;
            vertical-align: middle;
        }
        .btn-action {
            margin: 2px;
            padding: 5px 10px;
            font-size: 12px;
        }
        .total-positivo {
            color: #28a745;
            font-weight: bold;
        }
        .total-pendiente {
            color: #dc3545;
            font-weight: bold;
        }
        .filter-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        <div id="content">
            <div class='pre-loader'>
                <img class='loading-gif' alt='loading' src="https://i.imgflip.com/9vd6wr.gif" />
            </div>
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#">
                            <span class="material-icons" style="vertical-align: middle;">history</span>
                            Histórico de Ventas
                        </a>
                        <button class="d-inline-block d-lg-none ml-auto more-button" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                            <span class="material-icons">more_vert</span>
                        </button>
                        <div class="collapse navbar-collapse d-lg-block d-xl-block d-sm-none d-md-none d-none">
                            <ul class="nav navbar-nav ml-auto">
                                <li class="dropdown nav-item active">
                                    <a href="#" class="nav-link" data-toggle="dropdown">
                                        <img src="../assets/img/reere.webp">
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="../cuenta/perfil.php">Mi perfil</a></li>
                                        <li><a href="../cuenta/salir.php">Salir</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="main-content">
                <!-- Botones de acción -->
                <div class="row mb-3">
                    <div class="col-12">
                        <?php if (in_array($usuario_rol, [1, 4])): ?>
                        <a href="nueva_venta.php" class="btn btn-success">
                            <span class="material-icons" style="vertical-align: middle;">add_circle</span>
                            Nueva Venta
                        </a>
                        <?php endif; ?>
                        <a href="escritorio.php" class="btn btn-secondary">
                            <span class="material-icons" style="vertical-align: middle;">arrow_back</span>
                            Volver al Escritorio
                        </a>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="filter-card">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Estado</label>
                            <select id="filtroEstado" class="form-control">
                                <option value="">Todos</option>
                                <option value="borrador">Borrador</option>
                                <option value="aprobado">Aprobado</option>
                                <option value="cancelado">Cancelado</option>
                                <option value="pendiente">Pendiente</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Fecha Desde</label>
                            <input type="date" id="filtroFechaDesde" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Fecha Hasta</label>
                            <input type="date" id="filtroFechaHasta" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button class="btn btn-primary btn-block" id="btnFiltrar">
                                <span class="material-icons" style="vertical-align: middle; font-size: 18px;">search</span>
                                Filtrar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de ventas -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tablaVentas" class="table table-hover table-ventas">
                                <thead>
                                    <tr>
                                        <th>ID Venta</th>
                                        <th>Ticket</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Vendedor</th>
                                        <th>Concepto</th>
                                        <th>Total</th>
                                        <th>Abono</th>
                                        <th>Saldo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result && $result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($row['idventa']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($row['ticket']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($row['fecha_venta'])); ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($row['nomcli'] . ' ' . $row['apecli']); ?></strong><br>
                                                    <small class="text-muted">NIT: <?php echo htmlspecialchars($row['numid']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['vendedor']); ?></td>
                                                <td><?php echo htmlspecialchars($row['concepto_salida']); ?></td>
                                                <td class="total-positivo">$<?php echo number_format($row['total_venta'], 0, ',', '.'); ?></td>
                                                <td>$<?php echo number_format($row['valor_abono'], 0, ',', '.'); ?></td>
                                                <td class="<?php echo $row['saldo_pendiente'] > 0 ? 'total-pendiente' : 'total-positivo'; ?>">
                                                    $<?php echo number_format($row['saldo_pendiente'], 0, ',', '.'); ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $estado = strtolower($row['estado']);
                                                    $badgeClass = 'badge-' . $estado;
                                                    ?>
                                                    <span class="badge-estado <?php echo $badgeClass; ?>">
                                                        <?php echo ucfirst($row['estado']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info btn-action" onclick="verDetalle(<?php echo $row['id']; ?>)" title="Ver Detalle">
                                                        <span class="material-icons" style="font-size: 16px;">visibility</span>
                                                    </button>
                                                    <?php if (in_array($usuario_rol, [1, 4])): ?>
                                                    <button class="btn btn-sm btn-warning btn-action" onclick="editarVenta(<?php echo $row['id']; ?>)" title="Editar">
                                                        <span class="material-icons" style="font-size: 16px;">edit</span>
                                                    </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-sm btn-success btn-action" onclick="verTicket(<?php echo $row['id']; ?>)" title="Ver Ticket">
                                                        <span class="material-icons" style="font-size: 16px;">receipt</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="11" class="text-center">No hay ventas registradas</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/loader.js"></script>
    <script src="../assets/js/datatable.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        // Toggle sidebar
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
            $('#content').toggleClass('active');
        });

        $('.more-button,.body-overlay').on('click', function() {
            $('#sidebar,.body-overlay').toggleClass('show-nav');
        });

        // Inicializar DataTable
        $('#tablaVentas').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "order": [[2, "desc"]],
            "pageLength": 25,
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: '<i class="material-icons" style="font-size: 16px;">file_download</i> Excel',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="material-icons" style="font-size: 16px;">picture_as_pdf</i> PDF',
                    titleAttr: 'Exportar a PDF',
                    className: 'btn btn-danger btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="material-icons" style="font-size: 16px;">print</i> Imprimir',
                    titleAttr: 'Imprimir',
                    className: 'btn btn-primary btn-sm'
                }
            ]
        });

        // Filtrar
        $('#btnFiltrar').click(function() {
            // Aquí puedes agregar lógica de filtrado AJAX si lo deseas
            location.reload();
        });
    });

    function verDetalle(id) {
        window.location.href = 'ver_venta.php?id=' + id;
    }

    function editarVenta(id) {
        window.location.href = 'editar_venta.php?id=' + id;
    }

    function verTicket(id) {
        window.open('ticket_venta.php?id=' + id, '_blank', 'width=800,height=600');
    }
    </script>
</body>
</html>
<?php ob_end_flush(); ?>
