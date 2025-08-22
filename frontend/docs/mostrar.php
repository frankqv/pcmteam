<!-- Documentos(Genericos) -->
<!--
remisión (despachos)Caja menor (Prestamocaja Asunto)Agradecimiento de la compraRUT para imprimirSeñalizacion de delicado- Guia de Envio (Generica, clientes habituales)
-->
<!-- Buscar el docuemnto de agradecimineto por su compra  -->
<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || in_array($_SESSION['rol'], [0])) {
    header('location: ../error404.php');
}
require_once '../../config/ctconex.php';
?>
<?php if (isset($_SESSION['id'])) { ?>
    <!doctype html>
    <html lang="es">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <title>PCMARKETTEAM</title>
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
        <link rel="icon" type="image/png" href="../../backend/img/favicon.webp" />
    </head>

    <body>
        <div class="wrapper">
            <div class="body-overlay"></div>
            <!-- layouts nav.php  |  Sidebar -->
            <?php include_once '../layouts/nav.php';
            include_once '../layouts/menu_data.php'; ?>
            <nav id="sidebar">
                <div class="sidebar-header">
                    <h3><img src="../../backend/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
                </div>
                <?php renderMenu($menu); ?>
            </nav>
            <!-- Page Content  -->
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
                            <a class="navbar-brand" href="#"> Documentos Generales </a>
                            <button class="d-inline-block d-lg-none ml-auto more-button" type="button"
                                data-toggle="collapse" data-target="#navbarSupportedContent"
                                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
                                            <img src="../../backend/img/reere.webp">
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
                </div> <!-- Nav Superior -->
                <div class="main-content">
                    <div class="row">
                        <!--Boton 1 -->
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-success">
                                        <span class="material-icons">receipt_long</span>
                                    </div>
                                </div>

                                <button class="btn btn-print" style="
                                                flex: 1;
                                                background: linear-gradient(45deg,rgb(0, 141, 151),rgb(1, 115, 128)); /* Azul marino con azul más claro */
                                                color: #ffffff;
                                                border: 1px solid #001c3d;
                                                border-radius: 8px;
                                                padding: 10px 15px;
                                                font-weight: bold;
                                                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
                                                transition: background 0.3s ease;"
                                    onmouseover="this.style.background='linear-gradient(45deg,rgb(72, 120, 164),rgb(42, 115, 199))'"
                                    onmouseout="this.style.background='linear-gradient(45deg,rgb(71, 132, 202),rgb(35, 125, 209))'"
                                    onclick="printPDF('gracias')">
                                    <i class="fa-solid fa-print"></i> Gracias por tu compra
                                </button>



                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">receipt_long</i> Porfavor, rellener los campos solicitados
                                        en formulario en la seccion de vista previa
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Boton 2 -->
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-rose">
                                        <span class="material-icons">
                                            description
                                        </span>
                                    </div>
                                </div>
                                <button class="btn btn-print" style="background: #2B6B5D; color:white;">
                                    <span class="material-symbols-outlined">Abrir</span> formulario
                                </button>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">request_page</i> Prestamos Caja menor
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Boton 3 -->
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-warning">
                                        <span class="material-icons">contact_page</span>
                                    </div>
                                </div>
                                <button class="btn btn-print" style="
                                        flex: 1;
                                        background: linear-gradient(45deg, #d97706, #fbbf24); /* Naranja quemado → dorado */
                                        color: #ffffff;
                                        border: 1px solid #b45309;
                                        border-radius: 8px;
                                        padding: 10px 15px;
                                        font-weight: bold;
                                        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
                                        transition: all 0.3s ease;"
                                    onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #d97706)'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 0 12px rgba(249, 168, 37, 0.6)'"
                                    onmouseout="this.style.background='linear-gradient(45deg, #d97706, #fbbf24)'; this.style.transform='scale(1)'; this.style.boxShadow='0 3px 6px rgba(0, 0, 0, 0.15)'"
                                    onclick=" printPDF('garantia')">
                                    <i class="fa-solid fa-print"></i> <b>Politica de Garantia</b>
                                </button>
                                <iframe id="pdfFrameGarantia" src="../docs/POLITICA_GRANTIA.pdf"
                                    style="display: none;"></iframe>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Politica de Garantia PCMARKETT
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Boton 4  RUT -->
                        <div class="col-lg-4 col-md-12 col-sm-12">
                            <div class="card card-stats">
                                <div class="card">
                                    <div class="card-body">
                                        <div style="display: flex; gap: 10px;">
                                            <div class="icon icon-success">
                                                <span class="material-icons">description</span>
                                            </div>
                                            <!-- Botón para imprimir el RUT AMERICAN -->
                                            <button class="btn btn-print" style="
                                                flex: 1;
                                                display: flex;
                                                background: linear-gradient(45deg, #002197, #00509d); /* Azul marino con azul más claro */
                                                color: #ffffff;
                                                border: 1px solid #001c3d;
                                                border-radius: 8px;
                                                padding: 10px 15px;
                                                font-weight: bold;
                                                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
                                                transition: background 0.3s ease;"
                                                onmouseover="this.style.background='linear-gradient(45deg, #00509d, #002147)'"
                                                onmouseout="this.style.background='linear-gradient(45deg, #002147, #00509d)'"
                                                onclick="printPDF('american')">
                                                <i class="fa-solid fa-print"></i> RUT AMERICAN
                                            </button>
                                            <button class="btn btn-print" style="
                                                 flex: 1;
                                                 display:flex;
                                                 background: linear-gradient(45deg, #1e5631, #2b9348); /* Verde profesional */
                                                 color: #ffffff;
                                                 border: 1px solid #164422;
                                                 border-radius: 8px;
                                                 padding: 10px 15px;
                                                 font-weight: bold;
                                                 box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
                                                 transition: background 0.3s ease, transform 0.2s ease; "
                                                onmouseover="this.style.background='linear-gradient(45deg, #2b9348, #1e5631)'; this.style.transform='scale(1.02)'"
                                                onmouseout="this.style.background='linear-gradient(45deg, #1e5631, #2b9348)'; this.style.transform='scale(1)'"
                                                onclick="printPDF('pcmarkett')">
                                                <i class="fa-solid fa-print"></i> RUT PCMARKETT
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Iframes ocultos con IDs únicos -->
                                <iframe id="pdfFrameAmerican" src="../docs/RUT AMERICAN SYSTEM.pdf"
                                    style="display: none;"></iframe>
                                <iframe id="pdfFramePcmarkett" src="../docs/RUT PCMARKETT.pdf"
                                    style="display: none;"></iframe>
                                <iframe id="pdfFrameGracias" src="../docs/GRACIAS POR TU COMPRA.pdf"
                                    style="display: none;"></iframe>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> Imprimir RUT
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- JavaScript para manejar la impresión - Colocar antes del cierre de </body> -->
                        <script>
                            function printPDF(tipo) {
                                let frameId, pdfSrc;
                                if (tipo === 'american') {
                                    frameId = 'pdfFrameAmerican';
                                    pdfSrc = '../docs/RUT AMERICAN SYSTEM.pdf';
                                } else if (tipo === 'pcmarkett') {
                                    frameId = 'pdfFramePcmarkett';
                                    pdfSrc = '../docs/RUT PCMARKETT.pdf';
                                } else if (tipo === 'garantia') {
                                    frameId = 'pdfFrameGarantia';
                                    pdfSrc = '../docs/POLITICA_GRANTIA.pdf';
                                } else if (tipo === 'gracias') {
                                    frameId = 'pdfFrameGracias';
                                    pdfSrc = '../docs/GRACIAS POR TU COMPRA.pdf';
                                }
                                const frame = document.getElementById(frameId);
                                // Función para imprimir cuando el PDF esté cargado
                                frame.onload = function () {
                                    try {
                                        frame.contentWindow.focus();
                                        frame.contentWindow.print();
                                    } catch (error) {
                                        // Fallback: abrir en nueva ventana si hay problemas de CORS
                                        console.log('Error de CORS, abriendo en nueva ventana');
                                        window.open(pdfSrc, '_blank');
                                    }
                                };
                                // Forzar recarga del iframe
                                frame.src = pdfSrc;
                            }
                        </script>
                        <!--Boton 5 -->
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-info">
                                        <span class="material-icons">description</span>
                                    </div>
                                </div>
                                <button onclick="window.print()" class="btn btn-print"
                                    style="background: #2B6B5D; color:white;">
                                    <span class="material-symbols-outlined"></span> Imprirmir
                                </button>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">update</i> imprimir Señalizacion de delicado
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Boton 6 -->
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="card card-stats">
                                <div class="card-header">
                                    <div class="icon icon-info">
                                        <span class="material-icons">edit_square</span>
                                    </div>
                                </div>
                                <button onclick="window.print()" class="btn btn-print"
                                    style="background: #2B6B5D; color:white;">
                                    <span class="material-symbols-outlined">Abrir</span> formulario
                                </button>
                                <div class="card-footer">
                                    <div class="stats">
                                        <i class="material-icons">description</i> Guía de Envio
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- botones superiores de nav, imprecion de documentos -->
                    <!-- Seccion de botones de ACCION #2 -->
                    <div class="row ">
                        <div class="col-lg-12 col-md-12">
                            <div class="card" style="min-height: 485px">
                                <div class="card-header card-header-text">
                                    <h4 class="card-title">Vista previa de documento</h4>
                                    <p class="category">Nuevos clientes reciente añadidos el dia de hoy</p>
                                </div>
                                <div class="card-content table-responsive">
                                    <!-- Contenido salga según el boton que   Seleciono el para generar el docuemnto -->
                                </div>
                            </div>
                        </div>
                        <!-- Seccion de botones de ACCION #3 -->
                        <div class="col-lg-12 col-md-12">
                            <div class="card" style="min-height: 485px">
                                <div class="card-header card-header-text">
                                    <h4 class="card-title">Botones de accion</h4>
                                    <p class="category">Descargar o Imprirmir Documentos</p>
                                </div>
                                <div class="card-content table-responsive">
                                    <?php
                                    $docs
                                        ?>
                                    <button value="docs">Descargar en PDF</button>
                                    <button>Imprirmir AHORA</button>
                                    <!-- Contenido salga según el boton que   Seleciono el para generar el docuemnto -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                    function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                            ['Articulo', 'Stock'],
                            <?php
                            $stmt = $connect->prepare("SELECT producto.idprod, producto.codba, producto.nomprd, categoria.idcate, categoria.nomca, producto.precio, producto.stock, producto.foto, producto.venci, producto.esta, producto.fere, producto.serial, producto.marca, producto.ram, producto.disco, producto.prcpro, producto.pntpro, producto.tarpro, producto.grado FROM producto INNER JOIN categoria ON producto.idcate = categoria.idcate");
                            $stmt->setFetchMode(PDO::FETCH_ASSOC);
                            $stmt->execute();
                            while ($row = $stmt->fetch()) {
                                echo "['" . $row['nomprd'] . "', " . $row['stock'] . "],";
                            }
                            ?>
                        ]);
                        var options = {
                            //is3D:true,  
                            pieHole: 0.4
                        };
                        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                        chart.draw(data, options);
                    }
                </script>
                <script>
                    google.charts.load('current', {
                        'packages': ['corechart']
                    });
                    google.charts.setOnLoadCallback(drawChart);
                    function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                            ['Articulo', 'Stock'],
                            <?php
                            $stmt = $connect->prepare("SELECT * FROM clientes");
                            $stmt->setFetchMode(PDO::FETCH_ASSOC);
                            $stmt->execute();
                            while ($row = $stmt->fetch()) {
                                echo "['" . $row['apecli'] . "', " . $row['idclie'] . "],";
                            }
                            ?>
                        ]);
                        var options = {
                            //is3D:true,  
                            pieHole: 0.4
                        };
                        var chart = new google.visualization.PieChart(document.getElementById('piechartcli'));
                        chart.draw(data, options);
                    }
                </script>
                <script type="text/javascript">
                    google.charts.load('current', {
                        'packages': ['bar']
                    });
                    google.charts.setOnLoadCallback(drawStuff);
                    function drawStuff() {
                        var data = new google.visualization.arrayToDataTable([
                            ['Fecha', 'Monto'],
                            <?php
                            $id = $_SESSION['id'];
                            $stmt = $connect->prepare("SELECT SUM(total_price) total_price,placed_on FROM orders where placed_on = CURDATE()");
                            $stmt->setFetchMode(PDO::FETCH_ASSOC);
                            $stmt->execute();
                            while ($row = $stmt->fetch()) {
                                echo "['" . $row['placed_on'] . "', " . $row['total_price'] . "],";
                            }
                            ?>
                        ]);
                        var options = {
                            width: 900,
                            legend: {
                                position: 'none'
                            },
                            chart: {
                                title: '',
                                subtitle: ''
                            },
                            bars: 'horizontal', // Required for Material Bar Charts.
                            axes: {
                                x: {
                                    0: {
                                        side: 'top',
                                        label: 'Monto'
                                    } // Top x-axis.
                                }
                            },
                            bar: {
                                groupWidth: "90%"
                            }
                        };
                        var chart = new google.charts.Bar(document.getElementById('sale_values'));
                        chart.draw(data, options);
                    };
                </script>
                <script type="text/javascript">
                    google.charts.load('current', {
                        'packages': ['bar']
                    });
                    google.charts.setOnLoadCallback(drawStuff);
                    function drawStuff() {
                        var data = new google.visualization.arrayToDataTable([
                            ['Fecha', 'Monto'],
                            <?php
                            $id = $_SESSION['id'];
                            $stmt = $connect->prepare("SELECT servicio.idservc, plan.idplan, plan.prec,plan.foto, plan.nompla, servicio.ini, servicio.fin, clientes.idclie, clientes.numid, clientes.nomcli, clientes.apecli, clientes.naci, clientes.celu, clientes.correo, servicio.estod, servicio.fere, SUM(prec) as prec FROM servicio INNER JOIN plan ON servicio.idplan = plan.idplan INNER JOIN clientes ON servicio.idclie = clientes.idclie where servicio.ini = CURDATE()");
                            $stmt->setFetchMode(PDO::FETCH_ASSOC);
                            $stmt->execute();
                            while ($row = $stmt->fetch()) {
                                echo "['" . $row['ini'] . "', " . $row['prec'] . "],";
                            }
                            ?>
                        ]);
                        var options = {
                            width: 900,
                            legend: {
                                position: 'none'
                            },
                            chart: {
                                title: '',
                                subtitle: ''
                            },
                            bars: 'horizontal', // Required for Material Bar Charts.
                            axes: {
                                x: {
                                    0: {
                                        side: 'top',
                                        label: 'Monto'
                                    } // Top x-axis.
                                }
                            },
                            bar: {
                                groupWidth: "90%"
                            }
                        };
                        var chart = new google.charts.Bar(document.getElementById('services_values'));
                        chart.draw(data, options);
                    };
                </script>
                <script type="text/javascript">
                    google.charts.load('current', {
                        'packages': ['bar']
                    });
                    google.charts.setOnLoadCallback(drawStuff);
                    function drawStuff() {
                        var data = new google.visualization.arrayToDataTable([
                            ['Fecha', 'Monto'],
                            <?php
                            $id = $_SESSION['id'];
                            $stmt = $connect->prepare("SELECT ingresos.iding, ingresos.detalle, ingresos.total, ingresos.fec, SUM(total) as total FROM ingresos");
                            $stmt->setFetchMode(PDO::FETCH_ASSOC);
                            $stmt->execute();
                            while ($row = $stmt->fetch()) {
                                echo "['" . $row['fec'] . "', " . $row['total'] . "],";
                            }
                            ?>
                        ]);
                        var options = {
                            width: 900,
                            legend: {
                                position: 'none'
                            },
                            chart: {
                                title: '',
                                subtitle: ''
                            },
                            bars: 'horizontal', // Required for Material Bar Charts.
                            axes: {
                                x: {
                                    0: {
                                        side: 'top',
                                        label: 'Monto'
                                    } // Top x-axis.
                                }
                            },
                            bar: {
                                groupWidth: "90%"
                            }
                        };
                        var chart = new google.charts.Bar(document.getElementById('chart_div'));
                        chart.draw(data, options);
                    };
                </script>
                <script type="text/javascript">
                    google.charts.load('current', {
                        'packages': ['bar']
                    });
                    google.charts.setOnLoadCallback(drawStuff);
                    function drawStuff() {
                        var data = new google.visualization.arrayToDataTable([
                            ['Fecha', 'Monto'],
                            <?php
                            $id = $_SESSION['id'];
                            $stmt = $connect->prepare("SELECT gastos.idga, gastos.detall, gastos.total, gastos.fec, SUM(total) as total FROM gastos ");
                            $stmt->setFetchMode(PDO::FETCH_ASSOC);
                            $stmt->execute();
                            while ($row = $stmt->fetch()) {
                                echo "['" . $row['fec'] . "', " . $row['total'] . "],";
                            }
                            ?>
                        ]);
                        var options = {
                            width: 900,
                            legend: {
                                position: 'none'
                            },
                            chart: {
                                title: '',
                                subtitle: ''
                            },
                            bars: 'horizontal', // Required for Material Bar Charts.
                            axes: {
                                x: {
                                    0: {
                                        side: 'top',
                                        label: 'Monto'
                                    } // Top x-axis.
                                }
                            },
                            bar: {
                                groupWidth: "90%"
                            }
                        };
                        var chart = new google.charts.Bar(document.getElementById('gast_div'));
                        chart.draw(data, options);
                    };
                </script>
    </body>

    </html>
<?php } else {
    header('Location: ../error404.php');
} ?>
<?php ob_end_flush(); ?>