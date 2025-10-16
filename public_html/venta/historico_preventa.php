<?php
// public_html/venta/historico_preventa.php
ob_start();
session_start();

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7])) {
    header('location: ../error404.php');
    exit;
}

require_once('../../config/ctconex.php');
date_default_timezone_set('America/Bogota');

// Obtener el ID del usuario actual
$usuario_id = $_SESSION['id'];

// Procesar nueva solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'crear_solicitud') {
    try {
        $connect->beginTransaction();

        $cliente = htmlspecialchars(trim($_POST['cliente']));
        $cantidad = htmlspecialchars(trim($_POST['cantidad']));
        $descripcion = htmlspecialchars(trim($_POST['descripcion']));
        $marca = htmlspecialchars(trim($_POST['marca']));
        $modelo = htmlspecialchars(trim($_POST['modelo']));
        $observacion = htmlspecialchars(trim($_POST['observacion']));

        // Obtener datos del usuario
        $stmt_user = $connect->prepare("SELECT nombre, idsede FROM usuarios WHERE id = :id");
        $stmt_user->execute([':id' => $usuario_id]);
        $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

        $stmt = $connect->prepare("INSERT INTO solicitud_alistamiento (
            solicitante, usuario_id, sede, cliente, cantidad, descripcion, marca, modelo, observacion, estado
        ) VALUES (
            :solicitante, :usuario_id, :sede, :cliente, :cantidad, :descripcion, :marca, :modelo, :observacion, 'pendiente'
        )");

        $stmt->execute([
            ':solicitante' => $user['nombre'],
            ':usuario_id' => $usuario_id,
            ':sede' => $user['idsede'] ?? 'Sin definir',
            ':cliente' => $cliente,
            ':cantidad' => $cantidad,
            ':descripcion' => $descripcion,
            ':marca' => $marca,
            ':modelo' => $modelo,
            ':observacion' => $observacion
        ]);

        $connect->commit();
        $mensaje = "Solicitud de alistamiento creada exitosamente";
        $tipo_mensaje = "success";

    } catch (Exception $e) {
        $connect->rollBack();
        $mensaje = "Error: " . $e->getMessage();
        $tipo_mensaje = "error";
    }
}

// Obtener solicitudes del usuario actual
$sql = "SELECT
    sa.*,
    u.nombre as tecnico_nombre
FROM solicitud_alistamiento sa
LEFT JOIN usuarios u ON sa.tecnico_responsable = u.id
WHERE sa.usuario_id = :usuario_id
ORDER BY sa.fecha_solicitud DESC";

