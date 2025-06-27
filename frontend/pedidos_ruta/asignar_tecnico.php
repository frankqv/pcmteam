<?php
session_start();
include_once '../../backend/bd/ctconex.php';

if(!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 5, 6, 7])){
    header('location: ../error404.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idord = isset($_POST['idord']) ? (int)$_POST['idord'] : 0;
    $tecnico_id = isset($_POST['tecnico_id']) ? (int)$_POST['tecnico_id'] : 0;
    if ($idord > 0 && $tecnico_id > 0) {
        $stmt = $connect->prepare('UPDATE orders SET user_id = ? WHERE idord = ?');
        $stmt->execute([$tecnico_id, $idord]);
    }
}
header('Location: mostrar.php');
exit;
