/* JS de la plantilla utilizada de CodePen */
/* (los comentarios son sacados de la propia plantilla */

const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

/* === App: tiny UI helpers for validity === */
function setInvalid(el, msgId) {
  if (!el) return;
  const field = el.closest(".field") || el.parentElement;

  el.classList.remove("border-neutral-800");
  el.classList.add("border-red-500");
  el.setAttribute("aria-invalid", "true");

  if (msgId) {
    const msg = document.getElementById(msgId);
    if (msg) {
      field?.classList.add("show-error");
      el.setAttribute("aria-describedby", msgId);
    }
  }
}

function setValid(el) {
  if (!el) return;
  const field = el.closest(".field") || el.parentElement;

  el.classList.remove("border-red-500");
  el.classList.add("border-neutral-800");
  el.removeAttribute("aria-invalid");

  const desc = el.getAttribute("aria-describedby");
  if (desc) el.removeAttribute("aria-describedby");
  field?.classList.remove("show-error");
}

/* === App: stepper === */
const steps = $$(".step");
const nextBtn = $("#next");
const prevBtn = $("#prev");
const progress = $("#progress");
const summaryBox = $("#review_and_confirm");
const form = $("#form");

let current = 0;

function setProgress(pct) {
  progress.style.width = `${pct}%`;
}

function showStep(index) {
  steps.forEach((s, i) => s.classList.toggle("hidden", i !== index));

  prevBtn.classList.toggle("invisible", index === 0);
  nextBtn.textContent = index === steps.length - 1 ? "Generar CV" : "Siguiente";

  setProgress(((index + 1) / steps.length) * 100);

  if (index === steps.length - 1) generateSummary();

  const first = steps[index].querySelector("input, textarea, button");
  if (first) first.focus();
}

// VALIDACIONES

function validateStep(index) {
  let ok = true;

  const required = {
    0: ["nombre", "email"],
    1: ["experiencia"],
    2: ["formacion"]
  };

  const ids = required[index] || [];
  for (const id of ids) {
    const el = document.getElementById(id);
    if (!el) continue;

    const value = (el.value || "").trim();

    if (id === "email") {
      const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
      if (!emailOk) { setInvalid(el, "email_err"); ok = false; }
      else setValid(el);
      continue;
    }

    if (id === "nombre" && value.length < 3) {
      setInvalid(el, "nombre_err");
      ok = false;
      continue;
    }

    if ((id === "experiencia" || id === "formacion") && value.length < 15) {
      setInvalid(el, `${id}_err`);
      ok = false;
      continue;
    }

    if (!value) {
      setInvalid(el, `${id}_err`);
      ok = false;
    } else {
      setValid(el);
    }
  }

  if (index === 0) {
    const tel = document.getElementById("telefono");
    if (tel) {
      const t = (tel.value || "").trim();
      if (t !== "") {
        const telOk = /^[0-9+\s()-]{6,20}$/.test(t) && (t.replace(/\D/g, "").length >= 9);
        if (!telOk) {
          setInvalid(tel, "telefono_err");
          ok = false;
        } else setValid(tel);
      } else setValid(tel);
    }
  }

  return ok;
}

nextBtn.addEventListener("click", () => {
  if (current === steps.length - 1) {
    form.submit();
    return;
  }

  if (!validateStep(current)) return;

  current++;
  showStep(current);
});

prevBtn.addEventListener("click", () => {
  if (current === 0) return;
  current--;
  showStep(current);
});

/* === Tags === */
const tagContainer = $("#tagInput");
const tagField = $("#tagField");
const habilidadesHidden = $("#habilidades_hidden");

let tags = [];

function syncHiddenSkills() {
  habilidadesHidden.value = tags.join(", ");
}

function renderTags() {
  tagContainer?.querySelectorAll(".tag").forEach((el) => el.remove());

  tags.forEach((tag) => {
    const chip = document.createElement("button");
    chip.type = "button";
    chip.textContent = tag;
    chip.className =
      "tag bg-blue-600 text-white text-xs px-2 py-1 rounded-full cursor-pointer hover:bg-blue-500 transition";
    chip.addEventListener("click", () => {
      tags = tags.filter((t) => t !== tag);
      renderTags();
      syncHiddenSkills();
    });

    tagContainer.insertBefore(chip, tagField);
  });
}

if (tagField) {
  tagField.addEventListener("keydown", (e) => {
    if (e.key === "Enter" || e.key === ",") {
      e.preventDefault();
      const value = tagField.value.trim().replace(",", "");
      if (value && !tags.includes(value)) {
        tags.push(value);
        renderTags();
        syncHiddenSkills();
      }
      tagField.value = "";
    }
  });
}

/* === Photo preview === */
const fotoInput = $("#foto");
const photoPreview = $("#photoPreview");

function setPreviewPlaceholder() {
  if (!photoPreview) return;
  photoPreview.innerHTML = '<div class="text-xs text-neutral-500">No preview</div>';
}

if (fotoInput) {
  fotoInput.addEventListener("change", (e) => {
    const file = e.target.files && e.target.files[0];
    if (!file) {
      setPreviewPlaceholder();
      return;
    }
    if (!file.type.startsWith("image/")) {
      alert("Sube una imagen válida.");
      fotoInput.value = "";
      setPreviewPlaceholder();
      return;
    }
    if (file.size > 5 * 1024 * 1024) {
      alert("La imagen no puede superar 5MB.");
      fotoInput.value = "";
      setPreviewPlaceholder();
      return;
    }

    const url = URL.createObjectURL(file);
    photoPreview.innerHTML = "";
    const img = document.createElement("img");
    img.src = url;
    img.className = "w-full h-full object-cover";
    img.onload = () => URL.revokeObjectURL(url);
    photoPreview.appendChild(img);
  });
}

/* Resumen final (añadido mío) */
function generateSummary() {
  if (!summaryBox) return;

  syncHiddenSkills();

  const data = new FormData(form);
  summaryBox.innerHTML = "";

  const labels = {
    nombre: "Nombre",
    email: "Email",
    telefono: "Teléfono",
    ubicacion: "Ubicación",
    sobre_mi: "Sobre mí",     
    experiencia: "Experiencia",
    formacion: "Formación",
    habilidades: "Habilidades",
    idiomas: "Idiomas"
  };

  for (const [k, v] of data.entries()) {
    if (!v) continue;
    if (k === "foto") continue;

    const div = document.createElement("div");
    div.className = "bg-neutral-800 p-2 rounded-lg";
    div.innerHTML = `<span class="font-semibold">${labels[k] || k}:</span> ${String(v).replaceAll("\n", "<br>")}`;
    summaryBox.appendChild(div);
  }

  const file = fotoInput?.files?.[0];
  if (file) {
    const div = document.createElement("div");
    div.className = "bg-neutral-800 p-2 rounded-lg flex items-center gap-3";
    div.innerHTML = `<span class="font-semibold">Foto:</span> <span class="text-neutral-300 text-sm">${file.name}</span>`;
    summaryBox.appendChild(div);
  }
}

setPreviewPlaceholder();
showStep(0);