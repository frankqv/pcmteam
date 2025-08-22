<?php
session_start();
require_once '../../config/ctconex.php';

// Obtener ID del inventario desde la URL
$inventario_id = $_GET['id'] ?? null;

if (!$inventario_id) {
    echo "Error: ID de inventario no proporcionado";
    exit;
}

// Obtener datos del inventario
$sql_inventario = "SELECT * FROM bodega_inventario WHERE id = ?";
$stmt_inventario = $connect->prepare($sql_inventario);
$stmt_inventario->execute([$inventario_id]);
$inventario = $stmt_inventario->fetch();

if (!$inventario) {
    echo "Error: Equipo no encontrado";
    exit;
}

// Obtener el último diagnóstico
$sql_diagnostico = "SELECT * FROM bodega_diagnosticos 
                    WHERE inventario_id = ? 
                    ORDER BY fecha_diagnostico DESC 
                    LIMIT 1";
$stmt_diagnostico = $connect->prepare($sql_diagnostico);
$stmt_diagnostico->execute([$inventario_id]);
$diagnostico = $stmt_diagnostico->fetch();

// Obtener técnicos disponibles
$sql_tecnicos = "SELECT id, nombre FROM usuarios WHERE rol = '6' AND estado = '1'";
$stmt_tecnicos = $connect->prepare($sql_tecnicos);
$stmt_tecnicos->execute();
$tecnicos = $stmt_tecnicos->fetchAll();

// Obtener último mantenimiento si existe
$sql_mantenimiento = "SELECT * FROM bodega_mantenimiento 
                      WHERE inventario_id = ? 
                      ORDER BY fecha_registro DESC 
                      LIMIT 1";
$stmt_mantenimiento = $connect->prepare($sql_mantenimiento);
$stmt_mantenimiento->execute([$inventario_id]);
$ultimo_mantenimiento = $stmt_mantenimiento->fetch();

// Función helper para obtener valores
function obtenerValor($array, $key, $default = '') {
    return $array[$key] ?? $default;
}

