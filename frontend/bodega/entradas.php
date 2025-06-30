<?php
ob_start();
session_start();

if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 7])){
    header('location: ../error404.php');
}

require_once '../../backend/bd/ctconex.php';
?>
<?php if(isset($_SESSION['id'])) { ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Registro de Entradas - PCMARKETTEAM</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="stylesheet" href="../../backend/css/loader.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.png" />
</head>

<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
        
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../../backend/img/favicon.png" class="img-fluid"/><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-mone d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#"> Registro de Entradas </a>
                    </div>
                </nav>
            </div>

            <div class="main-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Nueva Entrada de Equipo</h4>
                            </div>
                            <div class="card-body">
                                <form id="entradaForm" method="POST" action="../../backend/php/st_add_entrada.php">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Código General</label>
                                                <input type="text" class="form-control" name="codigo_g" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Producto</label>
                                                <input type="text" class="form-control" name="producto" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Marca</label>
                                                <input type="text" class="form-control" name="marca" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Modelo</label>
                                                <input type="text" class="form-control" name="modelo" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Serial</label>
                                                <input type="text" class="form-control" name="serial" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Proveedor</label>
                                                <select class="form-control" name="proveedor" required>
                                                    <?php
                                                    $sql = "SELECT idprov, nomprov FROM proveedores WHERE estado = 1";
                                                    $result = $conn->query($sql);
                                                    while($row = $result->fetch_assoc()) {
                                                        echo "<option value='" . $row['idprov'] . "'>" . htmlspecialchars($row['nomprov']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Factura</label>
                                                <input type="text" class="form-control" name="factura" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Ubicación</label>
                                                <select class="form-control" name="ubicacion" required>
                                                    <option value="Bodega">Bodega</option>
                                                    <option value="Laboratorio">Laboratorio</option>
                                                    <option value="Exhibición">Exhibición</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Grado</label>
                                                <select class="form-control" name="grado" required>
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="C">C</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Observaciones</label>
                                                <textarea class="form-control" name="observaciones" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12 text-center">
                                            <button type="submit" class="btn btn-primary">Registrar Entrada</button>
                                            <button type="reset" class="btn btn-secondary">Limpiar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de últimas entradas -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Últimas Entradas</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="entradasTable" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Código</th>
                                                <th>Producto</th>
                                                <th>Proveedor</th>
                                                <th>Factura</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT e.*, i.codigo_g, i.producto, p.nomprov 
                                                    FROM bodega_entradas e 
                                                    JOIN bodega_inventario i ON e.inventario_id = i.id 
                                                    JOIN proveedores p ON e.proveedor_id = p.idprov 
                                                    ORDER BY e.fecha_entrada DESC LIMIT 10";
                                            $result = $conn->query($sql);
                                            
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['fecha_entrada']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['codigo_g']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['producto']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['nomprov']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['factura']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
                                                echo "<td>
                                                        <a href='javascript:void(0)' class='btn btn-info btn-sm view-btn' data-id='" . $row['id'] . "'><i class='material-icons'>visibility</i></a>
                                                      </td>";
                                                echo "</tr>";
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
    </div>

    <!-- Scripts -->
    <script src="../../backend/js/jquery-3.3.1.min.js"></script>
    <script src="../../backend/js/popper.min.js"></script>
    <script src="../../backend/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../backend/js/sidebarCollapse.js"></script>
    <script src="../../backend/js/loader.js"></script>

    <script>
    $(document).ready(function() {
        // Manejar envío del formulario
        $('#entradaForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    alert('Entrada registrada exitosamente');
                    location.reload();
                },
                error: function() {
                    alert('Error al registrar la entrada');
                }
            });
        });

        // Ver detalles
        $('.view-btn').click(function() {
            var id = $(this).data('id');
            // Implementar vista de detalles
        });
    });
    </script>
</body>
</html>
<?php } else { 
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>
