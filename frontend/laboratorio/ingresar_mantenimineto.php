<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limpieza y Mantenimiento</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f6fa; 
            color: #2c3e50;
            line-height: 1.6;
        }
        .container { 
            max-width: 1000px; 
            margin: 20px auto; 
            padding: 20px; 
        }
        .header { 
            background: linear-gradient(135deg, #3498db, #2980b9); 
            color: white; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 20px;
            text-align: center;
        }
        .card { 
            background: white; 
            border-radius: 8px; 
            padding: 25px; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #34495e;
            text-transform: uppercase;
            font-size: 12px;
        }
        select, input, textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e6ed;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        .btn {
            background: #27ae60;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn:hover { background: #229954; }
        .btn:disabled { background: #95a5a6; cursor: not-allowed; }
        .hidden { display: none; }
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
        .section-title {
            color: #2980b9;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        @media (max-width: 768px) {
            .info-grid, .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LIMPIEZA Y MANTENIMIENTO</h1>
        </div>
        <div class="card">
            <div class="info-grid">
                <div>
                    <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">CÓDIGO</div>
                    <div style="font-size: 24px; font-weight: bold; color: #2c3e50;"><?php echo h($inventario['codigo_g']); ?></div>
                    <div style="color: #7f8c8d;"><?php echo h($inventario['producto'] . ' - ' . $inventario['marca'] . ' / ' . $inventario['modelo']); ?></div>
                </div>
                <div style="text-align: right;">
                    <div><strong>SERIAL:</strong> <?php echo h($inventario['serial']); ?></div>
                    <div><strong>UBICACIÓN:</strong> <?php echo h($inventario['ubicacion']); ?></div>
                </div>
            </div>
            <form id="formularioTriage">
                <input type="hidden" name="inventario_id" value="<?php echo $inventario_id; ?>">
                <!-- Técnico Diagnóstico -->
                <div class="section-title">Asignar Técnico Diagnóstico</div>
                <div class="form-group">
                    <label for="tecnico_diagnostico">Técnico Diagnóstico</label>
                    <select id="tecnico_diagnostico" name="tecnico_diagnostico">
                        <option value="">-- Seleccionar --</option>
                        <?php foreach ($tecnicos as $tecnico): 
                            $selected = (obtenerValor($ultimoMantenimiento, 'tecnico_diagnostico') == $tecnico['id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $tecnico['id']; ?>" <?php echo $selected; ?>><?php echo h($tecnico['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Limpieza y Mantenimiento -->
                <div class="section-title">Limpieza y Mantenimiento</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="limpieza_electronico">Limpieza Electrónico</label>
                        <select id="limpieza_electronico" name="limpieza_electronico">
                            <?php $val = obtenerValor($ultimoMantenimiento, 'limpieza_electronico', 'pendiente'); ?>
                            <option value="pendiente" <?php echo ($val === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="realizada" <?php echo ($val === 'realizada') ? 'selected' : ''; ?>>Realizada</option>
                            <option value="no_aplica" <?php echo ($val === 'no_aplica') ? 'selected' : ''; ?>>No Aplica</option>
                        </select>
                        <div id="obs_limpieza_block" class="<?php echo ($val !== 'realizada') ? 'hidden' : ''; ?>" style="margin-top: 10px;">
                            <label for="obs_limpieza">Observaciones Limpieza</label>
                            <textarea id="obs_limpieza" name="observaciones_limpieza_electronico" rows="2"><?php echo h(obtenerValor($ultimoMantenimiento, 'observaciones_limpieza_electronico')); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mantenimiento_crema">Mantenimiento (Crema Disciplinaria)</label>
                        <?php $valC = obtenerValor($ultimoMantenimiento, 'mantenimiento_crema_disciplinaria', 'pendiente'); ?>
                        <select id="mantenimiento_crema" name="mantenimiento_crema_disciplinaria">
                            <option value="pendiente" <?php echo ($valC === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="realizada" <?php echo ($valC === 'realizada') ? 'selected' : ''; ?>>Realizada</option>
                            <option value="no_aplica" <?php echo ($valC === 'no_aplica') ? 'selected' : ''; ?>>No Aplica</option>
                        </select>
                        <div id="obs_crema_block" class="<?php echo ($valC !== 'realizada') ? 'hidden' : ''; ?>" style="margin-top: 10px;">
                            <label for="obs_crema">Observaciones Crema</label>
                            <textarea id="obs_crema" name="observaciones_mantenimiento_crema" rows="2"><?php echo h(obtenerValor($ultimoMantenimiento, 'observaciones_mantenimiento_crema')); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="mantenimiento_partes">Mantenimiento Partes</label>
                        <?php $valP = obtenerValor($ultimoMantenimiento, 'mantenimiento_partes', 'pendiente'); ?>
                        <select id="mantenimiento_partes" name="mantenimiento_partes">
                            <option value="pendiente" <?php echo ($valP === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="realizada" <?php echo ($valP === 'realizada') ? 'selected' : ''; ?>>Realizada</option>
                            <option value="no_aplica" <?php echo ($valP === 'no_aplica') ? 'selected' : ''; ?>>No Aplica</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cambio_piezas">Cambio Piezas</label>
                        <?php $valCP = obtenerValor($ultimoMantenimiento, 'cambio_piezas', 'no'); ?>
                        <select id="cambio_piezas" name="cambio_piezas">
                            <option value="no" <?php echo ($valCP === 'no') ? 'selected' : ''; ?>>No</option>
                            <option value="si" <?php echo ($valCP === 'si') ? 'selected' : ''; ?>>Sí</option>
                        </select>
                        <div id="piezas_block" class="<?php echo ($valCP !== 'si') ? 'hidden' : ''; ?>" style="margin-top: 10px;">
                            <label for="piezas_cambiadas">Piezas Solicitadas/Cambiadas</label>
                            <input type="text" id="piezas_cambiadas" name="piezas_solicitadas_cambiadas" value="<?php echo h(obtenerValor($ultimoMantenimiento, 'piezas_solicitadas_cambiadas')); ?>">
                        </div>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="proceso_reconstruccion">Proceso Reconstrucción</label>
                        <?php $valR = obtenerValor($ultimoMantenimiento, 'proceso_reconstruccion', 'no'); ?>
                        <select id="proceso_reconstruccion" name="proceso_reconstruccion">
                            <option value="no" <?php echo ($valR === 'no') ? 'selected' : ''; ?>>No</option>
                            <option value="si" <?php echo ($valR === 'si') ? 'selected' : ''; ?>>Sí</option>
                        </select>
                        <div id="parte_block" class="<?php echo ($valR !== 'si') ? 'hidden' : ''; ?>" style="margin-top: 10px;">
                            <label for="parte_reconstruida">Parte Reconstruida</label>
                            <input type="text" id="parte_reconstruida" name="parte_reconstruida" value="<?php echo h(obtenerValor($ultimoMantenimiento, 'parte_reconstruida')); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="limpieza_general">Limpieza General</label>
                        <?php $valLG = obtenerValor($ultimoMantenimiento, 'limpieza_general', 'pendiente'); ?>
                        <select id="limpieza_general" name="limpieza_general">
                            <option value="pendiente" <?php echo ($valLG === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="realizada" <?php echo ($valLG === 'realizada') ? 'selected' : ''; ?>>Realizada</option>
                            <option value="no_aplica" <?php echo ($valLG === 'no_aplica') ? 'selected' : ''; ?>>No Aplica</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remite_otra_area">Remite a Otra Área</label>
                    <?php $valRA = obtenerValor($ultimoMantenimiento, 'remite_otra_area', 'no'); ?>
                    <select id="remite_otra_area" name="remite_otra_area">
                        <option value="no" <?php echo ($valRA === 'no') ? 'selected' : ''; ?>>No</option>
                        <option value="si" <?php echo ($valRA === 'si') ? 'selected' : ''; ?>>Sí</option>
                    </select>
                </div>
                <div id="area_block" class="form-group>
                    <label for="area_remite">Área a la que Remite</label>
                    <select id="area_remite" name="area_remite">
                        <option value="">-- Seleccionar --</option>
                        
                            <option value=></option>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label for="proceso_electronico">Proceso Electrónico (Detalle)</label>
                    <textarea id="proceso_electronico" name="proceso_electronico" rows="3"><?php echo h(obtenerValor($ultimoMantenimiento, 'proceso_electronico')); ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="observaciones_globales">Observaciones Globales</label>
                    <textarea id="observaciones_globales" name="observaciones_globales" rows="3"><?php echo h(obtenerValor($ultimoMantenimiento, 'observaciones_globales')); ?></textarea>
                </div>
                <div id="alertas"></div>
                <div style="text-align: center; margin-top: 25px;">
                    <button type="button" id="btnGuardar" class="btn">GUARDAR Mantenimiento Y Limpieza</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos del formulario
        const elementos = {
            cambio_piezas: { select: '#cambio_piezas', block: '#piezas_block', valor: 'si' },
            remite_otra_area: { select: '#remite_otra_area', block: '#area_block', valor: 'si' },
            proceso_reconstruccion: { select: '#proceso_reconstruccion', block: '#parte_block', valor: 'si' },
            limpieza_electronico: { select: '#limpieza_electronico', block: '#obs_limpieza_block', valor: 'realizada' },
            mantenimiento_crema: { select: '#mantenimiento_crema', block: '#obs_crema_block', valor: 'realizada' }
        };
        // Configurar eventos de cambio
        Object.keys(elementos).forEach(function(key) {
            const config = elementos[key];
            const selectElement = document.querySelector(config.select);
            const blockElement = document.querySelector(config.block);
            
            if (selectElement && blockElement) {
                selectElement.addEventListener('change', function() {
                    if (this.value === config.valor) {
                        blockElement.classList.remove('hidden');
                    } else {
                        blockElement.classList.add('hidden');
                    }
                });
                // Ejecutar al cargar
                selectElement.dispatchEvent(new Event('change'));
            }
        });
        // Funciones de alerta
        function mostrarAlerta(tipo, mensaje) {
            const alertas = document.getElementById('alertas');
            alertas.innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
            setTimeout(() => {
                alertas.innerHTML = '';
            }, 5000);
        }
        // Validación del formulario
        function validarFormulario() {
            const inventarioId = document.querySelector('input[name="inventario_id"]').value;
            if (!inventarioId || parseInt(inventarioId) <= 0) {
                mostrarAlerta('error', 'ID de inventario inválido');
                return false;
            }
            const cambioPiezas = document.getElementById('cambio_piezas');
            if (cambioPiezas && cambioPiezas.value === 'si') {
                const piezas = document.getElementById('piezas_cambiadas');
                if (!piezas || piezas.value.trim() === '') {
                    mostrarAlerta('error', 'Debe especificar qué piezas se cambiaron');
                    piezas.focus();
                    return false;
                }
            }
            const remiteArea = document.getElementById('remite_otra_area');
            if (remiteArea && remiteArea.value === 'si') {
                const area = document.getElementById('area_remite');
                if (!area || area.value.trim() === '') {
                    mostrarAlerta('error', 'Debe seleccionar el área a la que remite');
                    area.focus();
                    return false;
                }
            }
            return true;
        }
        // Manejar envío del formulario
        document.getElementById('btnGuardar').addEventListener('click', function() {
            if (!validarFormulario()) return;
            const btn = this;
            const textoOriginal = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Guardando...';
            const formulario = document.getElementById('formularioTriage');
            const datosFormulario = new FormData(formulario);
            
            // La ruta al backend es relativa al archivo actual.
            // frontend/laboratorio/ingresar_myl.php -> ../../backend/php/st_ingresar_mantenimiento.php
            const rutaBackend = '../../backend/php/st_ingresar_mantenimiento.php';

            fetch(rutaBackend, {
                method: 'POST',
                body: datosFormulario,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    // Si el status no es 2xx, leemos el texto para ver el error
                    return response.text().then(text => { 
                        throw new Error(`Error del servidor: ${response.status} ${response.statusText}. Detalles: ${text}`);
                    });
                }
                return response.json();
            })
            .then(respuesta => {
                if (respuesta.status === 'success') {
                    mostrarAlerta('success', respuesta.message || 'Datos guardados correctamente');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarAlerta('error', respuesta.message || 'Error al guardar los datos');
                }
            })
            .catch(error => {
                console.error('Error en el fetch:', error);
                mostrarAlerta('error', 'Error de conexión: ' + error.message);
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = textoOriginal;
            });
        });
    });
    </script>
</body>
</html>