// Función helper para escapar HTML
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limpieza y Mantenimiento - <?php echo h($inventario['codigo_g']); ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f6fa; 
            color: #2c3e50;
            line-height: 1.6;
        }
        .container { 
            max-width: 1400px; 
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
        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
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
        .diagnostico-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            border-left: 4px solid #3498db;
        }
        .diagnostico-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .diagnostico-value {
            color: #7f8c8d;
            font-size: 14px;
        }
        .status-bueno { color: #27ae60; font-weight: 600; }
        .status-malo { color: #e74c3c; font-weight: 600; }
        .status-nd { color: #f39c12; font-weight: 600; }
        @media (max-width: 1200px) {
            .main-content { grid-template-columns: 1fr; }
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
            <p>Equipo: <?php echo h($inventario['codigo_g']); ?> - <?php echo h($inventario['producto'] . ' ' . $inventario['marca'] . ' ' . $inventario['modelo']); ?></p>
        </div>
        
        <div class="main-content">
            <!-- LADO IZQUIERDO: DATOS DEL DIAGNÓSTICO -->
            <div class="card">
                <div class="section-title">Resultados del TRIAGE 2</div>
                
                <?php if ($diagnostico): ?>
                    <div class="diagnostico-item">
                        <div class="diagnostico-label">Fecha del Diagnóstico:</div>
                        <div class="diagnostico-value"><?php echo date('d/m/Y H:i', strtotime($diagnostico['fecha_diagnostico'])); ?></div>
                    </div>
                    
                    <div class="diagnostico-item">
                        <div class="diagnostico-label">Cámara:</div>
                        <div class="diagnostico-value <?php echo 'status-' . strtolower($diagnostico['camara']); ?>">
                            <?php echo h($diagnostico['camara']); ?>
                        </div>
                    </div>
                    
                    <div class="diagnostico-item">
                        <div class="diagnostico-label">Teclado:</div>
                        <div class="diagnostico-value <?php echo 'status-' . strtolower($diagnostico['teclado']); ?>">
                            <?php echo h($diagnostico['teclado']); ?>
                        </div>
                    </div>
                    
                    <div class="diagnostico-item">
                        <div class="diagnostico-label">Parlantes:</div>
                        <div class="diagnostico-value <?php echo 'status-' . strtolower($diagnostico['parlantes']); ?>">
                            <?php echo h($diagnostico['parlantes']); ?>
                        </div>
                    </div>
                    
                    <div class="diagnostico-item">
                        <div class="diagnostico-label">Batería:</div>
                        <div class="diagnostico-value <?php echo 'status-' . strtolower($diagnostico['bateria']); ?>">
                            <?php echo h($diagnostico['bateria']); ?>
                        </div>
                    </div>
                    
                    <div class="diagnostico-item">
                        <div class="diagnostico-label">Micrófono:</div>
                        <div class="diagnostico-value <?php echo 'status-' . strtolower($diagnostico['microfono']); ?>">
                            <?php echo h($diagnostico['microfono']); ?>
                        </div>
                    </div>
                    
                    <div class="diagnostico-item">
                        <div class="diagnostico-label">Pantalla:</div>
                        <div class="diagnostico-value <?php echo 'status-' . strtolower($diagnostico['pantalla']); ?>">
                            <?php echo h($diagnostico['pantalla']); ?>
                        </div>
                    </div>
                    
                    <div class="diagnostico-item">
                        <div class="diagnostico-label">Puertos:</div>
                        <div class="diagnostico-value">
                            <?php 
                            if ($diagnostico['puertos']) {
                                $puertos = json_decode($diagnostico['puertos'], true);
                                if (is_array($puertos)) {
                                    foreach ($puertos as $puerto => $estado) {
                                        echo "<div><strong>$puerto:</strong> <span class='status-" . strtolower($estado) . "'>$estado</span></div>";
                                    }
                                } else {
                                    echo h($diagnostico['puertos']);
                                }
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="diagnostico-item">
                        <div class="diagnostico-label">Disco:</div>
                        <div class="diagnostico-value"><?php echo h($diagnostico['disco']); ?></div>
                    </div>
                    
                    <div class="diagnostico-item">
                        <div class="diagnostico-label">Estado de Reparación:</div>
                        <div class="diagnostico-value"><?php echo h($diagnostico['estado_reparacion']); ?></div>
                    </div>
                    
                    <?php if ($diagnostico['observaciones']): ?>
                    <div class="diagnostico-item">
                        <div class="diagnostico-label">Observaciones:</div>
                        <div class="diagnostico-value"><?php echo h($diagnostico['observaciones']); ?></div>
                    </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="alert alert-error">
                        No se encontró diagnóstico para este equipo.
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- LADO DERECHO: FORMULARIO DE MANTENIMIENTO -->
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
                        <div><strong>POSICIÓN:</strong> <?php echo h($inventario['posicion']); ?></div>
                        <div><strong>NOMBRE LOTE:</strong> <?php echo h($inventario['lote']); ?></div>
                    </div>
                </div>
                
                <!-- NUEVA SECCIÓN: EDITAR DATOS DEL EQUIPO -->
                <div class="section-title">✏️ Editar Datos del Equipo</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_modelo">Modelo:</label>
                        <input type="text" id="edit_modelo" name="edit_modelo" value="<?php echo h($inventario['modelo']); ?>" placeholder="Ej: Dell Latitude 3420">
                    </div>
                    <div class="form-group">
                        <label for="edit_procesador">Procesador:</label>
                        <input type="text" id="edit_procesador" name="edit_procesador" value="<?php echo h($inventario['procesador']); ?>" placeholder="Ej: Intel i5 11th Gen">
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_ram">RAM:</label>
                        <input type="text" id="edit_ram" name="edit_ram" value="<?php echo h($inventario['ram']); ?>" placeholder="Ej: 8GB, 16GB">
                    </div>
                    <div class="form-group">
                        <label for="edit_disco">Disco:</label>
                        <input type="text" id="edit_disco" name="edit_disco" value="<?php echo h($inventario['disco']); ?>" placeholder="Ej: 256GB SSD">
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_pulgadas">Pulgadas:</label>
                        <input type="text" id="edit_pulgadas" name="edit_pulgadas" value="<?php echo h($inventario['pulgadas']); ?>" placeholder="Ej: 14, 15.6">
                    </div>
                    <div class="form-group">
                        <label for="edit_grado">Grado:</label>
                        <select id="edit_grado" name="edit_grado">
                            <option value="">-- Seleccionar --</option>
                            <option value="A" <?php echo ($inventario['grado'] === 'A') ? 'selected' : ''; ?>>A - Excelente</option>
                            <option value="B" <?php echo ($inventario['grado'] === 'B') ? 'selected' : ''; ?>>B - Bueno</option>
                            <option value="C" <?php echo ($inventario['grado'] === 'C') ? 'selected' : ''; ?>>C - Regular</option>
                            <option value="SCRAP" <?php echo ($inventario['grado'] === 'SCRAP') ? 'selected' : ''; ?>>SCRAP</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_tactil">Táctil:</label>
                        <select id="edit_tactil" name="edit_tactil">
                            <option value="">-- Seleccionar --</option>
                            <option value="SI" <?php echo ($inventario['tactil'] === 'SI') ? 'selected' : ''; ?>>Sí</option>
                            <option value="NO" <?php echo ($inventario['tactil'] === 'NO') ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_activo_fijo">Activo Fijo:</label>
                        <input type="text" id="edit_activo_fijo" name="edit_activo_fijo" value="<?php echo h($inventario['activo_fijo']); ?>" placeholder="Código u observación">
                    </div>
                </div>
                
                <div style="text-align: center; margin: 20px 0;">
                    <button type="button" id="btnGuardarEquipo" class="btn" style="background: #e67e22;">💾 Guardar Cambios del Equipo</button>
                </div>
                
                <form id="formularioMantenimiento">
                    <input type="hidden" name="inventario_id" value="<?php echo $inventario_id; ?>">
                    
                    <!-- Técnico Diagnóstico -->
                    <div class="section-title">Asignar Técnico Diagnóstico</div>
                    <div class="form-group">
                        <label for="tecnico_diagnostico">Técnico Diagnóstico</label>
                        <select id="tecnico_diagnostico" name="tecnico_diagnostico" required>
                            <option value="">-- Seleccionar --</option>
                            <?php foreach ($tecnicos as $tecnico): 
                                $selected = (obtenerValor($ultimo_mantenimiento, 'tecnico_diagnostico') == $tecnico['id']) ? 'selected' : '';
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
                            <?php $val = obtenerValor($ultimo_mantenimiento, 'limpieza_electronico', 'pendiente'); ?>
                            <select id="limpieza_electronico" name="limpieza_electronico">
                                <option value="pendiente" <?php echo ($val === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="realizada" <?php echo ($val === 'realizada') ? 'selected' : ''; ?>>Realizada</option>
                                <option value="no_aplica" <?php echo ($val === 'no_aplica') ? 'selected' : ''; ?>>No Aplica</option>
                            </select>
                            <div id="obs_limpieza_block" class="<?php echo ($val !== 'realizada') ? 'hidden' : ''; ?>" style="margin-top: 10px;">
                                <label for="obs_limpieza">Observaciones Limpieza</label>
                                <textarea id="obs_limpieza" name="observaciones_limpieza_electronico" rows="2"><?php echo h(obtenerValor($ultimo_mantenimiento, 'observaciones_limpieza_electronico')); ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="mantenimiento_crema">Mantenimiento (Crema Disciplinaria)</label>
                            <?php $valC = obtenerValor($ultimo_mantenimiento, 'mantenimiento_crema_disciplinaria', 'pendiente'); ?>
                            <select id="mantenimiento_crema" name="mantenimiento_crema_disciplinaria">
                                <option value="pendiente" <?php echo ($valC === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="realizada" <?php echo ($valC === 'realizada') ? 'selected' : ''; ?>>Realizada</option>
                                <option value="no_aplica" <?php echo ($valC === 'no_aplica') ? 'selected' : ''; ?>>No Aplica</option>
                            </select>
                            <div id="obs_crema_block" class="<?php echo ($valC !== 'realizada') ? 'hidden' : ''; ?>" style="margin-top: 10px;">
                                <label for="obs_crema">Observaciones Crema</label>
                                <textarea id="obs_crema" name="observaciones_mantenimiento_crema" rows="2"><?php echo h(obtenerValor($ultimo_mantenimiento, 'observaciones_mantenimiento_crema')); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="mantenimiento_partes">Mantenimiento Partes</label>
                            <?php $valP = obtenerValor($ultimo_mantenimiento, 'mantenimiento_partes', 'pendiente'); ?>
                            <select id="mantenimiento_partes" name="mantenimiento_partes">
                                <option value="pendiente" <?php echo ($valP === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="realizada" <?php echo ($valP === 'realizada') ? 'selected' : ''; ?>>Realizada</option>
                                <option value="no_aplica" <?php echo ($valP === 'no_aplica') ? 'selected' : ''; ?>>No Aplica</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cambio_piezas">Cambio Piezas</label>
                            <?php $valCP = obtenerValor($ultimo_mantenimiento, 'cambio_piezas', 'no'); ?>
                            <select id="cambio_piezas" name="cambio_piezas">
                                <option value="no" <?php echo ($valCP === 'no') ? 'selected' : ''; ?>>No</option>
                                <option value="si" <?php echo ($valCP === 'si') ? 'selected' : ''; ?>>Sí</option>
                            </select>
                            <div id="piezas_block" class="<?php echo ($valCP !== 'si') ? 'hidden' : ''; ?>" style="margin-top: 10px;">
                                <label for="piezas_cambiadas">Piezas Solicitadas/Cambiadas</label>
                                <input type="text" id="piezas_cambiadas" name="piezas_solicitadas_cambiadas" value="<?php echo h(obtenerValor($ultimo_mantenimiento, 'piezas_solicitadas_cambiadas')); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="proceso_reconstruccion">Proceso Reconstrucción</label>
                            <?php $valR = obtenerValor($ultimo_mantenimiento, 'proceso_reconstruccion', 'no'); ?>
                            <select id="proceso_reconstruccion" name="proceso_reconstruccion">
                                <option value="no" <?php echo ($valR === 'no') ? 'selected' : ''; ?>>No</option>
                                <option value="si" <?php echo ($valR === 'si') ? 'selected' : ''; ?>>Sí</option>
                            </select>
                            <div id="parte_block" class="<?php echo ($valR !== 'si') ? 'hidden' : ''; ?>" style="margin-top: 10px;">
                                <label for="parte_reconstruida">Parte Reconstruida</label>
                                <input type="text" id="parte_reconstruida" name="parte_reconstruida" value="<?php echo h(obtenerValor($ultimo_mantenimiento, 'parte_reconstruida')); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="limpieza_general">Limpieza General</label>
                            <?php $valLG = obtenerValor($ultimo_mantenimiento, 'limpieza_general', 'pendiente'); ?>
                            <select id="limpieza_general" name="limpieza_general">
                                <option value="pendiente" <?php echo ($valLG === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="realizada" <?php echo ($valLG === 'realizada') ? 'selected' : ''; ?>>Realizada</option>
                                <option value="no_aplica" <?php echo ($valLG === 'no_aplica') ? 'selected' : ''; ?>>No Aplica</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="remite_otra_area">Remite a Otra Área</label>
                        <?php $valRA = obtenerValor($ultimo_mantenimiento, 'remite_otra_area', 'no'); ?>
                        <select id="remite_otra_area" name="remite_otra_area">
                            <option value="no" <?php echo ($valRA === 'no') ? 'selected' : ''; ?>>No</option>
                            <option value="si" <?php echo ($valRA === 'si') ? 'selected' : ''; ?>>Sí</option>
                        </select>
                    </div>
                    
                    <div id="area_block" class="form-group <?php echo ($valRA !== 'si') ? 'hidden' : ''; ?>">
                        <label for="area_remite">Área a la que Remite</label>
                        <select id="area_remite" name="area_remite">
                            <option value="">-- Seleccionar --</option>
                            <option value="bodega" <?php echo (obtenerValor($ultimo_mantenimiento, 'area_remite') === 'bodega') ? 'selected' : ''; ?>>Bodega</option>
                            <option value="laboratorio" <?php echo (obtenerValor($ultimo_mantenimiento, 'area_remite') === 'laboratorio') ? 'selected' : ''; ?>>Laboratorio</option>
                            <option value="control_calidad" <?php echo (obtenerValor($ultimo_mantenimiento, 'area_remite') === 'control_calidad') ? 'selected' : ''; ?>>Control de Calidad</option>
                            <option value="venta" <?php echo (obtenerValor($ultimo_mantenimiento, 'area_remite') === 'venta') ? 'selected' : ''; ?>>Venta</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="proceso_electronico">Proceso Electrónico (Detalle)</label>
                        <textarea id="proceso_electronico" name="proceso_electronico" rows="3"><?php echo h(obtenerValor($ultimo_mantenimiento, 'proceso_electronico')); ?></textarea>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="observaciones_globales">Observaciones Globales</label>
                        <textarea id="observaciones_globales" name="observaciones_globales" rows="3"><?php echo h(obtenerValor($ultimo_mantenimiento, 'observaciones_globales')); ?></textarea>
                    </div>
                    
                    <div id="alertas"></div>
                    
                    <div style="text-align: center; margin-top: 25px;">
                        <button type="button" id="btnGuardar" class="btn">GUARDAR Mantenimiento Y Limpieza</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Funcionalidad JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar/ocultar campos condicionales
            const limpiezaElectronico = document.getElementById('limpieza_electronico');
            const obsLimpiezaBlock = document.getElementById('obs_limpieza_block');
            
            limpiezaElectronico.addEventListener('change', function() {
                if (this.value === 'realizada') {
                    obsLimpiezaBlock.classList.remove('hidden');
                } else {
                    obsLimpiezaBlock.classList.add('hidden');
                }
            });
            
            const mantenimientoCrema = document.getElementById('mantenimiento_crema');
            const obsCremaBlock = document.getElementById('obs_crema_block');
            
            mantenimientoCrema.addEventListener('change', function() {
                if (this.value === 'realizada') {
                    obsCremaBlock.classList.remove('hidden');
                } else {
                    obsCremaBlock.classList.add('hidden');
                }
            });
            
            const cambioPiezas = document.getElementById('cambio_piezas');
            const piezasBlock = document.getElementById('piezas_block');
            
            cambioPiezas.addEventListener('change', function() {
                if (this.value === 'si') {
                    piezasBlock.classList.remove('hidden');
                } else {
                    piezasBlock.classList.add('hidden');
                }
            });
            
            const procesoReconstruccion = document.getElementById('proceso_reconstruccion');
            const parteBlock = document.getElementById('parte_block');
            
            procesoReconstruccion.addEventListener('change', function() {
                if (this.value === 'si') {
                    parteBlock.classList.remove('hidden');
                } else {
                    parteBlock.classList.add('hidden');
                }
            });
            
            const remiteOtraArea = document.getElementById('remite_otra_area');
            const areaBlock = document.getElementById('area_block');
            
            remiteOtraArea.addEventListener('change', function() {
                if (this.value === 'si') {
                    areaBlock.classList.remove('hidden');
                } else {
                    areaBlock.classList.add('hidden');
                }
            });
            
            // Guardar formulario
            document.getElementById('btnGuardar').addEventListener('click', function() {
                guardarMantenimiento();
            });

            // Guardar cambios del equipo
            document.getElementById('btnGuardarEquipo').addEventListener('click', function() {
                guardarCambiosEquipo();
            });
        });
        
        function guardarMantenimiento() {
            const form = document.getElementById('formularioMantenimiento');
            const formData = new FormData(form);
            
            // Convertir FormData a objeto
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            // Validar campos requeridos
            if (!data.tecnico_diagnostico) {
                mostrarAlerta('Debe seleccionar un técnico de diagnóstico', 'error');
                return;
            }
            
            // Deshabilitar botón
            const btnGuardar = document.getElementById('btnGuardar');
            btnGuardar.disabled = true;
            btnGuardar.textContent = 'Guardando...';
            
            // Enviar datos
            fetch('../../backend/php/procesar_mantenimiento.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Respuesta del servidor:', response);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data);
                if (data.success) {
                    mostrarAlerta('✅ ' + data.message + ' - Redirigiendo...', 'success');
                    // Limpiar formulario o redirigir
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    mostrarAlerta('❌ ' + (data.message || 'Error al guardar'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Verificar si es un error de red o de respuesta
                if (error.name === 'TypeError' && error.message.includes('JSON')) {
                    mostrarAlerta('Respuesta del servidor no válida', 'error');
                } else {
                    mostrarAlerta('Error de conexión con el servidor', 'error');
                }
            })
            .finally(() => {
                btnGuardar.disabled = false;
                btnGuardar.textContent = 'GUARDAR Mantenimiento Y Limpieza';
            });
        }

        function guardarCambiosEquipo() {
            const editModelo = document.getElementById('edit_modelo').value;
            const editProcesador = document.getElementById('edit_procesador').value;
            const editRam = document.getElementById('edit_ram').value;
            const editDisco = document.getElementById('edit_disco').value;
            const editPulgadas = document.getElementById('edit_pulgadas').value;
            const editGrado = document.getElementById('edit_grado').value;
            const editTactil = document.getElementById('edit_tactil').value;
            const editActivoFijo = document.getElementById('edit_activo_fijo').value;

            const data = {
                inventario_id: <?php echo $inventario_id; ?>,
                edit_modelo: editModelo,
                edit_procesador: editProcesador,
                edit_ram: editRam,
                edit_disco: editDisco,
                edit_pulgadas: editPulgadas,
                edit_grado: editGrado,
                edit_tactil: editTactil,
                edit_activo_fijo: editActivoFijo
            };

            const btnGuardarEquipo = document.getElementById('btnGuardarEquipo');
            btnGuardarEquipo.disabled = true;
            btnGuardarEquipo.textContent = 'Guardando...';

            fetch('../../backend/php/procesar_cambios_equipo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Respuesta del servidor:', response);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data);
                if (data.success) {
                    mostrarAlerta('✅ ' + data.message + ' - Redirigiendo...', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    mostrarAlerta('❌ ' + (data.message || 'Error al guardar'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (error.name === 'TypeError' && error.message.includes('JSON')) {
                    mostrarAlerta('Respuesta del servidor no válida', 'error');
                } else {
                    mostrarAlerta('Error de conexión con el servidor', 'error');
                }
            })
            .finally(() => {
                btnGuardarEquipo.disabled = false;
                btnGuardarEquipo.textContent = '💾 Guardar Cambios del Equipo';
            });
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
