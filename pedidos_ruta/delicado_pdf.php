<?php
require_once '../../backend/pdf/fpdf.php';
require_once '../../config/ctconex.php';

// Obtener el ID del pedido
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    // Primero, obtener el ID del cliente desde orders
    $sql_order = "SELECT user_cli FROM orders WHERE idord = ?";
    $stmt = $connect->prepare($sql_order);
    $stmt->execute([$id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($order) {
        // Ahora obtener los datos del cliente
        $sql_cliente = "SELECT c.*, s.celu as celu_remitente, s.nomem as nombre_remitente 
            FROM clientes c, setting s 
            WHERE c.idclie = ? AND s.idsett = 1";
        $stmt = $connect->prepare($sql_cliente);
        $stmt->execute([$order['user_cli']]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($datos) {
            
            // Crear PDF
            $pdf = new FPDF();
            $pdf->AddPage();
            
            // Título
            $pdf->SetFont('Times', 'B',54);
            $pdf->Cell(0, 10, 'DELICADO', 0, 1, 'C');
            
            // Ciudad
            $pdf->SetFont('Times', 'B', 32);
            $pdf->Cell(0, 12, convertUtf8( $datos['ciucli']), 0, 1, 'C');
            

            // Datos del cliente
            $pdf->SetFont('Arial', '', 16);
            $pdf->Ln(5);
            
            // Nombre completo del cliente
            $pdf->Cell(0, 8, convertUtf8('Para: ' . $datos['nomcli'] . ' ' . $datos['apecli']), 0, 1, 'C');
            
            // Numero de identificacion
            $pdf->Cell(0, 8, convertUtf8('C.C: ' . $datos['numid']), 0, 1, 'C');
            
            // Dirección
            $pdf->Cell(0, 8, convertUtf8('Direccion: ' . $datos['dircli']), 0, 1, 'C');
            
            // Celular del cliente
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

            // Generar PDF
            $pdf->Output('I', 'guia_envio_delicado.pdf');
            exit;
        }
    }
    
    // Si no se encontraron datos, mostrar mensaje de error
    die('No se encontraron los datos del pedido.');
    
} catch (PDOException $e) {
    die('Error en la base de datos: ' . $e->getMessage());
}
?> 