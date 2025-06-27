<?php
require_once '../../backend/pdf/fpdf.php';
require_once '../../backend/bd/ctconex.php';

class PDF extends FPDF {
    function Header() {
        // Título con Helvetica Bold
        $this->SetFont('Helvetica', 'B', 54);
        $this->Cell(0, 15, 'DELICADO', 0, 1, 'C');
        $this->Ln(5);
    }
}

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
            $pdf = new PDF();
            $pdf->AddPage();
            
            // Ciudad - usando Helvetica Bold para mayor impacto
            $pdf->SetFont('Helvetica', 'B', 32);
            $pdf->Cell(0, 15, utf8_decode($datos['ciucli']), 0, 1, 'C');
            
            // Datos del cliente
            $pdf->SetFont('Helvetica', '', 16);
            $pdf->Ln(8);
            
            // Nombre completo del cliente
            $pdf->Cell(0, 8, utf8_decode('Para: ' . $datos['nomcli'] . ' ' . $datos['apecli']), 0, 1, 'C');
            
            // Numero de identificacion
            $pdf->Cell(0, 8, utf8_decode('C.C: ' . $datos['numid']), 0, 1, 'C');
            
            // Dirección
            $pdf->Cell(0, 8, utf8_decode('Direccion: ' . $datos['dircli']), 0, 1, 'C');
            
            // Celular del cliente
            $pdf->Cell(0, 8, utf8_decode('Celular cliente: ' . $datos['celu']), 0, 1, 'C');
            
            // Datos del remitente
            $pdf->Ln(12);
            $pdf->SetFont('Helvetica', 'B', 14);
            $pdf->Cell(0, 8, 'DATOS DEL REMITENTE:', 0, 1, 'C');
            $pdf->SetFont('Helvetica', '', 14);
            $pdf->Cell(0, 8, utf8_decode($datos['nombre_remitente']), 0, 1, 'C');
            $pdf->Cell(0, 8, utf8_decode('Celular: ' . $datos['celu_remitente']), 0, 1, 'C');
            
            // Mensaje final - usando Helvetica Bold para destacar
            $pdf->Ln(10);
            $pdf->SetFont('Helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'Producto con sellos de seguridad', 0, 1, 'C');
            $pdf->Ln(5);
            $pdf->SetFont('Helvetica', 'B', 18);
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