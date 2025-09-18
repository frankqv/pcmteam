<?php
/* public_html/bodega/orden_nueva.php */
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1,4,5,6,7])) {
    header('location: ../error404.php');
    exit();
}
require_once '../../config/ctconex.php';

// Clientes
$clientes = [];
try {
    $q = $connect->query("SELECT idclie, nomcli, apecli FROM clientes ORDER BY nomcli ASC");
    $clientes = $q ? $q->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Nueva Orden - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
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
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg" style="background:#00b894;">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#"> NUEVA ORDEN </a>
                    </div>
                </nav>
            </div>
            <div class="main-content">
                <div class="card">
                    <div class="card-header"><h4>Crear Orden</h4></div>
                    <div class="card-body">
                        <form method="post" action="../controllers/st_add_order.php" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Cliente</label>
                                    <select name="cliente_id" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <?php foreach ($clientes as $c): ?>
                                            <option value="<?php echo (int)$c['idclie']; ?>"><?php echo htmlspecialchars(($c['nomcli']??'').' '.($c['apecli']??'')); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Total items</label>
                                    <input type="number" min="0" name="total_items" class="form-control" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Total pago</label>
                                    <input type="number" step="0.01" min="0" name="total_pago" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Fecha pago</label>
                                    <input type="datetime-local" name="fecha_pago" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Método de pago</label>
                                    <input type="text" name="metodo_pago" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Estado de pago</label>
                                    <select name="estado_pago" class="form-control" required>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Debe_plata">Debe_plata</option>
                                        <option value="Aceptado">Aceptado</option>
                                        <option value="total_pagado">total_pagado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Tipo de documento</label>
                                    <select name="tipo_doc" class="form-control" required>
                                        <option value="factura">factura</option>
                                        <option value="ticket">ticket</option>
                                        <option value="remision">remision</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Número de documento</label>
                                    <input type="text" name="num_documento" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Despachado en</label>
                                    <input type="text" name="despachado_en" class="form-control" placeholder="Sede / dirección / comentario">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Evidencia de pago</label>
                                <input type="file" name="evidencia_pago" class="form-control" accept="image/*,application/pdf">
                            </div>
                            <div class="text-center">
                                <button class="btn btn-primary">Crear Orden</button>
                                <a href="despacho.php" class="btn btn-secondary">Volver</a>
                            </div>
                        </form>
                        <hr>
                        <h5>Registrar Ingreso (Pago)</h5>
                        <form method="post" action="../controllers/st_add_ingreso.php" class="mt-3">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>ID Orden</label>
                                    <input type="number" name="orden_id" class="form-control" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Monto</label>
                                    <input type="number" step="0.01" min="0" name="monto" class="form-control" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Método</label>
                                    <input type="text" name="metodo_pago" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Referencia</label>
                                    <input type="text" name="referencia_pago" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Notas</label>
                                <input type="text" name="notas" class="form-control">
                            </div>
                            <div class="text-center">
                                <button class="btn btn-success">Registrar Ingreso</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>


