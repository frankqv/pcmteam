<!-- /bodega/entradas.php -->
<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
header('location: ../error404.php');
exit();
}
require_once '../../config/ctconex.php';
?>
<?php if (isset($_SESSION['id'])) { ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Registro de Entradas - PCMARKETTEAM</title>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/custom.css">
<link rel="stylesheet" href="../assets/css/loader.css">
<link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
<link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
</head>
<body>
<div class="wrapper">
    <div class="body-overlay"></div>
    <?php include_once '../layouts/nav.php';
    include_once '../layouts/menu_data.php'; ?>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><img src="../assets/img/favicon.webp" class="img-fluid" /><span>PCMARKETTEAM</span></h3>
        </div>
        <?php renderMenu($menu); ?>
    </nav>
    <!-- Page Content -->
    <div id="content">
        <!-- begin:: top-navbar -->
        <div class="top-navbar">
            <nav class="navbar navbar-expand-lg" style="background:rgb(250, 107, 107);">
                <div class="container-fluid">
                    <!-- Botón Sidebar -->
                    <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                        <span class="material-icons">arrow_back_ios</span>
                    </button>
                    <!-- Título dinámico -->
                    <?php
                    $titulo = "";
                    switch ($_SESSION['rol']) {
                        case 1:
                            $titulo = "ADMINISTRADOR";
                            break;
                        case 2:
                            $titulo = "DEFAULT";
                            break;
                        case 3:
                            $titulo = "CONTABLE";
                            break;
                        case 4:
                            $titulo = "COMERCIAL";
                            break;
                        case 5:
                            $titulo = "JEFE TÉCNICO";
                            break;
                        case 6:
                            $titulo = "TÉCNICO";
                            break;
                        case 7:
                            $titulo = "BODEGA";
                            break;
                        default:
                            $titulo = $userInfo['nombre'] ?? 'USUARIO';
                            break;
                    }
                    ?>
                    <!-- Branding -->
                    <a class="navbar-brand" href="#" style="color: #fff;">
                        <i class="fas fa-tools" style="margin-right: 8px; color: #f39c12;"></i>
                        <?php echo htmlspecialchars($titulo); ?> | <b>RESGISTRO DE ENTREDA</b>
                    </a>
                    <!-- Menú derecho (usuario) -->
                    <ul class="nav navbar-nav ml-auto">
                        <li class="dropdown nav-item active">
                            <a href="#" class="nav-link" data-toggle="dropdown">
                                <img src="../assets/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.webp'); ?>"
                                    alt="Foto de perfil"
                                    style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                            </a>
                            <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
                                <li><strong><?php echo htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?></strong>
                                </li>
                                <li><?php echo htmlspecialchars($userInfo['usuario'] ?? 'usuario'); ?></li>
                                <li><?php echo htmlspecialchars($userInfo['correo'] ?? 'correo@ejemplo.com'); ?>
                                </li>
                                <li>
                                    <?php echo htmlspecialchars(trim($userInfo['idsede'] ?? '') !== '' ? $userInfo['idsede'] : 'Sede sin definir'); ?>
                                </li>
                                <li class="mt-2">
                                    <a href="../cuenta/perfil.php" class="btn btn-sm btn-primary btn-block">Mi
                                        perfil</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <button class="d-inline-block d-lg-none ml-auto more-button" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="material-icons">more_vert</span>
                </button>
            </nav>
        </div> <!--- end:: top_navbar -->
        <div class="main-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card fade-in">
                        <div class="card-header">
                            <h4><i class="material-icons">add_box</i> Nueva Entrada de Equipo</h4>
                        </div>
                        <div class="card-body">
                            <!-- Botones de acción -->
                            <div class="row mb-3">
                                <div class="col-md-12 text-center">
                                    <button type="button" class="btn btn-success btn-lg" data-toggle="modal"
                                        data-target="#importModal">
                                        <i class="material-icons">upload_file</i> Importar desde Excel
                                    </button>
                                    <a href="../../backend/php/download_excel_template.php"
                                        class="btn btn-info btn-lg ml-2">
                                        <i class="material-icons">download</i> Descargar Plantilla
                                    </a>
                                    <button type="button" class="btn btn-danger btn-lg ml-2" id="testInsert">
                                        <i class="material-icons">bug_report</i> Probar Inserción
                                    </button>
                                </div>
                            </div>
                            <hr>
                            <form id="entradaForm" method="POST" action="../../backend/php/st_add_entrada.php">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="codigo_g">Código general del equipo <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="codigo_g" id="codigo_g"
                                                placeholder="Código general del equipo" required>
                                            <div class="invalid-feedback">El código general es requerido y no puede
                                                contener espacios</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="lote">Nombre del lote</label>
                                            <input type="text" class="form-control" name="lote" id="lote"
                                                placeholder="Nombre del lote Ejem: SistecLPPAD-1432">
                                            <small class="form-text text-muted">Campo opcional para agrupar
                                                equipos</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="ubse">Ubicación en sede <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="ubse" id="ubse" required>
                                                <option value="">----Seleccionar Ubicación en sede----</option>
                                                <option value="Principal">Principal</option>
                                                <option value="Unilago">Unilago</option>
                                                <option value="Cúcuta">Cúcuta</option>
                                                <option value="Medellín">Medellín</option>
                                            </select>
                                            <div class="invalid-feedback">Debe seleccionar una ubicación</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="posicion">Posición exacta dentro de la ubicación <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="posicion" id="posicion"
                                                placeholder="Posición exacta dentro de la ubicación Ejemplo ESTANTE-1-C " required>
                                            <div class="invalid-feedback">La posición es requerida</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="producto">Tipo de producto <span
                                                    class="text-danger">*</span></label>
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
                                            <div class="invalid-feedback">Debe seleccionar un tipo de producto</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="marca">Marca del equipo <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="marca" id="marca" required>
                                                <option value="">Seleccione la marca</option>
                                                <option value="HP">HP</option>
                                                <option value="Dell">Dell</option>
                                                <option value="Lenovo">Lenovo</option>
                                                <option value="Acer">Acer</option>
                                                <option value="CompuMax">CompuMax</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                            <div class="invalid-feedback">Debe seleccionar una marca</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="serial">Serial del fabricante <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="serial" id="serial"
                                                placeholder="Serial del fabricante" required>
                                            <div class="invalid-feedback">El serial es requerido</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="modelo">Referencia o Modelo del equipo <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="modelo" id="modelo"
                                                placeholder="Referencia o Modelo del Equipo" required>
                                            <div class="invalid-feedback">El modelo es requerido</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="procesador">Especificaciones del procesador</label>
                                            <input type="text" class="form-control" name="procesador"
                                                id="procesador" placeholder="Ej: Intel i5 8th Gen">
                                        </div>
                                        <div class="form-group">
                                            <label for="ram">Memoria RAM instalada <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="ram" id="ram" required>
                                                <option value="">Seleccione la memoria RAM</option>
                                                <option value="N/A">N/A</option>
                                                <option value="4GB">4GB</option>
                                                <option value="8GB">8GB</option>
                                                <option value="16GB">16GB</option>
                                                <option value="32GB">32GB</option>
                                                <option value="otro">Otro</option>
                                            </select>
                                            <div class="invalid-feedback">Debe seleccionar la memoria RAM</div>
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
                                            <label for="grado">Clasificación según procedimiento técnico <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="grado" id="grado" required>
                                                <option value="">Seleccione grado</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                                <option value="SCRAP">SCRAP</option>
                                                <option value="#N/D">#N/D</option>
                                            </select>
                                            <div class="invalid-feedback">Debe seleccionar un grado</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="disposicion">Estado actual del equipo en el proceso <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="disposicion" id="disposicion"
                                                required>
                                                <option value="">Seleccione el estado</option>
                                                <option value="En revisión">En revisión</option>
                                                <option value="Por Alistamiento">Por Alistamiento</option>
                                                <option value="En Laboratorio">En Laboratorio</option>
                                                <option value="En Bodega">En Bodega</option>
                                                <option value="Disposicion final">Disposicion final</option>
                                                <option value="Para Venta">Para Venta</option>
                                            </select>
                                            <div class="invalid-feedback">Debe seleccionar un estado</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Sección de Proveedor y Responsable -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="proveedor">Proveedor <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="proveedor" id="proveedor" required>
                                                <option value="">----------Seleccione Proveedor------------</option>
                                                <?php
                                                try {
                                                    $stmt_proveedores = $connect->prepare("SELECT id, nombre, nomenclatura FROM proveedores WHERE nombre IS NOT NULL ORDER BY nombre ASC");
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
                                            <div class="invalid-feedback">Debe seleccionar un proveedor</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tactil">¿El equipo es táctil? <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" required name="tactil" id="tactil">
                                                <option value="">----------Seleccione-----------</option>
                                                <option value="SI">Sí</option>
                                                <option value="NO">No</option>
                                            </select>
                                            <div class="invalid-feedback">Debe seleccionar si es táctil o no</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="estado">Estado</label>
                                            <input type="hidden" name="estado" value="activo">
                                            <input type="text" class="form-control" id="estado_display"
                                                value="Activo" readonly style="background-color: #e9ecef;">
                                            <small class="form-text text-muted">Todas las nuevas entradas se
                                                registran como activas</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="material-icons">save</i> Registrar Entrada
                                        </button>
                                        <button type="reset" class="btn btn-secondary btn-lg ml-2">
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
                    <div class="card slide-in">
                        <div class="card-header">
                            <h4><i class="material-icons">history</i> Últimas Entradas</h4>
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
                                                i.producto,
                                                i.marca,
                                                i.modelo,
                                                i.serial,
                                                i.grado,
                                                i.disposicion,
                                                i.ubicacion,
                                                i.posicion,
                                                i.lote,
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
                                            if ($result && $result->rowCount() > 0) {
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
                                                echo "<tr><td colspan='7' class='text-center'>No hay entradas registradas</td></tr>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "<tr><td colspan='7' class='text-center text-danger'>Error al cargar datos: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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
<!-- Modal para importación masiva -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="material-icons">upload_file</i> Importación Masiva desde Excel
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6><i class="material-icons">info</i> Instrucciones:</h6>
                    <ul class="mb-0">
                        <li>Descarga la plantilla Excel y llénala con tus datos</li>
                        <li>Los campos marcados con * son obligatorios</li>
                        <li>Si un código ya existe, se omitirá ese equipo y continuará con los demás</li>
                        <li>Máximo 10MB por archivo</li>
                        <li>Formatos soportados: .xlsx, .xls</li>
                    </ul>
                </div>
                <form id="importForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="excel_file">Seleccionar archivo Excel:</label>
                        <input type="file" class="form-control-file" id="excel_file" name="excel_file"
                            accept=".xlsx,.xls" required>
                        <small class="form-text text-muted">Selecciona el archivo Excel con los datos de los
                            equipos</small>
                    </div>
                    <div class="form-group">
                        <label for="import_observations">Observaciones generales (opcional):</label>
                        <textarea class="form-control" id="import_observations" name="import_observations" rows="3"
                            placeholder="Observaciones que se aplicarán a todos los equipos importados"></textarea>
                    </div>
                </form>
                <!-- Área de resultados -->
                <div id="importResults" style="display: none;">
                    <hr>
                    <h6><i class="material-icons">assessment</i> Resultados de la Importación:</h6>
                    <div id="resultsContent"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="startImport">
                    <i class="material-icons">upload</i> Iniciar Importación
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Scripts -->
<script src="../assets/js/jquery-3.3.1.min.js"></script>
<script src="../assets/js/popper.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
<script src="../assets/js/loader.js"></script>
<script>
    $(document).ready(function () {
        // Función para mostrar notificaciones mejorada
        function showNotification(message, type = 'success') {
            // Remover notificaciones existentes
            $('.alert.notification').remove();
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'check_circle' : 'error';
            const notification = $(`
                <div class="alert ${alertClass} alert-dismissible fade show notification" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    <i class="material-icons" style="vertical-align: middle; margin-right: 8px;">${icon}</i>
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);
            $('body').append(notification);
            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                notification.fadeOut(() => notification.remove());
            }, 5000);
        }
        // Validación mejorada del formulario
        function validateForm() {
            let isValid = true;
            let firstInvalid = null;
            // Limpiar mensajes de error previos
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
            // Validar campos requeridos
            $('#entradaForm [required]').each(function () {
                const field = $(this);
                const value = field.val().trim();
                if (!value) {
                    field.addClass('is-invalid');
                    field.siblings('.invalid-feedback').show();
                    if (!firstInvalid) {
                        firstInvalid = field;
                    }
                    isValid = false;
                }
            });
            // Validaciones específicas
            const codigo = $('#codigo_g').val().trim();
            if (codigo && codigo.includes(' ')) {
                $('#codigo_g').addClass('is-invalid');
                $('#codigo_g').siblings('.invalid-feedback').text('El código general no puede contener espacios').show();
                if (!firstInvalid) firstInvalid = $('#codigo_g');
                isValid = false;
            }
            if (codigo && codigo.length < 3) {
                $('#codigo_g').addClass('is-invalid');
                $('#codigo_g').siblings('.invalid-feedback').text('El código debe tener al menos 3 caracteres').show();
                if (!firstInvalid) firstInvalid = $('#codigo_g');
                isValid = false;
            }
            // Validar formato de serial (opcional pero recomendado)
            const serial = $('#serial').val().trim();
            if (serial && serial.length < 5) {
                $('#serial').addClass('is-invalid');
                $('#serial').siblings('.invalid-feedback').text('El serial debe tener al menos 5 caracteres').show();
                if (!firstInvalid) firstInvalid = $('#serial');
                isValid = false;
            }
            // Scroll al primer campo inválido
            if (!isValid && firstInvalid) {
                firstInvalid.focus();
                $('html, body').animate({
                    scrollTop: firstInvalid.offset().top - 100
                }, 500);
            }
            return isValid;
        }
        // Manejar envío del formulario con mejor manejo de errores
        $('#entradaForm').submit(function (e) {
            e.preventDefault();
            // Validar formulario
            if (!validateForm()) {
                showNotification('Por favor complete todos los campos requeridos correctamente', 'error');
                return;
            }
            // Deshabilitar botón y mostrar loading
            const submitBtn = $('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...');
            
            // Mostrar información de depuración en consola
            console.log('Enviando formulario a:', $(this).attr('action'));
            console.log('Datos del formulario:', $(this).serialize());
            
            // Enviar formulario vía AJAX
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                timeout: 30000, // 30 segundos de timeout
                beforeSend: function(xhr) {
                    console.log('Iniciando petición AJAX...');
                },
                success: function (response) {
                    console.log('Respuesta exitosa:', response);
                    if (response.success) {
                        showNotification(response.message || 'Entrada registrada exitosamente', 'success')
                        // Limpiar formulario
                        $('#entradaForm')[0].reset();
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').hide();
                        // Recargar tabla de entradas
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        showNotification(response.error || 'Error al registrar la entrada', 'error');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error AJAX completo:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error,
                        url: $(this).attr('action')
                    });
                    
                    // Mostrar respuesta completa en consola para depuración
                    console.log('Respuesta del servidor:', xhr.responseText);
                    
                    let errorMsg = 'Error al registrar la entrada';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMsg = response.error;
                        }
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                        console.log('Respuesta no válida del servidor:', xhr.responseText);
                        
                        if (xhr.status === 500) {
                            errorMsg = 'Error interno del servidor. Por favor contacte al administrador.';
                        } else if (xhr.status === 0) {
                            errorMsg = 'Error de conexión. Verifique su conexión a internet.';
                        } else if (xhr.status === 401) {
                            errorMsg = 'Sesión expirada. Por favor inicie sesión nuevamente.';
                            setTimeout(() => {
                                window.location.href = '../login.php';
                            }, 2000);
                        } else if (xhr.status === 404) {
                            errorMsg = 'Archivo del servidor no encontrado. Contacte al administrador.';
                        } else if (xhr.status === 400) {
                            errorMsg = 'Datos enviados incorrectos. Verifique la información.';
                        } else {
                            errorMsg = `Error ${xhr.status}: ${error}`;
                        }
                    }
                    showNotification(errorMsg, 'error');
                },
                complete: function () {
                    // Restaurar botón
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
        // Validación en tiempo real mejorada
        $('#codigo_g').on('input', function () {
            const value = $(this).val();
            const field = $(this);
            const feedback = field.siblings('.invalid-feedback');
            if (value.includes(' ')) {
                field.addClass('is-invalid');
                feedback.text('No se permiten espacios').show();
            } else if (value.length > 0 && value.length < 3) {
                field.addClass('is-invalid');
                feedback.text('Mínimo 3 caracteres').show();
            } else {
                field.removeClass('is-invalid');
                feedback.hide();
            }
        });
        // Validación para serial
        $('#serial').on('input', function () {
            const value = $(this).val();
            const field = $(this);
            const feedback = field.siblings('.invalid-feedback');
            if (value.length > 0 && value.length < 5) {
                field.addClass('is-invalid');
                feedback.text('El serial debe tener al menos 5 caracteres').show();
            } else {
                field.removeClass('is-invalid');
                feedback.hide();
            }
        });
        // Limpiar validaciones cuando el usuario cambia los valores
        $('input, select, textarea').on('input change', function () {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').hide();
        });
        // Auto-completar campos basado en selecciones
        $('#producto, #marca').on('change', function () {
            if ($('#codigo_g').val() === '') {
                const producto = $('#producto').val();
                const marca = $('#marca').val();
                if (producto && marca) {
                    const prefix = producto.substring(0, 2).toUpperCase() + marca.substring(0, 2).toUpperCase();
                    const timestamp = Date.now().toString().slice(-6);
                    $('#codigo_g').val(prefix + timestamp);
                }
            }
        });
        // Auto-completar lote
        $('#proveedor').on('change', function () {
            if ($('#lote').val() === '') {
                const proveedor = $(this).find('option:selected').text();
                if (proveedor && proveedor !== '----------Seleccione Proveedor------------') {
                    const nomenclatura = proveedor.split(' - ')[1];
                    if (nomenclatura) {
                        const fecha = new Date().toISOString().slice(2, 10).replace(/-/g, '');
                        $('#lote').val(nomenclatura + fecha);
                    }
                }
            }
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
                                        <tr><td><strong>Fecha Entrada:</strong></td><td>${data.fecha_entrada || 'N/A'}</td></tr>
                                        <tr><td><strong>Ubicación:</strong></td><td>${data.ubicacion || 'N/A'}</td></tr>
                                        <tr><td><strong>Posición:</strong></td><td>${data.posicion || 'N/A'}</td></tr>
                                        <tr><td><strong>Lote:</strong></td><td>${data.lote || 'N/A'}</td></tr>
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
                                        <tr><td><strong>Disco:</strong></td><td>${data.disco || 'N/A'}</td></tr>
                                        <tr><td><strong>Pulgadas:</strong></td><td>${data.pulgadas || 'N/A'}</td></tr>
                                        <tr><td><strong>Tactil:</strong></td><td>${data.tactil || 'N/A'}</td></tr>
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
                                        <tr><td><strong>Entrada Realizada por:</strong></td><td>${data.usuario_nombre || 'N/A'}</td></tr>
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
                case 'SCRAP': return 'dark';
                default: return 'secondary';
            }
        }
        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip();
        // Importación masiva desde Excel
        $('#startImport').on('click', function () {
            const fileInput = $('#excel_file')[0];
            const observations = $('#import_observations').val();
            if (!fileInput.files.length) {
                showNotification('❌ Por favor selecciona un archivo Excel', 'error');
                return;
            }
            const file = fileInput.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB
            if (file.size > maxSize) {
                showNotification('❌ El archivo es demasiado grande. Máximo 10MB permitido', 'error');
                return;
            }
            // Crear FormData
            const formData = new FormData();
            formData.append('excel_file', file);
            if (observations) {
                formData.append('import_observations', observations);
            }
            // Deshabilitar botón y mostrar loading
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Importando...');
            // Ocultar resultados previos
            $('#importResults').hide();
            // Realizar importación
            $.ajax({
                url: '../../backend/php/import_excel_equipos.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showNotification('✅ ' + response.message, 'success');
                        displayImportResults(response.results);
                        // Recargar tabla después de 2 segundos
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        showNotification('❌ ' + response.error, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    let errorMsg = 'Error al importar archivo';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMsg = response.error;
                        }
                    } catch (e) {
                        if (xhr.status === 500) {
                            errorMsg = 'Error interno del servidor';
                        } else if (xhr.status === 0) {
                            errorMsg = 'Error de conexión';
                        } else {
                            errorMsg = `Error ${xhr.status}: ${error}`;
                        }
                    }
                    showNotification('❌ ' + errorMsg, 'error');
                    console.error('Error de importación:', xhr.responseText);
                },
                complete: function () {
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
        // Función para mostrar resultados de importación
        function displayImportResults(results) {
            const resultsContent = $('#resultsContent');
            let html = '';
            // Resumen general
            html += `
                <div class="row mb-3 fade-in-up">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4>${results.total_rows}</h4>
                                <small>Total de filas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4>${results.success}</h4>
                                <small>Importados</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h4>${results.skipped}</h4>
                                <small>Omitidos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h4>${results.errors.length}</h4>
                                <small>Errores</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            // Detalles de equipos importados
            if (results.success > 0) {
                html += '<h6 class="text-success slide-in-right">✅ Equipos Importados Exitosamente:</h6>';
                html += '<div class="table-responsive slide-in-right"><table class="table table-sm table-success">';
                html += '<thead><tr><th>Fila</th><th>Código</th><th>Estado</th></tr></thead><tbody>';
                results.details.filter(d => d.status === 'success').forEach(detail => {
                    html += `<tr><td>${detail.row}</td><td>${detail.codigo}</td><td>${detail.message}</td></tr>`;
                });
                html += '</tbody></table></div>';
            }
            // Detalles de equipos omitidos
            if (results.skipped > 0) {
                html += '<h6 class="text-warning slide-in-right">⚠️ Equipos Omitidos:</h6>';
                html += '<div class="table-responsive slide-in-right"><table class="table table-sm table-warning">';
                html += '<thead><tr><th>Fila</th><th>Código</th><th>Razón</th></tr></thead><tbody>';
                results.details.filter(d => d.status === 'skipped').forEach(detail => {
                    html += `<tr><td>${detail.row}</td><td>${detail.codigo}</td><td>${detail.message}</td></tr>`;
                });
                html += '</tbody></table></div>';
            }
            // Detalles de errores
            if (results.errors.length > 0) {
                html += '<h6 class="text-danger slide-in-right">❌ Errores Encontrados:</h6>';
                html += '<div class="table-responsive slide-in-right"><table class="table table-sm table-danger">';
                html += '<thead><tr><th>Fila</th><th>Código</th><th>Error</th></tr></thead><tbody>';
                results.errors.forEach(error => {
                    html += `<tr><td>${error.row}</td><td>${error.codigo}</td><td>${error.error}</td></tr>`;
                });
                html += '</tbody></table></div>';
            }
            resultsContent.html(html);
            $('#importResults').show();
        }
        // Limpiar modal al cerrar
        $('#importModal').on('hidden.bs.modal', function () {
            $('#importForm')[0].reset();
            $('#importResults').hide();
        });
        
        // Botón de prueba de inserción
        $('#testInsert').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            
            btn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Probando...');
            
            console.log('Probando inserción en la base de datos...');
            
            $.ajax({
                url: '../../backend/php/test_insert.php',
                type: 'GET',
                dataType: 'json',
                timeout: 15000,
                success: function(response) {
                    console.log('Prueba de inserción exitosa:', response);
                    if (response.success) {
                        showNotification('✅ Prueba de inserción exitosa: ' + response.message, 'success');
                        console.log('Datos de prueba insertados:', response.test_data);
                    } else {
                        showNotification('❌ Error en prueba de inserción: ' + response.error, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en prueba de inserción:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    
                    let errorMsg = 'Error al probar inserción';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMsg = response.error;
                        }
                    } catch (e) {
                        if (xhr.status === 500) {
                            errorMsg = 'Error interno del servidor';
                        } else if (xhr.status === 404) {
                            errorMsg = 'Archivo de prueba no encontrado';
                        } else {
                            errorMsg = `Error ${xhr.status}: ${error}`;
                        }
                    }
                    
                    showNotification('❌ ' + errorMsg, 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
<style>
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    } .is-valid {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    } .btn i {
        vertical-align: middle;
        margin-right: 5px;
    } .form-group label .text-danger {
        font-size: 0.9em;
    } .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
        margin-bottom: 20px;
    } .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-top: none;
    } .badge {
        font-size: 0.8em;
        padding: 0.4em 0.6em;
    } .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    } .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    } .invalid-feedback {
        display: none;
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
    } .form-text {
        font-size: 0.875em;
        color: #6c757d;
    } .alert {
        border-radius: 0.375rem;
        border: 1px solid transparent;
        margin-bottom: 1rem;
    } .alert-success {
        color: #0f5132;
        background-color: #d1e7dd;
        border-color: #badbcc;
    } .alert-danger {
        color: #842029;
        background-color: #f8d7da;
        border-color: #f5c2c7;
    } .alert i {
        margin-right: 8px;
        vertical-align: middle;
    } .form-group {
        margin-bottom: 1.5rem;
    } .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    } .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1.1rem;
    } .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    } .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    } .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    } .btn-secondary:hover {
        background-color: #545b62;
        border-color: #545b62;
    } .table-responsive {
        border-radius: 0.375rem;
        overflow: hidden;
    } .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.075);
    } .view-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    } .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    } .modal-title {
        color: #495057;
        font-weight: 600;
    } .table-sm td,
    .table-sm th {
        padding: 0.5rem;
        font-size: 0.875rem;
    } .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        font-weight: 600;
    } .card-header h4 {
        margin: 0;
        color: #495057;
    } /* Animaciones */
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    } .slide-in {
        animation: slideIn 0.3s ease-out;
    }
    @keyframes slideIn {
        from {
            transform: translateX(-100%);
        }
        to {
            transform: translateX(0);
        }
    } /* Responsive */
    @media (max-width: 768px) {
        .btn-lg {
            padding: 0.5rem 1rem;
            font-size: 1rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .card-body {
            padding: 1rem;
        }
    } /* Estilos para importación */
    .modal-lg {
        max-width: 800px;
    } .table-sm th,
    .table-sm td {
        padding: 0.5rem;
        font-size: 0.875rem;
    } .table-success {
        background-color: #d4edda;
    } .table-warning {
        background-color: #fff3cd;
    } .table-danger {
        background-color: #f8d7da;
    } .bg-primary {
        background-color: #007bff !important;
    } .bg-success {
        background-color: #28a745 !important;
    } .bg-warning {
        background-color: #ffc107 !important;
    } .bg-danger {
        background-color: #dc3545 !important;
    } .card.bg-primary,
    .card.bg-success,
    .card.bg-warning,
    .card.bg-danger {
        border: none;
    } .card.bg-primary .card-body,
    .card.bg-success .card-body,
    .card.bg-warning .card-body,
    .card.bg-danger .card-body {
        padding: 1rem;
    } .card.bg-primary h4,
    .card.bg-success h4,
    .card.bg-warning h4,
    .card.bg-danger h4 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: bold;
    } .card.bg-primary small,
    .card.bg-success small,
    .card.bg-warning small,
    .card.bg-danger small {
        font-size: 0.875rem;
        opacity: 0.9;
    } .form-control-file {
        border: 2px dashed #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        text-align: center;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    } .form-control-file:hover {
        border-color: #007bff;
        background-color: #e7f3ff;
    } .form-control-file:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    } /* Animaciones para resultados */
    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    } .slide-in-right {
        animation: slideInRight 0.5s ease-out;
    }
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>
</body>
</html>
<?php } else {
header('Location: ../error404.php');
exit();
} ?>
<?php ob_end_flush(); ?>