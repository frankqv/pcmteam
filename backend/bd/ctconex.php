<?php
if(!isset($_SESSION)) 
    { 
        session_start(); 
    }

// Define database
define('dbhost', 'u171145084_pcmteam');
define('dbuser', 'root');
define('dbpass', 'PCcomercial2025');
define('dbname', 'u171145084_pcmteam');

// Connecting database
try {
    $connect = new PDO("mysql:host=".dbhost."; dbname=".dbname, dbuser, dbpass);
    $connect->query("set names utf8;");
    // $connect->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    //$connect->setAttribute( PDO::ATTR_EMULATE_PREPARES, true );
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
}
catch(PDOException $e) {
    echo $e->getMessage();
}
//---------------


?>

