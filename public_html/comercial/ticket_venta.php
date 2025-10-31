<?php
ob_start();
session_start();
require_once '../../config/ctconex.php';

// Verificar autenticación
if (!isset($_SESSION['rol'])) {
    header('location: ../error404.php');
    exit;
}

// Obtener ID de la venta
$venta_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($venta_id <= 0) {
    die('ID de venta inválido');
}

// Consultar información de la venta
$sql = "SELECT
    av.*,
    c.nomcli,
    c.apecli,
    c.numid,
    c.ciud,
    c.direcc,
    u.nombre as vendedor
FROM new_alistamiento_venta av
LEFT JOIN clientes c ON av.idcliente = c.idclie
LEFT JOIN usuarios u ON av.usuario_id = u.id
WHERE av.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $venta_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die('Venta no encontrada');
}

$venta = $result->fetch_assoc();

// Obtener texto de seguridad desde settings
$sql_settings = "SELECT valor FROM settings WHERE nombre = 'texto_seguridad_ticket' LIMIT 1";
$result_settings = $conn->query($sql_settings);
$texto_seguridad = "PRODUCTO CON SELLOS DE SEGURIDAD"; // Valor por defecto
if ($result_settings && $result_settings->num_rows > 0) {
    $row_settings = $result_settings->fetch_assoc();
    $texto_seguridad = $row_settings['valor'];
}

