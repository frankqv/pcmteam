<!-- pedidos_ruta/ver.php -->
<?php
session_start();
include_once '../../backend/bd/ctconex.php';

if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 5])){
    header('location: ../error404.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if(!$id) die('ID de pedido no válido');

// Consulta principal del pedido
$sql = "
SELECT o.*, c.nomcli, c.apecli, c.idsede, c.celu, c.correo, c.numid, s.servtxt, s.ini, s.fin, u.nombre as tecnico
FROM orders o
LEFT JOIN clientes c ON o.user_cli = c.idclie
LEFT JOIN servicio s ON s.idclie = c.idclie
LEFT JOIN usuarios u ON o.user_id = u.id
WHERE o.idord = ?
LIMIT 1
";
$stmt = $connect->prepare($sql);
$stmt->execute([$id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$pedido) die('Pedido no encontrado');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Pedido</title>
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <!----css3---->
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="stylesheet" href="../../backend/css/loader.css">
    <!-- Data Tables -->
    <link rel="stylesheet" type="text/css" href="../../backend/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../../backend/css/buttonsdataTables.css">
    <link rel="stylesheet" type="text/css" href="../../backend/css/font.css">
    <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!--google material icon-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />
</head>
<body>
<div class="container mt-4">
            <!-- layouts nav.php  |  Sidebar 
    <div class="top-navbar">
            <div class="body-overlay"></div>
        <?php    include_once '../layouts/nav.php';  include_once '../layouts/menu_data.php';    ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../../backend/img/favicon.png" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
    </div>
   Page Content  -->
    <div class="main-content">
    <h2>Detalle del Pedido #<?php echo 1000 + (int)$pedido['idord']; ?></h2>
    <table class="table table-bordered">
        <tr><th>Asesor</th><td><?php echo htmlspecialchars($pedido['tecnico'] ?? 'No asignado'); ?></td></tr>
        <tr><th>Fecha de Inicio</th><td><?php echo htmlspecialchars($pedido['ini'] ?? $pedido['placed_on']); ?></td></tr>
        <tr><th>Fecha de Entrega</th><td><?php echo htmlspecialchars($pedido['fin'] ?? ''); ?></td></tr>
        <tr><th>Descripción</th><td><?php echo htmlspecialchars($pedido['servtxt'] ?? ''); ?></td></tr>
        <tr><th>Cliente</th><td><?php echo htmlspecialchars($pedido['nomcli'] . ' ' . $pedido['apecli']); ?></td></tr>
        <tr><th>N°Idenficador</th><td><?php echo htmlspecialchars($pedido['numid']); ?></td></tr>
        <tr><th>Sede</th><td><?php echo htmlspecialchars($pedido['idsede']); ?></td></tr>
        <tr><th>Celular</th><td><?php echo htmlspecialchars($pedido['celu']); ?></td></tr>
        <tr><th>Correo</th><td><?php echo htmlspecialchars($pedido['correo']); ?></td></tr>
        <tr><th>Tienda</th><td><?php echo htmlspecialchars($pedido['idsede']); ?></td></tr>
        <tr><th>Despacho</th><td><?php echo htmlspecialchars($pedido['despacho'] ?? ''); ?></td></tr>
        <tr><th>Saldo</th><td>$<?php echo number_format($pedido['total_price'], 0, ',', '.'); ?></td></tr>
        <tr><th>Estado</th><td><?php 
            $map = [
                'Pagado' => 'Finalizado',
                'Pendiente' => 'Alistamiento',
                'Aceptado' => 'Enviado',
            ];
            echo $map[$pedido['payment_status']] ?? htmlspecialchars($pedido['payment_status']); 
        ?></td></tr>
    </table>
    <a href="mostrar.php" class="btn btn-secondary">Volver</a>
    <!-- Aquí puedes agregar la galería de fotos y más detalles -->
    </div>
</div>
</body>
</html>
