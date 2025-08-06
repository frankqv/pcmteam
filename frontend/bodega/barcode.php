<?php
ob_start();
session_start();

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 7])) {
    header('location: ../error404.php');
    exit;
}
require_once '../../backend/bd/ctconex.php';
?>
<?php if (isset($_SESSION['id'])) { ?>
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
                border: 1px solid #000;
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

            .preview-fila {
                display: flex;
                justify-content: center;
                gap: 0.2cm;
                margin-bottom: 0.3cm;
            }

            .formato-info {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 15px;
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
                    <h3><img src="../../backend/img/favicon.png" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
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
                                    <h4 class="mb-0" style="color: black;">Generador de Etiquetas - Formato 2 por Hoja</h4>
                                </div>
                                <div class="card-body">
                                    <div class="formato-info">
                                        <h6><strong>📏 Formato de Impresión:</strong></h6>
                                        <ul class="mb-0">
                                            <li><strong>Hoja:</strong> 10cm (ancho) x 2.5cm (alto)</li>
                                            <li><strong>Etiquetas:</strong> 2 etiquetas por hoja de 5cm x 2.5cm cada una</li>
                                            <li><strong>Distribución:</strong> Horizontal lado a lado</li>
                                        </ul>
                                    </div>

                                    <?php
                                    $errores = [];
                                    $datos = [];

                                    function generarTexto($datos, $numero)
                                    {
                                        return $datos['proveedor'] . $datos['lote'] . str_pad($numero, 3, '0', STR_PAD_LEFT) . $datos['fecha'];
                                    }

                                    if ($_POST) {
                                        $proveedor = strtoupper(trim($_POST['proveedor'] ?? ''));
                                        $cantidad = filter_var($_POST['cantidad'] ?? 0, FILTER_VALIDATE_INT);
                                        $lote = strtoupper(trim($_POST['lote'] ?? ''));

                                        if (empty($proveedor))
                                            $errores[] = "Proveedor requerido";
                                        if ($cantidad <= 0)
                                            $errores[] = "Cantidad debe ser mayor a 0";
                                        if (empty($lote) || strlen($lote) !== 1)
                                            $errores[] = "Lote debe ser una letra";

                                        if (empty($errores)) {
                                            $datos = [
                                                'proveedor' => $proveedor,
                                                'cantidad' => $cantidad,
                                                'lote' => $lote,
                                                'fecha' => substr(date('Y'), -2)
                                            ];

                                            $hojas_necesarias = ceil($cantidad / 2);
                                            echo "<div class='alert alert-success text-center'>";
                                            echo "Datos válidos. Se generarán <strong>{$hojas_necesarias} hojas</strong> para {$cantidad} etiquetas.";
                                            echo "</div>";

                                            // Mostrar previsualización
                                            echo "<div class='preview-section' id='previewSection'>";
                                            echo "<div class='card'>";
                                            echo "<div class='card-header bg-info text-white'>";
                                            echo "<h5 class='mb-0'>📋 Previsualización de Etiquetas</h5>";
                                            echo "</div>";
                                            echo "<div class='card-body'>";

                                            // Información del código
                                            $codigo_ejemplo1 = generarTexto($datos, 1);
                                            $codigo_ejemplo2 = generarTexto($datos, 2);
                                            echo "<div class='alert alert-info'>";
                                            echo "<strong>Códigos de ejemplo:</strong> <code>{$codigo_ejemplo1}</code> | <code>{$codigo_ejemplo2}</code><br>";
                                            echo "<strong>Cantidad a imprimir:</strong> {$cantidad} etiquetas<br>";
                                            echo "<strong>Hojas necesarias:</strong> {$hojas_necesarias}<br>";
                                            echo "<strong>Formato:</strong> 2 etiquetas por hoja (10cm x 2.5cm)";
                                            echo "</div>";

                                            // Ejemplo de fila de etiquetas
                                            echo "<div class='text-center mb-3'>";
                                            echo "<h6>Ejemplo de hoja (2 etiquetas):</h6>";
                                            echo "<div class='preview-fila'>";
                                            echo "<div class='etiqueta-preview'>";
                                            echo "<div class='barcode-container'>";
                                            echo "<svg id='barcode-ejemplo1'></svg>";
                                            echo "</div>";
                                            echo "<div class='texto-codigo'>{$codigo_ejemplo1}</div>";
                                            echo "</div>";
                                            echo "<div class='etiqueta-preview'>";
                                            echo "<div class='barcode-container'>";
                                            echo "<svg id='barcode-ejemplo2'></svg>";
                                            echo "</div>";
                                            echo "<div class='texto-codigo'>{$codigo_ejemplo2}</div>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";

                                            // Botón de impresión
                                            echo "<div class='text-center mt-3'>";
                                            echo "<button onclick='imprimirEtiquetas()' class='btn btn-success btn-lg'>";
                                            echo "🖨️ Imprimir {$cantidad} Etiquetas ({$hojas_necesarias} Hojas)";
                                            echo "</button>";
                                            echo "</div>";

                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";

                                            // Generar códigos para JavaScript
                                            $codigos = [];
                                            for ($i = 1; $i <= $datos['cantidad']; $i++) {
                                                $codigos[] = generarTexto($datos, $i);
                                            }
                                            ?>
                                            <script>
                                                // Función para imprimir etiquetas por pares
                                                function imprimirEtiquetas() {
                                                    var ventana = window.open('', '_blank', 'width=800,height=600');
                                                    var html = '<html><head><title>Etiquetas por Pares</title>';
                                                    html += '<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>';
                                                    html += '<style>';
                                                    html += '@page { size: 10cm 2.5cm; margin: 0; }';
                                                    html += '@media print { ';
                                                    html += '  body { margin: 0; padding: 0; }';
                                                    html += '  .hoja { page-break-after: always; }';
                                                    html += '  .hoja:last-child { page-break-after: avoid; }';
                                                    html += '}';
                                                    html += '.hoja {';
                                                    html += '  width: 10cm;';
                                                    html += '  height: 2.5cm;';
                                                    html += '  display: flex;';
                                                    html += '  flex-direction: row;';
                                                    html += '  margin: 0;';
                                                    html += '  padding: 0;';
                                                    html += '  box-sizing: border-box;';
                                                    html += '}';
                                                    html += '.etiqueta {';
                                                    html += '  width: 5cm;';
                                                    html += '  height: 2.5cm;';
                                                    html += '  border: 1px solid #000;';
                                                    html += '  box-sizing: border-box;';
                                                    html += '  padding: 0.2cm;';
                                                    html += '  display: flex;';
                                                    html += '  flex-direction: column;';
                                                    html += '  justify-content: center;';
                                                    html += '  align-items: center;';
                                                    html += '  background: white;';
                                                    html += '}';
                                                    html += '.barcode {';
                                                    html += '  text-align: center;';
                                                    html += '  margin-bottom: 0.1cm;';
                                                    html += '  flex-grow: 1;';
                                                    html += '  display: flex;';
                                                    html += '  align-items: center;';
                                                    html += '  justify-content: center;';
                                                    html += '}';
                                                    html += '.texto {';
                                                    html += '  text-align: center;';
                                                    html += '  font-size: 8px;';
                                                    html += '  font-family: Arial;';
                                                    html += '  margin-top: 0.1cm;';
                                                    html += '}';
                                                    html += '</style></head><body>';
                                                    
                                                    // Códigos generados desde PHP
                                                    var codigosEtiquetas = <?php echo json_encode($codigos); ?>;
                                                    
                                                    // Generar HTML para cada hoja (2 etiquetas por hoja)
                                                    for (var i = 0; i < codigosEtiquetas.length; i += 2) {
                                                        html += '<div class="hoja">';
                                                        
                                                        // Primera etiqueta (siempre existe)
                                                        html += '<div class="etiqueta">';
                                                        html += '<div class="barcode"><svg id="barcode' + i + '"></svg></div>';
                                                        html += '<div class="texto">' + codigosEtiquetas[i] + '</div>';
                                                        html += '</div>';
                                                        
                                                        // Segunda etiqueta (si existe)
                                                        if (i + 1 < codigosEtiquetas.length) {
                                                            html += '<div class="etiqueta">';
                                                            html += '<div class="barcode"><svg id="barcode' + (i + 1) + '"></svg></div>';
                                                            html += '<div class="texto">' + codigosEtiquetas[i + 1] + '</div>';
                                                            html += '</div>';
                                                        } else {
                                                            // Espacio vacío si es número impar
                                                            html += '<div class="etiqueta" style="border: none; background: transparent;"></div>';
                                                        }
                                                        
                                                        html += '</div>';
                                                    }
                                                    
                                                    html += '<script>';
                                                    html += 'window.onload = function() {';
                                                    html += '  var codigos = ' + JSON.stringify(<?php echo json_encode($codigos); ?>) + ';';
                                                    html += '  for (var i = 0; i < codigos.length; i++) {';
                                                    html += '    JsBarcode("#barcode"+i, codigos[i], {';
                                                    html += '      format: "CODE128B",';
                                                    html += '      width: 1.2,';
                                                    html += '      height: 25,';
                                                    html += '      displayValue: false,';
                                                    html += '      margin: 1';
                                                    html += '    });';
                                                    html += '  }';
                                                    html += '  setTimeout(function(){ window.print(); }, 1000);';
                                                    html += '};';
                                                    html += '<\/script>';
                                                    html += '</body></html>';
                                                    
                                                    ventana.document.write(html);
                                                    ventana.document.close();
                                                    ventana.focus();
                                                }
                                                // Mostrar previsualización cuando se carga la página
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    document.getElementById('previewSection').style.display = 'block';
                                                    if(typeof JsBarcode !== 'undefined') {
                                                        JsBarcode('#barcode-ejemplo1', '<?php echo $codigo_ejemplo1; ?>', {
                                                            format: 'CODE128',
                                                            width: 1.5,
                                                            height: 30,
                                                            displayValue: false,
                                                            margin: 2
                                                        });
                                                        JsBarcode('#barcode-ejemplo2', '<?php echo $codigo_ejemplo2; ?>', {
                                                            format: 'CODE128',
                                                            width: 1.5,
                                                            height: 30,
                                                            displayValue: false,
                                                            margin: 2
                                                        });
                                                    }
                                                });
                                            </script>
                                            <?php
                                        } else {
                                            echo "<div class='alert alert-danger'>" . implode('<br>', $errores) . "</div>";
                                        }
                                    }
                                    ?>
                                    
                                    <form method="POST">
                                        <div class="form-group mb-3">
                                            <label>Proveedor:</label>
                                            <input type="text" name="proveedor" maxlength="4" class="form-control" value="<?php echo htmlspecialchars($_POST['proveedor'] ?? ''); ?>" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Cantidad de Etiquetas:</label>
                                            <input type="number" name="cantidad" min="1" max="1000" class="form-control" value="<?php echo htmlspecialchars($_POST['cantidad'] ?? ''); ?>" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Identificador Lote (1 letra):</label>
                                            <input type="text" name="lote" maxlength="1" class="form-control" value="<?php echo htmlspecialchars($_POST['lote'] ?? ''); ?>" required>
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
                                        <li><strong>Configuración del papel:</strong> 10cm x 2.5cm</li>
                                        <li><strong>Formato:</strong> 2 etiquetas por hoja, lado a lado</li>
                                        <li>Llena el formulario con los datos requeridos</li>
                                        <li>Haz clic en "Generar Previsualización" para ver el ejemplo</li>
                                        <li>Haz clic en "Imprimir" para abrir la ventana de impresión</li>
                                        <li>Verifica que tu impresora esté configurada correctamente</li>
                                    </ol>
                                    <div class="alert alert-warning">
                                        <strong>⚠️ Importante:</strong> Si el número de etiquetas es impar, la última hoja tendrá una etiqueta en blanco a la derecha.
                                    </div>
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