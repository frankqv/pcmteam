<?php
/**
 * Script para reparar la tabla clientes y solucionar problemas de auto-increment
 * Ejecutar solo si hay problemas con la tabla
 */

include('bd/ctconex.php');

try {
    echo "Iniciando reparación de la tabla clientes...\n";
    
    // 1. Verificar el estado actual de la tabla
    $result = $connect->query("SHOW TABLE STATUS LIKE 'clientes'");
    $tableInfo = $result->fetch(PDO::FETCH_ASSOC);
    
    echo "Estado actual de la tabla:\n";
    echo "- Engine: " . $tableInfo['Engine'] . "\n";
    echo "- Auto_increment: " . $tableInfo['Auto_increment'] . "\n";
    echo "- Rows: " . $tableInfo['Rows'] . "\n";
    
    // 2. Reparar la tabla
    echo "\nReparando tabla...\n";
    $connect->exec("REPAIR TABLE clientes");
    
    // 3. Optimizar la tabla
    echo "Optimizando tabla...\n";
    $connect->exec("OPTIMIZE TABLE clientes");
    
    // 4. Verificar el auto_increment
    echo "Verificando auto_increment...\n";
    $maxId = $connect->query("SELECT MAX(idclie) as max_id FROM clientes")->fetch(PDO::FETCH_ASSOC);
    $maxId = $maxId['max_id'] ?: 0;
    
    // 5. Resetear auto_increment si es necesario
    if ($maxId > 0) {
        $connect->exec("ALTER TABLE clientes AUTO_INCREMENT = " . ($maxId + 1));
        echo "Auto_increment reseteado a: " . ($maxId + 1) . "\n";
    }
    
    // 6. Verificar estado final
    $result = $connect->query("SHOW TABLE STATUS LIKE 'clientes'");
    $tableInfo = $result->fetch(PDO::FETCH_ASSOC);
    
    echo "\nEstado final de la tabla:\n";
    echo "- Engine: " . $tableInfo['Engine'] . "\n";
    echo "- Auto_increment: " . $tableInfo['Auto_increment'] . "\n";
    echo "- Rows: " . $tableInfo['Rows'] . "\n";
    
    echo "\n¡Reparación completada exitosamente!\n";
    
} catch (Exception $e) {
    echo "Error durante la reparación: " . $e->getMessage() . "\n";
}
?> 