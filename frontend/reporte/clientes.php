<?php
ob_start();
     session_start();
    
    if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 1){
    header('location: ../error404.php');
  }
?>
<?php if(isset($_SESSION['id'])) { ?>

<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
       <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <title>PCMARKETTEAM</title>
         <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
         <!----css3---->
        <link rel="stylesheet" href="../../backend/css/custom.css">
        <link rel="stylesheet" href="../../backend/css/loader.css">


          <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->
     
     <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
     <!--google material icon-->
        <link href="https://fonts.googleapis.com/css2?family=Material+Icons"
      rel="stylesheet">
      <link rel="icon" type="image/png" href="../../backend/img/favicon.png"/>
  </head>
  <body>
  
<div class="wrapper">
   
<div class="body-overlay"></div>
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../../backend/img/favicon.png" class="img-fluid"/><span>PCMARKETTEAM</span></h3>
            </div>
            <ul class="list-unstyled components">
               <li  class="">
                    <a href="../administrador/escritorio.php" class="dashboard"><i class="material-icons">dashboard</i><span>Panel administrativo</span></a>
                </li>
          
               
               
                <li class="dropdown">
                    <a href="#homeSubmenu1" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                         <i class="material-icons">group</i><span>Clientes</span></a>
                    <ul class="collapse list-unstyled menu" id="homeSubmenu1">
                        <li>
                            <a href="../clientes/mostrar.php">Mostrar</a>
                        </li>
                        <li>
                            <a href="../clientes/nuevo.php">Nuevo</a>
                        </li>
                        
                    </ul>
                </li>
                
                <li class="dropdown">
                    <a href="#pageSubmenu2" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                         <i class="material-icons">dataset</i><span>Planes</span></a>
                    <ul class="collapse list-unstyled menu" id="pageSubmenu2">
                        <li>
                            <a href="../plan/mostrar.php">Mostrar</a>
                        </li>
                        <li>
                            <a href="../plan/nuevo.php">Nuevo</a>
                        </li>
                    </ul>
                </li>

                <li class="">
                    <a href="../servicio/mostrar.php"><i class="material-icons">view_timeline</i><span>Servicio</span></a>
                </li>
                    
                     <li class="dropdown">
                    <a href="#pageSubmenu3" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                         <i class="material-icons">manage_accounts</i>
                    
                         
                         <span>Usuarios</span></a>
                    <ul class="collapse list-unstyled menu" id="pageSubmenu3">
                        <li>
                            <a href="../usuario/mostrar.php">Mostrar</a>
                        </li>
                       
                    </ul>
                </li>
                      <li class="dropdown">
                    <a href="#pageSubmenu4" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                         <i class="material-icons">conveyor_belt</i><span>Productos</span></a>
                    <ul class="collapse list-unstyled menu" id="pageSubmenu4">
                        <li>
                            <a href="../producto/mostrar.php">Mostrar</a>
                        </li>
                        <li>
                            <a href="../producto/nuevo.php">Nuevo</a>
                        </li>
                    </ul>
                </li>
                    
                    <li class="dropdown ">
                    <a href="#pageSubmenu5" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                         <i class="material-icons">category</i><span>Categorias</span></a>
                    <ul class="collapse list-unstyled menu" id="pageSubmenu5">
                        <li class="">
                            <a href="../categoria/mostrar.php">Mostrar</a>
                        </li>
                        <li>
                            <a href="../categoria/nuevo.php">Nuevo</a>
                        </li>
                    </ul>
                </li>
               
                  
                  
                  <li class="dropdown">
                    <a href="#pageSubmenu6" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                         <i class="material-icons">point_of_sale</i><span>Ventas</span></a>
                    <ul class="collapse list-unstyled menu" id="pageSubmenu6">
                        <li>
                            <a href="../venta/mostrar.php">Mostrar</a>
                        </li>
                        <li>
                            <a href="../venta/nuevo.php">Nuevo</a>
                        </li>
                    </ul>
                </li>

                 <li class="dropdown">
                    <a href="#pageSubmenu09" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                         <i class="material-icons">shopping_basket</i><span>Compras</span></a>
                    <ul class="collapse list-unstyled menu" id="pageSubmenu09">
                        <li>
                            <a href="../compra/mostrar.php">Mostrar</a>
                        </li>
                        <li>
                            <a href="../compra/nuevo.php">Nuevo</a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#pageSubmenu010" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                         <i class="material-icons">savings</i><span>Gastos</span></a>
                    <ul class="collapse list-unstyled menu" id="pageSubmenu010">
                        <li>
                            <a href="../gastos/mostrar.php">Mostrar</a>
                        </li>
                        <li>
                            <a href="../gastos/nuevo.php">Nuevo</a>
                        </li>
                    </ul>
                </li>
               
                  
                    <li class="dropdown active">
                    <a href="#pageSubmenu7" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                         <i class="material-icons">signal_cellular_alt</i><span>Reportes</span></a>
                    <ul class="collapse list-unstyled menu" id="pageSubmenu7">
                        <li class="active">
                            <a href="../reporte/productos.php">Productos</a>
                        </li>
                        <li>
                            <a href="../reporte/clientes.php">Clientes</a>
                        </li>
                        <li class="">
                            <a href="../reporte/ventas.php">Ventas</a>
                        </li>
                    </ul>
                </li>
                <li class="">
                    <a href="../graficos/mostrar.php"><i class="material-icons">grain</i><span>Graficos</span></a>
                </li>
               <li class="">
                    <a href="../cuenta/configuracion.php"><i class="material-icons">settings</i><span>Configuracion</span></a>
                </li>
                   
            </ul>

        </nav>
          
        <!-- Page Content  -->
        <div id="content">
          <div class='pre-loader'>
    <img class='loading-gif' alt='loading' src="https://i.imgflip.com/9vd6wr.gif"/>
</div>
          <div class="top-navbar">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                        <span class="material-icons">arrow_back_ios</span>
                    </button>
                         
                         <a class="navbar-brand" href="#"> Reporte de clientes </a>
                         
                    <button class="d-inline-block d-lg-none ml-auto more-button" type="button" data-toggle="collapse"
                         data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="material-icons">more_vert</span>
                    </button>

                    <div class="collapse navbar-collapse d-lg-block d-xl-block d-sm-none d-md-none d-none" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">  
                         <li class="nav-item">
                                <a class="nav-link" href="../cuenta/configuracion.php">
                                        <span class="material-icons">settings</span>
                                        </a>
                            </li> 
                            <li class="dropdown nav-item active">
                                <a href="#" class="nav-link" data-toggle="dropdown">
                                   
                                   <img src="../../backend/img/reere.png" >
                                           
                               </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="../cuenta/perfil.php">Mi perfil</a>
                                    </li>
                                    <li>
                                        <a href="../cuenta/salir.php">Salir</a>
                                    </li>
                                    
                                </ul>
                            </li>
                            
                        </ul>
                    </div>
                </div>
            </nav>
         </div>
               
               
               <div class="main-content">
               <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../administrador/escritorio.php">Panel administrativo</a></li>
    <li class="breadcrumb-item"><a href="../reporte/clientes.php">Reporte </a></li>
    <li class="breadcrumb-item active" aria-current="page">Filtro de los clientes</li>
  </ol>
</nav>

                        <div class="row ">
                            <div class="col-lg-12 col-md-12">
                                <div class="card" style="min-height: 485px">
                                     
                                <div class="card-header card-header-text">
                                    <h4 class="card-title">Filtro de clientes</h4>
                                    <p class="category">Filtro de clientes por nombres</p>
                                </div>
                                   <div class="card-content">
                                 <form enctype="multipart/form-data" method="POST"  autocomplete="off">
    <label for="nombre">Nombre:</label>
    <input type="text" required class="" id="nomcli" name="nomcli">
    
    <input type="submit" value="Buscar" class="btn btn-danger text-white">
</form>

<?php
// Conexión a la base de datos con PDO
require '../../backend/bd/ctconex.php'; 
// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el nombre del formulario
    $nomcli = $_POST["nomcli"];

    // Consulta con búsqueda por nombre
    $sql = "SELECT * FROM clientes WHERE nomcli LIKE :nomcli";
    $stmt = $connect->prepare($sql);
    $stmt->bindValue(":nomcli", "%$nomcli%");
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Recorrer los resultados
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a los datos
            $id = $row['idclie'];
            $num = $row['numid'];
            $nomc = $row['nomcli'];
            $apec = $row['apecli'];
            $cell = $row['celu'];
           

            echo "<table class='table table-hover'>
  <thead>
    <tr>
      <th>DNI</th>
    <th>NOMBRE</th>
    <th>APELLIDO</th>
    <th>CELULAR</th>
   
    </tr>
    
  </thead>
  <tbody>
    <tr>
      <td>".$num."</td>
      <td>".$nomc."</td>
      <td>".$apec."</td>
      <td>".$cell."</td>
     
    </tr>
  </tbody>
</table>";


        }
    } else {
       
        echo '<span class="badge badge-danger">No hay datos disponibles</span>';
    }
}

// Cerrar la conexión
$conn = null;
?>




                                </div> 
                                </div>
                            </div>

                            
                            </div>

                         </div>

        </div>
    </div>

  
     <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
   <script src="../../backend/js/jquery-3.3.1.slim.min.js"></script>
   <script src="../../backend/js/popper.min.js"></script>
   <script src="../../backend/js/bootstrap.min.js"></script>
   <script src="../../backend/js/jquery-3.3.1.min.js"></script>
  
  
  <script type="text/javascript">
  $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
                    $('#content').toggleClass('active');
            });
               
                $('.more-button,.body-overlay').on('click', function () {
                $('#sidebar,.body-overlay').toggleClass('show-nav');
            });
               
        });

</script>
<script src="../../backend/js/loader.js"></script>
<script src="../../backend/js/reenvio.js"></script>

     
  </body>
  </html>





<?php }else{ 
    header('Location: ../error404.php');
 } ?>
 <?php ob_end_flush(); ?>     
