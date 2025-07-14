<?php
$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "registro";

$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);
$enlace->set_charset("utf8");
date_default_timezone_set("America/Caracas");

if (!$enlace) {
    die("Error en la conexión: " . mysqli_connect_error());
}
?>