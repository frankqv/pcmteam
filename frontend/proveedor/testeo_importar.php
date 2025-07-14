<?php
ob_start();
session_start();

// Verificar permisos
if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 5])){
    header('location: ../error404.php');
    exit();
}

// Mensajes de resultado
$mensaje = '';
$tipo_mensaje = '';

// Verificar si ZipArchive está disponible
function verificarZipArchive() {
    if (!extension_loaded('zip')) {
        return false;
    }
    if (!class_exists('ZipArchive')) {
        return false;
    }
    return true;
}

// Procesar descarga de plantilla
if (isset($_GET['descargar_plantilla'])) {
    if (!verificarZipArchive()) {
        $mensaje = 'Error: La extensión ZIP no está habilitada en PHP. No se puede generar archivos Excel.';
        $tipo_mensaje = 'danger';
    } else {
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="plantilla_proveedores.xlsx"');
            require_once '../../vendor/autoload.php';
            
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Encabezados
            $headers = ['nombre', 'nit', 'direccion', 'telefono', 'email'];
            $sheet->fromArray($headers, NULL, 'A1');
            
            // Fila de ejemplo
            $ejemplo = ['Ejemplo S.A.S.', '900123456', 'Calle 123 #45-67', '3001234567', 'ejemplo@email.com'];
            $sheet->fromArray($ejemplo, NULL, 'A2');
            
            // Formato visual para encabezados
            $headerStyle = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9E1F2']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ];
            $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
            
            // Ajustar ancho de columnas
            foreach (range('A', 'E') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
        } catch (Exception $e) {
            $mensaje = 'Error al generar plantilla: ' . $e->getMessage();
            $tipo_mensaje = 'danger';
        }
    }
}

// Procesar importación
if (isset($_POST['importar']) && isset($_FILES['archivo_excel']['tmp_name'])) {
    if (!verificarZipArchive()) {
        $mensaje = 'Error: La extensión ZIP no está habilitada en PHP. No se pueden procesar archivos Excel.';
        $tipo_mensaje = 'danger';
    } else {
        $archivo = $_FILES['archivo_excel']['tmp_name'];
        $nombreArchivo = $_FILES['archivo_excel']['name'];
        $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        
        // Validar archivo
        if (empty($archivo) || $_FILES['archivo_excel']['error'] !== UPLOAD_ERR_OK) {
            $mensaje = 'Error al subir el archivo.';
            $tipo_mensaje = 'danger';
        } elseif ($ext !== 'xlsx') {
            $mensaje = 'El archivo debe ser formato .xlsx';
            $tipo_mensaje = 'danger';
        } else {
            try {
                require_once '../../vendor/autoload.php';
                
                // Cargar archivo Excel
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
                
                // Conectar a la base de datos
                require_once '../../backend/bd/ctconex.php';
                $con = conexion();
                
                if (!$con) {
                    throw new Exception('Error de conexión a la base de datos');
                }
                
                $insertados = 0;
                $actualizados = 0;
                $errores = [];
                
                // Procesar cada fila
                foreach ($rows as $i => $row) {
                    if ($i == 0) continue; // Saltar encabezado
                    
                    // Limpiar datos
                    $nombre = trim($row[0] ?? '');
                    $nit = trim($row[1] ?? '');
                    $direccion = trim($row[2] ?? '');
                    $telefono = trim($row[3] ?? '');
                    $email = trim($row[4] ?? '');
                    
                    // Validaciones
                    if (empty($nombre) || empty($nit)) {
                        $errores[] = "Fila ".($i+1).": Nombre y NIT son obligatorios.";
                        continue;
                    }
                    
                    // Validar NIT (solo números)
                    if (!preg_match('/^\d+$/', $nit)) {
                        $errores[] = "Fila ".($i+1).": NIT debe contener solo números.";
                        continue;
                    }
                    
                    // Validar email si se proporciona
                    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errores[] = "Fila ".($i+1).": Email inválido.";
                        continue;
                    }
                    
                    // Validar Celular si se proporciona
                    if (!empty($telefono) && !preg_match('/^\d{7,15}$/', $telefono)) {
                        $errores[] = "Fila ".($i+1).": Celular debe tener entre 7 y 15 dígitos.";
                        continue;
                    }
                    
                    try {
                        // Verificar si el proveedor ya existe por NIT
                        $stmt_check = $con->prepare("SELECT id FROM proveedores WHERE nit = ?");
                        $stmt_check->execute([$nit]);
                        $existe = $stmt_check->fetch();
                        
                        if ($existe) {
                            // Actualizar proveedor existente
                            $stmt_update = $con->prepare("UPDATE proveedores SET nombre = ?, direccion = ?, telefono = ?, email = ? WHERE nit = ?");
                            if ($stmt_update->execute([$nombre, $direccion, $telefono, $email, $nit])) {
                                $actualizados++;
                            } else {
                                $errores[] = "Fila ".($i+1).": Error al actualizar proveedor existente.";
                            }
                        } else {
                            // Insertar nuevo proveedor
                            $stmt_insert = $con->prepare("INSERT INTO proveedores (nombre, nit, direccion, telefono, email, estado) VALUES (?, ?, ?, ?, ?, 1)");
                            if ($stmt_insert->execute([$nombre, $nit, $direccion, $telefono, $email])) {
                                $insertados++;
                            } else {
                                $errores[] = "Fila ".($i+1).": Error al insertar en la base de datos.";
                            }
                        }
                    } catch (PDOException $e) {
                        $errores[] = "Fila ".($i+1).": Error de base de datos - " . $e->getMessage();
                    }
                }
                
                // Generar mensaje de resultado
                $mensajes = [];
                if ($insertados > 0) {
                    $mensajes[] = "Proveedores insertados: $insertados";
                }
                if ($actualizados > 0) {
                    $mensajes[] = "Proveedores actualizados: $actualizados";
                }
                
                if (count($mensajes) > 0) {
                    $mensaje = "Importación completada. " . implode(', ', $mensajes) . ".";
                    $tipo_mensaje = 'success';
                }
                
                if (count($errores) > 0) {
                    $mensaje .= "<br><br><strong>Errores encontrados:</strong><br>" . implode('<br>', $errores);
                    $tipo_mensaje = $tipo_mensaje === 'success' ? 'warning' : 'danger';
                }
                
                if ($insertados == 0 && $actualizados == 0 && count($errores) == 0) {
                    $mensaje = "No se encontraron datos válidos para procesar.";
                    $tipo_mensaje = 'warning';
                }
                
            } catch (Exception $e) {
                $mensaje = 'Error al procesar el archivo: ' . $e->getMessage();
                $tipo_mensaje = 'danger';
            }
        }
    }
}

