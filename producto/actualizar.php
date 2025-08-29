<!-- public_html/producto/actualizar.php -->
<?php
ob_start();
session_start();

// Activa el reporte de errores temporalmente para debug:
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 4, 5, 6, 7])){
    header('location: ../error404.php');
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
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
</head>

<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <!-- layouts nav.php  |  Sidebar -->
        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>
        <!-- Page Content  -->
        <div id="content">
            <div class='pre-loader'>
                <img class='loading-gif' alt='loading' src="https://i.imgflip.com/9vd6wr.gif" />
            </div>
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#"> Productos </a>
                        <button class="d-inline-block d-lg-none ml-auto more-button" type="button"
                            data-toggle="collapse" data-target="#navbarSupportedContent"
                            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="material-icons">more_vert</span>
                        </button>
                        <div class="collapse navbar-collapse d-lg-block d-xl-block d-sm-none d-md-none d-none"
                            id="navbarSupportedContent">
                            <ul class="nav navbar-nav ml-auto">
                                <li class="nav-item">
                                    <a class="nav-link" href="../cuenta/configuracion.php">
                                        <span class="material-icons">settings</span>
                                    </a>
                                </li>
                                <li class="dropdown nav-item active">
                                    <a href="#" class="nav-link" data-toggle="dropdown">
                                        <img src="../assets/img/reere.webp">
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
                                <li class="breadcrumb-item"><a href="../administrador/escritorio.php">Panel
                                        administrativo</a></li>
                                <li class="breadcrumb-item"><a href="../producto/mostrar.php">Productos </a></li>
                                <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
                            </ol>
                        </nav>
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Actualizar Producto</h4>
                                <p class="category">Actualizar información completa del producto</p>
                            </div>
                            <div class="card-content table-responsive">
                                <div class="alert alert-warning">
                                    <strong>Estimado usuario!</strong> Los campos remarcados con <span
                                        class="text-danger">*</span> son necesarios.
                                </div>
                                <?php
                                    require '../../config/ctconex.php'; 
                                    // Usa parámetros preparados para evitar inyección SQL
                                    $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
                                    if ($id <= 0) {
                                        header('Location: ../error404.php');
                                        exit;
                                    }
                                    $sentencia = $connect->prepare("SELECT 
                                        producto.idprod, 
                                        producto.codba, 
                                        producto.nomprd, 
                                        categoria.idcate, 
                                        categoria.nomca, 
                                        producto.precio, 
                                        producto.stock, 
                                        producto.foto, 
                                        producto.venci, 
                                        producto.esta, 
                                        producto.fere, 
                                        producto.serial, 
                                        producto.marca, 
                                        producto.ram, 
                                        producto.disco, 
                                        producto.prcpro, 
                                        producto.pntpro, 
                                        producto.tarpro, 
                                        producto.grado 
                                    FROM producto 
                                    INNER JOIN categoria ON producto.idcate = categoria.idcate 
                                    WHERE producto.idprod = ?");
                                    // Ejecutar con parámetro preparado (más seguro)
                                    $sentencia->execute([$id]);
                                    $data = array();
                                    if($sentencia){
                                        while($r = $sentencia->fetchObject()){
                                            $data[] = $r;
                                        }
                                    }
                                ?>
                                <?php if(count($data)>0):?>
                                <?php foreach($data as $f):?>
                                <form enctype="multipart/form-data" method="POST" autocomplete="off">
                                    <!-- Imagen del producto -->
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <center>
                                                    <img src="../assets/img/subidas/<?php echo $f->foto; ?>" height="150" class="img-thumbnail">
                                                    <br><br>
                                                    <label for="foto">Cambiar imagen del producto</label>
                                                    <input type="file" class="form-control" name="foto" accept="image/*">
                                                    <input type="hidden" name="foto_actual" value="<?php echo $f->foto; ?>">
                                                </center>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    
                                    <!-- Información básica del producto -->
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtcode">Código del producto<span class="text-danger">*</span></label>
                                                <input type="text" maxlength="14" value="<?php echo $f->codba; ?>"
                                                    class="form-control" name="txtcode" required
                                                    placeholder="Código del producto">
                                                <input type="hidden" value="<?php echo $f->idprod; ?>" name="txtidc">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtnampr">Nombre del producto<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo $f->nomprd; ?>" name="txtnampr" required
                                                    placeholder="Nombre del producto">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtcate">Categoria del producto<span class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtcate">
                                                    <option value="<?php echo $f->idcate; ?>">
                                                        <?php echo $f->nomca; ?></option>
                                                    <option value="">----------Seleccione------------</option>
                                                    <?php
                                                        $stmt = $connect->prepare("SELECT * FROM categoria where estado='Activo' order by categoria.idcate desc");
                                                        $stmt->execute();
                                                        while($row=$stmt->fetch(PDO::FETCH_ASSOC))
                                                        {
                                                            extract($row);
                                                            if($idcate != $f->idcate) {
                                                                ?>
                                                                <option value="<?php echo $idcate; ?>"><?php echo $nomca; ?></option>
                                                                <?php
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Información adicional del producto -->
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtserial">Serial<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="<?php echo $f->serial; ?>" 
                                                    name="txtserial" required placeholder="Serial del producto">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtmarca">Marca<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="<?php echo $f->marca; ?>" 
                                                    name="txtmarca" required placeholder="Marca del producto">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtgrado">Grado<span class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtgrado">
                                                    <option value="<?php echo $f->grado; ?>"><?php echo $f->grado; ?></option>
                                                    <option value="">----------Seleccione------------</option>
                                                    <option value="A">Grado A</option>
                                                    <option value="B">Grado B</option>
                                                    <option value="C">Grado C</option>
                                                    <option value="0">Grado 0</option>
                                                    <option value="">Por definirse Vacio</option>
                                                    <option value="#N/D">#N/D</option>
                                                    <option value="SCRAP">SCRAP</option>
                                                    <option value="Nuevo">Nuevo</option>
                                                    <option value="Reacondicionado">Reacondicionado</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Configuración del Equipo -->
                                    <center>
                                        <h4><b>Configuración del</b> Equipo</h4>
                                        <hr />
                                    </center>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtprcpro">Procesador<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="<?php echo $f->prcpro; ?>" 
                                                    name="txtprcpro" required placeholder="Procesador del equipo">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtram">RAM<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="<?php echo $f->ram; ?>" 
                                                    name="txtram" required placeholder="Memoria RAM">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtdisco">Disco<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="<?php echo $f->disco; ?>" 
                                                    name="txtdisco" required placeholder="Disco duro">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="txtpntpro">Dimensiones<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="<?php echo $f->pntpro; ?>" 
                                                    name="txtpntpro" required placeholder="Dimensiones del equipo">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="txttarpro">Tarjeta Gráfica<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="<?php echo $f->tarpro; ?>" 
                                                    name="txttarpro" required placeholder="Tarjeta gráfica">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Detalles Comerciales -->
                                    <center>
                                        <h4><b>Detalles</b> Comerciales</h4>
                                        <hr />
                                    </center>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtpre">Precio del producto<span class="text-danger">*</span></label>
                                                <input type="text" value="<?php echo $f->precio; ?>"
                                                    class="form-control"
                                                    onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"
                                                    name="txtpre" required placeholder="Precio del producto">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtstc">Stock del producto<span class="text-danger">*</span></label>
                                                <input type="text" maxlength="4" value="<?php echo $f->stock; ?>"
                                                    class="form-control"
                                                    onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"
                                                    name="txtstc" required placeholder="Stock del producto">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="txtvenc">Vencimiento del producto</label>
                                                <input type="date" value="<?php echo $f->venci; ?>"
                                                    class="form-control" name="txtvenc"
                                                    placeholder="Fecha de vencimiento">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label for="txtesta">Estado del producto<span class="text-danger">*</span></label>
                                                <select class="form-control" required name="txtesta">
                                                    <option value="<?php echo $f->esta; ?>">
                                                        <?php echo $f->esta; ?></option>
                                                    <option value="">--------Seleccione---------</option>
                                                    <option value="Activo">Activo</option>
                                                    <option value="Inactivo">Inactivo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button name='stupdprod' class="btn btn-success text-white">Guardar Cambios</button>
                                            <a class="btn btn-danger text-white"
                                                href="../producto/mostrar.php">Cancelar</a>
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
    <?php include_once '../../backend/php/st_updprodc.php' ?>
    <script type="text/javascript">
    $(document).ready(function() {
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
            $('#content').toggleClass('active');
        });
        $('.more-button,.body-overlay').on('click', function() {
            $('#sidebar,.body-overlay').toggleClass('show-nav');
        });
    });
    </script>
    <script src="../assets/js/loader.js"></script>
</body>

</html>
<?php }else{ 
    header('Location: ../error404.php');
 } ?>
<?php ob_end_flush(); ?>