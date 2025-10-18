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
    if ($d3 === false) {
        echo '<script type="text/javascript">
swal("¡Error!", "Error en la preparación de la consulta de servicio", "error").then(function() {
            window.location = "../servicio/mostrar.php";
        });
        </script>';
        exit;
    }
    $d4 = $connect->prepare("INSERT INTO ingresos (detalle,total,fec) VALUES('VENTA DE MEMBRESIAS','$total','$ini')");
    if ($d4 === false) {
        echo '<script type="text/javascript">
swal("¡Error!", "Error en la preparación de la consulta de ingresos", "error").then(function() {
            window.location = "../servicio/mostrar.php";
        });
        </script>';
        exit;
    }
    $inserted1 = $d3->execute();
    $inserted2 = $d4->execute();
    if ($inserted1 && $inserted2) {
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
        if ($d3) print_r($d3->errorInfo());
        if ($d4) print_r($d4->errorInfo());
    }
}
