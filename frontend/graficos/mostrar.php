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
       <!-- layouts nav.php  |  Sidebar -->
        <?php    include_once '../layouts/nav.php';  include_once '../layouts/menu_data.php';    ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../../backend/img/favicon.png" class="img-fluid"><span>PCMARKETTEAM</span></h3>
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
                         
                         <a class="navbar-brand" href="#"> Graficos </a>
                         
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
    <div class="row">
       <div class="col-lg-4 col-md-6 col-sm-6">
           <div class="card card-stats">
               <div class="card-header">
                    <div class="icon icon-warning">
                        <span class="material-icons">price_check</span>
                    </div>
                </div>
                <div class="card-content">         
         <?php 
    require '../../backend/bd/ctconex.php';  
$sql = "SELECT SUM(total) total_price FROM ingresos ";
$result = $connect->query($sql); //$pdo sería el objeto conexión
$total_price = $result->fetchColumn();

?> 
                                    <p class="category"><strong>Ingresos totales</strong></p>
                                    <h3 class="card-title">S/<?php echo  $total_price; ?></h3>
                                </div>

                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
           </div>
       </div>


       <div class="col-lg-4 col-md-6 col-sm-6">
           <div class="card card-stats">
               <div class="card-header">
                    <div class="icon icon-rose">
                        <span class="material-icons">savings</span>
                    </div>
                </div>
                <div class="card-content">         
         <?php 

$sql = "SELECT SUM(total) total_gas FROM gastos ";
$result = $connect->query($sql); //$pdo sería el objeto conexión
$total_gas = $result->fetchColumn();

?> 
                                    <p class="category"><strong>Gastos totales</strong></p>
                                    <h3 class="card-title">S/<?php echo  $total_gas; ?></h3>
                                </div>

                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
           </div>
       </div>



        <div class="col-lg-4 col-md-6 col-sm-6">
           <div class="card card-stats">
               <div class="card-header">
                    <div class="icon icon-success">
                        <span class="material-icons">price_change</span>
                    </div>
                </div>
                <div class="card-content">         
         <?php 

$sql = "SELECT SUM(total) total_gas FROM gastos ";
$result = $connect->query($sql); //$pdo sería el objeto conexión
$total_gas = $result->fetchColumn();

?> 

 <?php  
$sql = "SELECT SUM(total) total_price FROM ingresos ";
$result = $connect->query($sql); //$pdo sería el objeto conexión
$total_price = $result->fetchColumn();

?> 
                                    <p class="category"><strong>Ganancias totales</strong></p>
                                    <h3 class="card-title">S/<?php echo number_format($total_price - $total_gas, 2); ?></h3>


                                </div>

                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
           </div>
       </div>



       <div class="col-lg-4 col-md-6 col-sm-6">
           <div class="card card-stats">
               <div class="card-header">
                    <div class="icon icon-info">
                        <span class="material-icons">attach_money</span>
                    </div>
                </div>
                <div class="card-content">         
         <?php 

$sql = "SELECT SUM(total) total_price, fec FROM ingresos where fec = CURDATE()";
$result = $connect->query($sql); //$pdo sería el objeto conexión
$total_in = $result->fetchColumn();

?> 
 
                                    <p class="category"><strong>Ingresos hoy</strong></p>
                                    <h3 class="card-title">S/<?php echo  $total_in; ?></h3>
                                </div>

                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
           </div>
       </div>


       <div class="col-lg-4 col-md-6 col-sm-6">
           <div class="card card-stats">
               <div class="card-header">
                    <div class="icon icon-info">
                        <span class="material-icons">attach_money</span>
                    </div>
                </div>
                <div class="card-content">         
         <?php 

$sql = "SELECT  SUM(total) AS total, WEEK(fec) AS fec
FROM ingresos GROUP BY WEEK(fec)
ORDER BY fec ASC";
$result = $connect->query($sql); //$pdo sería el objeto conexión
$total_in = $result->fetchColumn();

?> 
 
                                    <p class="category"><strong>Ingresos semanal</strong></p>
                                    <h3 class="card-title">S/<?php echo  $total_in; ?></h3>
                                </div>

                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
           </div>
       </div>


       <div class="col-lg-4 col-md-6 col-sm-6">
           <div class="card card-stats">
               <div class="card-header">
                    <div class="icon icon-info">
                        <span class="material-icons">attach_money</span>
                    </div>
                </div>
                <div class="card-content">         
         <?php 

$sql = "SELECT  SUM(total) AS total, MONTH(fec) AS fec
FROM ingresos GROUP BY MONTH(fec)
ORDER BY fec ASC";
$result = $connect->query($sql); //$pdo sería el objeto conexión
$total_inme = $result->fetchColumn();

