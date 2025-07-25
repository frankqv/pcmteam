<?php

   # Incluyendo librerias necesarias #
    require "../../backend/pdf/code128.php";

    function convertUtf8($text) {
        return mb_convert_encoding($text, 'UTF-8', 'auto');
    }

    $pdf = new PDF_Code128('P','mm',array(80,258));
    $pdf->SetMargins(4,10,4);
    $pdf->AddPage();
    
    # Encabezado y datos de la empresa #
    $pdf->SetFont('Arial','B',10);
    $pdf->SetTextColor(0,0,0);
    
     require '../../backend/bd/ctconex.php';

    $stmt = $connect->prepare("SELECT * FROM setting");
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();
while($a = $stmt->fetch()){

    $pdf->MultiCell(0,5,convertUtf8(strtoupper($a['nomem'])),0,'C',false);
    $pdf->SetFont('Arial','',9);
    $pdf->MultiCell(0,5,convertUtf8("RUC: ".$a['ruc']),0,'C',false);
    $pdf->MultiCell(0,5,convertUtf8("Direccion: ".$a['direc1']),0,'C',false);
    $pdf->MultiCell(0,5,convertUtf8("Celular:".$a['celu']),0,'C',false);


   }

    $pdf->Ln(1);
    $pdf->Cell(0,5,convertUtf8("------------------------------------------------------"),0,0,'C');
    $pdf->Ln(5);

   
    $pdf->MultiCell(0,5,convertUtf8("Fecha:".date("d/m/Y") ),0,'C',false);

     $id = $_GET['id'];
    $stmt = $connect->prepare("SELECT servicio.idservc, plan.idplan, plan.foto, plan.prec,plan.nompla, servicio.ini, servicio.fin, clientes.idclie, clientes.numid, clientes.nomcli, clientes.apecli, clientes.naci, clientes.celu, clientes.correo, servicio.estod, servicio.fere FROM servicio INNER JOIN plan ON servicio.idplan = plan.idplan INNER JOIN clientes ON servicio.idclie = clientes.idclie WHERE servicio.idservc= '$id'");
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();
while($row = $stmt->fetch()){

    $pdf->MultiCell(0,5,convertUtf8("Caja Nro: 1"),0,'C',false);


    $pdf->MultiCell(0,5,convertUtf8("Cajero: administrador"),0,'C',false);
    $pdf->SetFont('Arial','B',10);

    
  
    $pdf->MultiCell(0,5,convertUtf8(strtoupper("Ticket Nro:". $row['idservc'] )),0,'C',false);
    $pdf->SetFont('Arial','',9);

    $pdf->Ln(1);
    $pdf->Cell(0,5,convertUtf8("------------------------------------------------------"),0,0,'C');
    $pdf->Ln(5);


    $pdf->MultiCell(0,5,convertUtf8("Cliente:". $row['nomcli'] ."\n". $row['apecli']),0,'C',false);

    $pdf->MultiCell(0,5,convertUtf8("Telefono:". $row['celu']),0,'C',false);
    

    $pdf->Ln(1);
    $pdf->Cell(0,5,convertUtf8("-------------------------------------------------------------------"),0,0,'C');
    $pdf->Ln(3);

    # Tabla de productos #
   
    $pdf->Cell(70,5,convertUtf8("Servicio"),0,0,'C');
    

    $pdf->Ln(3);
    $pdf->Cell(72,5,convertUtf8("-------------------------------------------------------------------"),0,0,'C');
    $pdf->Ln(3);


    /*----------  Detalles de la tabla  ----------*/
   
    
   
    $pdf->Cell(30,4,convertUtf8($row['nompla']),0,0,'C');
  
    $pdf->Ln(4);
    $pdf->MultiCell(0,4,convertUtf8($row['ini'] ."\n". $row['fin']),0,'C',false);
    $pdf->Ln(7);
    /*----------  Fin Detalles de la tabla  ----------*/



    $pdf->Cell(72,5,convertUtf8("-------------------------------------------------------------------"),0,0,'C');

        $pdf->Ln(5);

    # Impuestos & totales #
    $pdf->Cell(18,5,convertUtf8(""),0,0,'C');
    $pdf->Cell(22,5,convertUtf8("SUBTOTAL"),0,0,'C');
    $pdf->Cell(32,5,convertUtf8("S/".$row['prec']),0,0,'C');

    $pdf->Ln(5);

    $pdf->Cell(18,5,convertUtf8(""),0,0,'C');
    $pdf->Cell(22,5,convertUtf8("IVA (0%)"),0,0,'C');
    $pdf->Cell(32,5,convertUtf8("S/".$row['prec']),0,0,'C');

    $pdf->Ln(5);

    $pdf->Cell(72,5,convertUtf8("-------------------------------------------------------------------"),0,0,'C');

    $pdf->Ln(5);

    $pdf->Cell(18,5,convertUtf8(""),0,0,'C');
    $pdf->Cell(22,5,convertUtf8("TOTAL A PAGAR"),0,0,'C');
    $pdf->Cell(32,5,convertUtf8("S/".$row['prec']),0,0,'C');

    
    $pdf->Ln(10);

    $pdf->MultiCell(0,5,convertUtf8("*** Precios de productos incluyen impuestos. Para poder realizar un reclamo o devolución debe de presentar este ticket ***"),0,'C',false);

    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(0,7,convertUtf8("Gracias por su compra"),'',0,'C');

    $pdf->Ln(9);

    # Codigo de barras #
    $pdf->Code128(5,$pdf->GetY(),"COD000001V000".$row['idservc'],70,20);
    $pdf->SetXY(0,$pdf->GetY()+21);
    $pdf->SetFont('Arial','',14);
    $pdf->MultiCell(0,5,convertUtf8("COD000001V000".$row['idservc']),0,'C',false);
}
    # Nombre del archivo PDF #
   
    $pdf->Output('ticket.pdf', 'D');