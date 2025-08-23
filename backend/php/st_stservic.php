<?php
require_once __DIR__ . '../../../config/ctconex.php';
if (isset($_POST['staddserv'])) {
    $idplan = $_POST['txtpln'];
    $ini = $_POST['txtini'];
    $fin = $_POST['txtfin'];
    $idclie = $_POST['txtcli'];
    $estod = $_POST['txtesta'];
    $meto = $_POST['txtmeto'];
    $total = $_POST['txtprec'];
    $cancel = $_POST['txtcanc'];
    $servtxt = $_POST['servtxt'];
    $servfoto = $_POST['servfoto'];
    $responsable = $_POST['responsable'];
$d3 = $connect->prepare("INSERT INTO servicio (idplan, ini,fin,idclie,estod,meto,canc,servtxt,servfoto, responsable) VALUES('$idplan','$ini','$fin','$idclie','$estod','$meto','$cancel','$servtxt','$servfoto', '$responsable')");
$d4 = $connect->prepare("INSERT INTO ingresos (detalle,total,fec) VALUES('VENTA DE MEMBRESIAS','$total','$ini')");
    $inserted = $d3->execute();
    $inserted = $d4->execute();
if ($inserted > 0) {
    echo '<script type="text/javascript">
swal("¡Registrado!", "Se agrego correctamente", "success").then(function() {
            window.location = "../servicio/mostrar.php";
        });
        </script>';
    } else {
    echo '<script type="text/javascript">
swal("¡Error!", "No se pueden agregar datos", "error").then(function() {
            window.location = "../servicio/mostrar.php";
        });
        </script>';
    print_r($sql->errorInfo());
    }
}
?>