?> 
 
                                    <p class="category"><strong>Ingresos mensual</strong></p>
                                    <h3 class="card-title">S/<?php echo  $total_inme; ?></h3>
                                </div>

                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
           </div>
       </div>


<div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-success">
                                        <span class="material-icons">
point_of_sale
</span>

                                    </div>
                                </div>
                                <div class="card-content">
                                    <?php 
                                                $sql = "SELECT SUM(total_price) total_price,placed_on FROM orders where placed_on = CURDATE()";
                                                $result = $connect->query($sql); //$pdo sería el objeto conexión
                                                $total_price = $result->fetchColumn();

                                                 ?> 
                                    <p class="category"><strong>Ventas de producto hoy</strong></p>
                                    <h3 class="card-title">S/<?php echo  $total_price; ?> </h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-success">
                                        <span class="material-icons">
point_of_sale
</span>

                                    </div>
                                </div>
                                <div class="card-content">
                                    <?php 
                                                $sql = "SELECT  SUM(total_price) AS total_price, WEEK(placed_on) AS placed_on
FROM orders
GROUP BY WEEK(placed_on)
ORDER BY placed_on ASC";
                                                $result = $connect->query($sql); //$pdo sería el objeto conexión
                                                $total_price = $result->fetchColumn();

                                                 ?> 
                                    <p class="category"><strong>Ventas de producto semanal</strong></p>
                                    <h3 class="card-title">S/<?php echo  $total_price; ?> </h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-success">
                                        <span class="material-icons">
point_of_sale
</span>

                                    </div>
                                </div>
                                <div class="card-content">
                                    <?php 
                                                $sql = "SELECT  SUM(total_price) AS total_price, MONTH(placed_on) AS placed_on
FROM orders
GROUP BY MONTH(placed_on)
ORDER BY placed_on ASC";
                                                $result = $connect->query($sql); //$pdo sería el objeto conexión
                                                $total_price = $result->fetchColumn();

                                                 ?> 
                                    <p class="category"><strong>Ventas de producto mensual</strong></p>
                                    <h3 class="card-title">S/<?php echo  $total_price; ?> </h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-rose">
                                        <span class="material-icons">
point_of_sale
</span>

                                    </div>
                                </div>
                                <div class="card-content">
                                    <?php 
                    $stmt1 = $connect->prepare("SELECT servicio.idservc, plan.idplan, plan.prec,plan.foto, plan.nompla, servicio.ini, servicio.fin, clientes.idclie, clientes.numid, clientes.nomcli, clientes.apecli, clientes.naci, clientes.celu, clientes.correo, servicio.estod, servicio.fere, SUM(prec) as prec FROM servicio INNER JOIN plan ON servicio.idplan = plan.idplan INNER JOIN clientes ON servicio.idclie = clientes.idclie where servicio.ini = CURDATE()");

        $stmt1->setFetchMode(PDO::FETCH_ASSOC);
        $stmt1->execute();
                 ?> 
                                    <p class="category"><strong>Ventas de servicios hoy</strong></p>
                                    <h3 class="card-title"><?php 
        while($row = $stmt1->fetch()) { 
            echo "S/ " . $row['prec'] . "<br>";
        }
    ?> </h3>
    
                                </div>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-rose">
                                        <span class="material-icons">
