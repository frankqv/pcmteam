/* NOTA  crear un ticket para los usuarios cuando hagan unaventa se pueda imprimir desde un boton en la  */
/* informacion 
* tabla de "clientes"

<br/>Titulo de DELICADO
<br/>CUIDAD DEL CLIENTE
<br/>direccion: CLIENTE
<br/>CC/NIT: cedula cliente
<br/>y un parrafo que diga "PRODUCTO CON SELLOS DE SEGURIDAD"
* informacion en la tabla de "settings"
Tambien crear par seccion de alistamiento 
*/
/* tamnie si se puede para ahorrar papael se imprima de pares la orden 1, la 2, en la misma hoja.


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
