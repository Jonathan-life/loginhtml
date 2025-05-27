<?php
session_start();
include "db.php";  // Conexión a la base de datos
// Si no existe la sesión o no es el tipo correcto, redirige al login
if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "admin") {
    header("Location: login.php");
    exit;
}
$_SESSION['admin'] = true;   // Datos admin
$_SESSION['user'] = true;    // Datos usuario

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1 class="text-center mb-4">Panel del Administrador</h1>

    <!-- Subir PDF -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">📤 Subir Archivo para un Usuario</div>
        <div class="card-body">
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">RUC del usuario</label>
                    <input type="text" name="ruc" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Archivo</label>
                    <input type="file" name="archivo" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Subir</button>
            </form>
        </div>
    </div>


    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">👤 Registrar Nuevo Usuario</div>
        <div class="card-body">
            <form id="createUserForm">
                <div class="mb-3">
                    <label class="form-label">RUC del nuevo usuario</label>
                    <input type="text" name="ruc" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="text" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Crear Usuario</button>
            </form>
        </div>
    </div>


    <div class="text-center">
        <a href="logout.php" class="btn btn-outline-danger">Cerrar sesión</a>
    </div>
</div>

<div class="toast-container">
    <div id="toastMessage" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastBody">Mensaje...</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
    </div>
</div>
<div class="text-center mb-4">
    <a href="ver_archivos.php" class="btn btn-info">📁 Ver Carpetas y Archivos</a>
</div>


<!-- Bootstrap JS y AJAX -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById("uploadForm").addEventListener("submit", function(e) {
    e.preventDefault();

    if (!confirm("¿Estás seguro de subir este archivo?")) return;

    const form = this;
    const formData = new FormData(form);

    fetch("upload.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(msg => {
        showToast(msg, msg.includes("exitosamente") ? 'success' : 'danger');
        if (msg.includes("exitosamente")) {
            form.reset();  // Limpiar formulario si fue exitoso
        }
    });
});

document.getElementById("createUserForm").addEventListener("submit", function(e) {
    e.preventDefault();

    if (!confirm("¿Deseas crear este usuario?")) return;

    const form = this;
    const formData = new FormData(form);

    fetch("registrar_usuario.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(msg => {
        showToast(msg, msg.includes("creado") ? 'success' : 'danger');
        if (msg.includes("creado")) {
            form.reset();  // Limpiar formulario si fue exitoso
        }
    });
});

function showToast(message, type = 'success') {
    const toastEl = document.getElementById("toastMessage");
    const toastBody = document.getElementById("toastBody");
    toastBody.innerText = message;
    toastEl.className = `toast align-items-center text-bg-${type} border-0`;
    new bootstrap.Toast(toastEl).show();
}
</script>


</body>
</html>
