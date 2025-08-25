<?php
require_once __DIR__ . '../../../config/ctconex.php';

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limpieza y Mantenimiento - Rediseño</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
       body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #2c3e50;
            line-height: 1.6;
            min-height: 100vh;
        }
       .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
       /* Header Section */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
       .header h1 {
            font-size: 2.2em;
            margin-bottom: 10px;
            font-weight: 700;
        }
       .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
       /* Main Layout */
        .main-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 30px;
            align-items: start;
        }
       /* Card Styles */
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e8ecf3;
        }
       .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f3f5;
        }
       .card-header h3 {
            color: #495057;
            font-size: 1.3em;
            font-weight: 600;
            margin-left: 10px;
        }
       .card-icon {
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }
       /* Diagnosis Results Panel */
        .diagnosis-panel {
            position: sticky;
            top: 20px;
        }
       .diagnosis-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            margin-bottom: 8px;
            background: #f8f9fb;
            border-radius: 8px;
            border-left: 4px solid #e9ecef;
        }
       .diagnosis-label {
            font-weight: 500;
            color: #495057;
            font-size: 0.9em;
        }
       .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
        }
       .status-bueno {
            background: #d4edda;
            color: #155724;
        }
       .status-malo {
            background: #f8d7da;
            color: #721c24;
        }
       .status-nd {
            background: #fff3cd;
            color: #856404;
        }
       /* Equipment Info Section */
        .equipment-info {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
            padding: 25px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
        }
       .equipment-main {
            display: flex;
            flex-direction: column;
        }
       .equipment-code {
            font-size: 2.5em;
            font-weight: 800;
            color: #495057;
            margin-bottom: 5px;
            letter-spacing: -1px;
        }
       .equipment-description {
            color: #6c757d;
            font-size: 1.1em;
            margin-bottom: 15px;
        }
       .equipment-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
       .detail-item {
            display: flex;
            flex-direction: column;
        }
       .detail-label {
            font-size: 0.8em;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 3px;
        }
       .detail-value {
            font-weight: 500;
            color: #495057;
        }
       /* Form Sections */
        .form-section {
            margin-bottom: 35px;
        }
       .section-title {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
       .section-title h4 {
            color: #495057;
            font-size: 1.2em;
            font-weight: 600;
            margin-left: 10px;
        }
       .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
       .form-group {
            display: flex;
            flex-direction: column;
        }
       .form-group.full-width {
            grid-column: 1 / -1;
        }
       label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
       input,
        select,
        textarea {
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.95em;
            transition: all 0.3s ease;
            background: white;
        }
       input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
       textarea {
            resize: vertical;
            min-height: 80px;
        }
       /* Conditional Fields */
        .conditional-field {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fb;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
       .hidden {
            display: none;
        }
       /* Buttons */
        .btn-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
        }
       .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            min-width: 180px;
        }
       .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
       .btn-secondary {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }
       .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
       .btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }
       /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: 500;
        }
       .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
       .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
       /* Responsive Design */
        @media (max-width: 1200px) {
            .main-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
           .diagnosis-panel {
                position: static;
            }
        }
       @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
           .header {
                padding: 20px;
            }
           .header h1 {
                font-size: 1.8em;
            }
           .equipment-info {
                grid-template-columns: 1fr;
                gap: 20px;
            }
           .equipment-code {
                font-size: 2em;
            }
           .form-grid {
                grid-template-columns: 1fr;
            }
           .btn-container {
                flex-direction: column;
                align-items: center;
            }
           .btn {
                width: 100%;
                max-width: 300px;
            }
        }
       /* Animation for smooth transitions */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
           to {
                opacity: 1;
                transform: translateY(0);
            }
        }
       .card {
            animation: slideIn 0.5s ease-out;
        }
    </style>
    <link rel="stylesheet" href="../../backend/css/datatable.css" />
    <link rel="stylesheet" href="../../backend/css/buttonsdataTables.css" />
    <link rel="stylesheet" href="../../backend/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../backend/css/custom.css">
    <link rel="icon" type="image/png" href="../../backend/img/favicon.webp"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet" />

</head>

<body>

<?php include_once '../layouts/nav.php';
        include_once '../layouts/menu_data.php'; ?>
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><img src="../../backend/img/favicon.webp" class="img-fluid"><span>PCMARKETTEAM</span></h3>
            </div>
            <?php if (function_exists('renderMenu')) {
                renderMenu($menu);
            } ?>
        </nav>


        <div id="content">
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔧 LIMPIEZA Y MANTENIMIENTO</h1>
            <p>Equipo: EQ011 - Dell Latitude 5520 Intel i5-10400T</p>
        </div>

        
