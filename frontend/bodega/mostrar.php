<!-- ROL 7. Bodega -->
<?php
ob_start();
    session_start();

if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 7])){
header('location: ../error404.php');
}

// Incluir el archivo de conexión
require_once '../../backend/bd/ctconex.php';

?>
<?php if(isset($_SESSION['id'])) { ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Listado General de Inventario - PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <!----css3---->
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="stylesheet" href="../../backend/css/loader.css">

    <!-- Data Tables -->
    <link rel="stylesheet" type="text/css" href="../../backend/css/datatable.css">
    <link rel="stylesheet" type="text/css" href="../../backend/css/buttonsdataTables.css">
    <link rel="stylesheet" type="text/css" href="../../backend/css/font.css">
    <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!--google material icon-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />
</head>

<body>
    <div class="wrapper">

        <!-- layouts nav.php  |  Sidebar -->
        <div class="body-overlay"></div>s
        <?php    include_once '../layouts/nav.php';  include_once '../layouts/menu_data.php';    ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../../backend/img/favicon.png" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>

        <!-- Page Content  -->
        <div id="content">
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#"> Listado General de Inventario </a>
                    </div>
                </nav>
            </div>

            <div class="main-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Inventario General</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Producto</th>
                                                <th>Marca</th>
                                                <th>Modelo</th>
                                                <th>Serial</th>
                                                <th>Ubicación</th>
                                                <th>Grado</th>
                                                <th>Estado</th>
                                                <th>Última Modificación</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT i.*, 
                                                    CASE 
                                                        WHEN d.estado_reparacion IS NOT NULL THEN d.estado_reparacion
                                                        WHEN cc.estado_final IS NOT NULL THEN cc.estado_final
                                                        ELSE i.disposicion 
                                                    END as estado_actual
                                                    FROM bodega_inventario i
                                                    LEFT JOIN bodega_diagnosticos d ON i.id = d.inventario_id 
                                                        AND d.id = (SELECT MAX(id) FROM bodega_diagnosticos WHERE inventario_id = i.id)
                                                    LEFT JOIN bodega_control_calidad cc ON i.id = cc.inventario_id 
                                                        AND cc.id = (SELECT MAX(id) FROM bodega_control_calidad WHERE inventario_id = i.id)
                                                    WHERE i.estado = 'activo'
                                                    ORDER BY i.fecha_modificacion DESC";
                                            
                                            $result = $conn->query($sql);
                                            
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['codigo_g']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['producto']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['marca']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['serial']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['ubicacion']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['grado']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['estado_actual']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['fecha_modificacion']) . "</td>";
                                                    echo "<td class='text-center'>
                                                            <a href='javascript:void(0)' class='btn btn-info btn-sm view-btn' data-id='" . $row['id'] . "'><i class='material-icons'>visibility</i></a>
                                                            <a href='javascript:void(0)' class='btn btn-primary btn-sm edit-btn' data-id='" . $row['id'] . "'><i class='material-icons'>edit</i></a>
                                                            <a href='javascript:void(0)' class='btn btn-danger btn-sm delete-btn' data-id='" . $row['id'] . "'><i class='material-icons'>delete</i></a>
                                                          </td>";
                                                    echo "</tr>";
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!---  Contenido de MAIN -->

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="../../backend/js/jquery-3.3.1.slim.min.js"></script>
        <script src="../../backend/js/popper.min.js"></script>
        <script src="../../backend/js/bootstrap.min.js"></script>
        <script src="../../backend/js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="../../backend/js/sidebarCollapse.js"></script>

        <script src="../../backend/js/loader.js"></script>
        <!-- Data Tables -->
        <script type="text/javascript" src="../../backend/js/datatable.js"></script>
        <script type="text/javascript" src="../../backend/js/datatablebuttons.js"></script>
        <script type="text/javascript" src="../../backend/js/jszip.js"></script>
        <script type="text/javascript" src="../../backend/js/pdfmake.js"></script>
        <script type="text/javascript" src="../../backend/js/vfs_fonts.js"></script>
        <script type="text/javascript" src="../../backend/js/buttonshtml5.js"></script>
        <script type="text/javascript" src="../../backend/js/buttonsprint.js"></script>

        <script type="text/javascript" src="../../backend/js/example.js"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="../../backend/js/chart/Chart.js"></script>
        <script>
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);
        </script>

        <!-- Modal para ver detalles -->
        <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalles del Equipo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="viewModalBody">
                        <!-- Los detalles se cargarán aquí dinámicamente -->
                    </div>
                </div>
            </div>
        </div>

        <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#example').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                }
            });

            // Ver detalles
            $('.view-btn').click(function() {
                var id = $(this).data('id');
                $.ajax({
                    url: '../../backend/php/get_inventario_details.php',
                    type: 'GET',
                    data: { id: id },
                    success: function(response) {
                        $('#viewModalBody').html(response);
                        $('#viewModal').modal('show');
                    }
                });
            });

            // Editar equipo
            $('.edit-btn').click(function() {
                var id = $(this).data('id');
                window.location.href = 'editar_inventario.php?id=' + id;
            });

            // Eliminar equipo
            $('.delete-btn').click(function() {
                if (confirm('¿Está seguro de que desea eliminar este equipo?')) {
                    var id = $(this).data('id');
                    $.ajax({
                        url: '../../backend/php/delete_inventario.php',
                        type: 'POST',
                        data: { id: id },
                        success: function(response) {
                            alert('Equipo eliminado exitosamente');
                            location.reload();
                        },
                        error: function() {
                            alert('Error al eliminar el equipo');
                        }
                    });
                }
            });
        });
        </script>
    </div>
</body>

</html>


<?php }else{ 
header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>