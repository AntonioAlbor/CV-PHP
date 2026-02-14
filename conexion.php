<?php
/* Conexión a la base de datos MySQL usando mysqli
*/

// Datos de conexión al servidor
$server = "localhost";
$user   = "root";
$db     = "curriculum_php";

// Intentamos primero con contraseña "root"
$pass = "root";
$conexion = @new mysqli($server, $user, $pass, $db);

// Si falla, intentamos con contraseña vacía
if ($conexion->connect_errno) {
    $pass = "";
    $conexion = @new mysqli($server, $user, $pass, $db);
}

// Si sigue fallando, mostramos error
if ($conexion->connect_errno) {
    die("Error de conexión: " . $conexion->connect_error);
}


?>