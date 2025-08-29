<?php
ob_start();
    session_start();
    // Verificar si no hay sesión o el rol no es 1 ni 2
    if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2)) {
        header('../error404.php');
        exit; 
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
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
         <!----css3---->
        <link rel="stylesheet" href="../assets/css/custom.css">
        <link rel="stylesheet" href="../assets/css/loader.css">

             
          <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->
     
     <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
     <!--google material icon-->
        <link href="https://fonts.googleapis.com/css2?family=Material+Icons"
      rel="stylesheet">
      <link rel="icon" type="image/png" href="../assets/img/favicon.webp"/>
  </head>
  <body>
  
<div class="wrapper">
   
<div class="body-overlay"></div>
       <!-- layouts nav.php  |  Sidebar -->
        <?php    include_once '../layouts/nav.php';  include_once '../layouts/menu_data.php';    ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
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
                         
                         <a class="navbar-brand" href="#">SERVICIOS</a>
                         
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
                                   
                                   <img src="../assets/img/reere.webp" >
                                           
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
                         <div class="row ">
                        <div class="col-lg-12 col-md-12">
                            <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../administrador/escritorio.php">Panel administrativo</a></li>
    <li class="breadcrumb-item"><a href="../plan/mostrar.php">Planes  </a></li>
    <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
  </ol>
</nav>
                            <div class="card" style="min-height: 485px">
                                <div class="card-header card-header-text">
                                    <h4 class="card-title">Servicios Recientes</h4>
                                    <p class="category">Actualizar plan reciente añadidos el dia de hoy</p>
                                </div>
                                
                               <div class="card-content table-responsive">
                                   <div class="alert alert-warning">
  <strong>Estimado usuario!</strong> Los campos remarcados con <span class="text-danger">*</span> son necesarios.
</div> 
<?php
 require '../../config/ctconex.php'; 
 $id = $_GET['id'];
 $sentencia = $connect->prepare("SELECT * FROM plan WHERE idplan= '$id';");
 $sentencia->execute();

$data =  array();
if($sentencia){
  while($r = $sentencia->fetchObject()){
    $data[] = $r;
  }
}
   ?>
   <?php if(count($data)>0):?>
        <?php foreach($data as $f):?>
<form enctype="multipart/form-data" method="POST"  autocomplete="off">
    <div class="row">
        

  <div class="col-md-4 col-lg-4">
   <div class="form-group">
    <label for="email">Nombre del servicio<span class="text-danger">*</span></label>
    <input type="text" value="<?php echo  $f->nompla  ; ?>" class="form-control"  name="txtnampla" required placeholder="Nombre del servicio">
   <input type="hidden" value="<?php echo  $f->idplan  ; ?>" name="txtidc">
</div>   
  </div>

<div class="col-md-4 col-lg-4">
   <div class="form-group">
    <label for="email">Precio del servicio<span class="text-danger">*</span></label>
    <input type="text" value="<?php echo  $f->prec  ; ?>" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  class="form-control"  name="txtprepl" required placeholder="Precio del servicio">
   
</div>   
  </div>


<div class="col-md-4 col-lg-4">
   <div class="form-group">
    <label for="email">Estado del servicio<span class="text-danger">*</span></label>
    <select class="form-control" required name="txtesta">
          <option value="<?php echo  $f->estp  ; ?>"><?php echo  $f->estp  ; ?></option>
          <option>--------Seleccione---------</option>
            <option value="Activo">Activo</option>
            <option value="Inactivo">Inactivo</option>                                 
    </select>
</div>   
  </div> 
 
    </div>



    <hr>
<div class="form-group">
        <div class="col-sm-12">
            <button name='stupdplan' class="btn btn-success text-white">Guardar</button>                       
            <a class="btn btn-danger text-white" href="../plan/mostrar.php">Cancelar</a>
        </div>
    </div>
</form>
<?php endforeach; ?>
    <?php else:?>
    <div class="alert alert-warning" role="alert">
  No se encontró ningún dato!
</div>

    <?php endif; ?> 
                                </div>
                                
                            </div>
                        </div>
                      
                    </div>

                         </div>

        </div>
    </div>

  
     <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
   <script src="../assets/js/jquery-3.3.1.slim.min.js"></script>
   <script src="../assets/js/popper.min.js"></script>
   <script src="../assets/js/bootstrap.min.js"></script>
   <script src="../assets/js/jquery-3.3.1.min.js"></script>
  <script src="../assets/js/sweetalert.js"></script>
   <?php
    include_once '../../backend/php/st_updpln.php'
?>
  
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
<script src="../assets/js/loader.js"></script>

        
  </body>
  </html>





<?php }else{ 
    header('../error404.php');
 } ?>
 <?php ob_end_flush(); ?>     
