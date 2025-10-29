<?php
ob_start();
session_start();
require_once '../../config/ctconex.php';
// Verificar autenticación
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 4])) {
    header('location: ../error404.php');
    exit;
}
// ==========================================
// FUNCIONES AUXILIARES
// ==========================================
function generarIdVenta($conn) {
    $year = date('Y');
    $prefix = "AV-{$year}-";
    $sql = "SELECT idventa FROM new_alistamiento_venta
            WHERE idventa LIKE ?
            ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $searchPattern = $prefix . '%';
    $stmt->bind_param('s', $searchPattern);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $lastNumber = intval(substr($row['idventa'], -4));
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }
    return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
}
function generarTicket($conn) {
    $fecha = date('Ymd');
    $sql = "SELECT ticket FROM new_alistamiento_venta
            WHERE ticket LIKE ?
            ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $searchPattern = "TKT-{$fecha}-%";
    $stmt->bind_param('s', $searchPattern);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $lastNumber = intval(substr($row['ticket'], -4));
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }
    return "TKT-{$fecha}-" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
}
function subirComprobantes($files) {
    $uploadDir = '../assets/img/comprobantes/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $rutasComprobantes = [];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    if (!empty($files['comprobantes']['name'][0])) {
        foreach ($files['comprobantes']['name'] as $key => $name) {
            if ($files['comprobantes']['error'][$key] === 0) {
                $tmpName = $files['comprobantes']['tmp_name'][$key];
                $size = $files['comprobantes']['size'][$key];
                $type = $files['comprobantes']['type'][$key];
                if (!in_array($type, $allowedTypes) || $size > $maxSize) {
                    continue;
                }
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                $timestamp = time();
                $newName = "comprobante_{$timestamp}_{$key}.{$extension}";
                $destino = $uploadDir . $newName;
                if (move_uploaded_file($tmpName, $destino)) {
                    $rutasComprobantes[] = $newName;
                }
            }
        }
    }
    return json_encode($rutasComprobantes);
}
// ==========================================
// ENDPOINTS AJAX
// ==========================================
if (isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    switch ($_POST['action']) {
        case 'buscar_cliente':
            $term = isset($_POST['q']) ? trim($_POST['q']) : '';
            if (strlen($term) < 2) {
                echo json_encode(['results' => []]);
                exit;
            }
            $sql = "SELECT idclie, numid, nomcli, apecli, correo, celu, dircli, idsede
                    FROM clientes
                    WHERE numid LIKE ? OR nomcli LIKE ? OR apecli LIKE ? OR correo LIKE ? OR celu LIKE ?
                    LIMIT 20";
            $stmt = $conn->prepare($sql);
            $searchTerm = "%{$term}%";
            $stmt->bind_param('sssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            $clientes = [];
            while ($row = $result->fetch_assoc()) {
                $clientes[] = [
                    'id' => $row['idclie'],
                    'text' => $row['nomcli'] . ' ' . $row['apecli'] . ' - ' . $row['numid'],
                    'numid' => $row['numid'],
                    'nombre' => $row['nomcli'] . ' ' . $row['apecli'],
                    'telefono' => $row['celu'] ?: 'No registrado',
                    'sede_cliente' => $row['idsede'] ?: 'No especificado',
                    'direccion' => $row['dircli'] ?: ''
                ];
            }
            echo json_encode(['results' => $clientes]);
            exit;
        case 'buscar_inventario':
            $term = isset($_POST['q']) ? trim($_POST['q']) : '';
            $sql = "SELECT id, producto, marca, modelo, procesador, ram, disco, grado, precio
                    FROM bodega_inventario
                    WHERE grado IN ('A', 'B')
                    AND estado = 'activo'
                    AND disposicion NOT IN ('Vendido', 'Dañado')";
            if (!empty($term)) {
                $sql .= " AND (producto LIKE ? OR marca LIKE ? OR modelo LIKE ? OR procesador LIKE ?)";
            }
            $sql .= " ORDER BY id DESC LIMIT 50";
            $stmt = $conn->prepare($sql);
            if (!empty($term)) {
                $searchTerm = "%{$term}%";
                $stmt->bind_param('ssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $productos = [];
            while ($row = $result->fetch_assoc()) {
                $productos[] = [
                    'id' => $row['id'],
                    'producto' => $row['producto'],
                    'marca' => $row['marca'] ?: '',
                    'modelo' => $row['modelo'] ?: '',
                    'procesador' => $row['procesador'] ?: '',
                    'ram' => $row['ram'] ?: '',
                    'disco' => $row['disco'] ?: '',
                    'grado' => $row['grado'],
                    'precio' => floatval($row['precio'])
                ];
            }
            echo json_encode($productos);
            exit;
        case 'guardar_venta':
            try {
                $conn->begin_transaction();
                // Generar IDs el id tickets Nota: cambia luego por códg factura saque el world_office el programa contable usa Anyi y las demas contadoras
                $idventa = generarIdVenta($conn);
                $ticket = generarTicket($conn);
                // Datos básicos
                $usuario_id = $_SESSION['id'];
                $sede = mysqli_real_escape_string($conn, $_POST['sede']);
                $idcliente = intval($_POST['idcliente']);
                $tipo_cliente = mysqli_real_escape_string($conn, $_POST['tipo_cliente'] ?? 'Cliente Regular');
                $direccion = mysqli_real_escape_string($conn, $_POST['direccion']);
                $sede_cliente = mysqli_real_escape_string($conn, $_POST['sede_cliente'] ?? 'Tienda Física');
                $concepto_salida = mysqli_real_escape_string($conn, $_POST['concepto_salida'] ?? 'Venta Física');
                $observacion_global = mysqli_real_escape_string($conn, $_POST['observacion_global'] ?? '');
                $estado = mysqli_real_escape_string($conn, $_POST['estado'] ?? 'borrador');
                // Productos
                $productos = json_decode($_POST['productos'], true);
                if (empty($productos)) {
                    throw new Exception('Debe agregar al menos un producto');
                }
                $productosArray = [];
                $subtotal = 0;
                foreach ($productos as $prod) {
                    $cantidad = intval($prod['cantidad']);
                    $precio_unit = floatval($prod['precio_unitario']);
                    $productosArray[] = [
                        'id_inventario' => $prod['id_inventario'] ?? null,
                        'cantidad' => $cantidad,
                        'descripcion' => $prod['descripcion'],
                        'marca' => $prod['marca'] ?? '',
                        'modelo' => $prod['modelo'] ?? '',
                        'observacion' => $prod['observacion'] ?? ''
                    ];
                    $subtotal += ($cantidad * $precio_unit);
                }
                $productos_json = json_encode($productosArray, JSON_UNESCAPED_UNICODE);
                // Valores financieros
                $descuento = floatval($_POST['descuento'] ?? 0);
                $total_venta = $subtotal - $descuento;
                $valor_abono = floatval($_POST['valor_abono'] ?? 0);
                $saldo_pendiente = $total_venta - $valor_abono;
                $saldo_inicial = $total_venta;
                $saldo_final = $saldo_pendiente;
                $metodo_pago_abono = mysqli_real_escape_string($conn, $_POST['metodo_pago_abono'] ?? '');
                $metodo_pago_saldo = '';
                // Subir comprobantes
                $foto_comprobante = '';
                if (!empty($_FILES['comprobantes']['name'][0])) {
                    $foto_comprobante = subirComprobantes($_FILES);
                }
                // Insertar en la base de datos
                $sql = "INSERT INTO new_alistamiento_venta (
                    idventa, ticket, estado, fecha_venta, usuario_id, sede,
                    idcliente, tipo_cliente, direccion, canal_venta, concepto_salida,
                    cantidad, descripcion,
                    subtotal, descuento, total_venta,
                    valor_abono, metodo_pago_abono,
                    saldo_inicial, saldo_pendiente, saldo_final,
                    observacion_global, foto_comprobante
                ) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception('Error en prepare: ' . $conn->error);
                }
                $stmt->bind_param(
                    'sssississsssddddsdddss',
                    $idventa, $ticket, $estado, $usuario_id, $sede,
                    $idcliente, $tipo_cliente, $direccion, $sede_cliente, $concepto_salida,
                    $productos_json, $productos_json,
                    $subtotal, $descuento, $total_venta,
                    $valor_abono, $metodo_pago_abono,
                    $saldo_inicial, $saldo_pendiente, $saldo_final,
                    $observacion_global, $foto_comprobante
                );
                if (!$stmt->execute()) {
                    throw new Exception('Error al guardar: ' . $stmt->error);
                }
                // Obtener el ID de la venta insertada
                $alistamiento_venta_id = $conn->insert_id;
                // ==========================================
                // REGISTRAR EN TABLA INGRESOS (si hay abono)
                // ==========================================
                if ($valor_abono > 0 && !empty($metodo_pago_abono)) {
                    $detalle = "Abono inicial - Venta {$idventa}";
                    $referencia_pago = !empty($_POST['referencia_pago']) ? mysqli_real_escape_string($conn, $_POST['referencia_pago']) : '';
                    $observacion_ingresos = "Primer abono registrado al momento de crear la venta";
                    $sql_ingreso = "INSERT INTO ingresos (
                        alistamiento_venta_id,
                        detalle,
                        total,
                        metodo_pago,
                        referencia_pago,
                        recibido_por,
                        idcliente,
                        observacion_ingresos,
                        fecha_registro
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                    $stmt_ingreso = $conn->prepare($sql_ingreso);
                    if (!$stmt_ingreso) {
                        throw new Exception('Error al preparar ingreso: ' . $conn->error);
                    }
                    $stmt_ingreso->bind_param(
                        'isdssiis',
                        $alistamiento_venta_id,
                        $detalle,
                        $valor_abono,
                        $metodo_pago_abono,
                        $referencia_pago,
                        $usuario_id,
                        $idcliente,
                        $observacion_ingresos
                    );
                    if (!$stmt_ingreso->execute()) {
                        throw new Exception('Error al guardar ingreso: ' . $stmt_ingreso->error);
                    }
                    $stmt_ingreso->close();
                }
                $conn->commit();
                // Mensaje de éxito
                $message = 'Venta guardada correctamente';
                if ($valor_abono > 0 && !empty($metodo_pago_abono)) {
                    $message .= ' y abono registrado en ingresos';
                }
                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'idventa' => $idventa,
                    'ticket' => $ticket,
                    'abono_registrado' => ($valor_abono > 0 && !empty($metodo_pago_abono))
                ]);
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;
    }
    exit;
}
?>
<?php if (isset($_SESSION['id'])) { ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Nueva Venta - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <style>
        .grado-badge {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            color: white;
        }
        .grado-A { background: #00CC54; }
        .grado-B { background: #F0DD00; color: #333; }
        .grado-C { background: #CC0618; }
        .producto-card {
            border: 2px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .producto-card:hover {
            border-color: #00CC54;
            box-shadow: 0 2px 8px rgba(0, 204, 84, 0.2);
        }
        .item-producto {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
        }
        .item-producto .btn-remove {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .total-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .total-box .row > div {
            padding: 10px 0;
        }
        .total-box label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .total-final {
            font-size: 2rem;
            font-weight: bold;
            color: #00CC54;
        }
        .section-card {
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .section-header {
            background: linear-gradient(135deg, #2B6B5D 0%, #1a4a3f 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
        }
    </style>
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
            <div class='pre-loader'>
                <img class='loading-gif' alt='loading' src="https://i.imgflip.com/9vd6wr.gif" />
            </div>
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#">
                            <span class="material-icons" style="vertical-align: middle;">add_shopping_cart</span>
                            Nueva Venta
                        </a>
                        <button class="d-inline-block d-lg-none ml-auto more-button" type="button"
                            data-toggle="collapse" data-target="#navbarSupportedContent">
                            <span class="material-icons">more_vert</span>
                        </button>
                        <div class="collapse navbar-collapse d-lg-block d-xl-block d-sm-none d-md-none d-none">
                            <ul class="nav navbar-nav ml-auto">
                                <li class="dropdown nav-item active">
                                    <a href="#" class="nav-link" data-toggle="dropdown">
                                        <img src="../assets/img/reere.webp">
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="../cuenta/perfil.php">Mi perfil</a></li>
                                        <li><a href="../cuenta/salir.php">Salir</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="main-content">
                <!-- Botón Volver -->
                <div class="row mb-3">
                    <div class="col-12">
                        <a href="historico_venta.php" class="btn btn-secondary">
                            <span class="material-icons" style="vertical-align: middle;">arrow_back</span>
                            Volver a Registro completo de ventas
                        </a>
                    </div>
                </div>
                <form id="formNuevaVenta">
                    <!-- INFORMACIÓN DEL VENDEDOR -->
                    <div class="card section-card">
                        <div class="section-header">
                            <h5 class="mb-0">
                                <span class="material-icons" style="vertical-align: middle;">badge</span>
                                Información del Vendedor
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Vendedor</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>Usuario</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['usuario'] ?? ''); ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>Sede del Vendedor</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['idsede'] ?? 'No asignada'); ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- SECCIÓN 1: CLIENTE -->
                    <div class="card section-card">
                        <div class="section-header">
                            <h5 class="mb-0">
                                <span class="material-icons" style="vertical-align: middle;">person</span>
                                1. Información del Cliente
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="buscarCliente">Buscar Cliente *</label>
                                    <select id="buscarCliente" class="form-control" style="width: 100%;"></select>
                                    <small class="text-muted">Buscar por NIT, nombre, apellido, correo o celular</small>
                                </div>
                                <div class="col-md-6">
                                    <label>Información del Cliente</label>
                                    <div id="infoCliente" class="alert alert-info" style="display: none;">
                                        <strong id="clienteNombre"></strong><br>
                                        <span id="clienteTelefono"></span> | <span id="clienteCanal"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- SECCIÓN 2: INFORMACIÓN GENERAL -->
                    <div class="card section-card">
                        <div class="section-header">
                            <h5 class="mb-0">
                                <span class="material-icons" style="vertical-align: middle;">info</span>
                                2. Información General
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="txtSede">Sede *</label>
                                    <select id="txtSede" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Bogotá Principal" selected>Bogotá Principal</option>
                                        <option value="Medellín">Medellín</option>
                                        <option value="Cali">Cali</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="txtConcepto">Concepto de Salida *</label>
                                    <select id="txtConcepto" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Venta Física">Venta Física</option>
                                        <option value="Servicio Técnico">Servicio Técnico</option>
                                        <option value="Garantía">Garantía</option>
                                        <option value="Préstamo">Préstamo</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="txtUbicacion">Dirección de Envío *</label>
                                    <input type="text" id="txtUbicacion" class="form-control" placeholder="Dirección de Envío" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- SECCIÓN 3: PRODUCTOS -->
                    <div class="card section-card">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <span class="material-icons" style="vertical-align: middle;">inventory</span>
                                3. Productos
                            </h5>
                            <div>
                                <button type="button" class="btn btn-light btn-sm" id="btnBuscarInventario">
                                    <span class="material-icons" style="vertical-align: middle; font-size: 18px;">search</span>
                                    Buscar en Inventario
                                </button>
                                <button type="button" class="btn btn-light btn-sm" id="btnAgregarManual">
                                    <span class="material-icons" style="vertical-align: middle; font-size: 18px;">edit</span>
                                    Agregar Manual
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="listaItems">
                                <p class="text-muted text-center">No hay items agregados. Use los botones de arriba para agregar productos.</p>
                            </div>
                        </div>
                    </div>
                    <!-- SECCIÓN 4: INFORMACIÓN FINANCIERA -->
                    <div class="card section-card">
                        <div class="section-header">
                            <h5 class="mb-0">
                                <span class="material-icons" style="vertical-align: middle;">payments</span>
                                4. Información Financiera
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="total-box">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Subtotal</label>
                                        <h3 id="displaySubtotal" style="color: #333;">$0</h3>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="txtDescuento">Descuento</label>
                                        <input type="number" id="txtDescuento" class="form-control form-control-lg" value="0" min="0" step="1000">
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Total a Pagar</label>
                                        <div class="total-final" id="displayTotal">$0</div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="txtAbono">Valor Abono</label>
                                        <input type="number" id="txtAbono" class="form-control form-control-lg" value="0" min="0" step="1000">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="txtMedioAbono">Medio de Pago Abono</label>
                                        <select id="txtMedioAbono" class="form-control form-control-lg">
                                            <option value="">Seleccione</option>
                                            <option value="Efectivo">Efectivo</option>
                                            <option value="Transferencia">Transferencia</option>
                                            <option value="Tarjeta Crédito">Tarjeta Crédito</option>
                                            <option value="Tarjeta Débito">Tarjeta Débito</option>
                                            <option value="Nequi">Nequi</option>
                                            <option value="Daviplata">Daviplata</option>
                                            <option value="Bancolombia">Bancolombia</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Saldo Pendiente</label>
                                        <h3 id="displaySaldo" style="color: #CC0618;">$0</h3>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="txtReferencia">Referencia de Pago (Opcional)</label>
                                        <input type="text" id="txtReferencia" class="form-control" placeholder="Ej: Transf-12345, Recibo-789">
                                        <small class="text-muted">Número de referencia, voucher o comprobante</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fotoComprobante">Comprobantes de Pago (Opcional)</label>
                                        <input type="file" id="fotoComprobante" class="form-control" name="comprobantes[]" multiple accept="image/*,application/pdf">
                                        <small class="text-muted">Formatos permitidos: JPG, PNG, PDF (máx 5MB cada uno)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- SECCIÓN 5: OBSERVACIONES -->
                    <div class="card section-card">
                        <div class="section-header">
                            <h5 class="mb-0">
                                <span class="material-icons" style="vertical-align: middle;">notes</span>
                                5. Observaciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="txtObservacion">Observación Global</label>
                                    <textarea id="txtObservacion" class="form-control" rows="4" placeholder="Notas generales de la venta..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- BOTONES DE ACCIÓN -->
                    <div class="row mb-4">
                        <div class="col-12 text-right">
                            <a href="escritorio.php" class="btn btn-secondary btn-lg">
                                <span class="material-icons" style="vertical-align: middle;">close</span>
                                Cancelar
                            </a>
                            <button type="button" class="btn btn-primary btn-lg" id="btnGuardarBorrador">
                                <span class="material-icons" style="vertical-align: middle;">save</span>
                                Guardar como Borrador
                            </button>
                            <button type="button" class="btn btn-success btn-lg" id="btnGuardarAprobar">
                                <span class="material-icons" style="vertical-align: middle;">check_circle</span>
                                Guardar y Aprobar
                            </button>
                        </div>
                    </div>
                    <input type="hidden" id="hiddenClienteId">
                    <input type="hidden" id="hiddenCanalVenta">
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Buscar Inventario -->
    <div class="modal fade" id="modalBuscarInventario" tabindex="-1">
        <div class="modal-dialog" style="max-width: 90%; width: 90%;">
            <div class="modal-content">
                <div class="modal-header" style="background: #2B6B5D; color: white;">
                    <h5 class="modal-title">
                        <span class="material-icons" style="vertical-align: middle;">search</span>
                        Buscar Productos en Inventario
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" style="color: white;">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" id="txtBuscarInventario" class="form-control form-control-lg" placeholder="Buscar por marca, modelo, procesador, ram, disco...">
                    </div>
                    <div id="resultadosInventario" style="max-height: 500px; overflow-y: auto;">
                        <p class="text-muted text-center">Escriba para buscar productos...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Agregar Manual -->
    <div class="modal fade" id="modalAgregarManual" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #2B6B5D; color: white;">
                    <h5 class="modal-title">Agregar Producto Manual</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color: white;">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formProductoManual">
                        <div class="form-group">
                            <label for="txtManualProducto">Producto *</label>
                            <input type="text" id="txtManualProducto" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="txtManualMarca">Marca</label>
                                <input type="text" id="txtManualMarca" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="txtManualModelo">Modelo</label>
                                <input type="text" id="txtManualModelo" class="form-control">
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label for="txtManualObservacion">Observación</label>
                            <textarea id="txtManualObservacion" class="form-control" rows="2" placeholder="Ej: con mouse, programas instalados, etc."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="txtManualCantidad">Cantidad *</label>
                                <input type="number" id="txtManualCantidad" class="form-control" value="1" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="txtManualPrecio">Precio Unitario *</label>
                                <input type="number" id="txtManualPrecio" class="form-control" min="0" step="1000" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnAgregarProductoManual">Agregar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/loader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        // Toggle sidebar
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
            $('#content').toggleClass('active');
        });
        $('.more-button,.body-overlay').on('click', function() {
            $('#sidebar,.body-overlay').toggleClass('show-nav');
        });
        // Array de productos
        let productos = [];
        // Inicializar Select2 para buscar clientes
        $('#buscarCliente').select2({
            ajax: {
                url: 'nueva_venta.php',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        action: 'buscar_cliente',
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return data;
                }
            },
            placeholder: 'Buscar cliente por NIT, nombre, correo...',
            minimumInputLength: 2
        });
        // Evento al seleccionar cliente
        $('#buscarCliente').on('select2:select', function(e) {
            const data = e.params.data;
            $('#hiddenClienteId').val(data.id);
            $('#hiddenCanalVenta').val(data.sede_cliente);
            $('#txtUbicacion').val(data.direccion);
            $('#clienteNombre').text(data.nombre);
            $('#clienteTelefono').text('Tel: ' + data.telefono);
            $('#clienteCanal').text('Canal: ' + data.sede_cliente);
            $('#infoCliente').show();
        });
        // Abrir modal buscar inventario
        $('#btnBuscarInventario').click(function() {
            $('#modalBuscarInventario').modal('show');
            cargarInventario('');
        });
        // Buscar en inventario con delay
        let searchTimeout;
        $('#txtBuscarInventario').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                cargarInventario($(this).val());
            }, 300);
        });
        // Función para cargar inventario
        function cargarInventario(busqueda) {
            $.ajax({
                url: 'nueva_venta.php',
                type: 'POST',
                data: {
                    action: 'buscar_inventario',
                    q: busqueda
                },
                dataType: 'json',
                success: function(data) {
                    let html = '';
                    if (data.length === 0) {
                        html = '<p class="text-muted text-center">No se encontraron productos</p>';
                    } else {
                        data.forEach(item => {
                            const precio = new Intl.NumberFormat('es-CO', {style: 'currency', currency: 'COP', minimumFractionDigits: 0}).format(item.precio);
                            html += `
                                <div class="producto-card" onclick='agregarProductoDesdeInventario(${JSON.stringify(item)})'>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <strong style="font-size: 16px;">${item.producto}</strong><br>
                                            <small><b>Marca:</b> ${item.marca} | <b>Modelo:</b> ${item.modelo}</small><br>
                                            <small>${item.procesador} - ${item.ram} - ${item.disco}</small>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <span class="grado-badge grado-${item.grado}">${item.grado}</span><br>
                                            <strong style="font-size: 18px; color: #00CC54;">${precio}</strong>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    $('#resultadosInventario').html(html);
                }
            });
        }
        // Función para agregar producto desde inventario (global)
        window.agregarProductoDesdeInventario = function(item) {
            const producto = {
                id_inventario: item.id,
                cantidad: 1,
                descripcion: item.producto,
                marca: item.marca,
                modelo: item.modelo,
                observacion: '',
                precio_unitario: item.precio
            };
            productos.push(producto);
            renderizarProductos();
            $('#modalBuscarInventario').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Producto agregado',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        };
        // Abrir modal agregar manual
        $('#btnAgregarManual').click(function() {
            $('#formProductoManual')[0].reset();
            $('#modalAgregarManual').modal('show');
        });
        // Agregar producto manual
        $('#btnAgregarProductoManual').click(function() {
            if (!$('#txtManualProducto').val() || !$('#txtManualPrecio').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'Complete los campos obligatorios'
                });
                return;
            }
            const producto = {
                id_inventario: null,
                cantidad: parseInt($('#txtManualCantidad').val()),
                descripcion: $('#txtManualProducto').val(),
                marca: $('#txtManualMarca').val(),
                modelo: $('#txtManualModelo').val(),
                observacion: $('#txtManualObservacion').val(),
                precio_unitario: parseFloat($('#txtManualPrecio').val())
            };
            productos.push(producto);
            renderizarProductos();
            $('#modalAgregarManual').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Producto agregado',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        });
        // Función para renderizar productos
        function renderizarProductos() {
            if (productos.length === 0) {
                $('#listaItems').html('<p class="text-muted text-center">No hay items agregados. Use los botones de arriba para agregar productos.</p>');
                calcularTotales();
                return;
            }
            let html = '';
            productos.forEach((prod, index) => {
                const total = prod.cantidad * prod.precio_unitario;
                const totalFormateado = new Intl.NumberFormat('es-CO', {style: 'currency', currency: 'COP', minimumFractionDigits: 0}).format(total);
                const precioFormateado = new Intl.NumberFormat('es-CO', {style: 'currency', currency: 'COP', minimumFractionDigits: 0}).format(prod.precio_unitario);
                html += `
                    <div class="item-producto" data-index="${index}">
                        <button type="button" class="btn btn-danger btn-sm btn-remove" onclick="eliminarProducto(${index})">
                            <span class="material-icons" style="font-size: 18px;">delete</span>
                        </button>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Producto</label>
                                <input type="text" class="form-control" value="${prod.descripcion}" onchange="actualizarProducto(${index}, 'descripcion', this.value)">
                            </div>
                            <div class="col-md-3">
                                <label>Marca</label>
                                <input type="text" class="form-control" value="${prod.marca}" onchange="actualizarProducto(${index}, 'marca', this.value)">
                            </div>
                            <div class="col-md-3">
                                <label>Modelo</label>
                                <input type="text" class="form-control" value="${prod.modelo}" onchange="actualizarProducto(${index}, 'modelo', this.value)">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label>Observación</label>
                                <input type="text" class="form-control" value="${prod.observacion}" placeholder="Ej: con mouse, con programas..." onchange="actualizarProducto(${index}, 'observacion', this.value)">
                            </div>
                            <div class="col-md-2">
                                <label>Cantidad</label>
                                <input type="number" class="form-control" value="${prod.cantidad}" min="1" onchange="actualizarProducto(${index}, 'cantidad', this.value)">
                            </div>
                            <div class="col-md-2">
                                <label>Precio Unit.</label>
                                <input type="number" class="form-control" value="${prod.precio_unitario}" min="0" step="1000" onchange="actualizarProducto(${index}, 'precio_unitario', this.value)">
                            </div>
                            <div class="col-md-2">
                                <label>Total</label>
                                <input type="text" class="form-control" value="${totalFormateado}" readonly style="font-weight: bold; background: #e9ecef;">
                            </div>
                        </div>
                    </div>
                `;
            });
            $('#listaItems').html(html);
            calcularTotales();
        }
        // Función para actualizar producto (global)
        window.actualizarProducto = function(index, campo, valor) {
            if (campo === 'cantidad' || campo === 'precio_unitario') {
                productos[index][campo] = parseFloat(valor);
            } else {
                productos[index][campo] = valor;
            }
            renderizarProductos();
        };
        // Función para eliminar producto (global)
        window.eliminarProducto = function(index) {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    productos.splice(index, 1);
                    renderizarProductos();
                    Swal.fire({
                        icon: 'success',
                        title: 'Producto eliminado',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        };
        // Calcular totales
        function calcularTotales() {
            let subtotal = 0;
            productos.forEach(prod => {
                subtotal += prod.cantidad * prod.precio_unitario;
            });
            const descuento = parseFloat($('#txtDescuento').val()) || 0;
            const total = subtotal - descuento;
            const abono = parseFloat($('#txtAbono').val()) || 0;
            const saldo = total - abono;
            $('#displaySubtotal').text(new Intl.NumberFormat('es-CO', {style: 'currency', currency: 'COP', minimumFractionDigits: 0}).format(subtotal));
            $('#displayTotal').text(new Intl.NumberFormat('es-CO', {style: 'currency', currency: 'COP', minimumFractionDigits: 0}).format(total));
            $('#displaySaldo').text(new Intl.NumberFormat('es-CO', {style: 'currency', currency: 'COP', minimumFractionDigits: 0}).format(saldo));
        }
        // Eventos de cambio en valores financieros
        $('#txtDescuento, #txtAbono').on('input', calcularTotales);
        // Guardar como borrador
        $('#btnGuardarBorrador').click(function() {
            guardarVenta('borrador');
        });
        // Guardar y aprobar
        $('#btnGuardarAprobar').click(function() {
            guardarVenta('aprobado');
        });
        // Función para guardar venta
        function guardarVenta(estado) {
            // Validaciones
            if (!$('#hiddenClienteId').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cliente requerido',
                    text: 'Debe seleccionar un cliente'
                });
                return;
            }
            if (!$('#txtSede').val() || !$('#txtConcepto').val() || !$('#txtUbicacion').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'Complete todos los campos obligatorios'
                });
                return;
            }
            if (productos.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin productos',
                    text: 'Debe agregar al menos un producto'
                });
                return;
            }
            // Preparar FormData
            const formData = new FormData();
            formData.append('action', 'guardar_venta');
            formData.append('idcliente', $('#hiddenClienteId').val());
            formData.append('sede', $('#txtSede').val());
            formData.append('tipo_cliente', 'Cliente Regular');
            formData.append('direccion', $('#txtUbicacion').val());
            formData.append('sede_cliente', $('#hiddenCanalVenta').val() || 'Tienda Física');
            formData.append('concepto_salida', $('#txtConcepto').val());
            formData.append('observacion_global', $('#txtObservacion').val());
            formData.append('productos', JSON.stringify(productos));
            formData.append('descuento', $('#txtDescuento').val());
            formData.append('valor_abono', $('#txtAbono').val());
            formData.append('metodo_pago_abono', $('#txtMedioAbono').val());
            formData.append('referencia_pago', $('#txtReferencia').val());
            formData.append('estado', estado);
            // Agregar archivos
            const archivos = $('#fotoComprobante')[0].files;
            for (let i = 0; i < archivos.length; i++) {
                formData.append('comprobantes[]', archivos[i]);
            }
            // Mostrar loading
            Swal.fire({
                title: 'Guardando venta...',
                html: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            // Enviar petición
            $.ajax({
                url: 'nueva_venta.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Venta guardada',
                            html: `<p>${response.message}</p><p><strong>ID Venta:</strong> ${response.idventa}</p><p><strong>Ticket:</strong> ${response.ticket}</p>`,
                            confirmButtonColor: '#00CC54'
                        }).then(() => {
                            window.location.href = 'escritorio.php';
                        });
/// ---- ALTER TABLE plan //  ADD COLUMN direccion VARCHAR(255), //  ADD COLUMN ciudad VARCHAR(100);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error del servidor',
                        text: 'Ocurrió un error al guardar la venta: ' + error
                    });
                }
            });
        }
    });
    </script>
</body>
</html>
<?php } else { header('Location: ../error404.php'); } ?>
<?php ob_end_flush(); ?>
