<?php
$server = "localhost";
$user   = "root";
$db     = "curriculum_php";

// Intento sin contraseña
$pass = "";
$conexion = new mysqli($server, $user, $pass, $db);

// Si falla intentamos con root"
if ($conexion->connect_errno) {
    $pass = "root";
    $conexion = new mysqli($server, $user, $pass, $db);
}

// Error total
if ($conexion->connect_errno) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>