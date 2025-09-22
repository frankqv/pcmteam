<?php
ob_start();
session_start();

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 7])) {
    header('location: ../error404.php');
    exit();
}

require_once '../../config/ctconex.php';

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
    <title>Despachos Pendientes - PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <!-- Data Tables -->
    <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/font.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <style>
        .order-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            transition: box-shadow 0.3s ease;
        }
        .order-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .serial-list {
            font-family: monospace;
            font-size: 0.9rem;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
        }
        .status-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-enviado {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <?php include_once '../layouts/nav.php';
        include_once '../layouts/menu_data.php'; ?>
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        <!-- Page Content -->
        <div id="content">
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg" style="background: #27ae60;">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#"> <B>DESPACHOS PENDIENTES</B> </a>
                        <a class="navbar-brand" href="#"> Gestión de Envíos </a>
                    </div>
                    <!-- Menú derecho (usuario) -->
                    <ul class="nav navbar-nav ml-auto">
                        <li class="dropdown nav-item active">
                            <a href="#" class="nav-link" data-toggle="dropdown">
                                <img src="../assets/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.webp'); ?>"
                                    alt="Foto de perfil"
                                    style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                            </a>
                            <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
                                <li><strong><?php echo htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?></strong></li>
                                <li><?php echo htmlspecialchars($userInfo['usuario'] ?? 'usuario'); ?></li>
                                <li><?php echo htmlspecialchars($userInfo['correo'] ?? 'correo@ejemplo.com'); ?></li>
                                <li class="mt-2">
                                    <a href="../cuenta/perfil.php" class="btn btn-sm btn-primary btn-block">Mi perfil</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
            
            <div class="main-content">
                <!-- Resumen de Despachos -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <?php
                                $sql_pendientes = "SELECT COUNT(*) as total FROM orders WHERE despacho = 'Pendiente' AND payment_status = 'Aceptado'";
                                $result_pendientes = $conn->query($sql_pendientes);
                                $pendientes = $result_pendientes->fetch_assoc()['total'];
                                ?>
                                <h4 class="mb-0"><?php echo $pendientes; ?></h4>
                                <div class="text-white-50">Órdenes Pendientes</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <?php
                                $sql_enviados = "SELECT COUNT(*) as total FROM orders WHERE despacho = 'Enviado' AND payment_status = 'Aceptado'";
                                $result_enviados = $conn->query($sql_enviados);
                                $enviados = $result_enviados->fetch_assoc()['total'];
                                ?>
                                <h4 class="mb-0"><?php echo $enviados; ?></h4>
                                <div class="text-white-50">Órdenes Enviadas</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <?php
                                $sql_total = "SELECT COUNT(*) as total FROM orders WHERE payment_status = 'Aceptado'";
                                $result_total = $conn->query($sql_total);
                                $total = $result_total->fetch_assoc()['total'];
                                ?>
                                <h4 class="mb-0"><?php echo $total; ?></h4>
                                <div class="text-white-50">Total Órdenes</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Órdenes Pendientes -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Órdenes Pendientes de Despacho</h4>
                            </div>
                            <div class="card-body">
                                <?php
                                // Consulta para obtener órdenes pendientes con detalles
                                $sql_ordenes = "SELECT o.*, c.nomcli, c.apecli, c.celu, c.dircli
                                                FROM orders o
                                                INNER JOIN clientes c ON o.user_cli = c.idclie
                                                WHERE o.despacho = 'Pendiente' 
                                                AND o.payment_status = 'Aceptado'
                                                ORDER BY o.placed_on DESC";
                                
                                $result_ordenes = $conn->query($sql_ordenes);
                                
                                if ($result_ordenes && $result_ordenes->num_rows > 0) {
                                    while ($orden = $result_ordenes->fetch_assoc()) {
                                        // Obtener detalles de la venta (seriales específicos)
                                        $sql_detalles = "SELECT vd.*, bi.codigo_g, bi.marca, bi.modelo
                                                        FROM venta_detalles vd
                                                        INNER JOIN bodega_inventario bi ON vd.inventario_id = bi.id
                                                        WHERE vd.orden_id = ?";
                                        
                                        $stmt_detalles = $conn->prepare($sql_detalles);
                                        $stmt_detalles->bind_param("i", $orden['idord']);
                                        $stmt_detalles->execute();
                                        $detalles_result = $stmt_detalles->get_result();
                                        
                                        echo "<div class='order-card'>";
                                        echo "<div class='row'>";
                                        echo "<div class='col-md-8'>";
                                        echo "<h5>Orden #" . $orden['idord'] . " - " . htmlspecialchars($orden['nomcli'] . ' ' . $orden['apecli']) . "</h5>";
                                        echo "<p><strong>Cliente:</strong> " . htmlspecialchars($orden['nomcli'] . ' ' . $orden['apecli']) . "</p>";
                                        echo "<p><strong>Teléfono:</strong> " . htmlspecialchars($orden['celu']) . "</p>";
                                        echo "<p><strong>Dirección:</strong> " . htmlspecialchars($orden['dircli']) . "</p>";
                                        echo "<p><strong>Productos:</strong> " . htmlspecialchars($orden['total_products']) . "</p>";
                                        echo "<p><strong>Total:</strong> $" . number_format($orden['total_price'], 0, ',', '.') . "</p>";
                                        echo "<p><strong>Fecha:</strong> " . htmlspecialchars($orden['placed_on']) . "</p>";
                                        echo "</div>";
                                        echo "<div class='col-md-4'>";
                                        echo "<div class='serial-list'>";
                                        echo "<strong>Seriales a Despachar:</strong><br>";
                                        
                                        $seriales = [];
                                        while ($detalle = $detalles_result->fetch_assoc()) {
                                            $seriales[] = $detalle['serial'];
                                            echo "<span class='badge badge-secondary mr-1 mb-1'>" . htmlspecialchars($detalle['serial']) . "</span>";
                                        }
                                        
                                        echo "</div>";
                                        echo "<div class='mt-3'>";
                                        echo "<button class='btn btn-success btn-sm procesar-despacho' data-orden-id='" . $orden['idord'] . "'>";
                                        echo "<i class='material-icons'>local_shipping</i> Procesar Despacho";
                                        echo "</button>"; echo "<br/>"; echo"<br/>";
                                        echo "<button class='btn btn-success btn-sm procesar-despacho' data-orden-id='" . $orden['idord'] . "'>";
                                        echo "<i href='delicado.php' class='material-icons'>local_shipping</i> Procesar Despacho";
                                        echo "</button>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                } else {
                                    echo "<div class='alert alert-info text-center'>";
                                    echo "<h4>No hay órdenes pendientes de despacho</h4>";
                                    echo "<p>Todas las órdenes han sido procesadas.</p>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para confirmar despacho -->
    <div class="modal fade" id="despachoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Despacho</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de marcar esta orden como despachada?</p>
                    <div id="despachoInfo"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirmarDespacho">Confirmar Despacho</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
    <script src="../assets/js/loader.js"></script>
    <script>
        $(document).ready(function() {
            // Manejar clic en procesar despacho
            $('.procesar-despacho').click(function() {
                var ordenId = $(this).data('orden-id');
                $('#confirmarDespacho').data('orden-id', ordenId);
                $('#despachoModal').modal('show');
            });
            
            // Confirmar despacho
            $('#confirmarDespacho').click(function() {
                var ordenId = $(this).data('orden-id');
                var button = $(this);
                
                button.prop('disabled', true).text('Procesando...');
                
                $.ajax({
                    url: '../../backend/php/procesar_despacho.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ orden_id: ordenId }),
                    success: function(response) {
                        try {
                            var result = JSON.parse(response);
                            if (result.status === 'success') {
                                alert('Despacho procesado exitosamente!');
                                location.reload();
                            } else {
                                alert('Error: ' + result.message);
                                button.prop('disabled', false).text('Confirmar Despacho');
                            }
                        } catch (e) {
                            alert('Error al procesar la respuesta del servidor');
                            button.prop('disabled', false).text('Confirmar Despacho');
                        }
                    },
                    error: function() {
                        alert('Error de conexión. Intente nuevamente.');
                        button.prop('disabled', false).text('Confirmar Despacho');
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>