point_of_sale
</span>

                                    </div>
                                </div>
                                <div class="card-content">
                                    <?php 
                    $stmt1 = $connect->prepare("SELECT servicio.idservc, plan.idplan, plan.prec,plan.foto, plan.nompla, servicio.ini, servicio.fin, clientes.idclie, clientes.numid, clientes.nomcli, clientes.apecli, clientes.naci, clientes.celu, clientes.correo, servicio.estod, servicio.fere, SUM(prec) as prec ,WEEK(ini) AS ini FROM servicio INNER JOIN plan ON servicio.idplan = plan.idplan INNER JOIN clientes ON servicio.idclie = clientes.idclie  GROUP BY WEEK(ini)
ORDER BY ini ASC");

        $stmt1->setFetchMode(PDO::FETCH_ASSOC);
        $stmt1->execute();
                 ?> 
                                    <p class="category"><strong>Ventas de servicios semanal</strong></p>
                                    <h3 class="card-title"><?php 
        while($row1 = $stmt1->fetch()) { 
            echo "S/ " . $row1['prec'] . "<br>";
        }
    ?> </h3>
    
                                </div>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-rose">
                                        <span class="material-icons">
point_of_sale
</span>

                                    </div>
                                </div>
                                <div class="card-content">
                                    <?php 
                    $stmt1 = $connect->prepare("SELECT servicio.idservc, plan.idplan, plan.prec,plan.foto, plan.nompla, servicio.ini, servicio.fin, clientes.idclie, clientes.numid, clientes.nomcli, clientes.apecli, clientes.naci, clientes.celu, clientes.correo, servicio.estod, servicio.fere, SUM(prec) as prec ,MONTH(ini) AS ini FROM servicio INNER JOIN plan ON servicio.idplan = plan.idplan INNER JOIN clientes ON servicio.idclie = clientes.idclie  GROUP BY MONTH(ini)
ORDER BY ini ASC");

        $stmt1->setFetchMode(PDO::FETCH_ASSOC);
        $stmt1->execute();
                 ?> 
                                    <p class="category"><strong>Ventas de servicios mensual</strong></p>
                                    <h3 class="card-title"><?php 
        while($row1 = $stmt1->fetch()) { 
            echo "S/ " . $row1['prec'] . "<br>";
        }
    ?> </h3>
    
                                </div>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-success">
                                        <span class="material-icons">
monetization_on
</span>

                                    </div>
                                </div>
                                <div class="card-content">
                                    <?php 
                                                $sql = "SELECT SUM(total_price) total_price,placed_on FROM compra where placed_on = CURDATE()";
                                                $result = $connect->query($sql); //$pdo sería el objeto conexión
                                                $total_pricec = $result->fetchColumn();

                                                 ?> 
                                    <p class="category"><strong>Compras de producto hoy</strong></p>
                                    <h3 class="card-title">S/<?php echo  $total_pricec; ?> </h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-success">
                                        <span class="material-icons">
monetization_on
</span>

                                    </div>
                                </div>
                                <div class="card-content">
                                    <?php 
                                                $sql = "SELECT SUM(total_price) total_price, WEEK(placed_on) AS placed_on FROM compra ";
                                                $result = $connect->query($sql); //$pdo sería el objeto conexión
                                                $total_priceca = $result->fetchColumn();

                                                 ?> 
                                    <p class="category"><strong>Compra de producto semanal</strong></p>
                                    <h3 class="card-title">S/<?php echo  $total_priceca; ?> </h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-success">
                                        <span class="material-icons">
monetization_on
</span>

                                    </div>
                                </div>
                                <div class="card-content">
                                    <?php 
                                                $sql = "SELECT SUM(total_price) total_price, MONTH(placed_on) AS placed_on FROM compra ";
                                                $result = $connect->query($sql); //$pdo sería el objeto conexión
                                                $total_pricecaa = $result->fetchColumn();

                                                 ?> 
                                    <p class="category"><strong>Compra de producto mensual</strong></p>
                                    <h3 class="card-title">S/<?php echo  $total_pricecaa; ?> </h3>
                                </div>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Recién actualizado
                                    </div>
                                </div>
                            </div>
                        </div>

    </div>

    <div class="row ">
        <div class="col-lg-6 col-md-6">
                                <div class="card" style="min-height: 485px">
                                    
                                     <div class="card-header card-header-text">
                                    <h4 class="card-title">Grafico de ingresos</h4>
                                </div>
                                   <div class="card-content">
                                  <div id="chart_div"  class="tcentrado"></div>  
                                </div> 
                                </div>
                            </div>


                            <div class="col-lg-6 col-md-6">
                                <div class="card" style="min-height: 485px">
                                    
                                     <div class="card-header card-header-text">
                                    <h4 class="card-title">Grafico de gastos</h4>
                                </div>
                                   <div class="card-content">
                                  <div id="gas_div"  class="tcentrado"></div>  
                                </div> 
                                </div>
                            </div>
    </div>
    <div class="row">
     <div class="col-lg-6 col-md-6">
                                <div class="card" style="min-height: 485px">
                                    
                                     <div class="card-header card-header-text">
                                    <h4 class="card-title">Resumen de las ventas de servicios de planes</h4>
                                </div>
                                   <div class="card-content">
                                  <div id="curve_chart"  class="tcentrado"></div>  
                                </div> 
                                </div>
                            </div> 



                             <div class="col-lg-6 col-md-6">
                                <div class="card" style="min-height: 485px">
                                    
                                     <div class="card-header card-header-text">
                                    <h4 class="card-title">Resumen de las ventas de productos</h4>
                                </div>
                                   <div class="card-content">
                                  <div id="venta_chart"  class="tcentrado"></div>  
                                </div> 
                                </div>
                            </div>   
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6">
                                <div class="card" style="min-height: 485px">
                                    
                                     <div class="card-header card-header-text">
                                    <h4 class="card-title">Resumen de las compras de los productos</h4>
                                </div>
                                   <div class="card-content">
                                  <div id="compra_chart"  class="tcentrado"></div>  
                                </div> 
                                </div>
                            </div>


                            <div class="col-lg-6 col-md-6">
                                <div class="card" style="min-height: 485px">
                                    
                                     <div class="card-header card-header-text">
                                    <h4 class="card-title">Resumen de los productos por stock</h4>
                                </div>
                                   <div class="card-content">
                                  <div id="product_3d"  class="tcentrado"></div>  
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
  <script src="../../backend/js/sweetalert.js"></script>
   <?php
    include_once '../../backend/php/st_updconfi.php'
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
<script src="../../backend/js/loader.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="../../backend/js/chart/Chart.js"></script>

 <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Fecha', 'Ingresos'],
          <?php
                        
        $stmt = $connect->prepare("SELECT SUM(total) total_price , fec FROM ingresos where month(fec) and year(fec) GROUP BY ingresos.fec ORDER BY ingresos.fec aSC");

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch()) { 
            echo "['".$row['fec']."', ".$row['total_price']."],";
        }

            ?>
        ]);

        var options = {
          title: '',
          hAxis: {title: 'Fecha',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>

 <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Fecha', 'Gastos'],
          <?php
                        
        $stmt = $connect->prepare("SELECT SUM(total) total_gas , fec FROM gastos where month(fec) and year(fec) GROUP BY gastos.fec ORDER BY gastos.fec aSC");

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch()) { 
            echo "['".$row['fec']."', ".$row['total_gas']."],";
        }

            ?>
        ]);

        var options = {
          title: '',
          hAxis: {title: 'Fecha',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('gas_div'));
        chart.draw(data, options);
      }
    </script>

    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Fecha', 'Ventas'],
          
          <?php
                        
        $stmt = $connect->prepare("SELECT servicio.idservc, plan.idplan, plan.prec,plan.foto, plan.nompla, servicio.ini, servicio.fin, clientes.idclie, clientes.numid, clientes.nomcli, clientes.apecli, clientes.naci, clientes.celu, clientes.correo, servicio.estod, servicio.fere, SUM(prec) as prec FROM servicio INNER JOIN plan ON servicio.idplan = plan.idplan INNER JOIN clientes ON servicio.idclie = clientes.idclie where month(ini) and year(ini) GROUP BY servicio.ini ORDER BY servicio.ini aSC");

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch()) { 
            echo "['".$row['ini']."', ".$row['prec']."],";
        }

            ?>
          
        ]);

        var options = {
          title: '',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Fecha', 'Ventas'],
          
          <?php
                        
        $stmt = $connect->prepare("SELECT SUM(total_price) total_price,placed_on FROM orders where month(placed_on) and year(placed_on) GROUP BY orders.placed_on ORDER BY orders.placed_on aSC");

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch()) { 
            echo "['".$row['placed_on']."', ".$row['total_price']."],";
        }

            ?>

        ]);

        var options = {
          title: '',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('venta_chart'));

        chart.draw(data, options);
      }
    </script>

    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Fecha', 'Compra'],
          
          <?php
                        
        $stmt = $connect->prepare("SELECT SUM(total_price) total_price,placed_on FROM compra where month(placed_on) and year(placed_on) GROUP BY compra.placed_on ORDER BY compra.placed_on aSC");

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch()) { 
            echo "['".$row['placed_on']."', ".$row['total_price']."],";
        }

            ?>


        ]);

        var options = {
          title: '',
          hAxis: {title: 'Fecha', minValue: 0, maxValue: 15},
          vAxis: {title: 'Compra', minValue: 0, maxValue: 15},
          legend: 'none'
        };

        var chart = new google.visualization.ScatterChart(document.getElementById('compra_chart'));

        chart.draw(data, options);
      }
    </script>
<script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Producto', 'Stock'],
          <?php
                        
        $stmt = $connect->prepare("SELECT producto.idprod, producto.codba, producto.nomprd, categoria.idcate, categoria.nomca, producto.precio, producto.stock, producto.foto, producto.venci, producto.esta, producto.fere FROM producto INNER JOIN categoria ON producto.idcate = categoria.idcate");

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch()) { 
            echo "['".$row['nomprd']."', ".$row['stock']."],";
        }

            ?>
        ]);

        var options = {
          title: '',
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('product_3d'));
        chart.draw(data, options);
      }
    </script>


        
  </body>
  </html>





<?php }else{ 
    header('Location: ../error404.php');
 } ?>
 <?php ob_end_flush(); ?>     
