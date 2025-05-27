<?php  
require_once('../../backend/bd/ctconex.php');

if(isset($_POST['staddcust']))
{
    $numid = $_POST['txtnum'];
    $nomcli = $_POST['txtnaame'];
    $apecli = $_POST['txtape'];
    $naci = $_POST['txtnaci'];
    $correo = $_POST['txtema'];
    $celu = $_POST['txtcel'];
    $estad = $_POST['txtesta'];
    
    if(empty($numid)){
        $errMSG = "Por favor ingresa el número de identificación.";
    } else {
        // Validamos primero que el documento no exista
        $sql = "SELECT * FROM clientes WHERE numid=:numid";
        $stmt = $connect->prepare($sql);
        $stmt->bindParam(':numid', $numid);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo '<script type="text/javascript">
                    swal("Error!", "El número de identificación ya está registrado!", "error").then(function() {
                        window.location = "../clientes/mostrar.php";
                    });
                </script>';
        } else {
            // Validamos si el correo electrónico ya está registrado
            $sql_correo = "SELECT * FROM clientes WHERE correo=:correo";
            $stmt_correo = $connect->prepare($sql_correo);
            $stmt_correo->bindParam(':correo', $correo);
            $stmt_correo->execute();

            if ($stmt_correo->rowCount() > 0) {
                echo '<script type="text/javascript">
                        swal("Error!", "El correo electrónico ya está registrado!", "error").then(function() {
                            window.location = "../clientes/mostrar.php";
                        });
                    </script>';
            } else {
                // Insertamos los datos en la base de datos
                $sql = "INSERT INTO clientes(numid, nomcli, apecli, naci, correo, celu, estad) VALUES (:numid, :nomcli, :apecli, :naci, :correo, :celu, :estad)";
                $stmt = $connect->prepare($sql);
                $stmt->bindParam(':numid', $numid);
                $stmt->bindParam(':nomcli', $nomcli);
                $stmt->bindParam(':apecli', $apecli);
                $stmt->bindParam(':naci', $naci);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':celu', $celu);
                $stmt->bindParam(':estad', $estad);

                if($stmt->execute()) {
                    echo '<script type="text/javascript">
                            swal("¡Registrado!", "Cliente agregado correctamente", "success").then(function() {
                                window.location = "../clientes/mostrar.php";
                            });
                        </script>';
                } else {
                    $errMSG = "Error al insertar los datos.";
                }
            }
        }
    }
}
?>