// Decodificar productos JSON
$productos = json_decode($venta['cantidad'], true);
if (!is_array($productos)) {
    $productos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket de Venta - <?php echo htmlspecialchars($venta['idventa']); ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 0; }
            .page-break { page-break-after: always; }
        }

        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }

        .ticket-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .ticket {
            width: 80mm;
            background: white;
            padding: 15px;
            margin: 0 auto 20px;
            border: 2px dashed #333;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .ticket-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .ticket-title {
            font-size: 24px;
            font-weight: bold;
            color: #d9534f;
            margin: 0;
            letter-spacing: 2px;
        }

        .empresa-info {
            text-align: center;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .cliente-info {
            margin: 15px 0;
            font-size: 11px;
            line-height: 1.6;
        }

        .cliente-info .label {
            font-weight: bold;
            display: inline-block;
            width: 80px;
        }

        .venta-info {
            margin: 15px 0;
            font-size: 11px;
            line-height: 1.6;
            border-top: 1px dashed #333;
            border-bottom: 1px dashed #333;
            padding: 10px 0;
        }

        .productos-table {
            width: 100%;
            font-size: 10px;
            margin: 10px 0;
        }

        .productos-table th {
            border-bottom: 1px solid #000;
            padding: 5px 2px;
            text-align: left;
        }

        .productos-table td {
            padding: 5px 2px;
            border-bottom: 1px dotted #ccc;
        }

        .total-section {
            margin-top: 10px;
            font-size: 12px;
            font-weight: bold;
            text-align: right;
        }

        .seguridad-text {
            margin-top: 15px;
            padding: 10px;
            background: #fff3cd;
            border: 2px solid #ffc107;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            color: #856404;
        }

        .barcode-section {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #333;
        }

        .ticket-footer {
            text-align: center;
            font-size: 9px;
            margin-top: 15px;
            color: #666;
        }

        .btn-imprimir {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <button class="btn btn-primary btn-imprimir no-print" onclick="window.print()">
        Imprimir Ticket
    </button>

    <button class="btn btn-secondary btn-imprimir no-print" onclick="window.close()" style="top: 70px;">
        Cerrar
    </button>

    <div class="ticket-container">
        <?php
        // Imprimir 2 tickets idénticos (para ahorrar papel)
        for ($i = 1; $i <= 2; $i++):
        ?>
        <div class="ticket <?php echo $i == 1 ? '' : 'page-break'; ?>">
            <!-- Encabezado -->
            <div class="ticket-header">
                <h1 class="ticket-title">DELICADO</h1>
                <div class="empresa-info">
                    <strong>PCMARKETTEAM</strong><br>
                    Ticket de Venta
                </div>
            </div>

            <!-- Información del Cliente -->
            <div class="cliente-info">
                <div>
                    <span class="label">Ciudad:</span>
                    <span><?php echo htmlspecialchars($venta['ciud'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <span class="label">Cliente:</span>
                    <span><?php echo htmlspecialchars($venta['nomcli'] . ' ' . $venta['apecli']); ?></span>
                </div>
                <div>
                    <span class="label">Dirección:</span>
                    <span><?php echo htmlspecialchars($venta['direcc'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <span class="label">CC/NIT:</span>
                    <span><?php echo htmlspecialchars($venta['numid']); ?></span>
                </div>
            </div>

            <!-- Información de la Venta -->
            <div class="venta-info">
                <div>
                    <span class="label">ID Venta:</span>
                    <strong><?php echo htmlspecialchars($venta['idventa']); ?></strong>
                </div>
                <div>
                    <span class="label">Ticket:</span>
                    <strong><?php echo htmlspecialchars($venta['ticket']); ?></strong>
                </div>
                <div>
                    <span class="label">Fecha:</span>
                    <?php echo date('d/m/Y H:i', strtotime($venta['fecha_venta'])); ?>
                </div>
                <div>
                    <span class="label">Vendedor:</span>
                    <?php echo htmlspecialchars($venta['vendedor']); ?>
                </div>
                <div>
                    <span class="label">Sede:</span>
                    <?php echo htmlspecialchars($venta['sede']); ?>
                </div>
                <?php if (!empty($venta['concepto_salida'])): ?>
                <div>
                    <span class="label">Concepto:</span>
                    <?php echo htmlspecialchars($venta['concepto_salida']); ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Productos -->
            <table class="productos-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th style="text-align: center;">Cant.</th>
                        <th style="text-align: right;">Precio</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $subtotal_calc = 0;
                    foreach ($productos as $producto):
                        $cantidad = isset($producto['cantidad']) ? floatval($producto['cantidad']) : 0;
                        $precio = isset($producto['precio']) ? floatval($producto['precio']) : 0;
                        $total_producto = $cantidad * $precio;
                        $subtotal_calc += $total_producto;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['descripcion'] ?? 'N/A'); ?></td>
                        <td style="text-align: center;"><?php echo $cantidad; ?></td>
                        <td style="text-align: right;">$<?php echo number_format($precio, 0, ',', '.'); ?></td>
                        <td style="text-align: right;">$<?php echo number_format($total_producto, 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Totales -->
            <div class="total-section">
                <div>
                    Subtotal: $<?php echo number_format($venta['subtotal'], 0, ',', '.'); ?>
                </div>
                <?php if ($venta['descuento'] > 0): ?>
                <div style="color: #d9534f;">
                    Descuento: -$<?php echo number_format($venta['descuento'], 0, ',', '.'); ?>
                </div>
                <?php endif; ?>
                <div style="font-size: 14px; margin-top: 5px; border-top: 2px solid #000; padding-top: 5px;">
                    TOTAL: $<?php echo number_format($venta['total_venta'], 0, ',', '.'); ?>
                </div>
                <?php if ($venta['valor_abono'] > 0): ?>
                <div style="color: #5cb85c;">
                    Abonado: $<?php echo number_format($venta['valor_abono'], 0, ',', '.'); ?>
                </div>
                <?php endif; ?>
                <?php if ($venta['saldo_pendiente'] > 0): ?>
                <div style="color: #d9534f;">
                    Saldo: $<?php echo number_format($venta['saldo_pendiente'], 0, ',', '.'); ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Texto de Seguridad -->
            <div class="seguridad-text">
                <?php echo htmlspecialchars($texto_seguridad); ?>
            </div>

            <!-- Código de Barras / QR -->
            <div class="barcode-section">
                <svg width="200" height="50">
                    <!-- Código de barras simulado -->
                    <text x="50%" y="25" text-anchor="middle" font-family="Courier New" font-size="20" font-weight="bold">
                        <?php echo htmlspecialchars($venta['ticket']); ?>
                    </text>
                    <rect x="20" y="30" width="2" height="15" fill="black"/>
                    <rect x="25" y="30" width="4" height="15" fill="black"/>
                    <rect x="32" y="30" width="2" height="15" fill="black"/>
                    <rect x="37" y="30" width="6" height="15" fill="black"/>
                    <rect x="46" y="30" width="2" height="15" fill="black"/>
                    <rect x="51" y="30" width="3" height="15" fill="black"/>
                    <rect x="57" y="30" width="2" height="15" fill="black"/>
                    <rect x="62" y="30" width="5" height="15" fill="black"/>
                    <rect x="70" y="30" width="2" height="15" fill="black"/>
                    <rect x="75" y="30" width="4" height="15" fill="black"/>
                    <rect x="82" y="30" width="2" height="15" fill="black"/>
                    <rect x="87" y="30" width="3" height="15" fill="black"/>
                    <rect x="93" y="30" width="2" height="15" fill="black"/>
                    <rect x="98" y="30" width="6" height="15" fill="black"/>
                    <rect x="107" y="30" width="2" height="15" fill="black"/>
                    <rect x="112" y="30" width="4" height="15" fill="black"/>
                    <rect x="119" y="30" width="2" height="15" fill="black"/>
                    <rect x="124" y="30" width="5" height="15" fill="black"/>
                    <rect x="132" y="30" width="2" height="15" fill="black"/>
                    <rect x="137" y="30" width="3" height="15" fill="black"/>
                    <rect x="143" y="30" width="2" height="15" fill="black"/>
                    <rect x="148" y="30" width="6" height="15" fill="black"/>
                    <rect x="157" y="30" width="2" height="15" fill="black"/>
                    <rect x="162" y="30" width="4" height="15" fill="black"/>
                    <rect x="169" y="30" width="2" height="15" fill="black"/>
                    <rect x="174" y="30" width="3" height="15" fill="black"/>
                </svg>
            </div>

            <!-- Footer -->
            <div class="ticket-footer">
                Gracias por su compra<br>
                PCMARKETTEAM - www.pcmarketteam.com<br>
                Este documento es válido como comprobante de venta
            </div>

            <?php if ($i == 1): ?>
            <div style="text-align: center; margin-top: 10px; font-size: 9px; color: #999;">
                COPIA CLIENTE
            </div>
            <?php else: ?>
            <div style="text-align: center; margin-top: 10px; font-size: 9px; color: #999;">
                COPIA EMPRESA
            </div>
            <?php endif; ?>
        </div>
        <?php endfor; ?>
    </div>

    <script>
        // Auto-imprimir al cargar (opcional)
        // window.addEventListener('load', function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>
