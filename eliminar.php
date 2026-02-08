<?php
require_once __DIR__ . "/conexion.php";

// Recogemos el ID de la versión a eliminar que viene por POST desde el formulario del historial
$id = (int)($_POST["id"] ?? 0);
// Recogemos el email asociado a esa versión para volver al historial del mismo email tras borrar
$email = trim($_POST["email"] ?? "");

if ($id <= 0) die("ID no válido.");

// Consulta sql para eliminar la versión con ese ID
$stmt = $conexion->prepare("DELETE FROM cvs WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Redirigimos de nuevo al historial del mismo email
header("Location: historial.php?email=" . urlencode($email));
exit;