<?php
ob_start();
session_start();

if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 7])){
    header('location: ../error404.php');
    exit;
}

require_once '../../backend/bd/ctconex.php';
?>
<?php if(isset($_SESSION['id'])) { ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Generador de Códigos de Barras - PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="stylesheet" href="../../backend/css/loader.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />
</head>
<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../../backend/img/favicon.png" class="img-fluid"/><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        <!-- Page Content -->
        <div id="content">
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#"> Generador de Etiquetas ZPL </a>
                    </div>
                </nav>
            </div>
            <div class="main-content">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card mt-4">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0">Generador de Códigos de Barras para Zebra GK420T</h4>
                            </div>
                            <div class="card-body">
                                <?php
                                error_reporting(E_ALL);
                                ini_set('display_errors', 1);
                                $errores = [];
                                $datos = [];
                                function generarTexto($datos, $numero) {
                                  return $datos['proveedor'] . $datos['lote'] . str_pad($numero, 3, '0', STR_PAD_LEFT) . $datos['fecha'];
                                }
                                if (isset($_GET['zpl'])) {
                                  $datos_zpl = [
                                    'proveedor' => $_GET['proveedor'] ?? '',
                                    'cantidad' => (int) ($_GET['cantidad'] ?? 0),
                                    'lote' => $_GET['lote'] ?? '',
                                    'fecha' => substr(date('Y'), -2)
                                  ];
                                  header('Content-Type: application/octet-stream');
                                  header('Content-Disposition: attachment; filename="etiquetas.zpl"');
                                  for ($i = 1; $i <= $datos_zpl['cantidad']; $i++) {
                                    $codigo = generarTexto($datos_zpl, $i);
                                    echo "^XA\n";
                                    echo "^CF0,30\n";
                                    echo "^FO30,30^BY2\n";
                                    echo "^BCN,80,Y,N,N\n";
                                    echo "^FD>{$codigo}^FS\n";
                                    echo "^FO30,120^ADN,30,20^FD{$codigo}^FS\n";
                                    echo "^XZ\n";
                                  }
                                  exit;
                                }
                                if ($_POST) {
                                  $proveedor = strtoupper(trim($_POST['proveedor'] ?? ''));
                                  $cantidad = filter_var($_POST['cantidad'] ?? 0, FILTER_VALIDATE_INT);
                                  $lote = strtoupper(trim($_POST['lote'] ?? ''));
                                  if (empty($proveedor)) $errores[] = "Proveedor requerido";
                                  if ($cantidad <= 0) $errores[] = "Cantidad debe ser mayor a 0";
                                  if (empty($lote) || strlen($lote) !== 1) $errores[] = "Lote debe ser una letra";
                                  if (empty($errores)) {
                                    $datos = [
                                      'proveedor' => $proveedor,
                                      'cantidad' => $cantidad,
                                      'lote' => $lote,
                                      'fecha' => substr(date('Y'), -2)
                                    ];
                                    echo "<div class='alert alert-success text-center'>";
                                    echo "Datos válidos. Haz clic en el botón para descargar las etiquetas.";
                                    echo "<div class='mt-3'><a href='?zpl=1&" . http_build_query($datos) . "' target='_blank' class='btn btn-success'>Descargar ZPL</a></div>";
                                    echo "</div>";
                                  } else {
                                    echo "<div class='alert alert-danger'>" . implode('<br>', $errores) . "</div>";
                                  }
                                }
                                ?>
                                <form method="POST">
                                    <div class="form-group mb-3">
                                        <label>Proveedor:</label>
                                        <input type="text" name="proveedor" maxlength="10" class="form-control" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label>Cantidad de Etiquetas:</label>
                                        <input type="number" name="cantidad" min="1" class="form-control" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label>Lote (1 letra):</label>
                                        <input type="text" name="lote" maxlength="1" class="form-control" required>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Generar</button>
                                        <button type="reset" class="btn btn-secondary ms-2">Limpiar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card mt-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">🖨️ ¿Cómo usar el archivo .zpl con tu Zebra GK420T?</h5>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li>Haz clic en <strong>Descargar ZPL</strong> para guardar el archivo <code>etiquetas.zpl</code>.</li>
                                    <li>Conecta tu impresora Zebra GK420T y asegúrate que esté en modo ZPL.</li>
                                    <li>Arrastra el archivo sobre el ícono de la impresora, o usa este comando en Windows:<br>
                                        <code>copy /b etiquetas.zpl LPT1:</code> o <code>copy /b etiquetas.zpl "\\NombrePC\NombreImpresora"</code>
                                    </li>
                                    <li>En Mac/Linux puedes usar:<br>
                                        <code>lpr -P NombreImpresora etiquetas.zpl</code>
                                    </li>
                                </ol>
                                <p class="mb-0">💡 También puedes usar la app Zebra Setup Utilities para enviar el archivo fácilmente.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="../../backend/js/jquery-3.3.1.min.js"></script>
    <script src="../../backend/js/popper.min.js"></script>
    <script src="../../backend/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../backend/js/sidebarCollapse.js"></script>
    <script src="../../backend/js/loader.js"></script>
</body>
</html>
<?php } else { 
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>
