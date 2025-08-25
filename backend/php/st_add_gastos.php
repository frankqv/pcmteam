    <?php 
require_once __DIR__ . '../../../config/ctconex.php';
if(isset($_POST['staddgast']))
{
    $detall=trim($_POST['detta']);
    $total=trim($_POST['montoot']);
    $fec=trim($_POST['feat']);
    $d3 = $connect->prepare("INSERT INTO gastos (detall,total,fec) VALUES('$detall','$total','$fec')");
    if ($d3 === false) {
        echo '<script type="text/javascript">
swal("Error!", "Error en la preparación de la consulta", "error").then(function() {
            window.location = "../gastos/mostrar.php";
        });
        </script>';
        exit;
    }
    
    $inserted = $d3->execute();
    if($inserted){
        echo '<script type="text/javascript">
swal("¡Registrado!", "Agregado correctamente", "success").then(function() {
            window.location = "../gastos/mostrar.php";
        });
        </script>';
    } else {
        echo '<script type="text/javascript">
swal("Error!", "No se pueden agregar datos,  comuníquese con el administrador!", "error").then(function() {
            window.location = "../gastos/mostrar.php";
        });
        </script>';
        if ($d3) print_r($d3->errorInfo()); 
    } }
?>