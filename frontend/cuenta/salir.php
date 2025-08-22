<?php
	require '../../config/ctconex.php';
	session_destroy();
	$url = "../../home.php";
	header("Location: $url");
?>