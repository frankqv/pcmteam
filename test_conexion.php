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

echo "<br><a href='frontend/login.php'>Ir al login</a>";
?> 