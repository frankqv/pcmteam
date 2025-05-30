<?php
if (!isset($_SESSION)) {
    session_start();
}

// Datos correctos de conexión a MySQL en Hostinger
define('dbhost', 'localhost');
define('dbuser', 'u171145084_pcmteam');
define('dbpass', ''); // ← Asegúrate de que esta contraseña esté bien escrita
define('dbname', 'u171145084_pcmteam');

try {
    $connect = new PDO("mysql:host=" . dbhost . ";dbname=" . dbname, dbuser, dbpass);
    $connect->query("SET NAMES utf8;");
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '❌ Error de conexión: ' . $e->getMessage();
}
?>
