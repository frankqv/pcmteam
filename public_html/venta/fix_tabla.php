<?php
// FIX RÁPIDO: Agregar AUTO_INCREMENT a la tabla
require_once '../../config/ctconex.php';

echo "<h2>🔧 Reparando tabla solicitud_alistamiento...</h2>";

try {
    // Agregar AUTO_INCREMENT y PRIMARY KEY
    $sql = "ALTER TABLE `solicitud_alistamiento`
            MODIFY `id` int NOT NULL AUTO_INCREMENT,
            ADD PRIMARY KEY (`id`)";

    $connect->exec($sql);

    echo "<p style='color: green; font-size: 20px;'>✅ <strong>TABLA REPARADA EXITOSAMENTE</strong></p>";
    echo "<p>El campo <code>id</code> ahora tiene AUTO_INCREMENT</p>";
    echo "<hr>";
    echo "<p><a href='preventa.php' style='font-size: 18px; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Ir a Pre-venta →</a></p>";

} catch (PDOException $e) {
    $errorMsg = $e->getMessage();

    // Si el error es porque ya existe PRIMARY KEY, está bien
    if (strpos($errorMsg, 'Multiple primary key') !== false || strpos($errorMsg, 'Duplicate key') !== false) {
        echo "<p style='color: orange;'>⚠️ La PRIMARY KEY ya existe. Intentando solo agregar AUTO_INCREMENT...</p>";

        try {
            $sql2 = "ALTER TABLE `solicitud_alistamiento` MODIFY `id` int NOT NULL AUTO_INCREMENT";
            $connect->exec($sql2);
            echo "<p style='color: green; font-size: 20px;'>✅ <strong>AUTO_INCREMENT AGREGADO</strong></p>";
            echo "<hr>";
            echo "<p><a href='preventa.php' style='font-size: 18px; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Ir a Pre-venta →</a></p>";
        } catch (Exception $e2) {
            echo "<p style='color: red;'>❌ Error: " . $e2->getMessage() . "</p>";
            echo "<p>Ejecuta manualmente en phpMyAdmin:</p>";
            echo "<pre style='background: #f5f5f5; padding: 15px;'>ALTER TABLE solicitud_alistamiento MODIFY id INT NOT NULL AUTO_INCREMENT;</pre>";
        }

    } else {
        echo "<p style='color: red;'>❌ Error: " . $errorMsg . "</p>";
        echo "<p>Ejecuta manualmente en phpMyAdmin:</p>";
        echo "<pre style='background: #f5f5f5; padding: 15px;'>ALTER TABLE solicitud_alistamiento
MODIFY id INT NOT NULL AUTO_INCREMENT,
ADD PRIMARY KEY (id);</pre>";
    }
}

// Verificar resultado
echo "<hr><h3>Verificación:</h3>";
try {
    $stmt = $connect->query("SHOW COLUMNS FROM solicitud_alistamiento WHERE Field = 'id'");
    $col = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($col) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Campo</th><td>{$col['Field']}</td></tr>";
        echo "<tr><th>Tipo</th><td>{$col['Type']}</td></tr>";
        echo "<tr><th>Null</th><td>{$col['Null']}</td></tr>";
        echo "<tr><th>Key</th><td><strong>{$col['Key']}</strong></td></tr>";
        echo "<tr><th>Extra</th><td><strong style='color: green;'>{$col['Extra']}</strong></td></tr>";
        echo "</table>";

        if (strpos($col['Extra'], 'auto_increment') !== false) {
            echo "<p style='color: green; font-weight: bold;'>✅ El campo tiene AUTO_INCREMENT correctamente</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>❌ AÚN FALTA AUTO_INCREMENT</p>";
        }
    }
} catch (Exception $e) {
    echo "<p>Error al verificar: " . $e->getMessage() . "</p>";
}
?>
