<?php
// Evitar cualquier output antes del PDF
ob_start();
require_once '../../backend/pdf/fpdf.php';
require_once '../../backend/bd/ctconex.php';
class PDF extends FPDF
{
    function Header()
    {
        // Título con Arial Bold (más compatible que Helvetica)
        $this->SetFont('Arial', 'B', 54);
        $this->Cell(0, 15, 'DELICADO', 0, 1, 'C');
        $this->Ln(5);
    }
}
// Función para convertir texto UTF-8 de forma segura
function convertUtf8($text)
{
    // Verificar si el texto ya está en la codificación correcta
    if (mb_check_encoding($text, 'UTF-8')) {
        return iconv('UTF-8', 'ISO-8859-1//IGNORE', $text);
    }
    return $text;
}
// Obtener el ID del pedido con validación
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    ob_end_clean();
    die('ID de pedido no válido.');
}
try {
    // Configurar PDO para manejo de errores
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Primero, obtener el ID del cliente desde orders
    $sql_order = "SELECT user_cli FROM orders WHERE idord = ?";
    $stmt = $connect->prepare($sql_order);
    $stmt->execute([$id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        ob_end_clean();
        die('No se encontró el pedido con ID: ' . $id);
    }
    // Obtener los datos del cliente
    $sql_cliente = "SELECT c.*, s.celu as celu_remitente, s.nomem as nombre_remitente 
        FROM clientes c 
        CROSS JOIN setting s 
        WHERE c.idclie = ? AND s.idsett = 1";
    $stmt = $connect->prepare($sql_cliente);
    $stmt->execute([$order['user_cli']]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$datos) {
        ob_end_clean();
        die('No se encontraron los datos del cliente.');
    }
    // Limpiar cualquier output previo
    ob_end_clean();
    // Crear PDF
    $pdf = new PDF();
    $pdf->AddPage();
    // Ciudad - usando Arial Bold para mayor impacto
    $pdf->SetFont('Arial', 'B', 32);
    $pdf->Cell(0, 15, convertUtf8($datos['ciucli']), 0, 1, 'C');
    // Datos del cliente
    $pdf->SetFont('Arial', '', 16);
    $pdf->Ln(8);
    // Nombre completo del cliente
    $nombre_completo = 'Para: ' . $datos['nomcli'] . ' ' . $datos['apecli'];
    $pdf->Cell(0, 8, convertUtf8($nombre_completo), 0, 1, 'C');
    // Numero de identificacion
    $cc_text = 'C.C: ' . $datos['numid'];
    $pdf->Cell(0, 8, convertUtf8($cc_text), 0, 1, 'C');
    // Dirección
    $direccion_text = 'Direccion: ' . $datos['dircli'];
    $pdf->Cell(0, 8, convertUtf8($direccion_text), 0, 1, 'C');
    // Celular del cliente
    $celular_text = 'Celular cliente: ' . $datos['celu'];
    $pdf->Cell(0, 8, convertUtf8($celular_text), 0, 1, 'C');
    // Datos del remitente
    $pdf->Ln(12);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 8, 'DATOS DEL REMITENTE:', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 8, convertUtf8($datos['nombre_remitente']), 0, 1, 'C');
    $celular_remitente = 'Celular: ' . $datos['celu_remitente'];
    $pdf->Cell(0, 8, convertUtf8($celular_remitente), 0, 1, 'C');
    // Mensaje final - usando Arial Bold para destacar
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Producto con sellos de seguridad', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->Cell(0, 12, 'FACTURA AQUI', 0, 0, 'C');
    // Generar PDF
    $pdf->Output('I', 'guia_envio_delicado.pdf');
} catch (PDOException $e) {
    ob_end_clean();
    die('Error en la base de datos: ' . $e->getMessage());
} catch (Exception $e) {
    ob_end_clean();
    die('Error al generar el PDF: ' . $e->getMessage());
}
?>