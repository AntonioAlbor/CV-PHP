<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Crear CV</title>

  <!-- Tailwind CDN (para la plantilla https://codepen.io/cansuari/pen/jOVMJzg) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="stylesheet" href="form-css.css" />
</head>

<!-- Unicos cambios realizados en la plantilla son
        - En datos personales:
            * Eliminación de el apartado de Género y Fecha de nacimiento 
            * Añadido de Email y Teléfono y apartado de Sobre mí 
        - En Experiencia laboral:
            * Eliminación de la estrcutura que habia dejando solo un textarea para que el usuario escriba su experiencia laboral
              (los demás apartado se separaron en Formación, habilidades e idiomas)
        - En Formación, habilidades e idiomas: 
            * Reutilizaicón de los campos anteriores (Formación, habilidades e idiomas)
        - Por último se elimina el apartado de cotnacto y hacemos uno de revisión 
        
        (+ algunos comentarios sueltos) -->

<body class="bg-neutral-950 text-neutral-100 min-h-screen flex items-center justify-center p-4">

<!-- La barra de progreso -->
  <div id="container" class="w-full max-w-4xl space-y-8 transition-all duration-500">
    <div class="relative w-full h-1 bg-neutral-800 rounded-full overflow-hidden" aria-hidden="true">
      <div id="progress" class="absolute top-0 left-0 h-1 bg-blue-500 transition-all duration-500 ease-out" style="width: 25%"></div>
    </div>

    <!-- Despues del formulario, que nos envie a ver_cv.php, donde se procesarán los datos y se guardarán en la BD -->
    <form id="form" class="space-y-6 relative" method="post" action="/CV_PHP/ver_cv.php" enctype="multipart/form-data" novalidate>
      <h1 class="text-2xl font-bold">Generador de Currículum Vitae</h1>

      <!-- Datos personales -->
      <section class="step space-y-4" data-step="0">
        <h2 class="text-xl font-semibold">Datos personales</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="field">
            <label for="nombre" class="block text-sm text-neutral-400 mb-1">Nombre completo <span class="text-red-400">*</span></label>
            <input id="nombre" name="nombre" required
              class="peer w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition" />
            <p class="field-error text-xs text-red-400 mt-1" id="nombre_err">Escribe tu nombre.</p>
          </div>

          <div class="field">
            <label for="telefono" class="block text-sm text-neutral-400 mb-1">Teléfono</label>
            <input id="telefono" name="telefono"
              class="peer w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition" />
          </div>

          <div class="md:col-span-2 field">
            <label for="email" class="block text-sm text-neutral-400 mb-1">Email <span class="text-red-400">*</span></label>
            <input id="email" type="email" name="email" required
              class="peer w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition"
              placeholder="nombre@ejemplo.com" />
            <p class="field-error text-xs text-red-400 mt-1" id="email_err">Email no válido.</p>
          </div>

          <div class="md:col-span-2 field">
            <label for="ubicacion" class="block text-sm text-neutral-400 mb-1">Ubicación (Ciudad / Provincia)</label>
            <input id="ubicacion" name="ubicacion"
              class="peer w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition"
              placeholder="Ej: Jerez de la Frontera, Cádiz" />
          </div>

          <!-- Sobre mí -->
          <div class="md:col-span-2 field">
            <label for="sobre_mi" class="block text-sm text-neutral-400 mb-1">Sobre mí</label>
            <textarea id="sobre_mi" name="sobre_mi" rows="4"
              class="peer w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition"
              placeholder="Ej: Soy estudiante de DAW, me gusta..."></textarea>
          </div>

          <div class="md:col-span-2">
            <label for="foto" class="block text-sm text-neutral-400 mb-1">Foto (opcional)</label>
            <div class="flex gap-4 items-start">
              <div id="photoPreview" class="w-20 h-20 rounded-full overflow-hidden bg-gradient-to-b from-neutral-800 to-neutral-900 flex items-center justify-center border border-neutral-800 text-center text-xs text-neutral-600">
                No Preview
              </div>
              <div class="flex-1 min-w-0">
                <input id="foto" name="foto" type="file" accept="image/*"
                  class="block w-full text-sm text-neutral-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-neutral-800 file:text-neutral-200 hover:file:bg-neutral-700 transition" />
                <p class="text-xs text-neutral-500 mt-2">PNG/JPG. Máx 5MB.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Experiencia laboral -->
      <section class="step hidden space-y-4" data-step="1">
        <h2 class="text-xl font-semibold">Experiencia laboral</h2>

        <div class="field">
          <label for="experiencia" class="block text-sm text-neutral-400 mb-1">Describe tu experiencia (puestos, empresas, fechas, funciones) <span class="text-red-400">*</span></label>
          <textarea id="experiencia" name="experiencia" rows="7" required
            class="peer w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition"
            placeholder="Ej: 2024 - 2025 | Empresa X | Técnico..."></textarea>
          <p class="field-error text-xs text-red-400 mt-1" id="experiencia_err">Este campo es obligatorio.</p>
        </div>
      </section>

      <!-- Formación, habilidades e idiomas -->
      <section class="step hidden space-y-4" data-step="2">
        <h2 class="text-xl font-semibold">Formación, habilidades e idiomas</h2>

        <div class="field">
          <label for="formacion" class="block text-sm text-neutral-400 mb-1">Formación académica <span class="text-red-400">*</span></label>
          <textarea id="formacion" name="formacion" rows="6" required
            class="peer w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition"
            placeholder="Ej: 2023 - 2025 | CFGS DAW | Centro..."></textarea>
          <p class="field-error text-xs text-red-400 mt-1" id="formacion_err">Este campo es obligatorio.</p>
        </div>

        <div class="field">
          <label id="skills-label" class="block text-sm text-neutral-400 mb-1">Habilidades (pulsa Enter o coma)</label>
          <div id="tagInput" class="flex flex-wrap items-center gap-2 w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2 focus-within:ring-2 focus-within:ring-blue-500">
            <input id="tagField" type="text" placeholder="Ej: HTML, CSS, PHP..."
              class="flex-1 bg-transparent outline-none text-neutral-100 placeholder-neutral-600" />
          </div>
          <input type="hidden" name="habilidades" id="habilidades_hidden" value="">
          <p class="text-xs text-neutral-500 mt-2">Haz clic en una habilidad para borrarla.</p>
        </div>

        <div class="field">
          <label for="idiomas" class="block text-sm text-neutral-400 mb-1">Idiomas</label>
          <textarea id="idiomas" name="idiomas" rows="3"
            class="peer w-full bg-neutral-900 border border-neutral-800 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition"
            placeholder="Ej: Español (Nativo), Inglés (B2)"></textarea>
        </div>
      </section>

      <!-- Revisión -->
      <section class="step hidden space-y-4" data-step="3">
        <h2 class="text-xl font-semibold">Revisión</h2>
        <div id="review_and_confirm" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-neutral-900 border border-neutral-800 rounded-lg p-4 text-sm text-neutral-400"></div>

        <p class="text-neutral-400 text-sm">
          Si está todo bien, pulsa <b>Generar CV</b>.
        </p>
      </section>

      <!-- Botones de navegación -->
      <div class="flex justify-between pt-4 border-t border-neutral-800">
        <button id="prev" type="button"
          class="px-4 py-2 bg-neutral-800 rounded-lg hover:bg-neutral-700 transition invisible">
          Atrás
        </button>

        <button id="next" type="button"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition">
          Siguiente
        </button>
      </div>

    </form>
  </div>

  <script src="form-js.js"></script>
</body>
</html>