// Plantilla genérica
if (isset($_GET['descargar_plantilla_generica'])) {
    if (!verificarZipArchive()) {
        $mensaje = 'Error: La extensión ZIP no está habilitada en PHP. No se puede generar archivos Excel.';
        $tipo_mensaje = 'danger';
    } else {
        try {
            require_once '../../vendor/autoload.php';
            $headers = ['nombre', 'nit', 'direccion', 'telefono', 'email'];
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray($headers, NULL, 'A1');
            
            // Formato visual para encabezados
            $headerStyle = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9E1F2']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ];
            $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
            foreach (range('A', 'E') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="plantilla_proveedores.xlsx"');
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
        } catch (Exception $e) {
            $mensaje = 'Error al generar plantilla: ' . $e->getMessage();
            $tipo_mensaje = 'danger';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Importar Proveedores | PCMARKETTEAM</title>
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="stylesheet" href="../../backend/css/loader.css">
    <link rel="stylesheet" type="text/css" href="../../backend/css/font.css">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />
</head>
<body>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Importar Proveedores desde Excel</h5>
            <div>
                <a href="?descargar_plantilla=1" class="btn btn-light btn-sm me-2">📥 Descargar Plantilla</a>
                <a href="mostrar.php" class="btn btn-outline-light btn-sm">← Volver</a>
            </div>
        </div>
        <div class="card-body">
            <?php if($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                    <?= $mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="archivo_excel" class="form-label">Selecciona archivo Excel (.xlsx):</label>
                    <input type="file" class="form-control" id="archivo_excel" name="archivo_excel" accept=".xlsx" required>
                    <div class="form-text">Solo archivos .xlsx son permitidos</div>
                </div>
                <button type="submit" name="importar" class="btn btn-success">
                    📊 Importar Proveedores
                </button>
            </form>
            
            <hr class="my-4">
            
            <div class="row">
                <div class="col-md-6">
                    <h6>📋 Formato requerido:</h6>
                    <p class="text-muted">La plantilla debe tener las siguientes columnas:</p>
                    <ul class="list-unstyled">
                        <li><strong>nombre</strong> - Nombre del proveedor (obligatorio)</li>
                        <li><strong>nit</strong> - NIT del proveedor (obligatorio, solo números)</li>
                        <li><strong>direccion</strong> - Dirección (opcional)</li>
                        <li><strong>telefono</strong> - Celular (opcional, 7-15 dígitos)</li>
                        <li><strong>email</strong> - Email (opcional, formato válido)</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>ℹ️ Información importante:</h6>
                    <ul class="text-muted">
                        <li>Si un proveedor ya existe (mismo NIT), se actualizará</li>
                        <li>La primera fila debe contener los encabezados</li>
                        <li>Los campos vacíos se procesarán como NULL</li>
                    </ul>
                </div>
            </div>
            
            <?php if (!verificarZipArchive()): ?>
                <div class="alert alert-warning mt-3">
                    <h6>⚠️ Extensión ZIP no disponible</h6>
                    <p>Para usar esta funcionalidad, necesitas habilitar la extensión ZIP en PHP:</p>
                    <ul>
                        <li>En XAMPP: Descomenta <code>extension=zip</code> en php.ini</li>
                        <li>En servidor Linux: <code>sudo apt-get install php-zip</code></li>
                        <li>Reinicia el servidor web después de los cambios</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="../../backend/js/jquery-3.3.1.slim.min.js"></script>
<script src="../../backend/js/popper.min.js"></script>
<script src="../../backend/js/bootstrap.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>