<?php
session_start();
require_once '../../backend/bd/ctconex.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Equipo - PCM Team</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5f5f5;
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header { 
            background: #007bff; 
            color: white; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px;
            text-align: center;
        }
        .search-box {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .search-input {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        input[type="text"], select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #1e7e34; }
        .equipo-info {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
            display: none;
        }
        .equipo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .equipo-field {
            background: white;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .field-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .field-value {
            font-weight: bold;
            color: #333;
        }
        .edit-form {
            display: none;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .alert {
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
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
        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
            .search-input { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Editar Equipo por Posición</h1>
        </div>

        <!-- Búsqueda -->
        <div class="search-box">
            <h3>🔍 Buscar Equipo</h3>
            <div class="search-input">
                <input type="text" id="posicion" placeholder="Ingresa la posición (ej: ESTANTE-1-A)" style="flex: 1;">
                <button type="button" id="btnBuscar" class="btn">Buscar</button>
            </div>
        </div>

        <!-- Información del Equipo -->
        <div id="equipoInfo" class="equipo-info">
            <h3>📋 Datos del Equipo</h3>
            <div class="equipo-grid" id="equipoGrid"></div>
            <div style="text-align: center;">
                <button type="button" id="btnEditar" class="btn btn-success">✏️ Editar</button>
            </div>
        </div>

        <!-- Formulario de Edición -->
        <div id="editForm" class="edit-form">
            <h3>✏️ Editar Datos</h3>
            <form id="formularioEdicion">
                <input type="hidden" id="equipoId">
                <input type="hidden" id="posicionActual">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Modelo:</label>
                        <input type="text" id="editModelo" placeholder="Ej: Dell Latitude 3420">
                    </div>
                    <div class="form-group">
                        <label>Procesador:</label>
                        <input type="text" id="editProcesador" placeholder="Ej: Intel i5 11th Gen">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>RAM:</label>
                        <input type="text" id="editRam" placeholder="Ej: 8GB, 16GB">
                    </div>
                    <div class="form-group">
                        <label>Disco:</label>
                        <input type="text" id="editDisco" placeholder="Ej: 256GB SSD">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Pulgadas:</label>
                        <input type="text" id="editPulgadas" placeholder="Ej: 14, 15.6">
                    </div>
                    <div class="form-group">
                        <label>Grado:</label>
                        <select id="editGrado">
                            <option value="">-- Seleccionar --</option>
                            <option value="A">A - Excelente</option>
                            <option value="B">B - Bueno</option>
                            <option value="C">C - Regular</option>
                            <option value="SCRAP">SCRAP</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Táctil:</label>
                        <select id="editTactil">
                            <option value="">-- Seleccionar --</option>
                            <option value="SI">Sí</option>
                            <option value="NO">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Activo Fijo:</label>
                        <input type="text" id="editActivoFijo" placeholder="Código u observación">
                    </div>
                </div>
                
                <div id="alertas"></div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <button type="button" id="btnGuardar" class="btn btn-success">💾 Guardar</button>
                    <button type="button" id="btnCancelar" class="btn">❌ Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let equipoActual = null;
        
        // Eventos
        document.getElementById('btnBuscar').addEventListener('click', buscarEquipo);
        document.getElementById('posicion').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') buscarEquipo();
        });
        document.getElementById('btnEditar').addEventListener('click', mostrarFormulario);
        document.getElementById('btnGuardar').addEventListener('click', guardarCambios);
        document.getElementById('btnCancelar').addEventListener('click', cancelarEdicion);
        
        function buscarEquipo() {
            const posicion = document.getElementById('posicion').value.trim();
            if (!posicion) {
                mostrarAlerta('Ingresa una posición', 'error');
                return;
            }
            
            document.getElementById('btnBuscar').disabled = true;
            document.getElementById('btnBuscar').textContent = 'Buscando...';
            
            fetch(`../../backend/php/buscar_equipo_posicion.php?posicion=${encodeURIComponent(posicion)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        equipoActual = data.equipo;
                        mostrarEquipo(data.equipo);
                        mostrarAlerta('Equipo encontrado', 'success');
                    } else {
                        mostrarAlerta(data.message, 'error');
                        ocultarSecciones();
                    }
                })
                .catch(error => {
                    mostrarAlerta('Error de conexión', 'error');
                })
                .finally(() => {
                    document.getElementById('btnBuscar').disabled = false;
                    document.getElementById('btnBuscar').textContent = 'Buscar';
                });
        }
        
        function mostrarEquipo(equipo) {
            document.getElementById('equipoGrid').innerHTML = `
                <div class="equipo-field">
                    <div class="field-label">Código</div>
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
            `;
            
            // Llenar formulario
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
            
            document.getElementById('equipoInfo').style.display = 'block';
        }
        
        function mostrarFormulario() {
            document.getElementById('editForm').style.display = 'block';
            document.getElementById('btnEditar').style.display = 'none';
        }
        
        function cancelarEdicion() {
            document.getElementById('editForm').style.display = 'none';
            document.getElementById('btnEditar').style.display = 'inline-block';
        }
        
        function guardarCambios() {
            if (!equipoActual) return;
            
            const camposEditados = {};
            const campos = ['modelo', 'procesador', 'ram', 'disco', 'pulgadas', 'grado', 'tactil', 'activo_fijo'];
            
            campos.forEach(campo => {
                const valor = document.getElementById('edit' + campo.charAt(0).toUpperCase() + campo.slice(1)).value.trim();
                if (valor !== '' && valor !== (equipoActual[campo] || '')) {
                    camposEditados[campo] = valor;
                }
            });
            
            if (Object.keys(camposEditados).length === 0) {
                mostrarAlerta('No hay cambios para guardar', 'error');
                return;
            }
            
            if (!confirm('¿Guardar cambios?')) return;
            
            document.getElementById('btnGuardar').disabled = true;
            document.getElementById('btnGuardar').textContent = 'Guardando...';
            
            fetch('../../backend/php/editar_equipo.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    posicion: equipoActual.posicion,
                    campos_editados: camposEditados
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarAlerta('✅ Cambios guardados', 'success');
                    equipoActual = data.equipo;
                    mostrarEquipo(data.equipo);
                    cancelarEdicion();
                } else {
                    mostrarAlerta('❌ ' + data.message, 'error');
                }
            })
            .catch(error => {
                mostrarAlerta('Error de conexión', 'error');
            })
            .finally(() => {
                document.getElementById('btnGuardar').disabled = false;
                document.getElementById('btnGuardar').textContent = '💾 Guardar';
            });
        }
        
        function ocultarSecciones() {
            document.getElementById('equipoInfo').style.display = 'none';
            document.getElementById('editForm').style.display = 'none';
        }
        
        function mostrarAlerta(mensaje, tipo) {
            const alertasDiv = document.getElementById('alertas');
            const alerta = document.createElement('div');
            alerta.className = `alert alert-${tipo}`;
            alerta.textContent = mensaje;
            
            alertasDiv.appendChild(alerta);
            setTimeout(() => alerta.remove(), 5000);
        }
    </script>
</body>
</html>
