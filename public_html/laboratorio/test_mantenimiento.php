<?php
session_start();
require_once '../../config/ctconex.php';

// Listar equipos disponibles para probar
$sql = "SELECT id, codigo_g, producto, marca, modelo, serial, disposicion 
        FROM bodega_inventario 
        WHERE disposicion IN ('en_diagnostico', 'en_proceso') 
        ORDER BY fecha_ingreso DESC 
        LIMIT 10";
$stmt = $connect->prepare($sql);
$stmt->execute();
$equipos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Sistema de Mantenimiento</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .equipo { 
            background: #f8f9fa; 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 8px; 
            border-left: 4px solid #007bff;
        }
        .equipo h3 { margin: 0 0 10px 0; color: #007bff; }
        .equipo p { margin: 5px 0; }
        .btn { 
            background: #007bff; 
            color: white; 
            padding: 8px 16px; 
            text-decoration: none; 
            border-radius: 4px; 
            display: inline-block;
        }
        .btn:hover { background: #0056b3; }
        .status { 
            padding: 4px 8px; 
            border-radius: 4px; 
            font-size: 12px; 
            font-weight: bold;
        }
        .status-diagnostico { background: #ffc107; color: #212529; }
        .status-proceso { background: #17a2b8; color: white; }
    </style>
</head>
<body>
    <h1>Test - Sistema de Mantenimiento y Limpieza</h1>
    <p>Selecciona un equipo para ir al formulario de mantenimiento:</p>
    
    <?php if (empty($equipos)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px;">
            No hay equipos disponibles para mantenimiento.
        </div>
    <?php else: ?>
        <?php foreach ($equipos as $equipo): ?>
            <div class="equipo">
                <h3><?php echo htmlspecialchars($equipo['codigo_g']); ?></h3>
                <p><strong>Producto:</strong> <?php echo htmlspecialchars($equipo['producto']); ?></p>
                <p><strong>Marca/Modelo:</strong> <?php echo htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']); ?></p>
                <p><strong>Serial:</strong> <?php echo htmlspecialchars($equipo['serial']); ?></p>
                <p><strong>Estado:</strong> 
                    <span class="status status-<?php echo str_replace('en_', '', $equipo['disposicion']); ?>">
                        <?php echo htmlspecialchars($equipo['disposicion']); ?>
                    </span>
                </p>
                <a href="ingresar_m.php?id=<?php echo $equipo['id']; ?>" class="btn">
                    Ir a Mantenimiento
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <hr style="margin: 30px 0;">
    
    <h2>Verificar Base de Datos</h2>
    <p>Para verificar que todo funcione correctamente:</p>
    <ul>
        <li>Selecciona un equipo de la lista arriba</li>
        <li>Completa el formulario de mantenimiento</li>
        <li>Verifica que los datos se guarden en la tabla <code>bodega_mantenimiento</code></li>
        <li>Verifica que el estado del inventario cambie a "en_mantenimiento"</li>
    </ul>
    
    <h3>Estructura de la Base de Datos</h3>
    <p><strong>Tabla origen:</strong> <code>bodega_diagnosticos</code> (datos del TRIAGE 2)</p>
    <p><strong>Tabla destino:</strong> <code>bodega_mantenimiento</code> (datos del formulario)</p>
    <p><strong>Relación:</strong> Ambas tablas se conectan por <code>inventario_id</code></p>
</body>
</html>
