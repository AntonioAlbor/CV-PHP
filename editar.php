<?php
require_once __DIR__ . "/conexion.php";
// Para que los formularios sean mas dificiles de romper
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Recogemos el email enviado por GET o POST 
$id = (int)($_GET["id"] ?? $_POST["id"] ?? 0);
if ($id <= 0) die("ID no válido.");

// Consulta sql para coger la version con el ID y mostrar sus datos en el formulario para editar
$stmt = $conexion->prepare("SELECT * FROM cvs WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$base = $res->fetch_assoc();
$stmt->close();
if (!$base) die("No existe esa versión.");


if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Recogemos los datos del formulario con trim
  $nombre = trim($_POST["nombre"] ?? "");
  $email  = $base["email"]; // El email se mantiene, porque con el hacemos el control de versiones
  $telefono = trim($_POST["telefono"] ?? "");
  $ubicacion = trim($_POST["ubicacion"] ?? "");
  $sobre_mi = trim($_POST["sobre_mi"] ?? "");
  $experiencia = trim($_POST["experiencia"] ?? "");
  $formacion = trim($_POST["formacion"] ?? "");
  $habilidades = trim($_POST["habilidades"] ?? "");
  $idiomas = trim($_POST["idiomas"] ?? "");

  if ($nombre === "") die("El nombre es obligatorio.");

  // Siguiente versión para ese email
  $v = $conexion->prepare("SELECT IFNULL(MAX(version),0) FROM cvs WHERE email=?");
  $v->bind_param("s", $email);
  $v->execute();
  $v->bind_result($maxV);
  $v->fetch();
  $v->close();
  $nextVersion = (int)$maxV + 1;

  // Insertamos el CV editado como nueva fila o nueva version
  $ins = $conexion->prepare("
    INSERT INTO cvs (version, nombre, email, telefono, ubicacion, sobre_mi, experiencia, formacion, habilidades, idiomas)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");

  // los tipos de datos (i entero) (S string) 
  $ins->bind_param(
    "isssssssss",
    $nextVersion,
    $nombre,
    $email,
    $telefono,
    $ubicacion,
    $sobre_mi,
    $experiencia,
    $formacion,
    $habilidades,
    $idiomas
  );

  if (!$ins->execute()) {
    die("Error guardando nueva versión: " . e($ins->error));
  }
  // Guardamos el ID autoincrement de la nueva versión creada
  $newId = $ins->insert_id;
  $ins->close();

  header("Location: ver_version.php?id=" . $newId);
  exit;
}
?>

<!-- El formulario es del mismo estilo que la pnatilla de form.php pero mas resumida, los datos se rellenan de la versión que hemos cogido de la BD para editar -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Editar versión</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-neutral-950 text-neutral-100 min-h-screen p-6">
  <div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Editar versión (crea nueva)</h1>
      <a href="historial.php?email=<?= e($base["email"]) ?>" class="px-4 py-2 bg-neutral-800 rounded-lg hover:bg-neutral-700">Volver</a>
    </div>

    <div class="text-sm text-neutral-400">
      Estás editando <b>v<?= (int)$base["version"] ?></b> de <b><?= e($base["email"]) ?></b>. Al guardar se crea una versión nueva.
    </div>

    <form method="post" class="space-y-4">
      <input type="hidden" name="id" value="<?= (int)$base["id"] ?>">

      <label class="block">
        <span class="text-neutral-300">Nombre</span>
        <input name="nombre" value="<?= e($base["nombre"]) ?>" class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2">
      </label>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-neutral-300">Teléfono</span>
          <input name="telefono" value="<?= e($base["telefono"]) ?>" class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2">
        </label>
        <label class="block">
          <span class="text-neutral-300">Ubicación</span>
          <input name="ubicacion" value="<?= e($base["ubicacion"]) ?>" class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2">
        </label>
      </div>

      <label class="block">
        <span class="text-neutral-300">Sobre mí</span>
        <textarea name="sobre_mi" rows="3" class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2"><?= e($base["sobre_mi"]) ?></textarea>
      </label>

      <label class="block">
        <span class="text-neutral-300">Experiencia</span>
        <textarea name="experiencia" rows="5" class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2"><?= e($base["experiencia"]) ?></textarea>
      </label>

      <label class="block">
        <span class="text-neutral-300">Formación</span>
        <textarea name="formacion" rows="4" class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2"><?= e($base["formacion"]) ?></textarea>
      </label>

      <label class="block">
        <span class="text-neutral-300">Habilidades (separadas por comas)</span>
        <textarea name="habilidades" rows="2" class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2"><?= e($base["habilidades"]) ?></textarea>
      </label>

      <label class="block">
        <span class="text-neutral-300">Idiomas</span>
        <textarea name="idiomas" rows="2" class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2"><?= e($base["idiomas"]) ?></textarea>
      </label>

      <button class="px-4 py-2 bg-blue-600 rounded-lg hover:bg-blue-500">Guardar como nueva versión</button>
    </form>
  </div>
</body>
</html>