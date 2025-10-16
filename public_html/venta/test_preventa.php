<?php
// TEST: Verificar conexión y tabla solicitud_alistamiento
session_start();
require_once '../../config/ctconex.php';

echo "<h2>🔍 TEST: Solicitud de Alistamiento</h2>";
echo "<hr>";

// 1. Verificar conexión
echo "<h3>1. Verificar Conexión</h3>";
try {
    $test = $connect->query("SELECT 1");
    echo "✅ Conexión a base de datos: <strong>OK</strong><br>";
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
    exit;
}

// 2. Verificar estructura de tabla
echo "<hr><h3>2. Estructura de Tabla</h3>";
try {
    $stmt = $connect->query("DESCRIBE solicitud_alistamiento");
    $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columnas as $col) {
        $extra_class = $col['Extra'] == 'auto_increment' ? "style='background: #90EE90;'" : "";
        echo "<tr $extra_class>";
        echo "<td><strong>{$col['Field']}</strong></td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td><strong>{$col['Extra']}</strong></td>";
        echo "</tr>";
    }
    echo "</table>";

    // Verificar AUTO_INCREMENT
    $hasAutoIncrement = false;
    foreach ($columnas as $col) {
        if ($col['Extra'] == 'auto_increment') {
            $hasAutoIncrement = true;
            echo "<p>✅ Campo <strong>{$col['Field']}</strong> tiene AUTO_INCREMENT</p>";
        }
    }

    if (!$hasAutoIncrement) {
        echo "<p>❌ <strong>PROBLEMA:</strong> No hay campo con AUTO_INCREMENT</p>";
        echo "<p>🔧 <strong>Solución:</strong> Ejecuta este SQL:</p>";
        echo "<pre style='background: #f5f5f5; padding: 10px;'>";
        echo "ALTER TABLE solicitud_alistamiento\n";
        echo "MODIFY id INT NOT NULL AUTO_INCREMENT,\n";
        echo "ADD PRIMARY KEY (id);";
        echo "</pre>";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

// 3. Contar registros
echo "<hr><h3>3. Registros Existentes</h3>";
try {
    $stmt = $connect->query("SELECT COUNT(*) as total FROM solicitud_alistamiento");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total de registros: <strong>{$result['total']}</strong><br>";

    if ($result['total'] > 0) {
        $stmt = $connect->query("SELECT id, solicitante, sede, fecha_solicitud, estado FROM solicitud_alistamiento ORDER BY id DESC LIMIT 5");
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h4>Últimos 5 registros:</h4>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Solicitante</th><th>Sede</th><th>Fecha</th><th>Estado</th></tr>";
        foreach ($registros as $reg) {
            echo "<tr>";
            echo "<td>{$reg['id']}</td>";
            echo "<td>{$reg['solicitante']}</td>";
            echo "<td>{$reg['sede']}</td>";
            echo "<td>{$reg['fecha_solicitud']}</td>";
            echo "<td>{$reg['estado']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

// 4. Test de INSERT
echo "<hr><h3>4. Test de INSERT</h3>";
try {
    $connect->beginTransaction();

    $sql = "INSERT INTO solicitud_alistamiento (
                solicitante,
                usuario_id,
                sede,
                cliente,
                cantidad,
                descripcion,
                marca,
                modelo,
                observacion,
                tecnico_responsable,
                estado
            ) VALUES (
                'TEST_USER',
                1,
                'TEST_SEDE',
                'TEST_CLIENTE',
                '1',
                'TEST DESCRIPCION',
                'TEST_MARCA',
                'TEST_MODELO',
                'TEST_OBSERVACION',
                NULL,
                'pendiente'
            )";

    $stmt = $connect->prepare($sql);
    $resultado = $stmt->execute();

    $lastId = $connect->lastInsertId();

    if ($resultado && $lastId > 0) {
        echo "✅ INSERT exitoso<br>";
        echo "ID generado: <strong>$lastId</strong><br>";

        // Eliminar el registro de prueba
        $connect->query("DELETE FROM solicitud_alistamiento WHERE id = $lastId");
        echo "✅ Registro de prueba eliminado<br>";
    } else {
        echo "❌ INSERT falló pero no lanzó excepción<br>";
    }

    $connect->commit();
    echo "<p>✅ <strong>La tabla funciona correctamente</strong></p>";

} catch (PDOException $e) {
    $connect->rollBack();
    echo "❌ Error en INSERT: " . $e->getMessage() . "<br>";
    echo "Código: " . $e->getCode() . "<br>";
}

// 5. Verificar sesión
echo "<hr><h3>5. Verificar Sesión</h3>";
if (isset($_SESSION['id'])) {
    echo "✅ Usuario ID: <strong>{$_SESSION['id']}</strong><br>";
    echo "✅ Usuario Nombre: <strong>{$_SESSION['nombre']}</strong><br>";
    echo "✅ Usuario Rol: <strong>{$_SESSION['rol']}</strong><br>";
} else {
    echo "❌ No hay sesión activa<br>";
}

echo "<hr>";
echo "<p><a href='preventa.php'>← Volver a Pre-venta</a></p>";
?>
