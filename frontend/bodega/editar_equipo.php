<?php
session_start();
require_once '../../config/ctconex.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Equipo por Posición - PCM Team</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f6fa; 
            color: #2c3e50;
            line-height: 1.6;
        }
        .container { 
            max-width: 1200px; 
            margin: 20px auto; 
            padding: 20px; 
        }
        .header { 
            background: linear-gradient(135deg, #e74c3c, #c0392b); 
            color: white; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 20px;
            text-align: center;
        }
        .search-section {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .equipo-info {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: none;
        }
        .edit-form {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: none;
        }
        .historial {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: none;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
            text-transform: uppercase;
            font-size: 12px;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e6ed;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #e74c3c;
        }
        .btn {
            background: #e74c3c;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: 0.3s;
            margin-right: 10px;
        }
        .btn:hover { background: #c0392b; }
        .btn-success {
            background: #27ae60;
        }
        .btn-success:hover { background: #229954; }
        .btn:disabled { background: #95a5a6; cursor: not-allowed; }
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .equipo-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #e74c3c;
        }
        .equipo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .equipo-title {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
        }
        .equipo-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            background: #3498db;
            color: white;
        }
        .equipo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .equipo-field {
            background: white;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #e0e6ed;
        }
        .field-label {
            font-size: 11px;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .field-value {
            font-weight: 600;
            color: #2c3e50;
        }
        .historial-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            border-left: 3px solid #3498db;
        }
        .historial-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .historial-fecha {
            font-size: 12px;
            color: #7f8c8d;
        }
        .historial-usuario {
            font-size: 12px;
            color: #e74c3c;
            font-weight: 600;
        }
        .historial-cambio {
            font-size: 14px;
            color: #2c3e50;
        }
        .search-box {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 20px;
        }
        .search-input {
            flex: 1;
            max-width: 300px;
        }
        .instructions {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .instructions h3 {
            color: #856404;
            margin-bottom: 15px;
        }
        .instructions ul {
            color: #856404;
            padding-left: 20px;
        }
        .instructions li {
            margin-bottom: 8px;
        }
        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
            .equipo-grid { grid-template-columns: 1fr; }
            .search-box { flex-direction: column; align-items: stretch; }
            .search-input { max-width: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 EDITAR EQUIPO POR POSICIÓN</h1>
            <p>Sistema de edición de datos de equipos en inventario</p>
        </div>
        <!-- Instrucciones -->
        <div class="instructions">
            <h3>📋 Instrucciones de Uso</h3>
            <ul>
                <li><strong>1.</strong> Ingresa la posición del equipo (ej: ESTANTE-1-A, CAJA-B2)</li>
                <li><strong>2.</strong> Revisa la información actual del equipo</li>
                <li><strong>3.</strong> Edita los campos que desees modificar</li>
                <li><strong>4.</strong> Haz clic en "Guardar Cambios" para confirmar</li>
                <li><strong>5.</strong> Revisa el historial de cambios realizados</li>
            </ul>
        </div>
        <!-- Búsqueda por Posición -->
        <div class="search-section">
            <h2>🔍 Buscar Equipo por Posición</h2>
            <div class="search-box">
                <div class="search-input">
                    <label for="posicion">Posición del Equipo:</label>
                    <input type="text" id="posicion" placeholder="Ej: ESTANTE-1-A, CAJA-B2, etc.">
                </div>
                <button type="button" id="btnBuscar" class="btn">🔍 Buscar</button>
            </div>
        </div>
        <!-- Información del Equipo -->
        <div id="equipoInfo" class="equipo-info">
            <div class="equipo-header">
                <div class="equipo-title">📦 Equipo en posición: <span id="posicionEquipo"></span></div>
                <div class="equipo-status" id="estadoEquipo"></div>
            </div>
            
            <div class="equipo-grid" id="equipoGrid">
                <!-- Los campos se llenarán dinámicamente -->
            </div>
            
            <div style="margin-top: 20px; text-align: center;">
                <button type="button" id="btnEditar" class="btn btn-success">✏️ Editar Equipo</button>
            </div>
        </div>
        <!-- Formulario de Edición -->
        <div id="editForm" class="edit-form">
            <h2>✏️ Editar Datos del Equipo</h2>
            <form id="formularioEdicion">
                <input type="hidden" id="equipoId" name="equipo_id">
                <input type="hidden" id="posicionActual" name="posicion">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editModelo">Modelo:</label>
                        <input type="text" id="editModelo" name="modelo" placeholder="Ej: Dell Latitude 3420">
                    </div>
                    <div class="form-group">
                        <label for="editProcesador">Procesador:</label>
                        <input type="text" id="editProcesador" name="procesador" placeholder="Ej: Intel i5 11th Gen">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editRam">RAM:</label>
                        <input type="text" id="editRam" name="ram" placeholder="Ej: 8GB, 16GB">
                    </div>
                    <div class="form-group">
                        <label for="editDisco">Disco:</label>
                        <input type="text" id="editDisco" name="disco" placeholder="Ej: 256GB SSD, 1TB HDD">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editPulgadas">Pulgadas:</label>
                        <input type="text" id="editPulgadas" name="pulgadas" placeholder="Ej: 14, 15.6, 17">
                    </div>
                    <div class="form-group">
                        <label for="editGrado">Grado:</label>
                        <select id="editGrado" name="grado">
                            <option value="">-- Seleccionar --</option>
                            <option value="A">A - Excelente</option>
                            <option value="B">B - Bueno</option>
                            <option value="C">C - Regular</option>
                            <option value="SCRAP">SCRAP - Desecho</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editTactil">Táctil:</label>
                        <select id="editTactil" name="tactil">
                            <option value="">-- Seleccionar --</option>
                            <option value="SI">Sí</option>
                            <option value="NO">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editActivoFijo">Activo Fijo:</label>
                        <input type="text" id="editActivoFijo" name="activo_fijo" placeholder="Código o observación">
                    </div>
                </div>
                
                <div id="alertas"></div>
                
                <div style="text-align: center; margin-top: 25px;">
                    <button type="button" id="btnGuardar" class="btn btn-success">💾 Guardar Cambios</button>
                    <button type="button" id="btnCancelar" class="btn">❌ Cancelar</button>
                </div>
            </form>
        </div>
        <!-- Historial de Cambios -->
        <div id="historial" class="historial">
            <h2>📜 Historial de Cambios</h2>
            <div id="historialContent">
                <!-- El historial se llenará dinámicamente -->
            </div>
        </div>
    </div>
    <script>
        let equipoActual = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Buscar equipo
            document.getElementById('btnBuscar').addEventListener('click', buscarEquipo);
            document.getElementById('posicion').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') buscarEquipo();
            });
            
            // Botones de edición
            document.getElementById('btnEditar').addEventListener('click', mostrarFormularioEdicion);
            document.getElementById('btnGuardar').addEventListener('click', guardarCambios);
            document.getElementById('btnCancelar').addEventListener('click', cancelarEdicion);
        });
        
        function buscarEquipo() {
            const posicion = document.getElementById('posicion').value.trim();
            
            if (!posicion) {
                mostrarAlerta('Debe ingresar una posición', 'error');
                return;
            }
            
            // Mostrar loading
            document.getElementById('btnBuscar').disabled = true;
            document.getElementById('btnBuscar').textContent = '🔍 Buscando...';
            
            fetch(`../../backend/php/buscar_equipo_posicion.php?posicion=${encodeURIComponent(posicion)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        equipoActual = data.equipo;
                        mostrarInformacionEquipo(data.equipo);
                        mostrarHistorial(data.historial);
                        mostrarAlerta('Equipo encontrado exitosamente', 'success');
                    } else {
                        mostrarAlerta(data.message, 'error');
                        ocultarSecciones();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarAlerta('Error de conexión', 'error');
                })
                .finally(() => {
                    document.getElementById('btnBuscar').disabled = false;
                    document.getElementById('btnBuscar').textContent = '🔍 Buscar';
                });
        }
        
        function mostrarInformacionEquipo(equipo) {
            document.getElementById('posicionEquipo').textContent = equipo.posicion;
            document.getElementById('estadoEquipo').textContent = equipo.disposicion;
            
            const grid = document.getElementById('equipoGrid');
            grid.innerHTML = `
                <div class="equipo-field">
                    <div class="field-label">Código General</div>
                    <div class="field-value">${equipo.codigo_g || 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">Producto</div>
                    <div class="field-value">${equipo.producto || 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">Marca</div>
                    <div class="field-value">${equipo.marca || 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">Modelo</div>
                    <div class="field-value">${equipo.modelo || 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">Serial</div>
                    <div class="field-value">${equipo.serial || 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">Procesador</div>
                    <div class="field-value">${equipo.procesador || 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">RAM</div>
                    <div class="field-value">${equipo.ram || 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">Disco</div>
                    <div class="field-value">${equipo.disco || 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">Pulgadas</div>
                    <div class="field-value">${equipo.pulgadas || 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">Grado</div>
                    <div class="field-value">${equipo.grado || 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">Táctil</div>
                    <div class="field-value">${equipo.tactil || 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">Activo Fijo</div>
                    <div class="field-value">${equipo.activo_fijo || 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">Ubicación</div>
                    <div class="field-value">${equipo.ubicacion || 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">Fecha Ingreso</div>
                    <div class="field-value">${equipo.fecha_ingreso ? new Date(equipo.fecha_ingreso).toLocaleDateString() : 'N/A'}</div>
                </div>
                <div class="equipo-field">
                    <div class="field-label">Última Modificación</div>
                    <div class="field-value">${equipo.fecha_modificacion ? new Date(equipo.fecha_modificacion).toLocaleDateString() : 'N/A'}</div>
                </div>
            `;
            
            // Llenar formulario de edición
            document.getElementById('equipoId').value = equipo.id;
            document.getElementById('posicionActual').value = equipo.posicion;
            document.getElementById('editModelo').value = equipo.modelo || '';
            document.getElementById('editProcesador').value = equipo.procesador || '';
            document.getElementById('editRam').value = equipo.ram || '';
            document.getElementById('editDisco').value = equipo.disco || '';
            document.getElementById('editPulgadas').value = equipo.pulgadas || '';
            document.getElementById('editGrado').value = equipo.grado || '';
            document.getElementById('editTactil').value = equipo.tactil || '';
            document.getElementById('editActivoFijo').value = equipo.activo_fijo || '';
            
            // Mostrar sección
            document.getElementById('equipoInfo').style.display = 'block';
        }
        
        function mostrarHistorial(historial) {
            const content = document.getElementById('historialContent');
            
            if (historial.length === 0) {
                content.innerHTML = '<p style="text-align: center; color: #7f8c8d;">No hay historial de cambios disponible</p>';
            } else {
                content.innerHTML = historial.map(cambio => `
                    <div class="historial-item">
                        <div class="historial-header">
                            <span class="historial-fecha">${new Date(cambio.fecha_cambio).toLocaleString()}</span>
                            <span class="historial-usuario">${cambio.usuario || 'Usuario'}</span>
                        </div>
                        <div class="historial-cambio">
                            <strong>${cambio.campo_modificado}:</strong> 
                            "${cambio.valor_anterior || 'N/A'}" → "${cambio.valor_nuevo || 'N/A'}"
                        </div>
                    </div>
                `).join('');
            }
            
            document.getElementById('historial').style.display = 'block';
        }
        
        function mostrarFormularioEdicion() {
            document.getElementById('editForm').style.display = 'block';
            document.getElementById('btnEditar').style.display = 'none';
        }
        
        function cancelarEdicion() {
            document.getElementById('editForm').style.display = 'none';
            document.getElementById('btnEditar').style.display = 'inline-block';
        }
        
        function guardarCambios() {
            if (!equipoActual) {
                mostrarAlerta('No hay equipo seleccionado', 'error');
                return;
            }
            
            // Recopilar campos editados
            const camposEditados = {};
            const campos = ['modelo', 'procesador', 'ram', 'disco', 'pulgadas', 'grado', 'tactil', 'activo_fijo'];
            
            campos.forEach(campo => {
                const valor = document.getElementById('edit' + campo.charAt(0).toUpperCase() + campo.slice(1)).value.trim();
                if (valor !== '' && valor !== (equipoActual[campo] || '')) {
                    camposEditados[campo] = valor;
                }
            });
            
            if (Object.keys(camposEditados).length === 0) {
                mostrarAlerta('No hay cambios para guardar', 'info');
                return;
            }
            
            // Confirmar cambios
            const confirmacion = confirm(`¿Estás seguro de que deseas guardar los siguientes cambios?\n\n${Object.entries(camposEditados).map(([campo, valor]) => `${campo}: ${valor}`).join('\n')}`);
            
            if (!confirmacion) return;
            
            // Deshabilitar botón
            document.getElementById('btnGuardar').disabled = true;
            document.getElementById('btnGuardar').textContent = '💾 Guardando...';
            
            // Enviar cambios
            fetch('../../backend/php/editar_equipo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    posicion: equipoActual.posicion,
                    campos_editados: camposEditados
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarAlerta('✅ ' + data.message, 'success');
                    // Actualizar información del equipo
                    equipoActual = data.equipo;
                    mostrarInformacionEquipo(data.equipo);
                    // Ocultar formulario
                    cancelarEdicion();
                    // Recargar historial
                    buscarEquipo();
                } else {
                    mostrarAlerta('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error de conexión', 'error');
            })
            .finally(() => {
                document.getElementById('btnGuardar').disabled = false;
                document.getElementById('btnGuardar').textContent = '💾 Guardar Cambios';
            });
        }
        
        function ocultarSecciones() {
            document.getElementById('equipoInfo').style.display = 'none';
            document.getElementById('editForm').style.display = 'none';
            document.getElementById('historial').style.display = 'none';
        }
        
        function mostrarAlerta(mensaje, tipo) {
            const alertasDiv = document.getElementById('alertas');
            const alerta = document.createElement('div');
            alerta.className = `alert alert-${tipo}`;
            alerta.textContent = mensaje;
            
            alertasDiv.appendChild(alerta);
            
            // Remover alerta después de 5 segundos
            setTimeout(() => {
                alerta.remove();
            }, 5000);
        }
    </script>
</body>
</html>
