<?php
require_once __DIR__ . '../../../config/ctconex.php';
//
if (isset($_POST['stupdprod'])) {
    $idprod = $_POST['txtidc'];
    $codba = $_POST['txtcode'];
    $nomprd = $_POST['txtnampr'];
    $idcate = $_POST['txtcate'];
    $precio = $_POST['txtpre'];
    $stock = $_POST['txtstc'];
    $venci = $_POST['txtvenc'];
    $esta = $_POST['txtesta'];
    $serial = $_POST['txtserial'];
    $marca = $_POST['txtmarca'];
    $grado = $_POST['txtgrado'];
    $prcpro = $_POST['txtprcpro'];
    $ram = $_POST['txtram'];
    $disco = $_POST['txtdisco'];
    $pntpro = $_POST['txtpntpro'];
    $tarpro = $_POST['txttarpro'];
    try {
        $query = "UPDATE producto SET codba=:codba, nomprd=:nomprd,idcate=:idcate,precio=:precio,stock=:stock,venci=:venci,esta=:esta,serial=:serial,marca=:marca,grado=:grado,prcpro=:prcpro,ram=:ram,disco=:disco,pntpro=:pntpro,tarpro=:tarpro WHERE idprod=:idprod LIMIT 1";
        $statement = $connect->prepare($query);
        $data = [
            ':codba' => $codba,
            ':nomprd' => $nomprd,
            ':idcate' => $idcate,
            ':precio' => $precio,
            ':stock' => $stock,
            ':venci' => $venci,
            ':esta' => $esta,
            ':serial' => $serial,
            ':marca' => $marca,
            ':grado' => $grado,
            ':prcpro' => $prcpro,
            ':ram' => $ram,
            ':disco' => $disco,
            ':pntpro' => $pntpro,
            ':tarpro' => $tarpro,
            ':idprod' => $idprod
        ];
        $query_execute = $statement->execute($data);
        if ($query_execute) {
            echo '<script type="text/javascript">
swal("¡Actualizado!", "Actualizado correctamente", "success").then(function() {
            window.location = "../producto/mostrar.php";
        });
        </script>';
            exit(0);
        } else {
            echo '<script type="text/javascript">
swal("Error!", "Error al actualizar", "error").then(function() {
            window.location = "../producto/mostrar.php";
        });
        </script>';
            exit(0);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>