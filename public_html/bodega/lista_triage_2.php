<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || !in_array((int) $_SESSION['rol'], [1, 2, 5, 6, 7])) {
    header('Location: ../error404.php');
    exit;
}
require_once '../../config/ctconex.php';
date_default_timezone_set('America/Bogota');
// Obtener técnicos para filtros o mostrar nombre
$tecnicos = [];
$resTec = $conn->query("SELECT id, nombre FROM usuarios WHERE rol IN (1,5,6,7) ORDER BY nombre");
while ($r = $resTec->fetch_assoc()) {
    $tecnicos[$r['id']] = $r['nombre'];
}
// --- DETECCIÓN: si existe bodega_triages o bodega_diagnosticos usamos la que haya ---
function table_exists($conn, $table)
{
    $safe = $conn->real_escape_string($table);
    $res = $conn->query("SHOW TABLES LIKE '{$safe}'");
    return ($res && $res->num_rows > 0);
}
$use_triages = table_exists($conn, 'bodega_triages');
$use_diagnos = table_exists($conn, 'bodega_diagnosticos');

if ($use_triages) {
    $sql = "
    SELECT 
        inv.id,
        inv.codigo_g,
        inv.producto,
        inv.marca,
        inv.modelo,
        inv.serial,
        inv.disposicion,
        inv.estado,
        inv.fecha_modificacion,
        u.nombre AS tecnico_inventario,
        t.estado AS triage_estado,
        t.categoria AS triage_categoria,
        t.observaciones AS triage_observaciones,
        t.fecha_registro AS triage_fecha,
        ut.id   AS tecnico_triage_id,
        ut.nombre AS tecnico_triage,
        usr.id  AS usuario_registra_id,
        usr.nombre AS usuario_registra
    FROM bodega_inventario inv
    LEFT JOIN usuarios u ON inv.tecnico_id = u.id
    INNER JOIN (
        SELECT bt1.*
        FROM bodega_triages bt1
        INNER JOIN (
            SELECT inventario_id, MAX(fecha_registro) AS max_fecha
            FROM bodega_triages
            GROUP BY inventario_id
        ) bt2 ON bt1.inventario_id = bt2.inventario_id AND bt1.fecha_registro = bt2.max_fecha
    ) t ON t.inventario_id = inv.id
    LEFT JOIN usuarios ut ON t.tecnico_id = ut.id
    LEFT JOIN usuarios usr ON t.usuario_registro = usr.id
    ORDER BY t.fecha_registro DESC, inv.fecha_modificacion DESC
    ";
    $result = $conn->query($sql);
    if (!$result) {
        die("Error en consulta (bodega_triages): " . $conn->error);
    }
} elseif ($use_diagnos) {
    $sql = "
    SELECT 
        inv.id,
        inv.codigo_g,
        inv.producto,
        inv.marca,
        inv.modelo,
        inv.serial,
        inv.disposicion,
        inv.estado,
        inv.fecha_modificacion,
        d.estado_reparacion AS triage_estado,
        NULL AS triage_categoria,
        d.observaciones AS triage_observaciones,
        d.fecha_diagnostico AS triage_fecha,
        ut.id   AS tecnico_triage_id,
        ut.nombre AS tecnico_triage
    FROM bodega_inventario inv
    INNER JOIN (
        SELECT bd1.*
        FROM bodega_diagnosticos bd1
        INNER JOIN (
            SELECT inventario_id, MAX(fecha_diagnostico) AS max_fecha
            FROM bodega_diagnosticos
            GROUP BY inventario_id
        ) bd2 ON bd1.inventario_id = bd2.inventario_id AND bd1.fecha_diagnostico = bd2.max_fecha
    ) d ON d.inventario_id = inv.id
    LEFT JOIN usuarios ut ON d.tecnico_id = ut.id
    ORDER BY d.fecha_diagnostico DESC, inv.fecha_modificacion DESC
    ";
    $result = $conn->query($sql);
    if (!$result) {
        die("Error en consulta (bodega_diagnosticos): " . $conn->error);
    }
} else {
    // Ninguna tabla encontrada
    $result = false;
    $mensaje_error_tablas = "<div class='alert alert-danger'>No se encontró ninguna tabla de triages (ni <code>bodega_triages</code> ni <code>bodega_diagnosticos</code>). Revisa tu base de datos o importa <code>EstructuraDB.sql</code>.</div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listado de Triages - Equipos con Triage 2 hecho</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/datatable.css" />
    <link rel="stylesheet" href="../assets/css/buttonsdataTables.css" />
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 12px;
            background: #f8f9fa;
        }
        table.dataTable thead th {
            background-color: #004080;
            color: white;
        }
        .small-muted {
            font-size: .9rem;
            color: #6c757d
        }
        /* Estilos para el modal */
        #diagDetailsModal .modal-dialog {
            max-width: 40vw !important;
            width: 40vw !important;
            margin: 1.75rem auto;
        }
        #diagDetailsModal .modal-content {
            max-height: 90vh;
            overflow-y: auto;
            width: 100%;
        }
        #modal-content-body {
            max-height: 75vh;
            overflow-y: auto;
            padding: 20px;
        }
        #modal-content-body p {
            word-wrap: break-word;
            word-break: break-word;
        }
        #modal-content-body .alert {
            word-wrap: break-word;
            word-break: break-word;
            white-space: pre-wrap;
        }
        .info-field {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
        }
        .info-field strong {
            min-width: 140px;
            flex-shrink: 0;
            margin-right: 10px;
        }
        .info-value {
            flex: 1;
            word-wrap: break-word;
            word-break: break-word;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="body-overlay"></div>
        <?php include_once '../layouts/nav.php';
        include_once '../layouts/menu_data.php'; ?>
        
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php if (function_exists('renderMenu')) {
                renderMenu($menu);
            } ?>
        </nav>
        
        <div id="content">
            <!-- begin:: top-navbar -->
            <div class="top-navbar">
                <nav class="navbar navbar-expand-lg" style="background: #f39c12;">
                    <div class="container-fluid">
                        <!-- Botón Sidebar -->
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        
                        <!-- Título dinámico -->
                        <?php
                        $titulo = "";
                        switch ($_SESSION['rol']) {
                            case 1: $titulo = "ADMINISTRADOR"; break;
                            case 2: $titulo = "DEFAULT"; break;
                            case 3: $titulo = "CONTABLE"; break;
                            case 4: $titulo = "COMERCIAL"; break;
                            case 5: $titulo = "JEFE TÉCNICO"; break;
                            case 6: $titulo = "TÉCNICO"; break;
                            case 7: $titulo = "BODEGA"; break;
                            default: $titulo = $userInfo['nombre'] ?? 'USUARIO'; break;
                        }
                        ?>
                        
                        <!-- Branding -->
                        <a class="navbar-brand" href="#" style="color: #fff; font-weight: bold;">
                            <i class="fas fa-tools" style="margin-right: 8px; color: #f39c12;"></i>
                            <b>Historial de INGRESAR TRIAGE 2 </b>
                        </a>
                        
                        <?php
                            require_once __DIR__ . '/../../config/ctconex.php';
                            $userInfo = [];
                            if (isset($_SESSION['id'])) {
                                $userId = $_SESSION['id'];
                                try {
                                    $sql = "SELECT nombre, usuario, correo, foto, idsede FROM usuarios WHERE id = :id";
                                    $stmt = $connect->prepare($sql);
                                    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) { 
                                    $userInfo = []; 
                                } 
                            }
                        ?>
                        
                        <!-- Menú derecho (usuario) -->
                        <ul class="nav navbar-nav ml-auto">
                            <li class="dropdown nav-item active">
                                <a href="#" class="nav-link" data-toggle="dropdown">
                                    <img src="../assets/img/<?php echo htmlspecialchars($userInfo['foto'] ?? 'reere.webp'); ?>"
                                        alt="Foto de perfil"
                                        style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                                </a>
                                <ul class="dropdown-menu p-3 text-center" style="min-width: 220px;">
                                    <li><strong><?php echo htmlspecialchars($userInfo['nombre'] ?? 'Usuario'); ?></strong></li>
                                    <li><?php echo htmlspecialchars($userInfo['usuario'] ?? 'usuario'); ?></li>
                                    <li><?php echo htmlspecialchars($userInfo['correo'] ?? 'correo@ejemplo.com'); ?></li>
                                    <li>
                                        <?php echo htmlspecialchars(trim($userInfo['idsede'] ?? '') !== '' ? $userInfo['idsede'] : 'Sede sin definir'); ?>
                                    </li>
                                    <li class="mt-2">
                                        <a href="../cuenta/perfil.php" class="btn btn-sm btn-primary btn-block">Mi perfil</a>
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
            </div>
            <!-- end:: top_navbar -->
            
            <div class="container-fluid">
                <h3 class="mt-3">Listado — Equipos con <strong>Triage 2</strong> registrado</h3>
                <p class="small-muted">Se muestran equipos que ya tienen al menos un triage (último triage por equipo).</p>
                
                <?php if (isset($mensaje_error_tablas)): ?>
                    <?php echo $mensaje_error_tablas; ?>
                <?php else: ?>
                
                <div class="mb-2">
                    <!-- Filtros rápidos -->
                    <label>Filtrar por técnico del triage:</label>
                    <select id="filterTecnico" class="form-control" style="max-width:300px; display:inline-block;">
                        <option value="">Todos</option>
                        <?php foreach ($tecnicos as $id => $nom): ?>
                            <option value="<?= (int) $id ?>"><?= htmlspecialchars($nom) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="table-responsive">
                    <table id="triageTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Serial</th>
                                <th>Triag. Fecha</th>
                                <th>Triag. Estado</th>
                                <th>Triag. Categoría</th>
                                <th>Técnico Triag.</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr data-tecnico="<?= (int) ($row['tecnico_triage_id'] ?? 0) ?>">
                                        <td><?= htmlspecialchars($row['codigo_g'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['producto'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['marca'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['modelo'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['serial'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['triage_fecha'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['triage_estado'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['triage_categoria'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['tecnico_triage'] ?? '-') ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm view-diag-btn" 
                                                    data-id="<?= (int) $row['id'] ?>" 
                                                    title="Ver Detalles del Diagnóstico">
                                                <span class="material-icons" style="color:#f2f2f2">visibility</span>
                                            </button>
                                            <a href="equipo_historia.php?id=<?= (int) $row['id'] ?>" class="btn btn-secondary btn-sm"
                                                title="Ver Historial Total Triage 2">
                                                <span class="material-icons">summarize</span>
                                            </a>
                                            <a href="editar_inventario.php?id=<?= (int) $row['id'] ?>"
                                                class="btn btn-info btn-sm" style="background-color: #dc3545;"
                                                title="Editar inventario Triage_1">
                                                <span class="material-icons">edit</span>
                                            </a>
                                            <a href="triage2.php?id=<?= (int) $row['id'] ?>" class="btn btn btn-sm"
                                                style="background-color:#f39c12;" title="Editar Triage_2">
                                                <span class="material-icons" style="color:#f2f2f2">edit</span>
                                            </a>
                                            <a href="../laboratorio/ingresar_m.php?id=<?= (int) $row['id'] ?>" class="btn btn btn-sm"
                                                style="background-color: #16a085;" title="Mantenimiento">
                                                <span class="material-icons" style="color:#f2f2f2">build_circle</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para detalles del diagnóstico -->
    <div class="modal fade" id="diagDetailsModal" tabindex="-1" role="dialog" aria-labelledby="diagDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-custom-wide" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="diagDetailsModalLabel">Detalles del Diagnóstico</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modal-content-body">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando detalles...</p>
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
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/datatable.js"></script>
    <script src="../assets/js/datatablebuttons.js"></script>
    <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
    
    <script>
    $(document).ready(function() {
        // Inicializar DataTable
        var table = $('#triageTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            language: { url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' },
            order: [[5, 'desc']],
            columnDefs: [
                { targets: [5], type: 'datetime' }
            ]
        });

        // Custom filter: filtrar por data-tecnico
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'triageTable') return true;
            var selected = $('#filterTecnico').val();
            if (!selected) return true;
            
            var rowNode = table.row(dataIndex).node();
            var rowTecnico = $(rowNode).data('tecnico') || 0;
            return parseInt(selected, 10) === parseInt(rowTecnico, 10);
        });

        // Cuando cambie el filtro, redibujar tabla
        $('#filterTecnico').on('change', function() {
            table.draw();
        });

        // Manejar clic en el botón "Ver Diagnóstico"
        $('#triageTable').on('click', '.view-diag-btn', function() {
            var inventarioId = $(this).data('id');
            
            // Mostrar modal con mensaje de carga
            $('#modal-content-body').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Cargando...</span></div><p class="mt-2">Cargando detalles...</p></div>');
            $('#diagDetailsModal').modal('show');
            
            // Petición AJAX para obtener los detalles
            $.ajax({
                url: '../controllers/get_triage2_fun.php',
                type: 'GET',
                data: { id: inventarioId },
                dataType: 'json',
                timeout: 10000, // 10 segundos timeout
                success: function(response) {
                    console.log('Respuesta recibida:', response); // Para debug
                    
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        // Función para truncar texto largo
                        function formatValue(value, maxLength = 100) {
                            if (!value || value === 'N/A') return 'N/A';
                            if (value.length > maxLength) {
                                return value.substring(0, maxLength) + '...';
                            }
                            return value;
                        }
                        
                        // Función para formatear puertos con saltos de línea
                        function formatPuertos(puertos) {
                            if (!puertos || puertos === 'N/A') return 'N/A';
                            // Reemplazar caracteres separadores comunes con saltos de línea
                            return puertos.replace(/[;,]/g, '<br>').replace(/"/g, '');
                        }
                        
                        var content = `
                            <div class="container-fluid">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h4>${data.marca || 'N/A'} ${data.modelo || 'N/A'}</h4>
                                        <div class="info-field">
                                            <strong>Código:</strong> 
                                            <span class="info-value">${data.codigo_g || 'N/A'}</span>
                                        </div>
                                        <div class="info-field">
                                            <strong>Serial:</strong> 
                                            <span class="info-value">${data.serial || 'N/A'}</span>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h5>Resultados del Diagnóstico</h5>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="info-field">
                                            <strong>Técnico:</strong> 
                                            <span class="info-value">${data.tecnico_nombre || 'N/A'}</span>
                                        </div>
                                        <div class="info-field">
                                            <strong>Fecha:</strong> 
                                            <span class="info-value">${data.fecha_diagnostico || 'N/A'}</span>
                                        </div>
                                        <div class="info-field">
                                            <strong>Cámara:</strong> 
                                            <span class="info-value">${formatValue(data.camara)}</span>
                                        </div>
                                        <div class="info-field">
                                            <strong>Teclado:</strong> 
                                            <span class="info-value">${formatValue(data.teclado)}</span>
                                        </div>
                                        <div class="info-field">
                                            <strong>Parlantes:</strong> 
                                            <span class="info-value">${formatValue(data.parlantes)}</span>
                                        </div>
                                        <div class="info-field">
                                            <strong>Batería:</strong> 
                                            <span class="info-value">${formatValue(data.bateria)}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="info-field">
                                            <strong>Micrófono:</strong> 
                                            <span class="info-value">${formatValue(data.microfono)}</span>
                                        </div>
                                        <div class="info-field">
                                            <strong>Pantalla:</strong> 
                                            <span class="info-value">${formatValue(data.pantalla)}</span>
                                        </div>
                                        <div class="info-field">
                                            <strong>Puertos:</strong> 
                                            <div class="info-value">${formatPuertos(data.puertos)}</div>
                                        </div>
                                        <div class="info-field">
                                            <strong>Disco:</strong> 
                                            <span class="info-value">${formatValue(data.disco)}</span>
                                        </div>
                                        <div class="info-field">
                                            <strong>Estado Reparación:</strong> 
                                            <span class="info-value">${formatValue(data.estado_reparacion)}</span>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h5>Fallas y Observaciones</h5>
                                <div class="info-field">
                                    <strong>Falla Eléctrica:</strong> 
                                    <span class="info-value">
                                        ${data.falla_electrica === 'si' ? 
                                            `<span class="badge badge-danger">SÍ</span><br>${data.detalle_falla_electrica || ''}` : 
                                            '<span class="badge badge-success">No</span>'
                                        }
                                    </span>
                                </div>
                                <div class="info-field">
                                    <strong>Falla Estética:</strong> 
                                    <span class="info-value">
                                        ${data.falla_estetica === 'si' ? 
                                            `<span class="badge badge-warning">SÍ</span><br>${data.detalle_falla_estetica || ''}` : 
                                            '<span class="badge badge-success">No</span>'
                                        }
                                    </span>
                                </div>
                                <div class="mt-3">
                                    <p><strong>Observaciones del Diagnóstico:</strong></p>
                                    <div class="alert alert-info">${data.observaciones || 'Sin observaciones.'}</div>
                                </div>
                                <div class="mt-3">
                                    <p><strong>Observaciones Generales del Inventario:</strong></p>
                                    <div class="alert alert-secondary">${data.inventario_observaciones || 'Sin observaciones.'}</div>
                                </div>
                            </div>
                        `;
                        $('#modal-content-body').html(content);
                    } else {
                        $('#modal-content-body').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Error: ${response.error || 'No se pudieron obtener los detalles'}
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', status, error); // Para debug
                    console.error('Respuesta:', xhr.responseText); // Para debug
                    
                    var errorMsg = 'No se pudo cargar la información.';
                    if (status === 'timeout') {
                        errorMsg = 'La consulta tardó demasiado tiempo. Intenta nuevamente.';
                    } else if (xhr.status === 404) {
                        errorMsg = 'El archivo get_triage_details.php no fue encontrado.';
                    } else if (xhr.status === 500) {
                        errorMsg = 'Error interno del servidor.';
                    }
                    
                    $('#modal-content-body').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> 
                            ${errorMsg}
                            <br><small>Error técnico: ${error}</small>
                        </div>
                    `);
                }
            });
        });
    });
    </script>
</body>
</html>