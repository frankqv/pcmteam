<?php
// Ayudame modificar esta archivo si hace falta
ob_start();
session_start();

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 6, 7])) {
    header('location: ../error404.php');
}
require_once '../../config/ctconex.php';
// Obtener el ID del equipo a editar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('location: inventario.php');
    exit;
}
// Obtener datos del equipo
try {
    $stmt = $connect->prepare("SELECT * FROM bodega_inventario WHERE id = ?");
    $stmt->execute([$id]);
    $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$equipo) {
        header('location: inventario.php');
        exit;
    }
} catch (PDOException $e) {
    header('location: inventario.php');
    exit;
}
?>
<?php if (isset($_SESSION['id'])) { ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Editar Equipo - PCMARKETTEAM</title>
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
        <?php include_once '../layouts/nav.php'; include_once '../layouts/menu_data.php'; ?>
        
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../assets/img/favicon.webp" class="img-fluid"/><span>PCMARKETTEAM</span></h3>
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
                        <a class="navbar-brand" href="#"> Editar Equipo - <?php echo htmlspecialchars($equipo['codigo_g']); ?></a>
                        <a class="navbar-brand" href="#"> Editar Equipo - <?php echo htmlspecialchars($equipo['codigo_g']); ?></a>
                    </div>
                </nav>
            </div>
            <div class="main-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Editar Equipo</h4>
                                <a href="../laboratorio/mostrar.php" class="btn btn-secondary btn-sm">
                                    <i class="material-icons">arrow_back</i> Volver al Inventario
                                </a>
                            </div>
                            <div class="card-body">
                                <form id="editarForm" method="POST" action="../../backend/php/update_inventario.php">
                                    <input type="hidden" name="id" value="<?php echo $equipo['id']; ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="codigo_g">Código general del equipo</label>
                                                <input type="text" class="form-control" name="codigo_g" id="codigo_g"
                                                    value="<?php echo htmlspecialchars($equipo['codigo_g']); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Ubicación en sede</label>
                                                <select class="form-control" name="ubicacion" required>
                                                    <option value="">----Seleccionar Ubicación en sede----</option>
                                                    <option value="Principal" <?php echo ($equipo['ubicacion'] == 'Principal') ? 'selected' : ''; ?>>Principal</option>
                                                    <option value="Unilago" <?php echo ($equipo['ubicacion'] == 'Unilago') ? 'selected' : ''; ?>>Unilago</option>
                                                    <option value="Cúcuta" <?php echo ($equipo['ubicacion'] == 'Cúcuta') ? 'selected' : ''; ?>>Cúcuta</option>
                                                    <option value="Medellín" <?php echo ($equipo['ubicacion'] == 'Medellín') ? 'selected' : ''; ?>>Medellín</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="posicion">Posición exacta dentro de la ubicación</label>
                                                <input type="text" class="form-control" name="posicion" id="posicion"
                                                    value="<?php echo htmlspecialchars($equipo['posicion']); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="producto">Tipo de producto</label>
                                                <select class="form-control" name="producto" id="producto" required>
                                                    <option value="">Seleccione el tipo de producto</option>
                                                    <option value="Portatil" <?php echo ($equipo['producto'] == 'Portatil') ? 'selected' : ''; ?>>Portatil</option>
                                                    <option value="Desktop" <?php echo ($equipo['producto'] == 'Desktop') ? 'selected' : ''; ?>>Desktop</option>
                                                    <option value="Monitor" <?php echo ($equipo['producto'] == 'Monitor') ? 'selected' : ''; ?>>Monitor</option>
                                                    <option value="AIO" <?php echo ($equipo['producto'] == 'AIO') ? 'selected' : ''; ?>>AIO</option>
                                                    <option value="Tablet" <?php echo ($equipo['producto'] == 'Tablet') ? 'selected' : ''; ?>>Tablet</option>
                                                    <option value="Celular" <?php echo ($equipo['producto'] == 'Celular') ? 'selected' : ''; ?>>Celular</option>
                                                    <option value="Impresora" <?php echo ($equipo['producto'] == 'Impresora') ? 'selected' : ''; ?>>Impresora</option>
                                                    <option value="Periferico" <?php echo ($equipo['producto'] == 'Periferico') ? 'selected' : ''; ?>>Periferico Computador</option>
                                                    <option value="otro" <?php echo ($equipo['producto'] == 'otro') ? 'selected' : ''; ?>>Otro</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="marca">Marca del equipo</label>
                                                <select class="form-control" name="marca" id="marca" required>
                                                    <option value="">Seleccione la marca</option>
                                                    <option value="HP" <?php echo ($equipo['marca'] == 'HP') ? 'selected' : ''; ?>>HP</option>
                                                    <option value="Dell" <?php echo ($equipo['marca'] == 'Dell') ? 'selected' : ''; ?>>Dell</option>
                                                    <option value="Lenovo" <?php echo ($equipo['marca'] == 'Lenovo') ? 'selected' : ''; ?>>Lenovo</option>
                                                    <option value="Acer" <?php echo ($equipo['marca'] == 'Acer') ? 'selected' : ''; ?>>Acer</option>
                                                    <option value="CompuMax" <?php echo ($equipo['marca'] == 'CompuMax') ? 'selected' : ''; ?>>CompuMax</option>
                                                    <option value="Otro" <?php echo ($equipo['marca'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="serial">Serial del fabricante</label>
                                                <input type="text" class="form-control" name="serial" id="serial"
                                                    value="<?php echo htmlspecialchars($equipo['serial']); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="modelo">Modelo específico del equipo</label>
                                                <input type="text" class="form-control" name="modelo" id="modelo"
                                                    value="<?php echo htmlspecialchars($equipo['modelo']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="procesador">Especificaciones del procesador</label>
                                                <input type="text" class="form-control" name="procesador" id="procesador"
                                                    value="<?php echo htmlspecialchars($equipo['procesador'] ?? ''); ?>" placeholder="Ej: Intel i5 8th Gen">
                                            </div>
                                            <div class="form-group">
                                                <label for="ram">Memoria RAM instalada</label>
                                                <select class="form-control" name="ram" id="ram" required>
                                                    <option value="">Seleccione la memoria RAM</option>
                                                    <option value="4GB" <?php echo ($equipo['ram'] == '4GB') ? 'selected' : ''; ?>>4GB</option>
                                                    <option value="8GB" <?php echo ($equipo['ram'] == '8GB') ? 'selected' : ''; ?>>8GB</option>
                                                    <option value="16GB" <?php echo ($equipo['ram'] == '16GB') ? 'selected' : ''; ?>>16GB</option>
                                                    <option value="32GB" <?php echo ($equipo['ram'] == '32GB') ? 'selected' : ''; ?>>32GB</option>
                                                    <option value="otro" <?php echo ($equipo['ram'] == 'otro') ? 'selected' : ''; ?>>Otro</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="disco">Tipo y capacidad del disco</label>
                                                <input type="text" class="form-control" name="disco" id="disco"
                                                    value="<?php echo htmlspecialchars($equipo['disco'] ?? ''); ?>" placeholder="Ej: SSD 256GB">
                                            </div>
                                            <div class="form-group">
                                                <label for="pulgadas">Tamaño de pantalla</label>
                                                <input type="text" class="form-control" name="pulgadas" id="pulgadas"
                                                    value="<?php echo htmlspecialchars($equipo['pulgadas'] ?? ''); ?>" placeholder="Ej: 15.6">
                                            </div>
                                            <div class="form-group">
                                                <label for="observaciones">Notas técnicas y observaciones</label>
                                                <textarea class="form-control" name="observaciones" id="observaciones" rows="3"
                                                    placeholder="Notas técnicas y observaciones"><?php echo htmlspecialchars($equipo['observaciones'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="grado">Clasificación según procedimiento técnico</label>
                                                <select class="form-control" name="grado" id="grado" required>
                                                    <option value="">Seleccione grado</option>
                                                    <option value="A" <?php echo ($equipo['grado'] == 'A') ? 'selected' : ''; ?>>A</option>
                                                    <option value="B" <?php echo ($equipo['grado'] == 'B') ? 'selected' : ''; ?>>B</option>
                                                    <option value="C" <?php echo ($equipo['grado'] == 'C') ? 'selected' : ''; ?>>C</option>
                                                    <option value="SCRAP" <?php echo ($equipo['grado'] == 'SCRAP') ? 'selected' : ''; ?>>SCRAP</option>
                                                    <option value="#N/D" <?php echo ($equipo['grado'] == '#N/D') ? 'selected' : ''; ?>>#N/D</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="disposicion">Estado actual del equipo en el proceso</label>
                                                <select class="form-control" name="disposicion" id="disposicion" required>
                                                    <option value="En revisión" <?php echo ($equipo['disposicion'] == 'En revisión') ? 'selected' : ''; ?>>En revisión</option>
                                                    <option value="En Alistamiento" <?php echo ($equipo['disposicion'] == 'En Alistamiento') ? 'selected' : ''; ?>>En Alistamiento</option>
                                                    <option value="En Laboratorio" <?php echo ($equipo['disposicion'] == 'En Laboratorio') ? 'selected' : ''; ?>>En Laboratorio</option>
                                                    <option value="En Bodega" <?php echo ($equipo['disposicion'] == 'En Bodega') ? 'selected' : ''; ?>>En Bodega</option>
                                                    <option value="Para Venta" <?php echo ($equipo['disposicion'] == 'Para Venta') ? 'selected' : ''; ?>>Para Venta</option>
                                                    <option value="Business Room" <?php echo ($equipo['disposicion'] == 'Business Room') ? 'selected' : ''; ?>>Business Room</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="estado">Estado</label>
                                                <select class="form-control" name="estado" id="estado" required>
                                                    <option value="activo" <?php echo ($equipo['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                                                    <option value="inactivo" <?php echo ($equipo['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                                    <option value="Business" <?php echo ($equipo['estado'] == 'Business') ? 'selected' : ''; ?>>Business</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12 text-center">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="material-icons">save</i> Actualizar Equipo
                                            </button>
                                            <a href="../laboratorio/mostar.php" class="btn btn-secondary">
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
    <!-- Scripts -->
    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../assets/js/sidebarCollapse.js"></script>
    <script src="../assets/js/loader.js"></script>
    <script>
        $(document).ready(function () {
            // Manejar envío del formulario
            $('#editarForm').submit(function (e) {
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
                            alert('Equipo actualizado exitosamente');
                            window.location.href = 'mostrar.php';
                        } else {
                            alert('Error: ' + (response.error || 'Error desconocido'));
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('Error al actualizar el equipo: ' + error);
                        console.error('Error:', xhr.responseText);
                    },
                    complete: function () {
                        $('button[type="submit"]').prop('disabled', false).html('<i class="material-icons">save</i> Actualizar Equipo');
                    }
                });
            });
            // Remover clase de error cuando el usuario empiece a escribir
            $('[required]').on('input change', function () {
                $(this).removeClass('is-invalid');
            });
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
