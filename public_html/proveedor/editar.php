<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('location: ../error404.php');
    exit;
}
require '../../config/ctconex.php';
// Verificar id en GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('location: ../error404.php');
    exit;
}
$id = intval($_GET['id']);
// Obtener proveedor actual
$sentencia = $connect->prepare("SELECT * FROM proveedores WHERE id = ? LIMIT 1;");
$sentencia->execute([$id]);
$proveedor = $sentencia->fetchObject();
if (!$proveedor) {
    header('location: ../error404.php');
    exit;
}
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tomar id desde POST si existe (hidden), si no usar el GET original
    $id_post = intval($_POST['id'] ?? $id);
    // Campos del formulario
    $nomenclatura = trim((string)($_POST['nomenclatura'] ?? ''));
    $nombre = trim((string)($_POST['nombre'] ?? ''));
    $celu = trim((string)($_POST['celu'] ?? ''));
    $correo = trim((string)($_POST['correo'] ?? ''));
    $dire = trim((string)($_POST['dire'] ?? ''));
    $cuiprov = trim((string)($_POST['cuiprov'] ?? '')); // ciudad
    $nit = trim((string)($_POST['nit'] ?? ''));
    $privado = isset($_POST['privado']) ? 1 : 0;
    // Preparar UPDATE (NO actualizamos id)
    $update = $connect->prepare("UPDATE proveedores 
        SET nomenclatura = ?, 
            nombre = ?, 
            celu = ?, 
            correo = ?, 
            cuiprov = ?, 
            nit = ?, 
            dire = ?, 
            privado = ? 
        WHERE id = ?");
    $ok = $update->execute([
        $nomenclatura,
        $nombre,
        $celu,
        $correo,
        $cuiprov,
        $nit,
        $dire,
        $privado,
        $id_post
    ]);
    if ($ok) {
        $mensaje = '<div class="alert alert-success">Proveedor actualizado correctamente.</div>';
        // Refrescar datos del proveedor
        $sentencia = $connect->prepare("SELECT * FROM proveedores WHERE id = ? LIMIT 1;");
        $sentencia->execute([$id_post]);
        $proveedor = $sentencia->fetchObject();
        // actualizar variable id por si cambió
        $id = $id_post;
    } else {
        $mensaje = '<div class="alert alert-danger">Error al actualizar el proveedor.</div>';
    }
}
?>
<?php if (isset($_SESSION['id'])) { ?>
    <!doctype html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Editar Proveedor - PCMARKETTEAM</title>
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/custom.css">
        <link rel="stylesheet" href="../assets/css/loader.css">
        <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
        <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    </head>
    <body>
        <div class="wrapper">
            <div class="body-overlay"></div>
            <?php include_once '../layouts/nav.php';
            include_once '../layouts/menu_data.php'; ?>
            <nav id="sidebar">
                <div class="sidebar-header">
                    <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
                </div>
                <?php renderMenu($menu); ?>
            </nav>
            <div id="content">
                <div class="top-navbar">
                    <nav class="navbar navbar-expand-lg" style="background: #fa6b6bff">
                        <div class="container-fluid">
                            <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                                <span class="material-icons">arrow_back_ios</span>
                            </button>
                            <a class="navbar-brand" href="#"> Editar Proveedor </a>
                        </div>
                    </nav>
                </div>
                <div class="main-content">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card mt-4">
                                <div class="card-header bg-warning text-white">
                                    <h4 class="mb-0">Editar Proveedor: <?php echo htmlspecialchars($proveedor->nombre ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h4>
                                </div>
                                <div class="card-body">
                                    <?php echo $mensaje; ?>
                                    <form method="POST">
                                        <!-- hidden id para asegurar que llegue por POST -->
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($proveedor->id ?? $id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                                        <div class="form-group mb-3">
                                            <label>ID Interno de Proveedor</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($proveedor->id ?? $id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Nomenclatura</label>
                                            <input type="text" name="nomenclatura" maxlength="10" class="form-control" value="<?php echo htmlspecialchars($proveedor->nomenclatura ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Nombre</label>
                                            <input type="text" name="nombre" maxlength="60" class="form-control" value="<?php echo htmlspecialchars($proveedor->nombre ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Celular</label>
                                            <input type="text" name="celu" maxlength="15"  id="celu" class="form-control" value="<?php echo htmlspecialchars($proveedor->celu ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                                        </div>
                                        <script>
                                            document.getElementById('celu').addEventListener('input', function(e) {
                                            let val = e.target.value.replace(/\D/g, '');
                                            val = val.replace(/(\d{3})(?=\d)/g, '$1 ').trim();
                                            e.target.value = val;
                                            });
                                        </script>
                                        <!-- nuevos añadidos -->
                                        <div class="form-group mb-3">
                                            <label>NIT</label>
                                            <input type="text" name="nit" maxlength="15" class="form-control" value="<?php echo htmlspecialchars($proveedor->nit ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Correo</label>
                                            <input type="email" name="correo" maxlength="60" class="form-control" value="<?php echo htmlspecialchars($proveedor->correo ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Ciudad</label>
                                            <input type="text" name="cuiprov" maxlength="20" class="form-control" value="<?php echo htmlspecialchars($proveedor->cuiprov ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Dirección</label>
                                            <input type="text" name="dire" maxlength="110" class="form-control" value="<?php echo htmlspecialchars($proveedor->dire ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Estado</label><br>
                                            <input type="checkbox" name="privado" value="1" <?php echo (isset($proveedor->privado) && $proveedor->privado == 1) ? 'checked' : ''; ?>> Activo
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-warning text-white">Guardar Cambios</button>
                                            <a href="../proveedor/mostrar.php" class="btn btn-secondary ml-2">Cancelar / Volver a la lista</a>
                                        </div>
                                    </form>
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
    </body>
    </html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>