<?php
// st_ingresar_mantenimiento.php - Backend para procesar formulario TRIAGE 2
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
// Función para enviar respuesta JSON
require_once __DIR__ . '../../../config/ctconex.php';
function enviarRespuesta($estado, $mensaje, $datos = null)
{
    $respuesta = [
        'status' => $estado,
        'message' => $mensaje,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    if ($datos !== null) {
        $respuesta['data'] = $datos;
    }
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}
// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    enviarRespuesta('error', 'Método no permitido');
}
// Iniciar sesión
session_start();
// Verificar permisos
$ROLES_PERMITIDOS = [1, 2, 5, 6, 7];
if (!isset($_SESSION['rol']) || !in_array((int) $_SESSION['rol'], $ROLES_PERMITIDOS, true)) {
    enviarRespuesta('error', 'No tiene permisos para realizar esta acción');
}
// Buscar archivo de conexión
$rutasConexion = [
    __DIR__ . '../../config/ctconex.php',
    __DIR__ . '../../../config/ctconex.php',
    dirname(__DIR__) . '/config/ctconex.php',
    dirname(__DIR__, 2) . '/config/ctconex.php'
];
$conexionEncontrada = false;
foreach ($rutasConexion as $ruta) {
    if (file_exists($ruta)) {
        require_once $ruta;
        $conexionEncontrada = true;
        break;
    }
}
if (!$conexionEncontrada) {
    enviarRespuesta('error', 'Error de configuración: archivo de conexión no encontrado');
}
// Establecer conexión
$mysqli = null;
if (isset($conn) && $conn instanceof mysqli) {
    $mysqli = $conn;
} elseif (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
    $mysqli = new mysqli('localhost', 'u171145084_pcmteam', 'PCcomercial2025*', 'u171145084_pcmteam');
    if ($mysqli->connect_error) {
        enviarRespuesta('error', 'Error de conexión a la base de datos');
    }
    $mysqli->set_charset('utf8');
} else {
    enviarRespuesta('error', 'Error de configuración de base de datos');
}
// Función para obtener valor POST
function obtenerPost($clave, $defecto = null)
{
    return isset($_POST[$clave]) && $_POST[$clave] !== '' ? trim($_POST[$clave]) : $defecto;
}
// Validar inventario_id
$inventario_id = (int) obtenerPost('inventario_id', 0);
if ($inventario_id <= 0) {
    enviarRespuesta('error', 'ID de inventario inválido');
}
// Verificar que el inventario existe
$stmt = $mysqli->prepare("SELECT id FROM bodega_inventario WHERE id = ? LIMIT 1");
if (!$stmt) {
    enviarRespuesta('error', 'Error en la consulta: ' . $mysqli->error);
}
$stmt->bind_param('i', $inventario_id);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows === 0) {
    enviarRespuesta('error', 'El inventario especificado no existe');
}
$stmt->close();
// Recopilar datos del formulario
$datos = [
    'inventario_id' => $inventario_id,
    'tecnico_diagnostico' => obtenerPost('tecnico_diagnostico'),
    'limpieza_electronico' => obtenerPost('limpieza_electronico', 'pendiente'),
    'observaciones_limpieza_electronico' => obtenerPost('observaciones_limpieza_electronico'),
    'mantenimiento_crema_disciplinaria' => obtenerPost('mantenimiento_crema_disciplinaria', 'pendiente'),
    'observaciones_mantenimiento_crema' => obtenerPost('observaciones_mantenimiento_crema'),
    'mantenimiento_partes' => obtenerPost('mantenimiento_partes', 'pendiente'),
    'cambio_piezas' => obtenerPost('cambio_piezas', 'no'),
    'piezas_solicitadas_cambiadas' => obtenerPost('piezas_solicitadas_cambiadas'),
    'proceso_reconstruccion' => obtenerPost('proceso_reconstruccion', 'no'),
    'parte_reconstruida' => obtenerPost('parte_reconstruida'),
    'limpieza_general' => obtenerPost('limpieza_general', 'pendiente'),
    'remite_otra_area' => obtenerPost('remite_otra_area', 'no'),
    'area_remite' => obtenerPost('area_remite'),
    'proceso_electronico' => obtenerPost('proceso_electronico'),
    'observaciones_globales' => obtenerPost('observaciones_globales'),
    'fecha_registro' => date('Y-m-d H:i:s'),
    'usuario_id' => isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : (isset($_SESSION['id']) ? (int) $_SESSION['id'] : null)
];
// Validaciones específicas
if ($datos['cambio_piezas'] === 'si' && empty($datos['piezas_solicitadas_cambiadas'])) {
    enviarRespuesta('error', 'Debe especificar qué piezas se cambiaron');
}
if ($datos['remite_otra_area'] === 'si' && empty($datos['area_remite'])) {
    enviarRespuesta('error', 'Debe seleccionar el área a la que remite');
}
if ($datos['proceso_reconstruccion'] === 'si' && empty($datos['parte_reconstruida'])) {
    enviarRespuesta('error', 'Debe especificar la parte reconstruida');
}
// Verificar si existe la tabla bodega_mantenimiento
$consultaTabla = $mysqli->query("SHOW TABLES LIKE 'bodega_mantenimiento'");
if (!$consultaTabla || $consultaTabla->num_rows === 0) {
    // Crear tabla si no existe
    $sqlCrearTabla = "
    CREATE TABLE IF NOT EXISTS bodega_mantenimiento (
        id int(11) NOT NULL AUTO_INCREMENT,
        inventario_id int(11) NOT NULL,
        tecnico_diagnostico int(11) DEFAULT NULL,
        limpieza_electronico enum('pendiente','realizada','no_aplica') DEFAULT 'pendiente',
        observaciones_limpieza_electronico text DEFAULT NULL,
        mantenimiento_crema_disciplinaria enum('pendiente','realizada','no_aplica') DEFAULT 'pendiente',
        observaciones_mantenimiento_crema text DEFAULT NULL,
        mantenimiento_partes enum('pendiente','realizada','no_aplica') DEFAULT 'pendiente',
        cambio_piezas enum('no','si') DEFAULT 'no',
        piezas_solicitadas_cambiadas text DEFAULT NULL,
        proceso_reconstruccion enum('no','si') DEFAULT 'no',
        parte_reconstruida varchar(255) DEFAULT NULL,
        limpieza_general enum('pendiente','realizada','no_aplica') DEFAULT 'pendiente',
        remite_otra_area enum('no','si') DEFAULT 'no',
        area_remite varchar(255) DEFAULT NULL,
        proceso_electronico text DEFAULT NULL,
        observaciones_globales text DEFAULT NULL,
        fecha_registro datetime DEFAULT CURRENT_TIMESTAMP,
        usuario_id int(11) DEFAULT NULL,
        PRIMARY KEY (id),
        KEY idx_inventario (inventario_id),
        KEY idx_fecha (fecha_registro)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if (!$mysqli->query($sqlCrearTabla)) {
        enviarRespuesta('error', 'Error creando tabla: ' . $mysqli->error);
    }
}
// Preparar la consulta de inserción
$campos = [
    'inventario_id',
    'tecnico_diagnostico',
    'limpieza_electronico',
    'observaciones_limpieza_electronico',
    'mantenimiento_crema_disciplinaria',
    'observaciones_mantenimiento_crema',
    'mantenimiento_partes',
    'cambio_piezas',
    'piezas_solicitadas_cambiadas',
    'proceso_reconstruccion',
    'parte_reconstruida',
    'limpieza_general',
    'remite_otra_area',
    'area_remite',
    'proceso_electronico',
    'observaciones_globales',
    'fecha_registro',
    'usuario_id'
];
$placeholders = str_repeat('?,', count($campos) - 1) . '?';
$sql = "INSERT INTO bodega_mantenimiento (" . implode(',', $campos) . ") VALUES ($placeholders)";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    enviarRespuesta('error', 'Error preparando consulta: ' . $mysqli->error);
}
// Preparar valores para bind_param
$valores = [];
$tipos = '';
foreach ($campos as $campo) {
    $valor = $datos[$campo];
    if ($campo === 'inventario_id' || $campo === 'tecnico_diagnostico' || $campo === 'usuario_id') {
        $tipos .= 'i';
        $valores[] = $valor === null ? null : (int) $valor;
    } else {
        $tipos .= 's';
        $valores[] = $valor;
    }
}
// Ejecutar la consulta
$stmt->bind_param($tipos, ...$valores);
if ($stmt->execute()) {
    $nuevoId = $mysqli->insert_id;
    enviarRespuesta('success', 'Datos de mantenimiento guardados correctamente', [
        'id' => $nuevoId,
        'inventario_id' => $inventario_id
    ]);
} else {
    enviarRespuesta('error', 'Error al guardar los datos: ' . $stmt->error);
}
$stmt->close();
$mysqli->close();
?>