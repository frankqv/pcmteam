<?php
// /config/ctconex.php
define('dbhost', 'localhost');
define('dbuser', 'u171145084_pcmteam');
define('dbpass', 'PCcomercial2025*');
define('dbname', 'u171145084_pcmteam');
try {
    // Conexión PDO
    date_default_timezone_set('America/Bogota');
    $connect = new PDO("mysql:host=" . dbhost . ";dbname=" . dbname . ";charset=utf8", dbuser, dbpass);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Conexión mysqli (para compatibilidad con código existente)
    $conn = new mysqli(dbhost, dbuser, dbpass, dbname);
// Aplicar zona horaria de sesión MySQL (Bogotá UTC-5)
if ($conn && !$conn->connect_errno) {
    $conn->query("SET time_zone = '-05:00'");
}

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
if (!ini_get('date.timezone')) {
    date_default_timezone_set('America/Bogota');
}

/** IMPORTANTE NO ELIMNAR lo necesita e archivo (bodega/triage2.php)
 * ensure_execute: Ejecuta un PDOStatement o mysqli_stmt con parámetros (si los hay)
 * - $stmt: PDOStatement o mysqli_stmt
 * - $params: array asociativo (PDO) o array indexado (mysqli)
 * Devuelve true/false (éxito) o lanza Exception en caso de objeto inválido.
 */
if (!function_exists('ensure_execute')) {
    function ensure_execute($stmt, $params = []) {
        try {
            // PDOStatement
            if (class_exists('PDOStatement') && $stmt instanceof PDOStatement) {
                if (empty($params)) {
                    return $stmt->execute();
                }
                // PDO acepta array asociativo o indexado
                return $stmt->execute($params);
            }
            // mysqli_stmt
            if (class_exists('mysqli_stmt') && $stmt instanceof mysqli_stmt) {
                if (empty($params)) {
                    return $stmt->execute();
                }
                // bind_param requires types and references
                $types = str_repeat('s', count($params));
                $refs = [];
                foreach ($params as $k => $v) {
                    $refs[$k] = &$params[$k];
                }
                array_unshift($refs, $types);
                if (!call_user_func_array([$stmt, 'bind_param'], $refs)) {
                    throw new Exception('bind_param failed');
                }
                return $stmt->execute();
            }
            throw new Exception('ensure_execute: unsupported statement object (' . gettype($stmt) . ')');
        } catch (Exception $e) {
            error_log('[ensure_execute] ' . $e->getMessage());
            throw $e;
        }
    }
}

?>
