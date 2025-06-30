<?php
if (!isset($_SESSION)) {
    session_start();
}

// Datos de conexión a MySQL
define('dbhost', 'localhost');
define('dbuser', 'u171145084_pcmteam');
define('dbpass', 'PCcomercial2025*');
define('dbname', 'u171145084_pcmteam');

// Conexión PDO
try {
    $connect = new PDO("mysql:host=" . dbhost . ";dbname=" . dbname, dbuser, dbpass);
    $connect->query("SET NAMES utf8;");
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '❌ Error de conexión PDO: ' . $e->getMessage();
}

// Conexión mysqli
$conn = new mysqli(dbhost, dbuser, dbpass, dbname);

// Verificar conexión mysqli
if ($conn->connect_error) {
    die("❌ Error de conexión mysqli: " . $conn->connect_error);
}

// Establecer charset
$conn->set_charset("utf8");
?>
