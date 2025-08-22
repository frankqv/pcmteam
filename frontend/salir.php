<?php
  require '../config/ctconex.php';
  session_destroy();
  $url = "login.php";
  header("Location: $url");
?>