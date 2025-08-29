









<?php
session_start();
require_once '../../config/ctconex.php';

// Listar posiciones disponibles para probar
$sql = "SELECT DISTINCT posicion, COUNT(*) as total_equipos 
        FROM bodega_inventario 
        WHERE posicion IS NOT NULL AND posicion != '' 
        GROUP BY posicion 
        ORDER BY posicion 
        LIMIT 20";
$stmt = $connect->prepare($sql);
$stmt->execute();
$posiciones = $stmt->fetchAll();

// Obtener estadísticas
$sql_stats = "SELECT 
                COUNT(*) as total_equipos,
                COUNT(CASE WHEN fecha_modificacion IS NOT NULL THEN 1 END) as equipos_modificados,
                COUNT(CASE WHEN fecha_modificacion IS NULL THEN 1 END) as equipos_sin_modificar
              FROM bodega_inventario";
$stmt_stats = $connect->prepare($sql_stats);
$stmt_stats->execute();
$stats = $stmt_stats->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Sistema de Edición de Equipos</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5f6fa;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
        }
        .header { 
            background: linear-gradient(135deg, #e74c3c, #c0392b); 
            color: white; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 20px;
            text-align: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #e74c3c;
        }
        .stat-label {
            color: #7f8c8d;
            margin-top: 10px;
        }
        .posiciones-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .posicion-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .posicion-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .posicion-count {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        .test-links {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-link {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px;
            transition: 0.3s;
        }
        .test-link:hover {
            background: #229954;
            transform: translateY(-2px);
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
        .warning {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 TEST - Sistema de Edición de Equipos</h1>
            <p>Verificación del funcionamiento del sistema de edición por posición</p>
        </div>

        <div class="instructions">
            <h3>📋 Instrucciones de Prueba</h3>
            <ul>
                <li><strong>1.</strong> Revisa las estadísticas del inventario</li>
                <li><strong>2.</strong> Selecciona una posición de la lista para probar</li>
                <li><strong>3.</strong> Haz clic en "Probar Edición" para ir al sistema</li>
                <li><strong>4.</strong> Verifica que la búsqueda funcione correctamente</li>
                <li><strong>5.</strong> Prueba editar algunos campos del equipo</li>
            </ul>
        </div>

        <!-- Estadísticas del Inventario -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['total_equipos']); ?></div>
                <div class="stat-label">Total de Equipos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['equipos_modificados']); ?></div>
                <div class="stat-label">Equipos Modificados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['equipos_sin_modificar']); ?></div>
                <div class="stat-label">Equipos Sin Modificar</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($posiciones); ?></div>
                <div class="stat-label">Posiciones Únicas</div>
            </div>
        </div>

        <!-- Posiciones Disponibles para Probar -->
        <div class="test-links">
            <h2>🔍 Posiciones Disponibles para Probar</h2>
            <p>Selecciona una posición para probar el sistema de edición:</p>
            
            <div class="posiciones-grid">
                <?php foreach ($posiciones as $posicion): ?>
                <div class="posicion-card">
                    <div class="posicion-title">📦 <?php echo htmlspecialchars($posicion['posicion']); ?></div>
                    <div class="posicion-count"><?php echo $posicion['total_equipos']; ?> equipo(s)</div>
                    <a href="editar_equipo.php?test_posicion=<?php echo urlencode($posicion['posicion']); ?>" 
                       class="test-link">Probar Edición</a>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($posiciones)): ?>
            <div class="warning">
                <strong>⚠️ No se encontraron posiciones</strong><br>
                Verifica que la tabla `bodega_inventario` tenga datos y que el campo `posicion` esté poblado.
            </div>
            <?php endif; ?>
        </div>

        <!-- Enlaces de Prueba -->
        <div class="test-links">
            <h2>🚀 Enlaces de Prueba</h2>
            <p>Acceso directo a las funcionalidades del sistema:</p>
            
            <a href="editar_equipo.php" class="test-link">📝 Sistema de Edición Principal</a>
            <a href="../../backend/php/test_conexion.php" class="test-link">🔌 Test de Conexión Backend</a>
            
            <h3 style="margin-top: 30px;">📊 Verificar Base de Datos</h3>
            <p>Ejecuta estos scripts SQL para verificar la estructura:</p>
            <ul>
                <li><strong>Crear tabla de log:</strong> <code>config/ctconex.phpcrear_tabla_log.sql</code></li>
                <li><strong>Verificar estructura:</strong> <code>config/ctconex.phpEstructuraDB.sql</code></li>
            </ul>
        </div>

        <!-- Información del Sistema -->
        <div class="test-links">
            <h2>ℹ️ Información del Sistema</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h4>📁 Archivos del Sistema</h4>
                    <ul>
                        <li><strong>public_html:</strong> <code>public_html/bodega/editar_equipo.php</code></li>
                        <li><strong>Backend Edición:</strong> <code>backend/php/editar_equipo.php</code></li>
                        <li><strong>Backend Búsqueda:</strong> <code>backend/php/buscar_equipo_posicion.php</code></li>
                        <li><strong>Documentación:</strong> <code>README_EDICION_EQUIPOS.md</code></li>
                    </ul>
                </div>
                <div>
                    <h4>🔧 Configuración</h4>
                    <ul>
                        <li><strong>Conexión BD:</strong> <code>config/ctconex.php</code></li>
                        <li><strong>Tabla Principal:</strong> <code>bodega_inventario</code></li>
                        <li><strong>Tabla Log:</strong> <code>bodega_log_cambios</code> (opcional)</li>
                        <li><strong>Campos Editables:</strong> 8 campos configurables</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Estado de la Instalación -->
        <div class="test-links">
            <h2>✅ Estado de la Instalación</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h4>✅ Completado</h4>
                    <ul style="color: #27ae60;">
                        <li>Sistema de edición completo</li>
                        <li>Backend funcional</li>
                        <li>public_html responsivo</li>
                        <li>Documentación completa</li>
                        <li>Scripts SQL preparados</li>
                    </ul>
                </div>
                <div>
                    <h4>⚠️ Requiere Verificación</h4>
                    <ul style="color: #f39c12;">
                        <li>Conexión a base de datos</li>
                        <li>Permisos de usuario</li>
                        <li>Existencia de datos de prueba</li>
                        <li>Tabla de log (opcional)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-redirección si se pasa posición por URL
        const urlParams = new URLSearchParams(window.location.search);
        const testPosicion = urlParams.get('test_posicion');
        
        if (testPosicion) {
            // Redirigir al sistema de edición con la posición pre-llenada
            window.location.href = `editar_equipo.php?posicion=${encodeURIComponent(testPosicion)}`;
        }
        
        // Función para probar conexión
        function testConexion() {
            fetch('../../backend/php/test_conexion.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Conexión exitosa: ' + data.message);
                    } else {
                        alert('❌ Error de conexión: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('❌ Error de conexión: ' + error.message);
                });
        }
    </script>
</body>
</html>
