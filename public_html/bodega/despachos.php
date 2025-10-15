<!-- Archivo Mientras tanto del 2025 Octubre 2 -->
 <?php
// testeo rápido 2025 Septeber 8
/* public_html/bodega/despachos.php */
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 4, 5,6, 7])) {
    header('location: ../error404.php');
    exit(); }
require_once '../../config/ctconex.php';
// Datos usuario
$userInfo = null;
if (isset($_SESSION['id'])) {
    $stmt = $connect->prepare("SELECT nombre, usuario, correo, rol, foto, idsede FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);}
if (!$userInfo) { header('location: ../error404.php'); exit(); }

// Cambio de estado de despacho (POST simple)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_estado') {
    $salidaId = (int)($_POST['salida_id'] ?? 0);
    $nuevo = $_POST['estado_despacho'] ?? 'pendiente';
    $guia = trim($_POST['guia_remision'] ?? '');
    $transportista = trim($_POST['transportista'] ?? '');
    $fotoNombre = null;
    if (!empty($_FILES['evidencia']['name']) && is_uploaded_file($_FILES['evidencia']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['evidencia']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','pdf'];
        if (in_array($ext, $allowed)) {
            $safe = 'evid_' . $salidaId . '_' . time() . '.' . $ext;
            $dest = realpath(__DIR__ . '/../assets/img');
            if ($dest && move_uploaded_file($_FILES['evidencia']['tmp_name'], $dest . DIRECTORY_SEPARATOR . $safe)) {
                $fotoNombre = $safe;
            }
        }
    }
    try {
        if ($fotoNombre) {
            $stmt = $connect->prepare("UPDATE bodega_salidas SET estado_despacho = ?, guia_remision = ?, transportista = ?, evidencia_foto = ?, despacho_fecha = NOW(), usuario_id = ? WHERE id = ?");
            $stmt->execute([$nuevo, $guia, $transportista, $fotoNombre, $_SESSION['id'], $salidaId]);
        } else {
            $stmt = $connect->prepare("UPDATE bodega_salidas SET estado_despacho = ?, guia_remision = ?, transportista = ?, despacho_fecha = NOW(), usuario_id = ? WHERE id = ?");
            $stmt->execute([$nuevo, $guia, $transportista, $_SESSION['id'], $salidaId]);
        }
    } catch (Exception $e) {}
    header('Location: despacho.php');
    exit();
}

// Listar salidas pendientes / en ruta
$salidas = [];
try {
    $q = $connect->query("SELECT s.*, i.codigo_g, i.marca, i.modelo, c.nomcli AS cliente_nombre
                          FROM bodega_salidas s
                          LEFT JOIN bodega_inventario i ON i.id = s.inventario_id
                          LEFT JOIN clientes c ON c.idclie = s.cliente_id
                          WHERE COALESCE(s.estado_despacho,'pendiente') IN ('pendiente','en_ruta')
                          ORDER BY s.fecha_salida DESC");
    $salidas = $q ? $q->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Exception $e) { $salidas = []; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Despachos - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
        <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
                <nav class="navbar navbar-expand-lg" style="background:#6c5ce7;">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#"> DESPACHO | <?php echo htmlspecialchars($userInfo['nombre'] ?? ''); ?> </a>
                    </div>
                </nav>
            </div>
            <div class="main-content">
                <div class="card">
                    <div class="card-header"><h4>Salidas pendientes / en ruta</h4></div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Equipo</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Guía</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($salidas)): ?>
                                    <tr><td colspan="7" class="text-center">Sin registros</td></tr>
                                <?php else: foreach ($salidas as $s): ?>
                                    <tr>
                                        <td><?php echo (int)$s['id']; ?></td>
                                        <td><?php echo htmlspecialchars($s['codigo_g'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars(($s['marca'] ?? '') . ' ' . ($s['modelo'] ?? '')); ?></td>
                                        <td><?php echo htmlspecialchars($s['cliente_nombre'] ?? ''); ?></td>
                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($s['estado_despacho'] ?? 'pendiente'); ?></span></td>
                                        <td><?php echo htmlspecialchars($s['guia_remision'] ?? ''); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#estadoModal" data-id="<?php echo (int)$s['id']; ?>" data-estado="<?php echo htmlspecialchars($s['estado_despacho'] ?? 'pendiente'); ?>" data-guia="<?php echo htmlspecialchars($s['guia_remision'] ?? ''); ?>" data-trans="<?php echo htmlspecialchars($s['transportista'] ?? ''); ?>">Actualizar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal actualizar estado -->
    <div class="modal fade" id="estadoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_estado">
                    <input type="hidden" name="salida_id" id="salidaId">
                    <div class="modal-header">
                        <h5 class="modal-title">Actualizar estado de despacho</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estado_despacho" id="estadoDespacho" class="form-control" required>
                                <option value="pendiente">pendiente</option>
                                <option value="en_ruta">en_ruta</option>
                                <option value="entregado">entregado</option>
                                <option value="cancelado">cancelado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Guía/Remisión</label>
                            <input type="text" name="guia_remision" id="guiaRemision" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Transportista</label>
                            <input type="text" name="transportista" id="transportista" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Evidencia (imagen/pdf)</label>
                            <input type="file" name="evidencia" class="form-control" accept="image/*,application/pdf">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        $('#estadoModal').on('show.bs.modal', function (event) {
            var btn = $(event.relatedTarget);
            $('#salidaId').val(btn.data('id'));
            $('#estadoDespacho').val(btn.data('estado'));
            $('#guiaRemision').val(btn.data('guia'));
            $('#transportista').val(btn.data('trans'));
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>