<?php
ob_start();
session_start();
    
if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 4, 7])){
    header('location: ../error404.php');
}
?>
<?php if(isset($_SESSION['id'])) { ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Nuevo Pedido en Ruta - PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <!----css3---->
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <style>
        #loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background-color: rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease-in-out;
        }
        
        #loader-wrapper.loaded {
            opacity: 0;
            visibility: hidden;
        }
        
        .select2-container {
            z-index: 9995;
        }
        
        .card {
            position: relative;
            z-index: 1;
        }
        
        .modal {
            z-index: 9996;
        }
        
        #sidebar {
            z-index: 9997;
        }
        
        .top-navbar {
            z-index: 9998;
        }
    </style>
    <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!--google material icon-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <!-- Sidebar -->
        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../../backend/img/favicon.png" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <div id="loader-wrapper">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#"> Nuevo Pedido en Ruta </a>
                        <button class="d-inline-block d-lg-none ml-auto more-button" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
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
                                        <img src="../../backend/img/reere.png">
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
                    <div class="col-lg-12 col-md-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../administrador/escritorio.php">Panel administrativo</a></li>
                                <li class="breadcrumb-item"><a href="../pedidos_ruta/mostrar.php">Pedidos en Ruta</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Nuevo Pedido</li>
                            </ol>
                        </nav>
                        <div class="card" style="min-height: 485px">
                            <div class="card-header card-header-text">
                                <h4 class="card-title">Registrar Nuevo Pedido en Ruta</h4>
                                <p class="category">Complete los datos del nuevo pedido</p>
                            </div>
                            <div class="card-content">
                                <div class="alert alert-warning">
                                    <strong>Estimado usuario!</strong> Los campos remarcados con <span class="text-danger">*</span> son necesarios.
                                </div>

                                <?php
                                require '../../backend/bd/ctconex.php';
                                
                                if(isset($_POST['add'])){
                                    $cliente_id = $_POST['cliente_id'];
                                    $servicio_id = $_POST['servicio_id'];
                                    $tecnico_id = $_POST['tecnico_id'];
                                    $fecha_asignacion = $_POST['fecha_asignacion'];
                                    $direccion_servicio = $_POST['direccion_servicio'];
                                    $descripcion = $_POST['descripcion'];
                                    $prioridad = $_POST['prioridad'];
                                    
                                    if(empty($cliente_id) || empty($servicio_id) || empty($tecnico_id) || empty($fecha_asignacion)){
                                        echo '<div class="alert alert-danger" role="alert">
                                            Todos los campos marcados con * son obligatorios
                                        </div>';
                                    } else {
                                        try {
                                            $stmt = $connect->prepare("INSERT INTO pedidos_ruta (cliente_id, servicio_id, tecnico_id, fecha_asignacion, direccion_servicio, descripcion, prioridad, estado) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pendiente')");
                                            $stmt->execute([$cliente_id, $servicio_id, $tecnico_id, $fecha_asignacion, $direccion_servicio, $descripcion, $prioridad]);
                                            
                                            echo '<div class="alert alert-success" role="alert">
                                                Pedido registrado correctamente
                                            </div>';
                                            echo '<meta http-equiv="refresh" content="2;url=mostrar.php">';
                                        } catch(PDOException $e) {
                                            echo '<div class="alert alert-danger" role="alert">
                                                Error al registrar el pedido: ' . $e->getMessage() . '
                                            </div>';
                                        }
                                    }
                                }
                                ?>

                                <form method="POST" autocomplete="off">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Cliente<span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="cliente_id" required>
                                                    <option value="">Seleccione un cliente</option>
                                                    <?php
                                                    $stmt = $connect->query("SELECT idclie, CONCAT(nomcli, ' ', apecli) as nombre FROM clientes WHERE estado = 1 ORDER BY nombre");
                                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        echo '<option value="'.$row['idclie'].'">'.$row['nombre'].'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Servicio<span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="servicio_id" required>
                                                    <option value="">Seleccione un servicio</option>
                                                    <?php
                                                    $stmt = $connect->query("SELECT idservc, servtxt FROM servicio WHERE estod = 'Activo' ORDER BY servtxt");
                                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        echo '<option value="'.$row['idservc'].'">'.$row['servtxt'].'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Técnico Asignado<span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="tecnico_id" required>
                                                    <option value="">Seleccione un técnico</option>
                                                    <?php
                                                    $stmt = $connect->query("SELECT id, CONCAT(nombre, ' ', apellido) as nombre FROM usuarios WHERE rol = '5' AND estado = '1' ORDER BY nombre");
                                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        echo '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Fecha de Asignación<span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="fecha_asignacion" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Dirección del Servicio<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="direccion_servicio" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Prioridad<span class="text-danger">*</span></label>
                                                <select class="form-control" name="prioridad" required>
                                                    <option value="Alta">Alta</option>
                                                    <option value="Media">Media</option>
                                                    <option value="Baja">Baja</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Descripción o Notas</label>
                                                <textarea class="form-control" name="descripcion" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="submit" name="add" class="btn btn-primary">
                                                <i class="material-icons">add</i> Registrar Pedido
                                            </button>
                                            <a href="mostrar.php" class="btn btn-danger">
                                                <i class="material-icons">cancel</i> Cancelar
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="../../backend/js/jquery-3.3.1.min.js"></script>
    <script src="../../backend/js/popper.min.js"></script>
    <script src="../../backend/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../backend/js/sidebarCollapse.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Función para mostrar el loader
        function showLoader() {
            $('#loader-wrapper').removeClass('loaded');
        }
        
        // Función para ocultar el loader
        function hideLoader() {
            $('#loader-wrapper').addClass('loaded');
        }
        
        $(document).ready(function() {
            // Ocultar el loader cuando la página esté lista
            setTimeout(hideLoader, 500);
            
            // Inicializar Select2
            $('.select2').select2({
                width: '100%',
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });
            
            // Establecer la fecha actual por defecto
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();
            today = yyyy + '-' + mm + '-' + dd;
            $('input[name="fecha_asignacion"]').val(today);
            
            // Manejar el envío del formulario
            $('form').on('submit', function(e) {
                showLoader();
            });
            
            // Asegurar que el sidebar esté por encima del loader
            $('#sidebar').css('z-index', '9997');
            $('.top-navbar').css('z-index', '9998');
        });
    </script>
</body>

</html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>
