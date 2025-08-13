<!--frontend/bodega/triage_2.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEGUNDO TRIAGE - Sistema de Diagnóstico</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Courier New', monospace;
            background-color: #000;
            color: #fff;
            min-height: 100vh;
        }
        .container-fluid {
            padding: 20px;
        }
        .header-section {
            border-bottom: 2px solid #17a2b8;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .main-title {
            font-size: 2.5rem;
            font-weight: bold;
            letter-spacing: 3px;
        }
        .date-display {
            font-size: 1.2rem;
            color: #17a2b8;
        }
        .section-border {
            border: 1px solid #17a2b8;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .scan-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, #17a2b8, #20c997);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin: 0 auto 15px auto;
        }
        .form-control,
        .form-select {
            background-color: #212529;
            border: 1px solid #17a2b8;
            color: #fff;
            font-family: 'Courier New', monospace;
        }
        .form-control:focus,
        .form-select:focus {
            background-color: #212529;
            border-color: #20c997;
            color: #fff;
            box-shadow: 0 0 5px rgba(23, 162, 184, 0.5);
        }
        .btn-custom {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            margin: 5px;
            min-width: 120px;
        }
        .component-row {
            background-color: #1a1a1a;
            margin-bottom: 8px;
            padding: 10px;
            border-radius: 5px;
            border-left: 3px solid #17a2b8;
        }
        .equipment-counter {
            background: linear-gradient(135deg, #17a2b8, #20c997);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .loading-spinner {
            display: none;
            color: #17a2b8;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                color: #000 !important;
                background: #fff !important;
            }
        }
    </style>
