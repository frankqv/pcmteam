<?php
echo "<h1>Información del Sistema</h1>";

echo "<h2>Información de PHP</h2>";
echo "Versión de PHP: " . phpversion() . "<br>";
echo "SAPI: " . php_sapi_name() . "<br>";
echo "Sistema operativo: " . PHP_OS . "<br>";

echo "Zip: " . (extension_loaded('zip') ? '✅ Sí' : '❌ No') . "<br>";
echo "ZipArchive: " . (class_exists('ZipArchive') ? '✅ Sí' : '❌ No') . "<br>";


echo "<h2>Extensiones cargadas</h2>";
echo "PDO: " . (extension_loaded('pdo') ? '✅ Sí' : '❌ No') . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Sí' : '❌ No') . "<br>";
echo "MySQLi: " . (extension_loaded('mysqli') ? '✅ Sí' : '❌ No') . "<br>";
echo "Session: " . (extension_loaded('session') ? '✅ Sí' : '❌ No') . "<br>";

echo "<h2>Configuración de sesiones</h2>";
echo "session.save_handler: " . ini_get('session.save_handler') . "<br>";
echo "session.save_path: " . ini_get('session.save_path') . "<br>";
echo "session.gc_maxlifetime: " . ini_get('session.gc_maxlifetime') . "<br>";

echo "<h2>Configuración de errores</h2>";
echo "display_errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "<br>";
echo "error_reporting: " . ini_get('error_reporting') . "<br>";
echo "log_errors: " . (ini_get('log_errors') ? 'On' : 'Off') . "<br>";

echo "<h2>Información del servidor</h2>";
echo "SERVER_SOFTWARE: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";

echo "<h2>Prueba de escritura de archivos</h2>";
$testFile = 'test_write.txt';
if (file_put_contents($testFile, 'Test de escritura')) {
    echo "✅ Escritura de archivos funciona<br>";
    unlink($testFile); // Eliminar archivo de prueba
} else {
    echo "❌ Problema con escritura de archivos<br>";
}

echo "<br><a href='frontend/login.php'>Ir al login</a>";
echo "<br><a href='test_conexion.php'>Probar conexión a BD</a>";
?> 

