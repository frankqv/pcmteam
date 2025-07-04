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
    <title>Generador de Etiquetas - PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="stylesheet" href="../../backend/css/loader.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />
    <style>
        .etiqueta-preview {
            width: 5cm;
            height: 2.5cm;
            border: 1px solid #00;
            display: inline-block;
            margin: 0.1cm;
            padding: 0.2cm;
            box-sizing: border-box;
            background: white;
        }
        .barcode-container {
            text-align: center;
            margin-bottom: 0.1cm;
        }
        .texto-codigo {
            text-align: center;
            font-size: 8px;
            font-family: Arial;
        }
        .preview-section {
            display: none;
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
                        <a class="navbar-brand" href="#"> Generador de Etiquetas </a>
                    </div>
                </nav>
            </div>
            <div class="main-content">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card mt-4">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0">Generador de Etiquetas (5cm x 2.5cm)</h4>
                            </div>
                            <div class="card-body">
                                <?php
                                $errores = [];
                                $datos = [];
                                
                                function generarTexto($datos, $numero) {
                                    return $datos['proveedor'] . $datos['lote'] . str_pad($numero, 3, '0', STR_PAD_LEFT) . $datos['fecha'];
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
                                        echo "Datos válidos. Haz clic en Imprimir para generar las etiquetas.";
                                        echo "</div>";
                                        
                                        // Mostrar previsualización
                                        echo "<div class='preview-section' id='previewSection'>";
                                        echo "<div class='card'>";
                                        echo "<div class='card-header bg-info text-white'>";
                                        echo "<h5 class='mb-0'>📋 Previsualización de Etiquetas</h5>";
                                        echo "</div>";
                                        echo "<div class='card-body'>";
                                        
                                        // Información del código
                                        $codigo_ejemplo = generarTexto($datos, 1);
                                        echo "<div class='alert alert-info'>";
                                        echo "<strong>Código de ejemplo:</strong> <code>{$codigo_ejemplo}</code><br>";
                                        echo "<strong>Cantidad a imprimir:</strong> {$cantidad} etiquetas<br>";
                                        echo "<strong>Medidas:</strong> 5cm x 2.5cm";
                                        echo "</div>";
                                        
                                        // Ejemplo de etiqueta
                                        echo "<div class='text-center mb-3'>";
                                        echo "<h6>Ejemplo de etiqueta:</h6>";
                                        echo "<div class='etiqueta-preview'>";
                                        echo "<div class='barcode-container'>";
                                        echo "<svg id='barcode-ejemplo'></svg>";
                                        echo "</div>";
                                        echo "<div class='texto-codigo'>{$codigo_ejemplo}</div>";
                                        echo "</div>";
                                        echo "</div>";
                                        
                                        // Botón de impresión
                                        echo "<div class='text-center mt-3'>";
                                        echo "<button onclick='imprimirEtiquetas()' class='btn btn-success btn-lg'>";
                                        echo "🖨️ Imprimir {$cantidad} Etiquetas";
                                        echo "</button>";
                                        echo "</div>";
                                        
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                        
                                        // Script para imprimir
                                        echo "<script>";
                                        echo "function imprimirEtiquetas() {";
                                        echo "  var ventana = window.open('', '_blank', 'width=800,height=600');";
                                        echo "  var html = '<html><head><title>Etiquetas</title>';";
                                        echo "  html += '<script src=\"https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js\"></script>';";
                                        echo "  html += '<style>';";
                                        echo "  html += '@media print { body { margin: 0; padding: 0; } }';";
                                        echo "  html += '.etiqueta { width: 5cm; height: 2.5cm; border: 1px solid #000; display: inline-block; margin: 0.1cm; padding: 0.2cm; box-sizing: border-box; }';";
                                        echo "  html += '.barcode { text-align: center; margin-bottom: 0.1cm; }';";
                                        echo "  html += '.texto { text-align: center; font-size: 8px; font-family: Arial; }';";
                                        echo "  html += '</style></head><body>';";
                                        
                                        // Generar etiquetas
                                        for ($i = 1; $i <= $cantidad; $i++) {
                                            $codigo = generarTexto($datos, $i);
                                            echo "  html += '<div class=\"etiqueta\">';";
                                            echo "  html += '<div class=\"barcode\"><svg id=\"barcode{$i}\"></svg></div>';";
                                            echo "  html += '<div class=\"texto\">{$codigo}</div>';";
                                            echo "  html += '</div>';";
                                        }
                                        
                                        echo "  html += '</body></html>';";
                                        echo "  ventana.document.write(html);";
                                        echo "  ventana.document.close();";
                                        echo "  ventana.focus();";
                                        echo "  setTimeout(function() {";
                                        echo "    if(typeof ventana.JsBarcode !== 'undefined') {";
                                        for ($i = 1; $i <= $cantidad; $i++) {
                                            $codigo = generarTexto($datos, $i);
                                            echo "      ventana.JsBarcode('#barcode{$i}', '{$codigo}', {";
                                            echo "        format: 'CODE128',";
                                            echo "        width: 1.5,";
                                            echo "        height: 30,";
                                            echo "        displayValue: false,";
                                            echo "        margin: 2";
                                            echo "      });";
                                        }
                                        echo "    }";
                                        echo "    setTimeout(function() {";
                                        echo "      ventana.print();";
                                        echo "    }, 1000);";
                                        echo "  }, 500);";
                                        echo "}";
                                        echo "</script>";
                                        
                                        // Script para mostrar y generar barcode
                                        echo "<script>";
                                        echo "document.addEventListener('DOMContentLoaded', function() {";
                                        echo "  document.getElementById('previewSection').style.display = 'block';";
                                        echo "  if(typeof JsBarcode !== 'undefined') {";
                                        echo "    JsBarcode('#barcode-ejemplo', '{$codigo_ejemplo}', {";
                                        echo "      format: 'CODE128',";
                                        echo "      width: 1.5,";
                                        echo "      height: 30,";
                                        echo "      displayValue: false,";
                                        echo "      margin: 2";
                                        echo "    });";
                                        echo "  }";
                                        echo "});";
                                        echo "</script>";
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
                                        <button type="submit" class="btn btn-primary">Generar Previsualización</button>
                                        <button type="reset" class="btn btn-secondary ms-2">Limpiar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card mt-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">🖨️ Instrucciones de Impresión</h5>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li>Llena el formulario con los datos requeridos.</li>
                                    <li>Haz clic en "Generar Previsualización" para ver el ejemplo.</li>
                                    <li>Haz clic en "Imprimir" para abrir la ventana de impresión.</li>
                                    <li>Configura tu impresora para papel de etiquetas de 5cm x 2.5cm.</li>
                                    <li>Imprime las etiquetas.</li>
                                </ol>
                                <p class="mb-0">💡 Asegúrate de que tu impresora esté configurada para las medidas correctas.</p>
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
    <!-- JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</body>
</html>
<?php } else { 
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?> 