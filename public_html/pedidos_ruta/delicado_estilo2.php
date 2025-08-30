<!-- Hoja de envio | Estilo letras parecido Time new Roman, Arial -->
<?php
ob_start(); // Evita que cualquier salida arruine el PDF
require_once '../../backend/pdf/fpdf.php';
require_once '../../config/ctconex.php';
// Función para convertir UTF-8 a ISO-8859-1 (compatible con FPDF)
function convertUtf8($text) {
    return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
}
// Validar ID recibido
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    ob_end_clean();
    die('ID de pedido no válido.');
}
try {
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // 1. Obtener el ID del cliente desde la orden
    $sql_order = "SELECT user_cli FROM orders WHERE idord = ?";
    $stmt = $connect->prepare($sql_order);
    $stmt->execute([$id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        ob_end_clean();
        die('No se encontró el pedido.');
    }
    // 2. Obtener datos del cliente y del remitente
    $sql_cliente = "SELECT c.*, s.celu AS celu_remitente, s.nomem AS nombre_remitente 
                    FROM clientes c, setting s 
                    WHERE c.idclie = ? AND s.idsett = 1";
    $stmt = $connect->prepare($sql_cliente);
    $stmt->execute([$order['user_cli']]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$datos) {
        ob_end_clean();
        die('No se encontraron datos del cliente.');
    }
    // 3. Crear PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    // Título principal
    $pdf->SetFont('Times', 'B', 54);
    $pdf->Cell(0, 10, 'DELICADO', 0, 1, 'C');
    // Ciudad
    $pdf->SetFont('Times', 'B', 32);
    $pdf->Cell(0, 12, convertUtf8($datos['ciucli']), 0, 1, 'C');
    // Datos del cliente
    $pdf->SetFont('Arial', '', 16);
    $pdf->Ln(5);
    $pdf->Cell(0, 8, convertUtf8('Para: ' . $datos['nomcli'] . ' ' . $datos['apecli']), 0, 1, 'C');
    $pdf->Cell(0, 8, convertUtf8('C.C: ' . $datos['numid']), 0, 1, 'C');
    $pdf->Cell(0, 8, convertUtf8('Dirección: ' . $datos['dircli']), 0, 1, 'C');
    $pdf->Cell(0, 8, convertUtf8('Celular cliente: ' . $datos['celu']), 0, 1, 'C');
    // Datos del remitente
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 8, 'DATOS DEL REMITENTE:', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 8, convertUtf8($datos['nombre_remitente']), 0, 1, 'C');
    $pdf->Cell(0, 8, convertUtf8('Celular: ' . $datos['celu_remitente']), 0, 1, 'C');
    // Mensaje final
    $pdf->Ln(8);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 8, 'Producto con sellos de seguridad', 0, 1, 'C');
    $pdf->Cell(0, 12, 'FACTURA AQUI', 0, 0, 'C');
    ob_end_clean(); // Limpia cualquier output previo
    $pdf->Output('I', 'guia_envio.pdf');
    exit;
} catch (PDOException $e) {
    ob_end_clean();
    die('Error en la base de datos: ' . $e->getMessage());
} catch (Exception $e) {
    ob_end_clean();
    die('Error: ' . $e->getMessage());
}
?>