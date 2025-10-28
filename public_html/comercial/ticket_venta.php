/* claude.ia ayudame crear un ticket para los usuarios cuando hagan unaventa se pueda imprimir desde un boton en la  */


<?php
# Incluyendo librerias necesarias #
require "../../backend/pdf/code128.php";
function convertUtf8($text)
{
  return mb_convert_encoding($text, 'UTF-8', 'auto');
}
$pdf = new PDF_Code128('P', 'mm', array(80, 258));
$pdf->SetMargins(4, 10, 4);
$pdf->AddPage();
# Encabezado y datos de la empresa #
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 0, 0);

require '../../config/ctconex.php';