<script src="../../backend/js/jquery-3.3.1.min.js"></script>
<script src="../../backend/js/bootstrap.min.js"></script>
        
        <div class="main-grid">
            <!-- Diagnosis Panel -->
            <div class="card diagnosis-panel">
                <div class="card-header">
                    <div class="card-icon">📋</div>
                    <h3>Resultados del TRIAGE</h3>
                </div>
                <div class="diagnosis-item">
                    <span class="diagnosis-label">Fecha Diagnóstico</span>
                    <span>14/08/2025 17:30</span>
                </div>
                <div class="diagnosis-item">
                    <span class="diagnosis-label">Cámara</span>
                    <span class="status-badge status-bueno">BUENO</span>
                </div>
                <div class="diagnosis-item">
                    <span class="diagnosis-label">Teclado</span>
                    <span class="status-badge status-malo">MALO</span>
                </div>
                <div class="diagnosis-item">
                    <span class="diagnosis-label">Parlantes</span>
                    <span class="status-badge status-malo">MALO</span>
                </div>
                <div class="diagnosis-item">
                    <span class="diagnosis-label">Batería</span>
                    <span class="status-badge status-bueno">BUENO</span>
                </div>
                <div class="diagnosis-item">
                    <span class="diagnosis-label">Micrófono</span>
                    <span class="status-badge status-malo">MALO</span>
                </div>
                <div class="diagnosis-item">
                    <span class="diagnosis-label">Pantalla</span>
                    <span class="status-badge status-bueno">BUENO</span>
                </div>
                <div class="diagnosis-item">
                    <span class="diagnosis-label">Estado</span>
                    <span class="status-badge status-bueno">APROBADO</span>
                </div>
            </div>
            <!-- Main Form -->
            <div class="card">
                <!-- Equipment Info -->
                <div class="equipment-info">
                    <div class="equipment-main">
                        <div class="equipment-code">EQ011</div>
                        <div class="equipment-description">Dell Latitude 5520 - Intel i5-10400T</div>
                    </div>
                    <div class="equipment-details">
                        <div class="detail-item">
                            <span class="detail-label">Serial</span>
                            <span class="detail-value">DL123456989</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Ubicación</span>
                            <span class="detail-value">Unilago</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Posición</span>
                            <span class="detail-value">ESTANTE-2-A</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Lote</span>
                            <span class="detail-value">sitecPc08-25</span>
                        </div>
                    </div>
                </div>
                <!-- Equipment Edit Section -->
                <div class="form-section">
                    <div class="section-title">
                        <div class="card-icon">✏️</div>
                        <h4>Editar Datos del Equipo</h4>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="edit_modelo">Modelo</label>
                            <input type="text" id="edit_modelo" value="Latitude 5520"
                                placeholder="Ej: Dell Latitude 3420">
                        </div>
                        <div class="form-group">
                            <label for="edit_procesador">Procesador</label>
                            <input type="text" id="edit_procesador" value="Intel i5-10400T"
                                placeholder="Ej: Intel i5 11th Gen">
                        </div>
                        <div class="form-group">
                            <label for="edit_ram">RAM</label>
                            <input type="text" id="edit_ram" value="8GB" placeholder="Ej: 8GB, 16GB">
                        </div>
                        <div class="form-group">
                            <label for="edit_disco">Disco</label>
                            <input type="text" id="edit_disco" value="256GB SSD" placeholder="Ej: 256GB SSD">
                        </div>
                        <div class="form-group">
                            <label for="edit_pulgadas">Pulgadas</label>
                            <input type="text" id="edit_pulgadas" value="15.6" placeholder="Ej: 14, 15.6">
                        </div>
                        <div class="form-group">
                            <label for="edit_grado">Grado</label>
                            <select id="edit_grado">
                                <option value="">-- Seleccionar --</option>
                                <option value="A">A - Excelente</option>
                                <option value="B">B - Bueno</option>
                                <option value="C" selected>C - Regular</option>
                                <option value="SCRAP">SCRAP</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Technician Assignment -->
                <div class="form-section">
                    <div class="section-title">
                        <div class="card-icon">👨‍🔧</div>
                        <h4>Asignar Técnico Diagnóstico</h4>
                    </div>
                    <div class="form-group">
                        <label for="tecnico_diagnostico">Técnico Diagnóstico</label>
                        <select id="tecnico_diagnostico" required>
                            <option value="">-- Seleccionar --</option>
                            <option value="1">Sergio Lara</option>
                            <option value="2">Juan González</option>
                            <option value="3">Luis González</option>
                            <option value="4">Fabian Sanchez</option>
                        </select>
                    </div>
                </div>
                <!-- Cleaning and Maintenance -->
                <div class="form-section">
                    <div class="section-title">
                        <div class="card-icon">🧽</div>
                        <h4>Limpieza y Mantenimiento</h4>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="limpieza_electronico">Limpieza Electrónico</label>
                            <select id="limpieza_electronico">
                                <option value="pendiente" selected>Pendiente</option>
                                <option value="realizada">Realizada</option>
                                <option value="no_aplica">No Aplica</option>
                            </select>
                            <div id="obs_limpieza_block" class="conditional-field hidden">
                                <label for="obs_limpieza">Observaciones Limpieza</label>
                                <textarea id="obs_limpieza" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="mantenimiento_crema">Mantenimiento (Crema Disciplinaria)</label>
                            <select id="mantenimiento_crema">
                                <option value="pendiente" selected>Pendiente</option>
                                <option value="realizada">Realizada</option>
                                <option value="no_aplica">No Aplica</option>
                            </select>
                            <div id="obs_crema_block" class="conditional-field hidden">
                                <label for="obs_crema">Observaciones Crema</label>
                                <textarea id="obs_crema" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cambio_piezas">Cambio Piezas</label>
                            <select id="cambio_piezas">
                                <option value="no" selected>No</option>
                                <option value="si">Sí</option>
                            </select>
                            <div id="piezas_block" class="conditional-field hidden">
                                <label for="piezas_cambiadas">Piezas Solicitadas/Cambiadas</label>
                                <input type="text" id="piezas_cambiadas">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="proceso_reconstruccion">Proceso Reconstrucción</label>
                            <select id="proceso_reconstruccion">
                                <option value="no" selected>No</option>
                                <option value="si">Sí</option>
                            </select>
                            <div id="parte_block" class="conditional-field hidden">
                                <label for="parte_reconstruida">Parte Reconstruida</label>
                                <input type="text" id="parte_reconstruida">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="remite_otra_area">Remite a Otra Área</label>
                        <select id="remite_otra_area">
                            <option value="no" selected>No</option>
                            <option value="si">Sí</option>
                        </select>
                        <div id="area_block" class="form-group hidden">
                            <label for="area_remite">Área a la que Remite</label>
                            <select id="area_remite">
                                <option value="">-- Seleccionar --</option>
                                <option value="bodega">Bodega</option>
                                <option value="laboratorio">Laboratorio</option>
                                <option value="control_calidad">Control de Calidad</option>
                                <option value="venta">Venta</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label for="proceso_electronico">Proceso Electrónico (Detalle)</label>
                        <textarea id="proceso_electronico" rows="3"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label for="observaciones_globales">Observaciones Globales</label>
                        <textarea id="observaciones_globales" rows="3"></textarea>
                    </div>
                </div>
                <!-- Buttons -->
                <div class="btn-container">
                    <button type="button" class="btn btn-secondary" id="btnGuardarEquipo">
                        💾 Guardar Cambios del Equipo
                    </button>
                    <button type="button" class="btn btn-primary" id="btnGuardar">
                        🔧 Guardar Mantenimiento y Limpieza
                    </button>
                </div>
                <div id="alertas"></div>
            </div>
        </div>
    </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Show/hide conditional fields
            const limpiezaElectronico = document.getElementById('limpieza_electronico');
            const obsLimpiezaBlock = document.getElementById('obs_limpieza_block');
            limpiezaElectronico.addEventListener('change', function () {
                if (this.value === 'realizada') {
                    obsLimpiezaBlock.classList.remove('hidden');
                } else {
                    obsLimpiezaBlock.classList.add('hidden');
                }
            });
            const mantenimientoCrema = document.getElementById('mantenimiento_crema');
            const obsCremaBlock = document.getElementById('obs_crema_block');
            mantenimientoCrema.addEventListener('change', function () {
                if (this.value === 'realizada') {
                    obsCremaBlock.classList.remove('hidden');
                } else {
                    obsCremaBlock.classList.add('hidden');
                }
            });
            const cambioPiezas = document.getElementById('cambio_piezas');
            const piezasBlock = document.getElementById('piezas_block');
            cambioPiezas.addEventListener('change', function () {
                if (this.value === 'si') {
                    piezasBlock.classList.remove('hidden');
                } else {
                    piezasBlock.classList.add('hidden');
                }
            });
            const procesoReconstruccion = document.getElementById('proceso_reconstruccion');
            const parteBlock = document.getElementById('parte_block');
            procesoReconstruccion.addEventListener('change', function () {
                if (this.value === 'si') {
                    parteBlock.classList.remove('hidden');
                } else {
                    parteBlock.classList.add('hidden');
                }
            });
            const remiteOtraArea = document.getElementById('remite_otra_area');
            const areaBlock = document.getElementById('area_block');
            remiteOtraArea.addEventListener('change', function () {
                if (this.value === 'si') {
                    areaBlock.classList.remove('hidden');
                } else {
                    areaBlock.classList.add('hidden');
                }
            });
            // Button handlers
            document.getElementById('btnGuardar').addEventListener('click', function () {
                mostrarAlerta('✅ Mantenimiento guardado correctamente', 'success');
            });
            document.getElementById('btnGuardarEquipo').addEventListener('click', function () {
                mostrarAlerta('✅ Cambios del equipo guardados correctamente', 'success');
            });
        });
        function mostrarAlerta(mensaje, tipo) {
            const alertasDiv = document.getElementById('alertas');
            const alerta = document.createElement('div');
            alerta.className = `alert alert-${tipo}`;
            alerta.textContent = mensaje;
            alertasDiv.appendChild(alerta);
            setTimeout(() => {
                alerta.remove();
            }, 5000);
        }
    </script>
</body>
</html>