</head>
<body class="bg-dark text-light">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row header-section">
            <div class="col-md-8">
                <button class="btn btn-success btn-custom no-print" onclick="location.reload()">
                    #REFI
                </button>
                <div class="mt-2 small">FECHA ASIGNACION</div>
            </div>
            <div class="col-md-4 text-center">
                <h1 class="main-title text-info">SEGUNDO TRIAGE</h1>
            </div>
            <div class="col-md-12 text-end">
                <div class="date-display" id="currentDate"></div>
                <button class="btn btn-outline-light btn-sm no-print" onclick="window.close()">
                    BORRAR
                </button>
            </div>
        </div>
        <div class="row">
            <!-- Left Column -->
            <div class="col-md-6">
                <!-- Scan Icon Section -->
                <div class="section-border text-center">
                    <div class="section-border">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label text-info fw-bold">TOTAL EQUIPOS ASIGNADOS</label>
                                <input type="text" class="form-control" id="total-equipos-asingnados" readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-info fw-bold">TOTAL EQUIPOS PROCESADOS</label>
                                <input type="text" class="form-control" id="total-equipos-procesados" readonly>
                            </div>
                        </div>
                        <h5 class="text-info mb-3">EQUIPOS INGRESADOS</h5>
                        <div class="equipment-counter">
                            <div id="equiposCounter"><?php ?></div>
                            <small>EQUIPOS PROCESADOS HOY</small>
                        </div>
                    </div>
                </div>
                <!-- Equipment Info -->
                <div class="section-border">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label text-info fw-bold">CODIGO</label>
                            <input type="text" class="form-control" id="equipoCodigo" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-info fw-bold">SERIAL</label>
                            <input type="text" class="form-control" id="equipoSerial" readonly>
                        </div>
                    </div>
                </div>
                <!-- Diagnostic Components -->
                <h1 class="alert alert-success">RESULTADO DEL TRIAGE 2</h1>
                <div class="section-border">
                    <h5 class="text-info mb-3">COMPONENTES DE DIAGNÓSTICO si es un portatil</h5>
                    <!-- Left side components -->
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>PARLANTE</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="Parlante">
                                    <option value="BUENO" selected>BUENO</option>
                                    <option value="MALO">MALO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>CAMARA</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="CAMARA">
                                    <option value="BUENO" selected>BUENO</option>
                                    <option value="MALO">MALO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>PANTALLA</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="PANTALLA">
                                    <option value="BUENO" selected>BUENO</option>
                                    <option value="MALO">MALO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>TECLADO</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="teclado">
                                    <option value="BUENO" selected>BUENO</option>
                                    <option value="MALO">MALO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>BATERIA</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="bateria">
                                    <option value="BUENO" selected>BUENO</option>
                                    <option value="MALO">MALO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>MICROFONO</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="microfono">
                                    <option value="BUENO" selected>BUENO</option>
                                    <option value="MALO">MALO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>DISCO</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="disco">
                                    <option value="BUENO" selected>BUENO</option>
                                    <option value="MALO">MALO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right side components -->
                <div class="section-border">
                    <div class="component-row">
                        <h5 class="text-info mb-3">COMPONENTES DE DIAGNÓSTICO tanto para portatiles O Computadores de
                            mesa.</h5>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>PUERTO VGA</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="puerto_vga">
                                    <option value="BUENO" selected>BUENO</option>
                                    <option value="MALO">MALO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>PUERTO DVI</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="puerto_DVI">
                                    <option value="BUENO" selected>BUENO</option>
                                    <option value="MALO">MALO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>PUERTO HDMI</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="puerto_hdmi">
                                    <option value="BUENO" selected>BUENO</option>
                                    <option value="MALO">MALO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>PUERTO USB</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="puerto_usb">
                                    <option value="BUENO" selected>BUENO</option>
                                    <option value="MALO">MALO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>PUERTO RED</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="puerto_red">
                                    <option value="BUENO" selected>BUENO</option>
                                    <option value="MALO">MALO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>VIDA UTIL DISCO</strong></div>
                            <div class="col-8">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="vida_util_disco" value="100" min="0"
                                        max="100">
                                    <span class="input-group-text bg-info text-dark">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Observations -->
                    <div class="section-border">
                        <h5 class="text-info mb-3">OBSERVACIONES</h5>
                        <textarea class="form-control" id="observaciones" rows="4"
                            placeholder="Ingrese observaciones del diagnóstico..."></textarea>
                    </div>
                    <!-- Equipment Status -->
                    <div class="section-border">
                        <h5 class="text-info mb-3">ESTADO DEL TRIAGE</h5>
                        <select class="form-select" id="estadoTriage">
                            <option value="aprobado">APROBADO</option>
                            <option value="falla_mecanica">FALLA MECÁNICA</option>
                            <option value="falla_electrica">FALLA ELÉCTRICA</option>
                            <option value="reparacion_cosmetica">REPARACIÓN COSMÉTICA</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Right Column -->
            <div class="col-md-6">
                <!-- Technician Info -->
                <div class="section-border">
                    <h5 class="text-info mb-3">TÉCNICO ASIGNADO</h5>
                    <div class="alert alert-info" id="technicanInfo">
                        <strong id="technicianName">nombre tecnico</strong>
                        <br><small>Técnico de Diagnóstico</small>
                    </div>
                </div>
                <!-- LIMPIEZA Y MANTENIMIENTO -->
                <h1 class="alert alert-success">LIMPIEZA Y MANTENIMIENTO</h1>
                <div class="section-border">
                    <h5 class="text-info mb-3">SECCION DE LIMPIEZA Y MANTENIMIENTO</h5>
                    <!-- Left side components -->
                    <h4>PROCESO DE LIMPIEZA</h4>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>LIMPIEZA ELECTRONICO</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="proceso-limpieza">
                                    <option value="REALIZADA" selected>REALIZADA</option>
                                    <option value="PENDIENTES">PENDIENTES</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <h4>PROCESO DE MANTENIMIENTO</h4>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>CAMBIO DE CREMA DISCIPADORA</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="crema-discipadora">
                                    <option value="REALIZADA" selected>REALIZADA</option>
                                    <option value="PENDIENTES">PENDIENTES</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>MANTENIMIENTO DE PARTES</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="mantenimiento-parte">
                                    <option value="REALIZADA" selected>REALIZADA</option>
                                    <option value="PENDIENTES">PENDIENTES</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>CAMBIO DE PIEZAS QUE NO FUNCIONAN</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="pieza-funcionan">
                                    <option value="SI" selected>SI</option>
                                    <option value="NO">NO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>¿QUE PIEZAS SE CAMBIARON?</strong></div>
                            <div class="col-8">
                                <label class="form-select form-select-sm" id="pieza-cambiaron" arial-placeholder="Componete Cambiado">a</label>
                            </div>
                        </div>
                    </div>
                    <h4>PROCESO DE RECONSTRUCCION</h4>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>RECONSTRUCCION</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="reconstruccion">
                                    <option value="REALIZADA" selected>NO</option>
                                    <option value="PENDIENTES">SI</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>PARTE RECONSTRUIDA</strong></div>
                            <div class="col-8">
                                <label class="form-select form-select-sm" id="parte-reconstuida">
                                </label>
                            </div>
                        </div>
                    </div>
                    <h4>PROCESO DE LIMPIEZA</h4>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>LIMPIEZA GENERAL</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="**">
                                    <option value="REALIZADA" selected>REALIZADA</option>
                                    <option value="PENDIENTES">PENDIENTES</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <h4>OTRA AREA</h4>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>¿REMITE A OTRA AREA?</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="remitir-area">
                                    <option value="SI" selected>SI</option>
                                    <option value="NO">NO</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="component-row">
                        <div class="row align-items-center">
                            <div class="col-4"><strong>¿A QUE AREA(S) REMITE?</strong></div>
                            <div class="col-8">
                                <select class="form-select form-select-sm" id="**">
                                    <option value="Proceso técnico" selected>1| Proceso técnico</option>
                                    <option value="Electrica" selected>2| Pr Electrica</option>
                                    <option value="pintura">3| PROCESO ESTÉTICO</option>
                                    <option value="Control-Calidad">4| QC | Control-Calidad</option>
                                    <option value="Business"> 5| Business Room</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Equipment Counter -->
                    <div class="section-border">
                        <h5 class="text-info mb-3">EQUIPOS INGRESADOS</h5>
                        <div class="equipment-counter">
                            <div id="equiposCounter"><?php ?></div>
                            <small>EQUIPOS PROCESADOS HOY</small>
                        </div>
                        <!-- Equipment Image Placeholder -->
                        <div class="mt-3 text-center">
                            <div
                                style="width: 120px; height: 90px; background: #333; border-radius: 8px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 2rem;">💻</span>
                            </div>
                            <small class="text-muted mt-2 d-block">EQUIPO ACTUAL</small>
                        </div>
                    </div>
                    <!-- Action Buttons -->
                    <div class="section-border no-print">
                        <div class="row">
                            <div class="col-4">
                                <button class="btn btn-success w-100 btn-custom" onclick="guardarDiagnostico()">
                                    <div class="loading-spinner" id="loadingGuardar">
                                        <div class="spinner-border spinner-border-sm me-2"></div>
                                    </div>
                                    GUARDAR
                                </button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-info w-100 btn-custom" onclick="imprimirReporte()">
                                    IMPRIMIR
                                </button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-warning w-100 btn-custom" onclick="siguienteEquipo()">
                                    SIGUIENTE
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hidden data -->
        <input type="hidden" id="currentEquipmentId" value="">
        <input type="hidden" id="technicianId" value="">
        <script>
            // Variables globales
            let equiposDisponibles = [];
            let equipoActualIndex = 0;
            let equiposProcesados = 0;
            // Inicialización al cargar la página
            document.addEventListener('DOMContentLoaded', function () {
                actualizarFecha();
                cargarDatosSesion();
                cargarEquiposAsignados();
                actualizarContadorEquipos();
            });
            // Actualizar fecha actual
            function actualizarFecha() {
                const now = new Date();
                const fecha = now.toLocaleDateString('es-CO', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
                document.getElementById('currentDate').textContent = fecha;
            }
            // Cargar datos de sesión (simulado para demo)
            function cargarDatosSesion() {
                // En implementación real, estos datos vendrían de PHP/sesión
                document.getElementById('technicianName').textContent = 'nombre del tecnico';
                document.getElementById('technicianId').value = '19'; // ID del técnico
            }
            // Simular carga de equipos asignados desde BD
            function cargarEquiposAsignados() {
                // En implementación real, esto sería una llamada AJAX a un endpoint PHP
                equiposDisponibles = [
                    {
                        id: 1,
                        codigo: 'EQ001',
                        serial: 'gwert111',
                        marca: 'Dell',
                        modelo: 'Latitude 5520'
                    },
                    {
                        id: 27,
                        codigo: 'EQ012',
                        serial: 'ATG1292',
                        marca: 'Dell',
                        modelo: 'Latitude 55223'
                    }
                ];
                if (equiposDisponibles.length > 0) {
                    cargarEquipoActual();
                } else {
                    alert('No hay equipos asignados para diagnóstico.');
                }
            }
            // Cargar equipo actual en la interfaz
            function cargarEquipoActual() {
                if (equipoActualIndex >= equiposDisponibles.length) {
                    alert('No hay más equipos pendientes de diagnóstico.');
                    return;
                }
                const equipo = equiposDisponibles[equipoActualIndex];
                document.getElementById('equipoCodigo').value = equipo.codigo;
                document.getElementById('equipoSerial').value = equipo.serial;
                document.getElementById('currentEquipmentId').value = equipo.id;
                // Limpiar formulario para nuevo diagnóstico
                limpiarFormulario();
                // Cargar diagnóstico existente si existe
                cargarDiagnosticoExistente(equipo.id);
            }
            // Limpiar formulario
            function limpiarFormulario() {
                // Resetear todos los componentes a "BUENO"
                ['teclado', 'parlante', 'bateria', 'microfono', 'disco',
                    'pantalla', 'puerto_vga', 'puerto_hdmi', 'puerto_usb', 'puerto_red'].forEach(id => {
                        document.getElementById(id).value = 'BUENO';
                    });
                document.getElementById('vida_util_disco').value = '100';
                document.getElementById('observaciones').value = '';
                document.getElementById('estadoTriage').value = 'aprobado';
            }
            // Cargar diagnóstico existente (simulado)
            function cargarDiagnosticoExistente(equipoId) {
                // En implementación real, esto sería una llamada AJAX
                // Por ahora, simulamos datos existentes para el equipo 27
                if (equipoId === 27) {
                    document.getElementById('observaciones').value = 'pantalla tallada resto ok';
                    document.getElementById('pantalla').value = 'MALO';
                }
            }
            // Actualizar contador de equipos
            function actualizarContadorEquipos() {
                // En implementación real, esto consultaría la BD
                document.getElementById('equiposCounter').textContent = equiposProcesados + <?php ?>;
            }
            // Guardar diagnóstico
            function guardarDiagnostico() {
                const equipoId = document.getElementById('currentEquipmentId').value;
                const tecnicoId = document.getElementById('technicianId').value;
                if (!equipoId) {
                    alert('No hay equipo seleccionado para guardar.');
                    return;
                }
                // Mostrar loading
                document.getElementById('loadingGuardar').style.display = 'inline-block';
                // Recopilar datos del diagnóstico
                const diagnosticoData = {
                    inventario_id: equipoId,
                    tecnico_id: tecnicoId,
                    camara: 'Funcional', // Valor fijo por ahora
                    teclado: document.getElementById('teclado').value,
                    parlantes: document.getElementById('parlante').value,
                    bateria: document.getElementById('bateria').value,
                    microfono: document.getElementById('microfono').value,
                    pantalla: document.getElementById('pantalla').value,
                    puertos: `VGA: ${document.getElementById('puerto_vga').value}, HDMI: ${document.getElementById('puerto_hdmi').value}, USB: ${document.getElementById('puerto_usb').value}, RED: ${document.getElementById('puerto_red').value}`,
                    disco: `${document.getElementById('disco').value} - Vida útil: ${document.getElementById('vida_util_disco').value}%`,
                    estado_reparacion: document.getElementById('estadoTriage').value,
                    observaciones: document.getElementById('observaciones').value
                };
                // Simular guardado exitoso
                setTimeout(() => {
                    document.getElementById('loadingGuardar').style.display = 'none';
                    alert('Diagnóstico guardado exitosamente.');
                    equiposProcesados++;
                    actualizarContadorEquipos();
                }, 1500);
            }
            // Imprimir reporte
            function imprimirReporte() {
                window.print();
            }
            // Cargar siguiente equipo
            function siguienteEquipo() {
                if (equipoActualIndex < equiposDisponibles.length - 1) {
                    equipoActualIndex++;
                    cargarEquipoActual();
                } else {
                    alert('No hay más equipos pendientes. Este es el último equipo.');
                }
            }
            // Funciones de utilidad
            function mostrarAlerta(mensaje, tipo = 'info') {
                const alertClass = tipo === 'error' ? 'alert-danger' : 'alert-info';
                const alertHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                    style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;" role="alert">
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
                document.body.insertAdjacentHTML('beforeend', alertHTML);
                // Auto-remover después de 5 segundos
                setTimeout(() => {
                    const alert = document.querySelector('.alert');
                    if (alert) alert.remove();
                }, 5000);
            }
            // Validar formulario antes de guardar
            function validarFormulario() {
                const observaciones = document.getElementById('observaciones').value.trim();
                if (observaciones.length < 10) {
                    mostrarAlerta('Las observaciones deben tener al menos 10 caracteres.', 'error');
                    return false;
                }
                return true;
            }
            // Atajos de teclado
            document.addEventListener('keydown', function (e) {
                // Ctrl + S para guardar
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    guardarDiagnostico();
                }
                // Ctrl + P para imprimir
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    imprimirReporte();
                }
                // Ctrl + N para siguiente equipo
                if (e.ctrlKey && e.key === 'n') {
                    e.preventDefault();
                    siguienteEquipo();
                }
            });
        </script>
        <!-- Font Awesome para iconos (opcional) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>

</html>