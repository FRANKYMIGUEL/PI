<?php
@session_start();
date_default_timezone_set('America/Mexico_City');
$usuario = "root";
$contrasena = "12345678";
$consulta= new PDO('mysql:host=localhost;dbname=spvabarrotera', $usuario, $contrasena);
?>
