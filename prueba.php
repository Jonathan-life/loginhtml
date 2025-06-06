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
  <meta charset="UTF-8" />
  <title>Panel Asescon</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>

  body, html {
    height: 100%;
    margin: 0;
  }

  .sidebar {
    height: 100vh;
    width: 22%;
    background-color: rgb(8, 60, 112);
    color: white;
    position: fixed;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding-top: 2rem;
    z-index: 1000; /* Para asegurar que la barra quede encima */
  }

  .main-content {
    margin-left: 22%; /* desplazamos el contenido para que no esté debajo */
    padding: 2rem;
  }
  .flecha-toggle {
      width: 100%;
      padding-left: 20px;
      padding-right: 6rem;
      margin-top: auto;
      display: flex;
      flex-direction: column;
      gap: 2rem;
}


    .titulo {
      width: 100%;
      text-align: center;
      margin-top: 40px;
      letter-spacing: 4px;
      font-size: 2.3rem;
      font-weight: bold;
      margin-bottom: 2rem;
    }
    .linea-titulo {
      width: 100%;
      margin: 0 auto 4rem auto;
      margin-top: 15px;
      border: none;
      border-bottom: 3px solid #ccc;
    }


    .contenedor-menu {
      width: 100%;
      padding-left: 60px;
      padding-right: 1rem;
      margin-top: -2rem;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .boton-menu,
    .btn-toggle,
    .sub-opcion {
      color: white;
      font-size: 1.2rem;
      font-weight: 400;
      text-align: left;
      background: none;
      letter-spacing: 1px;
      border: none;
      padding: 0.5rem 1rem;
      display: flex;
      align-items: center;
      gap: 0.7rem;
      width: 100%;
      text-decoration: none;
      cursor: pointer;
    }

    .btn-toggle {
      justify-content: space-between;
      border-radius: 8px;
    }

    .btn-toggle span {
      display: flex;
      align-items: center;
      gap: 0.7rem;
    }

    .boton-menu:hover,
    .btn-toggle:hover,
    .sub-opcion:hover,
    .cerrar-sesion:hover {
      background-color: #124b7f;
      border-radius: 8px;
    }

    .submenu {
      padding-left: 2rem;
      margin-top: 0.3rem;
    }

    .cerrar-sesion {
      color: white;
      text-decoration: none;
      padding: 0.5rem;
      width: 100%;
      font-size: 20px;
      text-align: left;
      border: none;
      margin-right: 50px;
      background: none;
      display: flex;
      align-items: center;
      gap: 0.7rem;
      cursor: pointer;


    }

    .icono-menu {
      font-size: 1rem;
      user-select: none;
    }

    .icono-img {
      width: 25px;
      height: 25px;
      object-fit: contain;
    }
  </style>
</head>
<body class="bg-light">

  <div class="sidebar">
    <div class="titulo">ASESCON</div>
      <hr class="linea-titulo" />

    
    <div class="contenedor-menu" id="menuAccordion">

      <!-- Administrativo -->
      <a href="#" class="boton-menu">
        <img src="archivos.png" class="icono-img" alt="Admin" />
        Administrativo
      </a>

      <!-- Contabilidad -->
      <button class="btn-toggle" data-bs-toggle="collapse" data-bs-target="#contaCollapse" aria-expanded="true">
        <span>
          <img src="contilidad.png" class="icono-img" alt="Contabilidad" />
          Contabilidad
        </span>
        <span class="icono-menu flecha-toggle">▲</span>
      </button>
      <div id="contaCollapse" class="collapse show submenu" data-bs-parent="#menuAccordion">
        <!-- Subopciones de contabilidad -->
        <a href="#" class="sub-opcion">
          <class="icono-img" alt="Cuentas" />
          Cuentas Contables
        </a>
      </div>

      <!-- Comprobantes contables -->
      <button class="btn-toggle" data-bs-toggle="collapse" data-bs-target="#comproCollapse" aria-expanded="false">
        <span>
          <class="icono-img" alt="Comprobantes" />
          Comprobantes contables
        </span>
        <span class="icono-menu">▲</span>
      </button>
      <div id="comproCollapse" class="collapse submenu" data-bs-parent="#menuAccordion">
        <!-- Subopciones de comprobantes -->
        <a href="#" class="sub-opcion">
        </a>
      </div>

    <a href="logout.php" class="cerrar-sesion">
      <img src="secion.png" class="icono-img" alt="Cerrar sesión" />
      Cerrar sesión
    </a>

    </div> <!-- FIN contenedor-menu -->
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- CONTENIDO PRINCIPAL -->
  <div class="main-content">
    <h1 class="text-center mb-4">Panel del Administrador</h1>

    <!-- Subir PDF -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">📤 Subir Archivo para un Usuario</div>
      <div class="card-body">
        <form id="uploadForm" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">RUC del usuario</label>
            <input type="text" name="ruc" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Archivo</label>
            <input type="file" name="archivo" class="form-control" required />
          </div>
          <button type="submit" class="btn btn-success">Subir</button>
        </form>
      </div>
    </div>

    <!-- Registrar usuario -->
    <div class="card mb-4">
      <div class="card-header bg-secondary text-white">👤 Registrar Nuevo Usuario</div>
      <div class="card-body">
        <form id="createUserForm">
          <div class="mb-3">
            <label class="form-label">RUC del nuevo usuario</label>
            <input type="text" name="ruc" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="text" name="password" class="form-control" required />
          </div>
          <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </form>
      </div>
    </div>
  </div>

  <!-- TOAST -->
  <div class="toast-container">
    <div id="toastMessage" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body" id="toastBody">Mensaje...</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Formulario subir archivo
    document.getElementById("uploadForm").addEventListener("submit", function (e) {
      e.preventDefault();
      if (!confirm("¿Estás seguro de subir este archivo?")) return;

      const form = this;
      const formData = new FormData(form);

      fetch("upload.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.text())
        .then((msg) => {
          showToast(msg, msg.includes("exitosamente") ? "success" : "danger");
          if (msg.includes("exitosamente")) {
            form.reset();
          }
        });
    });

    // Formulario crear usuario
    document.getElementById("createUserForm").addEventListener("submit", function (e) {
      e.preventDefault();
      if (!confirm("¿Deseas crear este usuario?")) return;

      const form = this;
      const formData = new FormData(form);

      fetch("registrar_usuario.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.text())
        .then((msg) => {
          showToast(msg, msg.includes("creado") ? "success" : "danger");
          if (msg.includes("creado")) {
            form.reset();
          }
        });
    });

    // Función mostrar toast
    function showToast(message, type = "success") {
      const toastEl = document.getElementById("toastMessage");
      const toastBody = document.getElementById("toastBody");
      toastBody.innerText = message;
      toastEl.className = `toast align-items-center text-bg-${type} border-0`;
      const toast = new bootstrap.Toast(toastEl);
      toast.show();
    }

    // Toggle menú lateral
    document.querySelectorAll('.btn-toggle').forEach(button => {
      button.addEventListener('click', () => {
        const targetId = button.getAttribute('data-target');
        const target = document.querySelector(targetId);
        const isVisible = target.classList.contains('show');

        // Cerrar todos los submenús
        document.querySelectorAll('.submenu').forEach(menu => {
          menu.classList.remove('show');
        });

        // Resetear todos los íconos a flecha abajo
        document.querySelectorAll('.btn-toggle .icono-menu').forEach(icon => {
          icon.textContent = '▼';
        });

        if (!isVisible) {
          target.classList.add('show');
          const icon = button.querySelector('.icono-menu');
          icon.textContent = '▲';
        }
      });
    });
  </script>
  
</body>
</html>
