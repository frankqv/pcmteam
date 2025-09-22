<?php
// /config/ctconex.php
// Archivo de conexión a la base de datos
// Datos de conexión a MySQL
define('dbhost', 'localhost');
define('dbuser', 'u171145084_pcmteam');
define('dbpass', 'PCcomercial2025*');
define('dbname', 'u171145084_pcmteam');
try {
    // Conexión PDO
    $connect = new PDO("mysql:host=" . dbhost . ";dbname=" . dbname . ";charset=utf8", dbuser, dbpass);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Conexión mysqli (para compatibilidad con código existente)
    $conn = new mysqli(dbhost, dbuser, dbpass, dbname);
    if ($conn->connect_error) {
        throw new Exception("Error de conexión mysqli: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
} catch (PDOException $e) {
    error_log("Error de conexión PDO: " . $e->getMessage());
    die("Error de conexión a la base de datos");
} catch (Exception $e) {
    error_log("Error de conexión mysqli: " . $e->getMessage());
    die("Error de conexión a la base de datos");
}
?>