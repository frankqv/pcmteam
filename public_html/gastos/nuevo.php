<?php
ob_start();
session_start();
require_once '../../config/ctconex.php';

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4])) {
    header('location: ../error404.php');
    exit;
}

$usuario_id = $_SESSION['id'];
?>
<?php if (isset($_SESSION['id'])) { ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo Gasto - PCMARKETTEAM</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/loader.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.webp" />
    <style>
        .gasto-item {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            position: relative;
        }
        .gasto-item:hover {
            border-color: #2B6B5D;
            box-shadow: 0 2px 10px rgba(43, 107, 93, 0.1);
        }
        .btn-remove-gasto {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .gasto-number {
            background: #2B6B5D;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #2B6B5D 0%, #1a4a3f 100%);
            color: white;
        }
        .total-section {
            background: #fff3cd;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            border: 2px solid #ffc107;
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
                        <button type="button" id="sidebarCollapse" class="d-xl-block d-lg-block d-md-none d-none">
                            <span class="material-icons">arrow_back_ios</span>
                        </button>
                        <a class="navbar-brand" href="#">
                            <span class="material-icons" style="vertical-align: middle;">receipt</span>
                            Registrar Gastos
                        </a>
                        <div class="collapse navbar-collapse">
                            <ul class="nav navbar-nav ml-auto">
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
                    <div class="col-lg-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../administrador/escritorio.php">Panel administrativo</a></li>
                                <li class="breadcrumb-item"><a href="mostrar.php">Gastos</a></li>
                                <li class="breadcrumb-item active">Nuevo</li>
                            </ol>
                        </nav>

                        <div class="card">
                            <div class="card-header card-header-custom">
                                <h4 class="mb-0">
                                    <i class="material-icons" style="vertical-align: middle;">add_circle</i>
                                    Registrar Gastos
                                </h4>
                            </div>

                            <div class="card-body">
                                <form id="formGastos" enctype="multipart/form-data" method="POST" autocomplete="off">
                                    <!-- Información General -->
                                    <div class="alert alert-info">
                                        <i class="material-icons" style="vertical-align: middle;">info</i>
                                        <strong>Información:</strong> Puede agregar múltiples gastos a la vez. Los campos con <span class="text-danger">*</span> son obligatorios.
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Cliente (Opcional)</label>
                                                <select class="form-control select2" id="idcliente" name="idcliente">
                                                    <option value="">Sin cliente asociado</option>
                                                    <?php
                                                    $sql_clientes = "SELECT idclie, nomcli, apecli, numid FROM clientes WHERE estad = 'Activo' ORDER BY nomcli ASC";
                                                    $result_clientes = $conn->query($sql_clientes);
                                                    while ($cliente = $result_clientes->fetch_assoc()):
                                                    ?>
                                                    <option value="<?php echo $cliente['idclie']; ?>">
                                                        <?php echo htmlspecialchars($cliente['nomcli'] . ' ' . $cliente['apecli'] . ' - ' . $cliente['numid']); ?>
                                                    </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Método de Pago <span class="text-danger">*</span></label>
                                                <select class="form-control" id="metodo_pago" name="metodo_pago" required>
                                                    <option value="">Seleccione...</option>
                                                    <option value="Efectivo">Efectivo</option>
                                                    <option value="Transferencia">Transferencia</option>
                                                    <option value="Nequi">Nequi</option>
                                                    <option value="Daviplata">Daviplata</option>
                                                    <option value="Tarjeta Débito">Tarjeta Débito</option>
                                                    <option value="Tarjeta Crédito">Tarjeta Crédito</option>
                                                    <option value="Otro">Otro</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Foto del Comprobante (Opcional)</label>
                                                <input type="file" class="form-control" name="foto_comprobante" accept="image/*,.pdf">
                                                <small class="form-text text-muted">Formatos: JPG, PNG, PDF. Máximo 5MB</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Observación General (Opcional)</label>
                                                <textarea class="form-control" name="observacion_general" rows="3" placeholder="Notas adicionales sobre estos gastos..."></textarea>
                                                <small class="form-text text-muted">Comentarios generales que aplican a todos los gastos</small>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- Listado de Gastos -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5><i class="material-icons" style="vertical-align: middle;">list</i> Detalle de Gastos</h5>
                                        <button type="button" class="btn btn-primary" id="btnAgregarGasto">
                                            <i class="material-icons" style="vertical-align: middle; font-size: 18px;">add</i>
                                            Agregar Gasto
                                        </button>
                                    </div>

                                    <div id="gastosContainer">
                                        <!-- Los gastos se agregarán aquí dinámicamente -->
                                    </div>

                                    <!-- Total -->
                                    <div class="total-section">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Cantidad de Gastos:</strong> <span id="totalGastos">0</span></p>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <h4><strong>Total General: $<span id="totalGeneral">0</span></strong></h4>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <button type="submit" name="staddgast" class="btn btn-success btn-lg">
                                            <i class="material-icons" style="vertical-align: middle;">save</i>
                                            Guardar Gastos
                                        </button>
                                        <a class="btn btn-danger btn-lg" href="mostrar.php">
                                            <i class="material-icons" style="vertical-align: middle;">cancel</i>
                                            Cancelar
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/loader.js"></script>

    <script>
    $(document).ready(function() {
        // Inicializar Select2
        $('.select2').select2({
            placeholder: 'Buscar cliente...',
            allowClear: true
        });

        // Sidebar
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
            $('#content').toggleClass('active');
        });

        $('.more-button,.body-overlay').on('click', function() {
            $('#sidebar,.body-overlay').toggleClass('show-nav');
        });

        let gastoCounter = 0;

        // Agregar gasto
        $('#btnAgregarGasto').click(function() {
            gastoCounter++;
            const gastoHtml = `
                <div class="gasto-item" data-gasto-id="${gastoCounter}">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-gasto" onclick="eliminarGasto(${gastoCounter})">
                        <i class="material-icons" style="font-size: 18px;">delete</i>
                    </button>

                    <div class="mb-3">
                        <span class="gasto-number">${gastoCounter}</span>
                        <strong>Gasto #${gastoCounter}</strong>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Descripción del Gasto <span class="text-danger">*</span></label>
                                <textarea class="form-control gasto-descripcion" name="gastos[${gastoCounter}][descripcion]" rows="2" required placeholder="Ej: Pago de arriendo, Compra de insumos, etc."></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Monto <span class="text-danger">*</span></label>
                                <input type="number" class="form-control gasto-monto" name="gastos[${gastoCounter}][monto]" step="0.01" min="0" required placeholder="0.00" onchange="calcularTotal()">
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#gastosContainer').append(gastoHtml);
            calcularTotal();
        });

        // Agregar el primer gasto automáticamente
        $('#btnAgregarGasto').click();
    });

    function eliminarGasto(id) {
        if (confirm('¿Está seguro de eliminar este gasto?')) {
            $(`.gasto-item[data-gasto-id="${id}"]`).remove();
            calcularTotal();
        }
    }

    function calcularTotal() {
        let total = 0;
        let cantidad = 0;

        $('.gasto-monto').each(function() {
            const monto = parseFloat($(this).val()) || 0;
            total += monto;
            if (monto > 0) cantidad++;
        });

        $('#totalGastos').text(cantidad);
        $('#totalGeneral').text(total.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 }));
    }

    // Validar formulario antes de enviar
    $('#formGastos').on('submit', function(e) {
        const gastos = $('.gasto-item').length;

        if (gastos === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Sin gastos',
                text: 'Debe agregar al menos un gasto'
            });
            return false;
        }

        return true;
    });
    </script>

    <?php include_once '../../backend/php/st_add_gastos.php'; ?>
</body>
</html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>
