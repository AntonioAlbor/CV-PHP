<?php
/* Conexión a la base de datos MySQL usando mysqli
*/

// Datos de conexión al servidor
$server = "localhost";
$user   = "root";           // Usuario de la base de datos
$pass   = "root";           // Contraseña del usuario
$db     = "curriculum_php"; // Nombre de la base de datos

// Creamos la conexión
$conexion = new mysqli($server, $user, $pass, $db);

// Comprobación de errores en la conexión
if ($conexion->connect_errno) {
  die("Error de conexión: " . $conexion->connect_error);
}

?>