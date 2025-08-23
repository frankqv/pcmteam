<?php
/**
 * Script específico para arreglar el auto_increment de la tabla clientes
 */
require_once __DIR__ . '../../config/ctconex.php';
try {
    echo "Arreglando auto_increment de la tabla clientes...\n";
    // 1. Obtener el máximo ID actual
    $result = $connect->query("SELECT MAX(idclie) as max_id FROM clientes");
    $maxId = $result->fetch(PDO::FETCH_ASSOC)['max_id'];
    echo "Máximo ID encontrado: " . ($maxId ?: 0) . "\n";
    // 2. Resetear auto_increment al siguiente valor
    $nextId = ($maxId ?: 0) + 1;
    $sql = "ALTER TABLE clientes AUTO_INCREMENT = $nextId";
    echo "Ejecutando: $sql\n";
    $connect->exec($sql);
    // 3. Verificar que se aplicó correctamente
    $result = $connect->query("SHOW TABLE STATUS LIKE 'clientes'");
    $tableInfo = $result->fetch(PDO::FETCH_ASSOC);
    echo "Auto_increment actualizado a: " . $tableInfo['Auto_increment'] . "\n";
    // 4. Probar una inserción simple
    echo "Probando inserción de prueba...\n";
    $testSql = "INSERT INTO clientes (numid, nomcli, apecli, naci, correo, celu, estad, dircli, ciucli, idsede) VALUES ('99999999', 'TEST', 'TEST', '1900-01-01', 'test@test.com', '3000000000', 'Activo', 'Test', 'Test', 'Principal')";
    $connect->exec($testSql);
    $newId = $connect->lastInsertId();
    echo "Nuevo registro insertado con ID: $newId\n";
    // 5. Eliminar el registro de prueba
    $connect->exec("DELETE FROM clientes WHERE numid = '99999999'");
    echo "Registro de prueba eliminado\n";
    echo "\n¡Auto_increment arreglado exitosamente!\n";
    echo "Ahora puedes importar archivos Excel sin problemas.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>