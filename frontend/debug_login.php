<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug del Login</h1>";

echo "<h2>1. Verificación de archivos</h2>";
$files = [
    '../backend/bd/ctconex.php',
    '../backend/css/style.css',
    '../backend/js/jquery-3.3.1.min.js',
    '../backend/js/reenvio.js',
    '../backend/img/sideimage.jpg',
    '../backend/img/favicon.png'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
    } else {
        echo "❌ $file NO existe<br>";
    }
}

echo "<h2>2. Verificación de sesión</h2>";
session_start();
echo "Session ID: " . session_id() . "<br>";
echo "Session status: " . session_status() . "<br>";
echo "Session variables: ";
print_r($_SESSION);

echo "<h2>3. Verificación de POST</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "Método POST detectado<br>";
    echo "Variables POST: ";
    print_r($_POST);
} else {
    echo "No es método POST<br>";
}

echo "<h2>4. Prueba de conexión a BD</h2>";
try {
    require_once '../backend/bd/ctconex.php';
    echo "✅ Conexión a BD exitosa<br>";
    
    // Probar consulta
    $stmt = $connect->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    echo "✅ Total usuarios: " . $result['total'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error de BD: " . $e->getMessage() . "<br>";
}

echo "<h2>5. Información del servidor</h2>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

echo "<br><a href='login.php'>Ir al login original</a>";
echo "<br><a href='test.php'>Ir al test simple</a>";
?> 