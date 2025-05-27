<?php
	require '../../backend/bd/ctconex.php';
	session_destroy();
	$url = "../../home.php";
	header("Location: $url");
?>
