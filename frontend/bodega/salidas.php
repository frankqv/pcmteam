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
    <title>Registro de Salidas - PCMARKETTEAM</title>
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
                        <a class="navbar-brand" href="#"> Registro de Salidas </a>
                    </div>
                </nav>
            </div>

            <div class="main-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Nueva Salida de Equipo</h4>
                            </div>
                            <div class="card-body">
                                <form id="salidaForm" method="POST" action="../../backend/php/st_add_salida.php">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Equipo</label>
                                                <select class="form-control" name="inventario_id" required>
                                                    <option value="">Seleccione un equipo</option>
                                                    <?php
                                                    $sql = "SELECT i.id, i.codigo_g, i.producto, i.marca, i.modelo, i.serial 
                                                            FROM bodega_inventario i 
                                                            WHERE i.estado = 'activo' 
                                                            AND i.disposicion = 'disponible'
                                                            ORDER BY i.fecha_modificacion DESC";
                                                    $result = $conn->query($sql);
                                                    while($row = $result->fetch_assoc()) {
                                                        echo "<option value='" . $row['id'] . "'>" . 
                                                             htmlspecialchars($row['codigo_g'] . ' - ' . $row['producto'] . ' ' . 
                                                             $row['marca'] . ' ' . $row['modelo'] . ' (' . $row['serial'] . ')') . 
                                                             "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Destino</label>
                                                <select class="form-control" name="destino" required>
                                                    <option value="Venta">Venta</option>
                                                    <option value="Reparación">Reparación</option>
                                                    <option value="Garantía">Garantía</option>
                                                    <option value="Exhibición">Exhibición</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Responsable</label>
                                                <select class="form-control" name="responsable_id" required>
                                                    <?php
                                                    $sql = "SELECT id, CONCAT(nombre, ' ', apellido) as nombre_completo 
                                                            FROM usuarios 
                                                            WHERE estado = 1 AND rol IN (1, 4, 5, 7)
                                                            ORDER BY nombre";
                                                    $result = $conn->query($sql);
                                                    while($row = $result->fetch_assoc()) {
                                                        echo "<option value='" . $row['id'] . "'>" . 
                                                             htmlspecialchars($row['nombre_completo']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Orden de Servicio</label>
                                                <input type="text" class="form-control" name="orden_servicio">
                                            </div>
                                            <div class="form-group">
                                                <label>Cliente</label>
                                                <select class="form-control" name="cliente_id">
                                                    <option value="">Seleccione un cliente</option>
                                                    <?php
                                                    $sql = "SELECT idclie, nomclie FROM clientes WHERE estado = 1";
                                                    $result = $conn->query($sql);
                                                    while($row = $result->fetch_assoc()) {
                                                        echo "<option value='" . $row['idclie'] . "'>" . 
                                                             htmlspecialchars($row['nomclie']) . "</option>";
                                                    }
                                                    ?>
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
                                            <button type="submit" class="btn btn-primary">Registrar Salida</button>
                                            <button type="reset" class="btn btn-secondary">Limpiar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de últimas salidas -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Últimas Salidas</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="salidasTable" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Código</th>
                                                <th>Producto</th>
                                                <th>Destino</th>
                                                <th>Responsable</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT s.*, i.codigo_g, i.producto, 
                                                    CONCAT(u.nombre, ' ', u.apellido) as responsable_nombre
                                                    FROM bodega_salidas s
                                                    JOIN bodega_inventario i ON s.inventario_id = i.id
                                                    JOIN usuarios u ON s.responsable_id = u.id
                                                    ORDER BY s.fecha_salida DESC LIMIT 10";
                                            $result = $conn->query($sql);
                                            
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['fecha_salida']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['codigo_g']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['producto']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['destino']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['responsable_nombre']) . "</td>";
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
        $('#salidaForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    alert('Salida registrada exitosamente');
                    location.reload();
                },
                error: function() {
                    alert('Error al registrar la salida');
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
