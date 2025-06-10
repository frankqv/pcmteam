<?php
  

  if(isset($_POST['stupdcst']))
{
    // DEBUGGING - Mostrar todos los datos recibidos
    echo "<h3>Datos recibidos del formulario:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    $idclie = $_POST['txtidc'];
    $numid = $_POST['txtnum'];
    $nomcli = $_POST['txtnaame'];
    $apecli = $_POST['txtape'];
    $naci = $_POST['txtnaci'];
    $correo = $_POST['txtema'];
    $celu = $_POST['txtcel'];
    $estad = $_POST['txtesta'];
    
    // Verificar si los campos existen en $_POST
    $dircli = isset($_POST['txtdire']) ? $_POST['txtdire'] : 'NO RECIBIDO';
    $ciucli = isset($_POST['txtciud']) ? $_POST['txtciud'] : 'NO RECIBIDO';
    $idsede = isset($_POST['txtsede']) ? $_POST['txtsede'] : 'NO RECIBIDO';


    try {
        $query = "UPDATE clientes SET numid=:numid, nomcli=:nomcli, apecli=:apecli, naci=:naci, correo=:correo, celu=:celu, estad=:estad, dircli=:dircli, ciucli=:ciucli, idsede=:idsede WHERE idclie=:idclie LIMIT 1";
        

        $statement = $connect->prepare($query);

        $data = [
            ':numid' => $numid,
            ':nomcli' => $nomcli,
            ':apecli' => $apecli,
            ':naci' => $naci,
            ':correo' => $correo,
            ':celu' => $celu,
            ':estad' => $estad,
            ':dircli' => $dircli,
            ':ciucli' => $ciucli,
            ':idsede' => $idsede,
            ':idclie' => $idclie
        ];
        

        
        $query_execute = $statement->execute($data);

        if($query_execute)
        {
           
            echo '<script type="text/javascript">
    swal("¡Actualizado!", "Actualizado correctamente", "success").then(function() {
                window.location = "../clientes/mostrar.php";
            });
            </script>';
        
            exit(0);
        }
        else
        {

            echo '<script type="text/javascript">
    swal("Error!", "Error al actualizar", "error").then(function() {
                window.location = "../clientes/mostrar.php";
            });
            </script>';
        
            exit(0);
        }

    } catch (PDOException $e) {
        echo "<h3>Error de PDO:</h3>";
        echo $e->getMessage();
    }
}


?>