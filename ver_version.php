<?php
require_once __DIR__ . "/conexion.php";
// Para que los formularios sean mas dificiles de romper
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Recogemos el ID de la versión con GET
$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("ID no válido.");

// Consulta sql para coger la versión con ese ID
$stmt = $conexion->prepare("SELECT * FROM cvs WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$cv = $res->fetch_assoc();
$stmt->close();

if (!$cv) die("No existe esa versión.");

// Se aplican distintas funciones para las habilidades (trim para los espacios, separamos por coma y filtramos los vacios)
$skills = array_filter(array_map("trim", explode(",", (string)$cv["habilidades"])));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>CV - <?= e($cv["nombre"]) ?></title>
  <link rel="stylesheet" href="cv.css">
</head>
<body>

<!-- Utilizamos la misma plantilla de CodePen que en ver_cv.php, pero esta vez con los datos de la versión que hemos cogido de la BD -->
<header class="header">
  <div class="container">
    <div class="teacher-name">
      <div class="row">
        <div class="col-sm-9">
          <h2><strong><?= e($cv["nombre"]) ?></strong></h2>
        </div>
        <div class="col-sm-3">
          <div class="button pull-right">
            <a href="historial.php?email=<?= e($cv["email"]) ?>" class="btn btn-outline-success btn-sm">Historial</a>
          </div>
        </div>
      </div>
    </div>

    <div class="row" style="margin-top:20px;">
      <div class="col-sm-6">
        <h5>Currículum Vitae (v<?= (int)$cv["version"] ?>)</h5>
        <p><strong>Email:</strong> <?= e($cv["email"]) ?></p>
        <?php if (!empty($cv["telefono"])): ?><p><strong>Teléfono:</strong> <?= e($cv["telefono"]) ?></p><?php endif; ?>
        <?php if (!empty($cv["ubicacion"])): ?><p><strong>Ubicación:</strong> <?= e($cv["ubicacion"]) ?></p><?php endif; ?>
      </div>

      <div class="col-sm-3 text-center">
        <div class="button-email">
          <button class="btn btn-outline-success btn-block" onclick="window.print()">Imprimir / Guardar PDF</button>
        </div>
      </div>
    </div>
  </div>
</header>

<div class="container">

  <div class="row">
    <div class="col-sm-12">
      <div class="card card-block text-xs-left">
        <h5>Sobre mí</h5>
        <p><?= $cv["sobre_mi"] ? nl2br(e($cv["sobre_mi"])) : "No indicado." ?></p> <!-- Si no hay texto se muestra "No indicado" -->
          <!-- nl2br para convertir los saltos de línea en <br> -->
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <div class="card card-block">
        <h5>Habilidades</h5>
        <?php if (!empty($skills)): ?>
          <ul class="list-group" style="margin-top:15px;margin-bottom:15px;">
            <?php foreach ($skills as $sk): ?>
              <li class="list-group-item"><?= e($sk) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>No indicadas.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <div class="card card-block text-xs-left">
        <h5>Experiencia</h5>
        <p><?= $cv["experiencia"] ? nl2br(e($cv["experiencia"])) : "No indicada." ?></p> <!-- Si no hay texto se muestra "No indicado" -->
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <div class="card card-block text-xs-left">
        <h5>Formación</h5>
        <p><?= $cv["formacion"] ? nl2br(e($cv["formacion"])) : "No indicada." ?></p> <!-- Si no hay texto se muestra "No indicado" -->
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <div class="card card-block text-xs-left">
        <h5>Idiomas</h5>
        <p><?= $cv["idiomas"] ? nl2br(e($cv["idiomas"])) : "No indicados." ?></p> <!-- Si no hay texto se muestra "No indicados" -->
      </div>
    </div>
  </div>

</div>
</body>
</html>