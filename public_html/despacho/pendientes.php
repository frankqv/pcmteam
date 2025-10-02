<?php
ob_start();
session_start();

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2,3,4,5,6, 7])) {
    header('location: ../error404.php');
    exit();
}

require_once '../../config/ctconex.php';

// --- LÓGICA DE PROCESAMIENTO DE DESPACHO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orden_id'])) {
    $ordenId = $_POST['orden_id'];
    
    try {
        $conn->begin_transaction();

        // 1. Obtener los inventario_id de los productos de la orden
        $sql_detalles = "SELECT inventario_id FROM venta_detalles WHERE orden_id = ?";
        $stmt_detalles = $conn->prepare($sql_detalles);
        $stmt_detalles->bind_param("i", $ordenId);
        $stmt_detalles->execute();
        $detalles_result = $stmt_detalles->get_result();
        $inventario_ids = [];
        while ($row = $detalles_result->fetch_assoc()) {
            $inventario_ids[] = $row['inventario_id'];
        }
        
        if (empty($inventario_ids)) {
            throw new Exception("No se encontraron productos para esta orden.");
        }
        
        // 2. Actualizar el estado de los productos en la tabla de inventario
        // Esta parte es CRUCIAL para que se "descuente del catálogo"
        $placeholders = implode(',', array_fill(0, count($inventario_ids), '?'));
        $sql_update_inventario = "UPDATE bodega_inventario SET disposicion = 'Vendida', estado = 'inactivo' WHERE id IN ($placeholders)";
        $stmt_update_inventario = $conn->prepare($sql_update_inventario);
        $types = str_repeat('i', count($inventario_ids));
        $stmt_update_inventario->bind_param($types, ...$inventario_ids);
        $stmt_update_inventario->execute();

        // 3. Actualizar el estado de la orden a 'Enviado' o 'Despachado'
        // Esto hace que la orden desaparezca de la lista de pendientes
        $sql_update_order = "UPDATE orders SET despacho = 'Enviado' WHERE idord = ?";
        $stmt_update_order = $conn->prepare($sql_update_order);
        $stmt_update_order->bind_param("i", $ordenId);
        $stmt_update_order->execute();

        $conn->commit();
        echo "<script>alert('Despacho procesado con éxito.'); window.location.href='despachos.php';</script>";
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error al procesar el despacho: " . $e->getMessage() . "'); window.location.href='despachos.php';</script>";
        exit;
    }
}
// --- FIN DE LA LÓGICA DE PROCESAMIENTO DE DESPACHO ---

// Resto del código (HTML, JavaScript, etc.) para mostrar la página
// ...
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Despachos Pendientes - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
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
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <?php 
        $userInfo = [];
        if (isset($_SESSION['id'])) {
            $sql = "SELECT nombre, usuario, correo, rol, foto, idsede FROM usuarios WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $userInfo = $result->fetch_assoc();
        }
        include_once '../layouts/nav.php';
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
                        <a class="navbar-brand" href="#"> <B>DESPACHOS PENDIENTES</B> </a>
                    </div>
                </nav>
            </div>
            
            <div class="main-content">
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

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Órdenes Pendientes de Despacho</h4>
                            </div>
                            <div class="card-body">
                                <?php
                                $sql_ordenes = "SELECT o.*, c.nomcli, c.apecli, c.celu, c.dircli
                                                FROM orders o
                                                INNER JOIN clientes c ON o.user_cli = c.idclie
                                                WHERE o.despacho = 'Pendiente' 
                                                AND o.payment_status = 'Aceptado'
                                                ORDER BY o.placed_on DESC";
                                
                                $result_ordenes = $conn->query($sql_ordenes);
                                
                                if ($result_ordenes && $result_ordenes->num_rows > 0) {
                                    while ($orden = $result_ordenes->fetch_assoc()) {
                                        $sql_detalles = "SELECT vd.*, bi.codigo_g, bi.marca, bi.modelo, bi.serial
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
                                        
                                        while ($detalle = $detalles_result->fetch_assoc()) {
                                            echo "<span class='badge badge-secondary mr-1 mb-1'>" . htmlspecialchars($detalle['serial']) . "</span>";
                                        }
                                        
                                        echo "</div>";
                                        echo "<div class='mt-3'>";
                                        echo "<button class='btn btn-success btn-sm procesar-despacho' data-orden-id='" . $orden['idord'] . "'>";
                                        echo "<i class='material-icons'>local_shipping</i> Procesar Despacho";
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

    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
    <script src="../assets/js/loader.js"></script>
    <script>
        $(document).ready(function() {
            $('.procesar-despacho').click(function() {
                var ordenId = $(this).data('orden-id');
                // Crear un formulario temporal para enviar el POST
                var form = $('<form action="despachos.php" method="POST" style="display:none;">' +
                             '<input type="hidden" name="orden_id" value="' + ordenId + '">' +
                             '</form>');
                $('body').append(form);
                form.submit();
            });
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>