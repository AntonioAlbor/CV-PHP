<?php
// Si alguien entra sin enviar el formulario lo mandamos al form otra vez
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: form.php');
  exit;
}

// Recogemos los datos del formulario (con trim para limpiar espacios)
$nombre     = trim($_POST['nombre'] ?? '');
$email      = trim($_POST['email'] ?? '');
$telefono   = trim($_POST['telefono'] ?? '');
$ubicacion  = trim($_POST['ubicacion'] ?? '');
$sobre_mi   = trim($_POST['sobre_mi'] ?? '');
$experiencia = trim($_POST['experiencia'] ?? '');
$formacion   = trim($_POST['formacion'] ?? '');
$habilidades = trim($_POST['habilidades'] ?? '');
$idiomas     = trim($_POST['idiomas'] ?? '');

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function nl($s){ return nl2br(e($s)); }

// VALIDACIONES

if ($nombre === '' || mb_strlen($nombre) < 3) {
  die("Error: el nombre es obligatorio y debe tener al menos 3 caracteres. <a href='form.php'>Volver</a>");
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  die("Error: email obligatorio y con formato válido. <a href='form.php'>Volver</a>");
}

if ($experiencia === '' || mb_strlen($experiencia) < 15) {
  die("Error: la experiencia es obligatoria y debe tener al menos 15 caracteres. <a href='form.php'>Volver</a>");
}

if ($formacion === '' || mb_strlen($formacion) < 15) {
  die("Error: la formación es obligatoria y debe tener al menos 15 caracteres. <a href='form.php'>Volver</a>");
}

if ($telefono !== '') {
  $telOkRegex = preg_match('/^[0-9+\s()\-]{6,20}$/', $telefono);
  $digits = preg_replace('/\D+/', '', $telefono);
  if (!$telOkRegex || strlen($digits) < 9) {
    die("Error: teléfono no válido. <a href='form.php'>Volver</a>");
  }
}

// Procesamos la foto (si se ha subido una)
$fotoPath = '';
if (!empty($_FILES['foto']['name']) && is_uploaded_file($_FILES['foto']['tmp_name'])) {
  $dir = __DIR__ . '/uploads/';
  if (!is_dir($dir)) mkdir($dir, 0777, true);

  $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
  $ok  = in_array($ext, ['jpg','jpeg','png','webp'], true);

  if ($ok) {
    $safeName = 'foto_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $dir.$safeName)) {
      $fotoPath = 'uploads/' . $safeName;
    }
  }
}

// Habilidades para mostrar en lista, se separan por comas
$skills = array_filter(array_map('trim', explode(',', $habilidades)));


// Apartado donde se guarda en la BD de MySQL (en mi caso he dcidido utilizar el email para las versiones)
require_once __DIR__ . "/conexion.php";

// Obtenemos la siguiente versión a guardar para ese email (si no hay ninguno, se guarda como versión 1)
$stmt = $conexion->prepare("SELECT IFNULL(MAX(version),0) FROM cvs WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($maxVersion);
$stmt->fetch();
$stmt->close();

$nextVersion = (int)$maxVersion + 1;

// Insertamos el nuevo CV con la versión siguiente
$ins = $conexion->prepare("
  INSERT INTO cvs
  (version, nombre, email, telefono, ubicacion, sobre_mi, experiencia, formacion, habilidades, idiomas)
  VALUES
  (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

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
  die("Error al guardar en la BD: " . e($ins->error) . " <a href='form.php'>Volver</a>");
}

$ins->close();

$versionGuardada = $nextVersion;

// Ahora mostramos el CV con los datos guardados previos
// El CV es de una plantilla de CodePen, pero esta adaptada para mostrar los mismos apartados que hemos rellenado previamente
// (https://codepen.io/nodws/pen/EmvwrZ)
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>CV - <?= e($nombre ?: 'Sin nombre') ?></title>
  <link rel="stylesheet" href="cv.css">
</head>

<body>

<header class="header">
  <div class="container">

    <div class="teacher-name">
      <div class="row">
        <div class="col-sm-9">
          <h2><strong><?= e($nombre ?: 'Nombre no indicado') ?></strong></h2>
        </div>

        <!-- Añadimos un nuevo botón para el historial que solo se muestra si hay un email (porque el historial se basa en el email) -->
        <div class="col-sm-3">
          <div class="button pull-right" style="display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;">
            <?php if ($email): ?>
              <a href="historial.php?email=<?= urlencode($email) ?>" class="btn btn-outline-success btn-sm">Historial</a>
            <?php endif; ?>

            <a href="form.php" class="btn btn-outline-success btn-sm">Volver</a>
          </div>
        </div>
      </div>
    </div>

    <div class="row" style="margin-top:20px;">
      <div class="col-sm-3">
        <?php if ($fotoPath): ?>
          <img class="rounded-circle" src="<?= e($fotoPath) ?>" alt="Foto de <?= e($nombre) ?>">
        <?php else: ?>
          <div class="rounded-circle placeholder-photo">SIN FOTO</div>
        <?php endif; ?>
      </div>

      <div class="col-sm-6">
        <h5>Currículum Vitae (v<?= (int)$versionGuardada ?>)</h5>
        <?php if ($email): ?><p><strong>Email:</strong> <?= e($email) ?></p><?php endif; ?>
        <?php if ($telefono): ?><p><strong>Teléfono:</strong> <?= e($telefono) ?></p><?php endif; ?>
        <?php if ($ubicacion): ?><p><strong>Ubicación:</strong> <?= e($ubicacion) ?></p><?php endif; ?>
      </div>

      <!-- Botón de imprimir/guardar PDF -->
      <div class="col-sm-3 text-center">
        <div class="button-email">
          <button class="btn btn-outline-success btn-block" onclick="window.print()">Imprimir / Guardar PDF</button>
        </div>
      </div>
    </div>

  </div>
</header>

<div class="container">

<!-- Apartado sobre mí -->
  <div class="row">
    <div class="col-sm-12">
      <div class="card card-block text-xs-left">
        <h5>Sobre mí</h5>
        <?php if ($sobre_mi): ?>
          <p><?= nl($sobre_mi) ?></p>
        <?php else: ?>
          <p>No indicado.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Apartado Habilidades -->
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

  <!-- Apartado experiencia laboral -->
  <div class="row">
    <div class="col-sm-12">
      <div class="card card-block text-xs-left">
        <h5>Experiencia</h5>
        <?php if ($experiencia): ?>
          <p><?= nl($experiencia) ?></p>
        <?php else: ?>
          <p>No indicada.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Apartado Formación -->
  <div class="row">
    <div class="col-sm-12">
      <div class="card card-block text-xs-left">
        <h5>Formación</h5>
        <?php if ($formacion): ?>
          <p><?= nl($formacion) ?></p>
        <?php else: ?>
          <p>No indicada.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

   <!-- Apartado Idiomas -->
  <div class="row">
    <div class="col-sm-12">
      <div class="card card-block text-xs-left">
        <h5>Idiomas</h5>
        <?php if ($idiomas): ?>
          <p><?= nl($idiomas) ?></p>
        <?php else: ?>
          <p>No indicados.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

</body>
</html>