$stmt = $connect->prepare($sql);
$stmt->execute([':usuario_id' => $usuario_id]);
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php if (isset($_SESSION['id'])) { ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Histórico de Solicitudes - PCMARKETTEAM</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        .timeline-item {
            position: relative;
            padding-left: 40px;
            padding-bottom: 30px;
            border-left: 2px solid #e9ecef;
        }
        .timeline-item:last-child {
            border-left: none;
        }
        .timeline-icon {
            position: absolute;
            left: -12px;
            top: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }
        .timeline-content {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .badge-pendiente {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 600;
        }
        .badge-en-proceso {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 600;
        }
        .badge-completada {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 600;
        }
        .badge-rechazada {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 600;
        }
        .estado-pendiente {
            background: #f093fb;
        }
        .estado-en-proceso {
            background: #667eea;
        }
        .estado-completada {
            background: #11998e;
        }
        .estado-rechazada {
            background: #eb3349;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="body-overlay"></div>

        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>

        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php renderMenu($menu); ?>
        </nav>

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
                        <a class="navbar-brand" href="#"> Mis Solicitudes de Alistamiento </a>
                        <button class="d-inline-block d-lg-none ml-auto more-button" type="button"
                            data-toggle="collapse" data-target="#navbarSupportedContent">
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
                                        <li><a href="../cuenta/perfil.php">Mi perfil</a></li>
                                        <li><a href="../cuenta/salir.php">Salir</a></li>
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
                                <li class="breadcrumb-item"><a href="../administrador/escritorio.php">Panel</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Mis Solicitudes</li>
                            </ol>
                        </nav>

                        <?php if (isset($mensaje)): ?>
                        <div class="alert alert-<?php echo $tipo_mensaje === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                            <strong><?php echo $tipo_mensaje === 'success' ? '¡Éxito!' : '¡Error!'; ?></strong> <?php echo htmlspecialchars($mensaje); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php endif; ?>

                        <div class="card">
                            <div class="card-header card-header-text d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">Historial de Solicitudes</h4>
                                    <p class="category">Solicitudes de alistamiento realizadas</p>
                                </div>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#nuevaSolicitudModal">
                                    <span class="material-icons">add</span> Nueva Solicitud
                                </button>
                            </div>
                            <div class="card-content">
                                <?php if (count($solicitudes) > 0): ?>
                                <div class="timeline">
                                    <?php foreach ($solicitudes as $solicitud): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-icon estado-<?php echo strtolower(str_replace(' ', '-', $solicitud['estado'])); ?>">
                                            <span class="material-icons" style="font-size: 14px;">
                                                <?php
                                                switch ($solicitud['estado']) {
                                                    case 'pendiente': echo 'schedule'; break;
                                                    case 'en proceso': echo 'autorenew'; break;
                                                    case 'completada': echo 'check_circle'; break;
                                                    case 'rechazada': echo 'cancel'; break;
                                                    default: echo 'help_outline';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h5 class="mb-1">Solicitud #<?php echo $solicitud['id']; ?></h5>
                                                    <small class="text-muted">
                                                        <span class="material-icons" style="font-size: 14px; vertical-align: middle;">access_time</span>
                                                        <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])); ?>
                                                    </small>
                                                </div>
                                                <span class="badge-<?php echo strtolower(str_replace(' ', '-', $solicitud['estado'])); ?>">
                                                    <?php echo ucfirst($solicitud['estado']); ?>
                                                </span>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>Cliente:</strong> <?php echo htmlspecialchars($solicitud['cliente']); ?></p>
                                                    <p class="mb-1"><strong>Cantidad:</strong> <?php echo htmlspecialchars($solicitud['cantidad']); ?></p>
                                                    <p class="mb-1"><strong>Sede:</strong> <?php echo htmlspecialchars($solicitud['sede']); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <?php if ($solicitud['marca']): ?>
                                                    <p class="mb-1"><strong>Marca:</strong> <?php echo htmlspecialchars($solicitud['marca']); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($solicitud['modelo']): ?>
                                                    <p class="mb-1"><strong>Modelo:</strong> <?php echo htmlspecialchars($solicitud['modelo']); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($solicitud['tecnico_nombre']): ?>
                                                    <p class="mb-1"><strong>Técnico:</strong> <?php echo htmlspecialchars($solicitud['tecnico_nombre']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <p class="mb-1 mt-2"><strong>Descripción:</strong></p>
                                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($solicitud['descripcion'])); ?></p>

                                            <?php if ($solicitud['observacion']): ?>
                                            <p class="mb-1"><strong>Observaciones:</strong></p>
                                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($solicitud['observacion'])); ?></p>
                                            <?php endif; ?>

                                            <button class="btn btn-sm btn-info mt-2" onclick="verDetalleSolicitud(<?php echo $solicitud['id']; ?>)">
                                                <span class="material-icons" style="font-size: 16px;">visibility</span>
                                                Ver Detalles
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info text-center">
                                    <h5><span class="material-icons" style="vertical-align: middle;">info</span> No has realizado solicitudes</h5>
                                    <p>Haz clic en "Nueva Solicitud" para crear tu primera solicitud de alistamiento</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nueva Solicitud -->
    <div class="modal fade" id="nuevaSolicitudModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Solicitud de Alistamiento</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="crear_solicitud">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cliente <span class="text-danger">*</span></label>
                                    <input type="text" name="cliente" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cantidad <span class="text-danger">*</span></label>
                                    <input type="text" name="cantidad" class="form-control" required placeholder="Ej: 5 equipos">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Marca</label>
                                    <input type="text" name="marca" class="form-control" placeholder="Ej: Dell, HP, Lenovo">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Modelo</label>
                                    <input type="text" name="modelo" class="form-control" placeholder="Ej: Latitude 5420">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descripción de Requerimientos <span class="text-danger">*</span></label>
                            <textarea name="descripcion" class="form-control" rows="4" required
                                      placeholder="Describe las especificaciones técnicas requeridas (procesador, RAM, disco, pantalla, etc.)"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea name="observacion" class="form-control" rows="3"
                                      placeholder="Información adicional o comentarios"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <span class="material-icons" style="font-size: 16px; vertical-align: middle;">send</span>
                            Enviar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/sweetalert.js"></script>
    <script src="../assets/js/loader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        function verDetalleSolicitud(id) {
            Swal.fire({
                title: 'Detalles de Solicitud #' + id,
                html: 'Cargando información detallada...',
                icon: 'info',
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#667eea'
            });
        }
    </script>
</body>
</html>
<?php } else {
    header('Location: ../error404.php');
    exit;
} ?>
<?php ob_end_flush(); ?>
