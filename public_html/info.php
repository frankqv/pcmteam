
<?php
// Datos de conexión a MySQL
define('dbhost', 'localhost');
define('dbuser', 'u171145084_pcmteam');
define('dbpass', 'PCcomercial2025*');
define('dbname', 'u171145084_pcmteam');

echo "<h2>Prueba de conexión a la base de datos</h2>";

// Conexión PDO
try {
    $connect = new PDO("mysql:host=" . dbhost . ";dbname=" . dbname, dbuser, dbpass);
    $connect->query("SET NAMES utf8;");
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    echo "✅ Conexión PDO exitosa<br>";
    
    // Probar consulta simple
    $stmt = $connect->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    echo "✅ Total de usuarios en la base de datos: " . $result['total'] . "<br>";
    
} catch (PDOException $e) {
    echo "❌ Error de conexión PDO: " . $e->getMessage() . "<br>";
}

// Conexión mysqli
try {
    $conn = new mysqli(dbhost, dbuser, dbpass, dbname);
    
    if ($conn->connect_error) {
        echo "❌ Error de conexión mysqli: " . $conn->connect_error . "<br>";
    } else {
        echo "✅ Conexión mysqli exitosa<br>";
        $conn->set_charset("utf8");
        
        // Probar consulta simple
        $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "✅ Total de usuarios (mysqli): " . $row['total'] . "<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error mysqli: " . $e->getMessage() . "<br>";
}

?> 

<?php
echo "<h1>Información del Sistema</h1>";

echo "<h2>Información de PHP</h2>";
echo "Versión de PHP: " . phpversion() . "<br>";
echo "SAPI: " . php_sapi_name() . "<br>";
echo "Sistema operativo: " . PHP_OS . "<br>";

echo "
<p>nota: ¿Por qué necesitas zip en el host?<br>
● Los archivos .xlsx son técnicamente archivos ZIP con contenido XML.<br>
● PhpSpreadsheet usa la clase ZipArchive de PHP para descomprimirlos y leerlos. <br>
● Si ZipArchive no está disponible, verás errores como: <p><br>";

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

echo "<br><a href='public_html/login.php'>Ir al login</a>";
echo "<br><a href='test_conexion.php'>Probar conexión a BD</a>";
?> 

