
<!-- contructor ROL 7. Bodega -->
<?php
ob_start();
    session_start();

if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 6, 5 , 7])){
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
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!----css3---->
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">

    <!-- Data Tables -->
    <link rel="stylesheet" type="text/css" href="../assets/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/buttonsdataTables.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/font.css">
    <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!--google material icon-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
</head>

<body>
    <div class="wrapper">

        <!-- layouts nav.php  |  Sidebar -->
        <div class="body-overlay"></div>
        <?php    include_once '../layouts/nav.php';  include_once '../layouts/menu_data.php';    ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>

        <!-- Page Content  -->
        <div id="content">

            <!-- Contenido de top-navbar-->

            <!-- Contenido de MAin-->

            <!-- Pagina en construccion.php  |  Sidebar -->
            <?php    include_once '../util/pag_builder.php';    ?>
            <?php construcionpage(); ?>

        </div>
        <!---  Contenido de MAIN -->

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="../assets/js/jquery-3.3.1.slim.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>

        <script src="../assets/js/loader.js"></script>
        <!-- Data Tables -->
        <script type="text/javascript" src="../assets/js/datatable.js"></script>
        <script type="text/javascript" src="../assets/js/datatablebuttons.js"></script>
        <script type="text/javascript" src="../assets/js/jszip.js"></script>
        <script type="text/javascript" src="../assets/js/pdfmake.js"></script>
        <script type="text/javascript" src="../assets/js/vfs_fonts.js"></script>
        <script type="text/javascript" src="../assets/js/buttonshtml5.js"></script>
        <script type="text/javascript" src="../assets/js/buttonsprint.js"></script>

        <script type="text/javascript" src="../assets/js/example.js"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="../assets/js/chart/Chart.js"></script>
        <script>
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);
        </script>

</body>

</html>


<?php }else{ 
header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>