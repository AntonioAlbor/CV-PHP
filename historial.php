<?php
require_once __DIR__ . "/conexion.php";
// Para que los formularios sean mas dificiles de romper
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Recogemos el email enviado por GET
$email = trim($_GET["email"] ?? "");
$rows = [];

// Solo hacemos la consulta si se ha introducido un email (para evitar mostrar todos los CVs de la BD)
if ($email !== "") {
  //Consulta sql para coger el historial de versiones de ese email, ordenado de mayor a menor versión (DESC)
  $stmt = $conexion->prepare("SELECT id, version, nombre FROM cvs WHERE email=? ORDER BY version DESC");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $res = $stmt->get_result();
  $rows = $res->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
}
?>

<!-- Ahora mostramos el historial de versiones de ese email en una tabla
 con opción a editarlo o eliminarla -->
 <!-- La tabla esta hecha en base al formulario original, por eso utiliza el mismo estilo la CDN de Tailwind -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Historial de CV</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-neutral-950 text-neutral-100 min-h-screen p-6">
  <div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Historial de versiones</h1>
      <a href="form.php" class="px-4 py-2 bg-neutral-800 rounded-lg hover:bg-neutral-700">Crear nuevo CV</a>
    </div>

    <!-- Botón con campo para buscar por email -->
    <form method="get" class="flex gap-3">
      <input name="email" value="<?= e($email) ?>" placeholder="Email (ej: nombre@ejemplo.com)"
        class="flex-1 bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2" />
      <button class="px-4 py-2 bg-blue-600 rounded-lg hover:bg-blue-500">Buscar</button>
    </form>

    <?php if ($email === ""): ?>
      <p class="text-neutral-400">Escribe un email para ver sus versiones guardadas.</p>
    <?php else: ?>
      <?php if (empty($rows)): ?>
        <p class="text-neutral-400">No hay versiones guardadas para <?= e($email) ?>.</p>
      <?php else: ?>
        <div class="overflow-x-auto border border-neutral-800 rounded-lg">
          <table class="w-full text-sm">
            <thead class="bg-neutral-900 text-neutral-300">
              <tr>
                <th class="text-left p-3">Versión</th>
                <th class="text-left p-3">Nombre</th>
                <th class="text-left p-3">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <!-- Para cada columna, o sea, cada cv mostramos la version y el nombre con ver editar o eliminar -->
              <?php foreach ($rows as $r): ?>
                <tr class="border-t border-neutral-800">
                  <td class="p-3">v<?= (int)$r["version"] ?></td>
                  <td class="p-3"><?= e($r["nombre"]) ?></td>
                  <td class="p-3 flex gap-2 flex-wrap">
                    <a class="px-3 py-1 bg-neutral-800 rounded hover:bg-neutral-700"
                       href="ver_version.php?id=<?= (int)$r["id"] ?>">Ver</a>

                    <a class="px-3 py-1 bg-blue-600 rounded hover:bg-blue-500"
                       href="editar.php?id=<?= (int)$r["id"] ?>">Editar (nueva)</a>

                    <form method="post" action="eliminar.php" onsubmit="return confirm('¿Eliminar esta versión?');">
                      <input type="hidden" name="id" value="<?= (int)$r["id"] ?>">
                      <input type="hidden" name="email" value="<?= e($email) ?>">
                      <button class="px-3 py-1 bg-red-600 rounded hover:bg-red-500">Eliminar</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    <?php endif; ?>

  </div>
</body>
</html>