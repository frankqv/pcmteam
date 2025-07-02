<!-- /bodega/entradas.php -->
<?php
ob_start();
session_start();

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    header('location: ../error404.php');
}

require_once '../../backend/bd/ctconex.php';
?>
<?php if (isset($_SESSION['id'])) { ?>
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
            <?php include_once '../layouts/nav.php';
            include_once '../layouts/menu_data.php'; ?>

            <!-- Sidebar -->
            <nav id="sidebar">
                <div class="sidebar-header">
                    <h3><img src="../../backend/img/favicon.png" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
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
                            <a class="navbar-brand"> Registro de Entradas </a>
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
                                                    <label for="codigo_g">Código general del equipo</label>
                                                    <input type="text" class="form-control" name="codigo_g" id="codigo_g"
                                                        placeholder="Código general del equipo" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Ubicación en sede</label>
                                                    <select class="form-control" name="ubse" required>
                                                        <option value="">----Seleccionar Ubicación en sede----</option>
                                                        <option value="Principal">Principal</option>
                                                        <option value="Unilago">Unilago</option>
                                                        <option value="Cúcuta">Cúcuta</option>
                                                        <option value="Medellín">Medellín</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="posicion">Posición exacta dentro de la ubicación</label>
                                                    <input type="text" class="form-control" name="posicion" id="posicion"
                                                        placeholder="Posición exacta dentro de la ubicación" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="producto">Tipo de producto</label>
                                                    <select class="form-control" name="producto" id="producto" required>
                                                        <option value="">Seleccione el tipo de producto</option>
                                                        <option value="Portatil">Portatil</option>
                                                        <option value="Desktop">Desktop</option>
                                                        <option value="Monitor">Monitor</option>
                                                        <option value="AIO">AIO</option>
                                                        <option value="Tablet">Tablet</option>
                                                        <option value="Celular">Celular</option>
                                                        <option value="Impresora">Impresora</option>
                                                        <option value="Periferico">Periferico Computador</option>
                                                        <option value="otro">Otro</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="marca">Marca del equipo</label>
                                                    <select class="form-control" name="marca" id="marca" required>
                                                        <option value="">Seleccione la marca</option>
                                                        <option value="HP">HP</option>
                                                        <option value="Dell">Dell</option>
                                                        <option value="Lenovo">Lenovo</option>
                                                        <option value="Acer">Acer</option>
                                                        <option value="CompuMax">CompuMax</option>
                                                        <option value="Otro">Otro</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="serial">Serial del fabricante</label>
                                                    <input type="text" class="form-control" name="serial" id="serial"
                                                        placeholder="Serial del fabricante" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="modelo">Modelo específico del equipo</label>
                                                    <input type="text" class="form-control" name="modelo" id="modelo"
                                                        placeholder="Modelo específico" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="procesador">Especificaciones del procesador</label>
                                                    <input type="text" class="form-control" name="procesador"
                                                        id="procesador" placeholder="Ej: Intel i5 8th Gen">
                                                </div>
                                                <div class="form-group">
                                                    <label for="ram">Memoria RAM instalada</label>
                                                    <select class="form-control" name="ram" id="ram" required>
                                                        <option value="">Seleccione la memoria RAM</option>
                                                        <option value="4GB">4GB</option>
                                                        <option value="8GB">8GB</option>
                                                        <option value="16GB">16GB</option>
                                                        <option value="32GB">32GB</option>
                                                        <option value="otro">Otro</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="disco">Tipo y capacidad del disco</label>
                                                    <input type="text" class="form-control" name="disco" id="disco"
                                                        placeholder="Ej: SSD 256GB">
                                                </div>
                                                <div class="form-group">
                                                    <label for="pulgadas">Tamaño de pantalla</label>
                                                    <input type="text" class="form-control" name="pulgadas" id="pulgadas"
                                                        placeholder="Ej: 15.6">
                                                </div>
                                                <div class="form-group">
                                                    <label for="observaciones">Notas técnicas y observaciones</label>
                                                    <textarea class="form-control" name="observaciones" id="observaciones"
                                                        rows="3" placeholder="Notas técnicas y observaciones"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="grado">Clasificación según procedimiento técnico</label>
                                                    <select class="form-control" name="grado" id="grado" required>
                                                        <option value="">Seleccione grado</option>
                                                        <option value="A">A</option>
                                                        <option value="B">B</option>
                                                        <option value="C">C</option>
                                                        <option value="SCRAP">SCRAP</option>
                                                        <option value="#N/D">#N/D</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="disposicion">Estado actual del equipo en el proceso</label>
                                                    <select class="form-control" name="disposicion" id="disposicion" required>
                                                        <option value="En revisión">En revisión</option>
                                                        <option value="Por Alistamiento">Por Alistamiento</option>
                                                        <option value="En Laboratorio">En Laboratorio</option>
                                                        <option value="En Bodega">En Bodega</option>
                                                        <option value="Para Venta">Para Venta</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sección de Proveedor y Responsable -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="proveedor">Proveedor <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="proveedor" id="proveedor" required>
                                                        <option value="">----------Seleccione Proveedor------------</option>
                                                        <?php
                                                        try {
                                                            $stmt_proveedores = $connect->prepare("SELECT id, nombre, nomenclatura FROM proveedores ORDER BY nombre ASC");
                                                            $stmt_proveedores->execute();
                                                            while ($row_prov = $stmt_proveedores->fetch(PDO::FETCH_ASSOC)) {
                                                                ?>
                                                                <option value="<?php echo htmlspecialchars($row_prov['id']); ?>">
                                                                    <?php echo htmlspecialchars($row_prov['nombre']); ?> -
                                                                    <?php echo htmlspecialchars($row_prov['nomenclatura']); ?>
                                                                </option>
                                                                <?php
                                                            }
                                                        } catch (PDOException $e) {
                                                            echo "<option value=''>Error al cargar proveedores</option>";
                                                            error_log("Error en consulta proveedores: " . $e->getMessage());
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="responsable">Responsable <span class="text-danger">*</span></label>
                                                    <select class="form-control" required name="responsable">
                                                        <option value="">----------Seleccione Responsable------------</option>
                                                        <?php
                                                        try {
                                                            $stmt_responsables = $connect->prepare("SELECT id, nombre, rol FROM usuarios WHERE rol IN (1, 7) AND estado = '1' ORDER BY nombre ASC");
                                                            $stmt_responsables->execute();
                                                            while ($row_resp = $stmt_responsables->fetch(PDO::FETCH_ASSOC)) {
                                                                $rol_texto = ($row_resp['rol'] == 1) ? 'Admin' : (($row_resp['rol'] == 7) ? 'Bodega' : 'Técnico');
                                                                ?>
                                                                <option value="<?php echo htmlspecialchars($row_resp['nombre']); ?>">
                                                                    <?php echo htmlspecialchars($row_resp['nombre']); ?> -
                                                                    <?php echo $rol_texto; ?>
                                                                </option>
                                                                <?php
                                                            }
                                                        } catch (PDOException $e) {
                                                            echo "<option value=''>Error al cargar responsables</option>";
                                                            error_log("Error en consulta responsables: " . $e->getMessage());
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="estado">Estado</label>
                                                    <input type="hidden" name="estado" value="activo">
                                                    <input type="text" class="form-control" id="estado" value="Activo" readonly style="background-color: #e9ecef;">
                                                    <small class="form-text text-muted">Todas las nuevas entradas se registran como activas</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12 text-center">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="material-icons">save</i> Registrar Entrada
                                                </button>
                                                <button type="reset" class="btn btn-secondary">
                                                    <i class="material-icons">clear</i> Limpiar
                                                </button>
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
                                                    <th>Responsable</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                try {
                                                    $sql = "SELECT 
                                                        e.id,
                                                        e.fecha_entrada,
                                                        e.cantidad,
                                                        e.observaciones as entrada_observaciones,
                                                        i.codigo_g,
                                                        i.item,
                                                        i.producto,
                                                        i.marca,
                                                        i.modelo,
                                                        i.serial,
                                                        i.grado,
                                                        i.disposicion,
                                                        i.ubicacion,
                                                        i.posicion,
                                                        p.nombre as proveedor_nombre,
                                                        p.nomenclatura as proveedor_nomenclatura,
                                                        u.nombre as usuario_nombre
                                                    FROM bodega_entradas e 
                                                    LEFT JOIN bodega_inventario i ON e.inventario_id = i.id 
                                                    LEFT JOIN proveedores p ON e.proveedor_id = p.id 
                                                    LEFT JOIN usuarios u ON e.usuario_id = u.id
                                                    ORDER BY e.fecha_entrada DESC 
                                                    LIMIT 10";

                                                    $result = $connect->query($sql);

                                                    if ($result) {
                                                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                            echo "<tr>";
                                                            echo "<td>" . htmlspecialchars(date('d/m/Y H:i', strtotime($row['fecha_entrada']))) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['codigo_g'] ?? 'N/A') . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['producto'] ?? 'N/A') . "</td>";
                                                            echo "<td>" . htmlspecialchars(($row['proveedor_nombre'] ?? 'N/A') .
                                                                ($row['proveedor_nomenclatura'] ? ' - ' . $row['proveedor_nomenclatura'] : '')) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['usuario_nombre'] ?? 'N/A') . "</td>";
                                                            echo "<td><span class='badge badge-info'>" . htmlspecialchars($row['disposicion'] ?? 'N/A') . "</span></td>";
                                                            echo "<td>
                                                            <button class='btn btn-info btn-sm view-btn' data-id='" . $row['id'] . "' 
                                                            data-toggle='tooltip' title='Ver detalles'>
                                                            <i class='material-icons'>visibility</i>
                                                            </button>
                                                            </td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='7'>No hay entradas registradas</td></tr>";
                                                    }
                                                } catch (PDOException $e) {
                                                    echo "<tr><td colspan='7'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                                    error_log("Error en consulta entradas: " . $e->getMessage());
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

        <!-- Modal para ver detalles de entrada -->
        <div class="modal fade" id="detallesModal" tabindex="-1" role="dialog" aria-labelledby="detallesModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detallesModalLabel">Detalles de Entrada</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="detallesContent">
                        <!-- El contenido se cargará dinámicamente -->
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
            $(document).ready(function () {
                // Manejar envío del formulario
                $('#entradaForm').submit(function (e) {
                    e.preventDefault();

                    // Validar campos requeridos
                    let isValid = true;
                    $(this).find('[required]').each(function () {
                        if (!$(this).val()) {
                            $(this).addClass('is-invalid');
                            isValid = false;
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });

                    if (!isValid) {
                        alert('Por favor complete todos los campos requeridos');
                        return;
                    }

                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: $(this).serialize(),
                        beforeSend: function () {
                            $('button[type="submit"]').prop('disabled', true).text('Procesando...');
                        },
                        success: function (response) {
                            if (response.success) {
                                alert('Entrada registrada exitosamente');
                                location.reload();
                            } else {
                                alert('Error: ' + (response.error || 'Error desconocido'));
                            }
                        },
                        error: function (xhr, status, error) {
                            alert('Error al registrar la entrada: ' + error);
                            console.error('Error:', xhr.responseText);
                        },
                        complete: function () {
                            $('button[type="submit"]').prop('disabled', false).html('<i class="material-icons">save</i> Registrar Entrada');
                        }
                    });
                });

                // Manejar clic en botón ver detalles
                $(document).on('click', '.view-btn', function () {
                    const entradaId = $(this).data('id');

                    // Mostrar modal
                    $('#detallesModal').modal('show');

                    // Cargar detalles vía AJAX
                    $.ajax({
                        url: '../../backend/php/get_entrada_details.php',
                        type: 'GET',
                        data: { id: entradaId },
                        dataType: 'json',
                        beforeSend: function () {
                            $('#detallesContent').html(`
                                <div class="text-center">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            `);
                        },
                        success: function (response) {
                            if (response.success) {
                                const data = response.data;
                                const html = `
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><strong>Información General</strong></h6>
                                            <table class="table table-sm">
                                                <tr><td><strong>Código:</strong></td><td>${data.codigo_g || 'N/A'}</td></tr>
                                                <tr><td><strong>Item:</strong></td><td>${data.item || 'N/A'}</td></tr>
                                                <tr><td><strong>Fecha Entrada:</strong></td><td>${data.fecha_entrada || 'N/A'}</td></tr>
                                                <tr><td><strong>Ubicación:</strong></td><td>${data.ubicacion || 'N/A'}</td></tr>
                                                <tr><td><strong>Posición:</strong></td><td>${data.posicion || 'N/A'}</td></tr>
                                                <tr><td><strong>Lote:</strong></td><td>${data.codigo_lote || 'N/A'}</td></tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6><strong>Especificaciones</strong></h6>
                                            <table class="table table-sm">
                                                <tr><td><strong>Producto:</strong></td><td>${data.producto || 'N/A'}</td></tr>
                                                <tr><td><strong>Marca:</strong></td><td>${data.marca || 'N/A'}</td></tr>
                                                <tr><td><strong>Modelo:</strong></td><td>${data.modelo || 'N/A'}</td></tr>
                                                <tr><td><strong>Serial:</strong></td><td>${data.serial || 'N/A'}</td></tr>
                                                <tr><td><strong>Procesador:</strong></td><td>${data.procesador || 'N/A'}</td></tr>
                                                <tr><td><strong>RAM:</strong></td><td>${data.ram || 'N/A'}</td></tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><strong>Estado y Clasificación</strong></h6>
                                            <table class="table table-sm">
                                                <tr><td><strong>Grado:</strong></td><td><span class="badge badge-${getGradeBadgeClass(data.grado)}">${data.grado || 'N/A'}</span></td></tr>
                                                <tr><td><strong>Disposición:</strong></td><td>${data.disposicion || 'N/A'}</td></tr>
                                                <tr><td><strong>Estado:</strong></td><td><span class="badge badge-success">${data.estado || 'N/A'}</span></td></tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6><strong>Proveedor y Usuario</strong></h6>
                                            <table class="table table-sm">
                                                <tr><td><strong>Proveedor:</strong></td><td>${data.proveedor_nombre || 'N/A'} ${data.proveedor_nomenclatura ? '- ' + data.proveedor_nomenclatura : ''}</td></tr>
                                                <tr><td><strong>Usuario:</strong></td><td>${data.usuario_nombre || 'N/A'}</td></tr>
                                                <tr><td><strong>Cantidad:</strong></td><td>${data.cantidad || '1'}</td></tr>
                                            </table>
                                        </div>
                                    </div>
                                    ${data.observaciones || data.entrada_observaciones ? `
                                    <div class="row">
                                        <div class="col-12">
                                            <h6><strong>Observaciones</strong></h6>
                                            <div class="alert alert-info">
                                                ${data.observaciones || data.entrada_observaciones}
                                            </div>
                                        </div>
                                    </div>
                                    ` : ''}
                                `;
                                $('#detallesContent').html(html);
                            } else {
                                $('#detallesContent').html(`
                                    <div class="alert alert-danger">
                                        <strong>Error:</strong> ${response.error || 'No se pudieron cargar los detalles'}
                                    </div>
                                `);
                            }
                        },
                        error: function (xhr, status, error) {
                            $('#detallesContent').html(`
                                <div class="alert alert-danger">
                                    <strong>Error:</strong> No se pudieron cargar los detalles de la entrada
                                </div>
                            `);
                            console.error('Error:', error);
                        }
                    });
                });

                // Función auxiliar para obtener clase de badge según el grado
                function getGradeBadgeClass(grado) {
                    switch (grado) {
                        case 'A': return 'success';
                        case 'B': return 'warning';
                        case 'C': return 'danger';
                        default: return 'secondary';
                    }
                }

                // Remover clase de error cuando el usuario empiece a escribir
                $('[required]').on('input change', function () {
                    $(this).removeClass('is-invalid');
                });

                // Inicializar tooltips
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>

        <style>
            .is-invalid {
                border-color: #dc3545 !important;
            }

            .btn i {
                vertical-align: middle;
                margin-right: 5px;
            }
        </style>
    </body>

    </html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>