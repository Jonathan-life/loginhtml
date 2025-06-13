<?php
session_start();
if ($_SESSION["tipo"] != "admin") {
    header("Location: login.php");
    exit;
}

// Obtener RUC único si viene por GET
$ruc = $_GET["ruc"] ?? "";

// Obtener RUCs múltiples si vienen por POST o GET
$rucsSeleccionados = [];

if (!empty($_POST['rucs'])) {
    $rucsSeleccionados = $_POST['rucs'];
} elseif (!empty($_GET['rucs'])) {
    $rucsSeleccionados = explode(",", $_GET['rucs']);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Panel Asescon</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .sidebar {
      position: fixed;
      width: 23%;
      height: 100vh;
      background-color: #f8f9fa;
      padding: 20px;
    }
    .main-content {
      margin-left: 24%;
      padding: 30px;
    }
    .boton-menu, .cerrar-sesion {
      display: block;
      margin-bottom: 15px;
      font-size: 18px;
      color: #333;
      text-decoration: none;
    }
    .icono-img {
      width: 20px;
      margin-right: 8px;
    }
  </style>
</head>
<body class="bg-light">

<!-- Sidebar -->
<div class="sidebar">
  <div class="titulo">ASESCON</div>
  <hr class="linea-titulo" />
  <div class="contenedor-menu" id="menuAccordion">
    <a href="admin_dashboard.php" class="boton-menu">
      <img src="archivos.png" class="icono-img" alt="Admin" />
      Administrativo
    </a>
    <a href="upload_file.php" class="boton-menu">
      <img src="contilidad.png" class="icono-img" alt="Contabilidad" />
      Subir archivos
    </a>
    <a href="logout.php" class="cerrar-sesion">
      <img src="secion.png" class="icono-img" alt="Cerrar sesión" />
      Cerrar sesión
    </a>
  </div>
</div>

<!-- Main Content -->
<div class="main-content">
  <h3 class="mb-4">
    Subir archivo(s) PDF a:
    <?php if (!empty($ruc)): ?>
      <strong><?= htmlspecialchars($ruc) ?></strong>
    <?php elseif (!empty($rucsSeleccionados)): ?>
      <strong><?= count($rucsSeleccionados) ?> usuario(s)</strong>
    <?php else: ?>
      <strong>No definido</strong>
    <?php endif; ?>
  </h3>

  <form id="uploadForm" enctype="multipart/form-data">
    <?php if (!empty($ruc)): ?>
      <input type="hidden" name="ruc" value="<?= htmlspecialchars($ruc) ?>">
    <?php endif; ?>

    <?php if (!empty($rucsSeleccionados)): ?>
      <input type="hidden" name="rucs" value='<?= json_encode($rucsSeleccionados) ?>'>
    <?php endif; ?>

    <div class="mb-3">
      <label class="form-label">Selecciona archivo(s) PDF</label>
      <input type="file" name="archivos[]" class="form-control" accept="application/pdf" multiple required />
    </div>

    <button type="submit" class="btn btn-success">Subir</button>
  </form>

  <!-- Toast de respuesta -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="responseToast" class="toast align-items-center text-white bg-primary border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body" id="toastMsg">...</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script>
document.getElementById("uploadForm").addEventListener("submit", function (e) {
  e.preventDefault();
  if (!confirm("¿Estás seguro de subir los archivos seleccionados?")) return;

  const form = this;
  const formData = new FormData(form);

  fetch("upload.php", {
    method: "POST",
    body: formData,
  })
  .then(res => res.text())
  .then(msg => {
    showToast(msg, msg.includes("exitosamente") ? "success" : "danger");
    if (msg.includes("exitosamente")) form.reset();
  })
  .catch(() => {
    showToast("Error en la carga del archivo", "danger");
  });
});

function showToast(message, type = "primary") {
  const toastEl = document.getElementById("responseToast");
  const toastMsg = document.getElementById("toastMsg");

  toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
  toastMsg.innerText = message;

  const toast = new bootstrap.Toast(toastEl);
  toast.show();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
