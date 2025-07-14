<?php
ob_start();
session_start();
if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 5])){
    header('location: ../error404.php');
    exit();
}

// Mensajes de resultado
$mensaje = '';
$tipo_mensaje = '';

// Procesar descarga de plantilla
if (isset($_GET['descargar_plantilla'])) {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="plantilla_proveedores.xlsx"');
    require_once '../../vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Encabezados que coinciden con la estructura de la base de datos
    $headers = ['privado', 'nombre', 'celu', 'correo', 'dire', 'cuiprov', 'nomenclatura', 'nit', 'telefono', 'email'];
    $sheet->fromArray($headers, NULL, 'A1');
    
    // Fila de ejemplo 1
    $ejemplo1 = [1, 'Ejemplo S.A.S.', 3202344974, 'ejemplo@email.com', 'Calle 123 #45-67', 'Bogotá', 'EJEMSAS', 900123456, '3001234567', 'contacto@ejemplo.com'];
    $sheet->fromArray($ejemplo1, NULL, 'A2');
    
    // Fila de ejemplo 2
    $ejemplo2 = [1, 'PcShek Tecnologia Y Servicios S A S', 3186890437, 'comercial@pcshek.com', 'TV 66 # 35 - 11 MD 3 BG 9', 'Bogotá', 'PCSH', 900987654, '3186890437', 'comercial@pcshek.com'];
    $sheet->fromArray($ejemplo2, NULL, 'A3');
    
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
    $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
    
    // Ajustar ancho de columnas
    foreach (range('A', 'J') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}

// Procesar importación
if (isset($_POST['importar']) && isset($_FILES['archivo_excel']['tmp_name'])) {
    require_once '../../vendor/autoload.php';
    $archivo = $_FILES['archivo_excel']['tmp_name'];
    $ext = pathinfo($_FILES['archivo_excel']['name'], PATHINFO_EXTENSION);
    
    if ($ext !== 'xlsx') {
        $mensaje = 'El archivo debe ser formato .xlsx';
        $tipo_mensaje = 'danger';
    } else {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            require_once '../../backend/bd/ctconex.php';
            $con = conexion();
            $insertados = 0;
            $errores = [];
            
            foreach ($rows as $i => $row) {
                if ($i == 0) continue; // Saltar encabezado
                
                // Mapear columnas según la estructura de la base de datos
                $privado = intval($row[0] ?? 1);
                $nombre = trim($row[1] ?? '');
                $celu = intval($row[2] ?? 0);
                $correo = trim($row[3] ?? '');
                $dire = trim($row[4] ?? '');
                $cuiprov = trim($row[5] ?? '');
                $nomenclatura = trim($row[6] ?? '');
                $nit = intval($row[7] ?? 0);
                $telefono = trim($row[8] ?? '');
                $email = trim($row[9] ?? '');
                
                // Validaciones básicas
                if ($nombre == '') {
                    $errores[] = "Fila ".($i+1).": El nombre es obligatorio.";
                    continue;
                }
                
                // Validar longitud de campos
                if (strlen($nombre) > 30) {
                    $errores[] = "Fila ".($i+1).": El nombre no puede tener más de 30 caracteres.";
                    continue;
                }
                
                if (strlen($correo) > 30) {
                    $errores[] = "Fila ".($i+1).": El correo no puede tener más de 30 caracteres.";
                    continue;
                }
                
                if (strlen($dire) > 30) {
                    $errores[] = "Fila ".($i+1).": La dirección no puede tener más de 30 caracteres.";
                    continue;
                }
                
                if (strlen($cuiprov) > 30) {
                    $errores[] = "Fila ".($i+1).": El cuiprov no puede tener más de 30 caracteres.";
                    continue;
                }
                
                if (strlen($nomenclatura) > 10) {
                    $errores[] = "Fila ".($i+1).": La nomenclatura no puede tener más de 10 caracteres.";
                    continue;
                }
                
                if (strlen($telefono) > 20) {
                    $errores[] = "Fila ".($i+1).": El Celular no puede tener más de 20 caracteres.";
                    continue;
                }
                
                if (strlen($email) > 100) {
                    $errores[] = "Fila ".($i+1).": El email no puede tener más de 100 caracteres.";
                    continue;
                }
                
                // Validación de email
                if ($correo && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    $errores[] = "Fila ".($i+1).": Correo inválido.";
                    continue;
                }
                
                if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errores[] = "Fila ".($i+1).": Email inválido.";
                    continue;
                }
                
                // Validar que celu sea un número válido (10 dígitos)
                if ($celu > 0 && ($celu < 1000000000 || $celu > 9999999999)) {
                    $errores[] = "Fila ".($i+1).": El celular debe tener 10 dígitos.";
                    continue;
                }
                
                // Validar que nit sea un número válido
                if ($nit > 0 && ($nit < 100000000 || $nit > 9999999999)) {
                    $errores[] = "Fila ".($i+1).": El NIT debe tener entre 9 y 10 dígitos.";
                    continue;
                }
                
                // Verificar si ya existe un proveedor con el mismo nombre o NIT
                $stmt_check = $con->prepare("SELECT id FROM proveedores WHERE nombre = ? OR (nit = ? AND nit > 0)");
                $stmt_check->execute([$nombre, $nit]);
                if ($stmt_check->fetch()) {
                    $errores[] = "Fila ".($i+1).": Ya existe un proveedor con este nombre o NIT.";
                    continue;
                }
                
                // Insertar en la base de datos
                $stmt = $con->prepare("INSERT INTO proveedores (privado, nombre, celu, correo, dire, cuiprov, nomenclatura, nit, telefono, email, estado, fecha_creacion, fecha_actualizacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())");
                
                // Convertir valores nulos para la base de datos
                $celu_db = $celu > 0 ? $celu : null;
                $nit_db = $nit > 0 ? $nit : null;
                $correo_db = $correo !== '' ? $correo : null;
                $dire_db = $dire !== '' ? $dire : null;
                $cuiprov_db = $cuiprov !== '' ? $cuiprov : null;
                $nomenclatura_db = $nomenclatura !== '' ? $nomenclatura : null;
                $telefono_db = $telefono !== '' ? $telefono : null;
                $email_db = $email !== '' ? $email : null;
                
                if ($stmt->execute([$privado, $nombre, $celu_db, $correo_db, $dire_db, $cuiprov_db, $nomenclatura_db, $nit_db, $telefono_db, $email_db])) {
                    $insertados++;
                } else {
                    $errores[] = "Fila ".($i+1).": Error al insertar en la base de datos.";
                }
            }
            
            if ($insertados > 0) {
                $mensaje = "Importación completada. Proveedores insertados: $insertados.";
                $tipo_mensaje = 'success';
            }
            
            if (count($errores) > 0) {
                $mensaje .= "<br><br>Errores encontrados:<br>".implode('<br>', $errores);
                $tipo_mensaje = $insertados > 0 ? 'warning' : 'danger';
            }
            
            if ($insertados == 0 && count($errores) == 0) {
                $mensaje = "No se encontraron datos válidos para importar.";
                $tipo_mensaje = 'warning';
            }
            
        } catch (Exception $e) {
            $mensaje = 'Error al procesar el archivo: ' . $e->getMessage();
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
            <a href="?descargar_plantilla=1" class="btn btn-info btn-sm">📥 Descargar plantilla Excel</a>
        </div>
        <div class="card-body">
            <?php if($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?>" role="alert">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="archivo_excel">Selecciona archivo Excel (.xlsx):</label>
                    <input type="file" class="form-control-file" id="archivo_excel" name="archivo_excel" accept=".xlsx" required>
                </div>
                <button type="submit" name="importar" class="btn btn-success mt-2">📊 Importar Proveedores</button>
                <a href="mostrar.php" class="btn btn-secondary mt-2">Volver</a>
            </form>
            
            <hr>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>Estructura de la plantilla:</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>privado</strong>: 1 o 0 (1=Privado, 0=Público)</li>
                        <li class="list-group-item"><strong>nombre</strong>: Nombre del proveedor (máx. 30 caracteres)</li>
                        <li class="list-group-item"><strong>celu</strong>: Número de celular (10 dígitos)</li>
                        <li class="list-group-item"><strong>correo</strong>: Email principal (máx. 30 caracteres)</li>
                        <li class="list-group-item"><strong>dire</strong>: Dirección (máx. 30 caracteres)</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Campos adicionales:</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>cuiprov</strong>: Ciudad/Ubicación (máx. 30 caracteres)</li>
                        <li class="list-group-item"><strong>nomenclatura</strong>: Código interno (máx. 10 caracteres)</li>
                        <li class="list-group-item"><strong>nit</strong>: NIT del proveedor (9-10 dígitos)</li>
                        <li class="list-group-item"><strong>telefono</strong>: Celular fijo (máx. 20 caracteres)</li>
                        <li class="list-group-item"><strong>email</strong>: Email secundario (máx. 100 caracteres)</li>
                    </ul>
                </div>
            </div>
            
            <div class="alert alert-info mt-3">
                <h6>Notas importantes:</h6>
                <ul>
                    <li>El campo <strong>nombre</strong> es obligatorio</li>
                    <li>Los campos de email deben tener formato válido</li>
                    <li>El celular debe tener exactamente 10 dígitos</li>
                    <li>El NIT debe tener entre 9 y 10 dígitos</li>
                    <li>No se pueden importar proveedores con nombre o NIT duplicados</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="../../backend/js/jquery-3.3.1.slim.min.js"></script>
<script src="../../backend/js/popper.min.js"></script>
<script src="../../backend/js/bootstrap.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>